<?php

namespace App\Services\Backup;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use ZipArchive;

/**
 * BackupService
 *
 * PHP-native MySQL backup + restore. No shell_exec / mysqldump dependency,
 * so it works the same on local XAMPP and shared hosting (Hostinger).
 *
 * Backup files live in storage/app/backups/ (outside public/).
 */
class BackupService
{
    public const TYPE_FULL = 'full';
    public const TYPE_DB = 'db';

    public const BACKUP_DIR = 'backups';

    public const SQL_CHUNK_SIZE = 500;

    private const STATEMENT_DELIMITER = '-- @CASTCREW_STMT_END@';

    public function listBackups(): array
    {
        $disk = Storage::disk('local');
        $disk->makeDirectory(self::BACKUP_DIR);

        $files = $disk->files(self::BACKUP_DIR);
        $items = [];

        foreach ($files as $path) {
            $filename = basename($path);
            if (!preg_match('/^castcrew_backup_(full|db|prerestore)_/', $filename)) {
                continue;
            }

            $type = 'db';
            if (str_starts_with($filename, 'castcrew_backup_full_')) $type = 'full';
            elseif (str_starts_with($filename, 'castcrew_backup_prerestore_')) $type = 'prerestore';

            $items[] = [
                'filename' => $filename,
                'path' => $path,
                'type' => $type,
                'size' => $disk->size($path),
                'size_human' => $this->humanFileSize($disk->size($path)),
                'created_at' => Carbon::createFromTimestamp($disk->lastModified($path)),
            ];
        }

        usort($items, fn($a, $b) => $b['created_at']->timestamp <=> $a['created_at']->timestamp);
        return $items;
    }

    public function createBackup(string $type = self::TYPE_FULL): array
    {
        if (!in_array($type, [self::TYPE_FULL, self::TYPE_DB], true)) {
            throw new RuntimeException("Invalid backup type: {$type}");
        }

        @set_time_limit(0);
        @ini_set('memory_limit', '512M');

        $timestamp = now()->format('Y-m-d_His');
        $disk = Storage::disk('local');
        $disk->makeDirectory(self::BACKUP_DIR);

        $sqlFilename = "castcrew_db_{$timestamp}.sql";
        $sqlAbsolute = storage_path('app/' . self::BACKUP_DIR . '/' . $sqlFilename);

        $this->dumpDatabaseToFile($sqlAbsolute);

        if ($type === self::TYPE_DB) {
            $finalName = "castcrew_backup_db_{$timestamp}.sql";
            $finalPath = self::BACKUP_DIR . '/' . $finalName;
            $disk->move(self::BACKUP_DIR . '/' . $sqlFilename, $finalPath);

            return [
                'filename' => $finalName,
                'path' => $finalPath,
                'type' => self::TYPE_DB,
                'size' => $disk->size($finalPath),
            ];
        }

        $zipName = "castcrew_backup_full_{$timestamp}.zip";
        $zipAbsolute = storage_path('app/' . self::BACKUP_DIR . '/' . $zipName);

        $zip = new ZipArchive();
        if ($zip->open($zipAbsolute, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            @unlink($sqlAbsolute);
            throw new RuntimeException('Failed to create zip archive.');
        }

        $zip->addFile($sqlAbsolute, 'database.sql');

        $publicStorage = storage_path('app/public');
        if (is_dir($publicStorage)) {
            $this->addDirectoryToZip($zip, $publicStorage, 'storage/app/public');
        }

        $manifest = json_encode([
            'system' => 'CastCrew',
            'created_at' => now()->toIso8601String(),
            'type' => 'full',
            'database' => config('database.connections.mysql.database'),
            'app_version' => config('app.version', 'unknown'),
        ], JSON_PRETTY_PRINT);
        $zip->addFromString('manifest.json', $manifest);

        $zip->close();
        @unlink($sqlAbsolute);

        return [
            'filename' => $zipName,
            'path' => self::BACKUP_DIR . '/' . $zipName,
            'type' => self::TYPE_FULL,
            'size' => filesize($zipAbsolute),
        ];
    }

    public function restoreFromUpload(string $uploadedFilePath, bool $createSafetyBackup = true): array
    {
        @set_time_limit(0);
        @ini_set('memory_limit', '512M');

        if (!file_exists($uploadedFilePath)) {
            throw new RuntimeException('Uploaded backup file not found.');
        }

        $safetyBackup = null;
        if ($createSafetyBackup) {
            $timestamp = now()->format('Y-m-d_His');
            $safetyName = "castcrew_backup_prerestore_{$timestamp}.sql";
            $safetyAbsolute = storage_path('app/' . self::BACKUP_DIR . '/' . $safetyName);
            Storage::disk('local')->makeDirectory(self::BACKUP_DIR);
            $this->dumpDatabaseToFile($safetyAbsolute);
            $safetyBackup = $safetyName;
        }

        // Detect file type by magic bytes — the uploaded file lives at a temp path
        // like "/tmp/phpXXXX" with no useful extension, so pathinfo() can't be trusted.
        $isZip = false;
        $fh = @fopen($uploadedFilePath, 'rb');
        if ($fh !== false) {
            $head = fread($fh, 4);
            fclose($fh);
            $isZip = ($head === "PK\x03\x04" || $head === "PK\x05\x06" || $head === "PK\x07\x08");
        }

        if (!$isZip) {
            $this->executeSqlFile($uploadedFilePath);
            return ['safety_backup' => $safetyBackup, 'restored_files' => false];
        }

        {
            $extractDir = storage_path('app/' . self::BACKUP_DIR . '/_restore_' . uniqid());
            if (!mkdir($extractDir, 0755, true) && !is_dir($extractDir)) {
                throw new RuntimeException('Failed to create temp restore directory.');
            }

            try {
                $zip = new ZipArchive();
                if ($zip->open($uploadedFilePath) !== true) {
                    throw new RuntimeException('Failed to open uploaded zip archive.');
                }
                $zip->extractTo($extractDir);
                $zip->close();

                $sqlPath = $extractDir . '/database.sql';
                if (!file_exists($sqlPath)) {
                    throw new RuntimeException('Backup archive is missing database.sql.');
                }
                $this->executeSqlFile($sqlPath);

                $restoredFiles = false;
                $sourceFiles = $extractDir . '/storage/app/public';
                if (is_dir($sourceFiles)) {
                    $this->copyDirectory($sourceFiles, storage_path('app/public'));
                    $restoredFiles = true;
                }

                return ['safety_backup' => $safetyBackup, 'restored_files' => $restoredFiles];
            } finally {
                $this->deleteDirectory($extractDir);
            }
        }
    }

    public function deleteBackup(string $filename): bool
    {
        if (!preg_match('/^castcrew_backup_(full|db|prerestore)_[\w\-]+\.(sql|zip)$/', $filename)) {
            throw new RuntimeException('Invalid backup filename.');
        }

        $path = self::BACKUP_DIR . '/' . $filename;
        $disk = Storage::disk('local');
        if (!$disk->exists($path)) {
            return false;
        }
        return $disk->delete($path);
    }

    public function pruneOldBackups(int $keepCount = 4): int
    {
        $auto = array_values(array_filter(
            $this->listBackups(),
            fn($b) => $b['type'] === 'full' || $b['type'] === 'db'
        ));

        $deleted = 0;
        if (count($auto) > $keepCount) {
            $toDelete = array_slice($auto, $keepCount);
            foreach ($toDelete as $item) {
                if ($this->deleteBackup($item['filename'])) {
                    $deleted++;
                }
            }
        }
        return $deleted;
    }

    public function getAbsolutePath(string $filename): string
    {
        if (!preg_match('/^castcrew_backup_(full|db|prerestore)_[\w\-]+\.(sql|zip)$/', $filename)) {
            throw new RuntimeException('Invalid backup filename.');
        }
        return storage_path('app/' . self::BACKUP_DIR . '/' . $filename);
    }

    private function dumpDatabaseToFile(string $absolutePath): void
    {
        $dir = dirname($absolutePath);
        if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
            throw new RuntimeException("Failed to create backup directory: {$dir}");
        }

        $handle = fopen($absolutePath, 'w');
        if ($handle === false) {
            throw new RuntimeException("Could not open backup file for writing: {$absolutePath}");
        }

        try {
            $dbName = config('database.connections.mysql.database');
            $delim = self::STATEMENT_DELIMITER;

            fwrite($handle, "-- CastCrew Database Backup\n");
            fwrite($handle, "-- Generated: " . now()->toIso8601String() . "\n");
            fwrite($handle, "-- Database: {$dbName}\n\n");
            fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n{$delim}\n");
            fwrite($handle, "SET NAMES utf8mb4;\n{$delim}\n");
            fwrite($handle, "SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';\n{$delim}\n\n");

            $tables = DB::select('SHOW TABLES');
            $tableKey = 'Tables_in_' . $dbName;

            foreach ($tables as $tableObj) {
                $table = $tableObj->{$tableKey} ?? array_values((array)$tableObj)[0];
                $this->writeTableToHandle($handle, $table);
            }

            fwrite($handle, "\nSET FOREIGN_KEY_CHECKS=1;\n{$delim}\n");
        } finally {
            fclose($handle);
        }
    }

    private function writeTableToHandle($handle, string $table): void
    {
        $quotedTable = '`' . str_replace('`', '``', $table) . '`';
        $delim = self::STATEMENT_DELIMITER;

        fwrite($handle, "\n-- ----------------------------\n");
        fwrite($handle, "-- Table: {$table}\n");
        fwrite($handle, "-- ----------------------------\n");
        fwrite($handle, "DROP TABLE IF EXISTS {$quotedTable};\n{$delim}\n");

        $createRow = DB::select("SHOW CREATE TABLE {$quotedTable}");
        $createSql = $createRow[0]->{'Create Table'} ?? null;
        if ($createSql) {
            fwrite($handle, $createSql . ";\n{$delim}\n\n");
        }

        $offset = 0;
        while (true) {
            $rows = DB::select("SELECT * FROM {$quotedTable} LIMIT " . self::SQL_CHUNK_SIZE . " OFFSET {$offset}");
            if (empty($rows)) break;

            foreach ($rows as $row) {
                $rowArr = (array)$row;
                $cols = array_map(fn($c) => '`' . str_replace('`', '``', $c) . '`', array_keys($rowArr));
                $vals = array_map(fn($v) => $this->escapeValue($v), array_values($rowArr));
                fwrite($handle, "INSERT INTO {$quotedTable} (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ");\n{$delim}\n");
            }

            if (count($rows) < self::SQL_CHUNK_SIZE) break;
            $offset += self::SQL_CHUNK_SIZE;
        }
    }

    private function escapeValue($value): string
    {
        if ($value === null) return 'NULL';
        if (is_bool($value)) return $value ? '1' : '0';
        if (is_int($value) || is_float($value)) return (string)$value;
        return DB::getPdo()->quote((string)$value);
    }

    private function executeSqlFile(string $path): void
    {
        $handle = fopen($path, 'r');
        if ($handle === false) {
            throw new RuntimeException("Could not open SQL file: {$path}");
        }

        $pdo = DB::getPdo();
        $buffer = '';
        $delim = self::STATEMENT_DELIMITER;
        $hasDelimiter = false;

        try {
            $pdo->exec('SET FOREIGN_KEY_CHECKS=0');

            while (!feof($handle)) {
                $line = fgets($handle);
                if ($line === false) break;

                if (rtrim($line) === $delim) {
                    $hasDelimiter = true;
                    $statement = trim(rtrim(trim($buffer), ';'));
                    if ($statement !== '') {
                        $pdo->exec($statement);
                    }
                    $buffer = '';
                    continue;
                }

                $buffer .= $line;
            }

            // Fallback: legacy SQL files (manually uploaded mysqldump output) without our delimiter.
            if (!$hasDelimiter) {
                $remaining = trim($buffer);
                if ($remaining !== '') {
                    $this->executeLegacySql($pdo, $remaining);
                }
            } else {
                $remaining = trim(rtrim(trim($buffer), ';'));
                if ($remaining !== '') {
                    $pdo->exec($remaining);
                }
            }
        } finally {
            $pdo->exec('SET FOREIGN_KEY_CHECKS=1');
            fclose($handle);
        }
    }

    /**
     * Best-effort splitter for SQL files without our delimiter (e.g. mysqldump output).
     * Splits on `;` at end of line, ignoring lines that begin with `--` or `/*`.
     */
    private function executeLegacySql(\PDO $pdo, string $sql): void
    {
        $lines = preg_split('/\r?\n/', $sql);
        $buffer = '';
        foreach ($lines as $line) {
            $trim = ltrim($line);
            if ($trim === '' || str_starts_with($trim, '--') || str_starts_with($trim, '/*')) {
                continue;
            }
            $buffer .= $line . "\n";
            if (preg_match('/;\s*$/', $line)) {
                $statement = trim(rtrim(trim($buffer), ';'));
                if ($statement !== '') {
                    $pdo->exec($statement);
                }
                $buffer = '';
            }
        }
        $remaining = trim(rtrim(trim($buffer), ';'));
        if ($remaining !== '') {
            $pdo->exec($remaining);
        }
    }

    private function addDirectoryToZip(ZipArchive $zip, string $sourceDir, string $zipPath): void
    {
        $sourceDir = rtrim($sourceDir, DIRECTORY_SEPARATOR);
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($sourceDir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            $localPath = substr($file->getPathname(), strlen($sourceDir) + 1);
            $localPath = str_replace(DIRECTORY_SEPARATOR, '/', $localPath);
            $entryName = $zipPath . '/' . $localPath;

            if ($file->isDir()) {
                $zip->addEmptyDir($entryName);
            } else {
                $zip->addFile($file->getPathname(), $entryName);
            }
        }
    }

    private function copyDirectory(string $source, string $destination): void
    {
        if (!is_dir($destination) && !mkdir($destination, 0755, true) && !is_dir($destination)) {
            throw new RuntimeException("Failed to create directory: {$destination}");
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            $relative = substr($file->getPathname(), strlen($source) + 1);
            $target = $destination . DIRECTORY_SEPARATOR . $relative;

            if ($file->isDir()) {
                if (!is_dir($target)) @mkdir($target, 0755, true);
            } else {
                @copy($file->getPathname(), $target);
            }
        }
    }

    private function deleteDirectory(string $path): void
    {
        if (!is_dir($path)) return;
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($iterator as $file) {
            if ($file->isDir()) @rmdir($file->getPathname());
            else @unlink($file->getPathname());
        }
        @rmdir($path);
    }

    private function humanFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
