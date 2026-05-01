<?php

namespace App\Console\Commands;

use App\Services\Backup\BackupService;
use App\Services\CompanySettingService;
use Illuminate\Console\Command;
use Throwable;

class RunAutoBackup extends Command
{
    protected $signature = 'opticrew:auto-backup {--keep=4 : How many recent auto-backups to keep}';
    protected $description = 'Run weekly automatic full backup of CastCrew (database + uploaded files), then prune old backups.';

    public function handle(BackupService $backups): int
    {
        $enabled = (int) CompanySettingService::get('auto_backup_enabled', 1) === 1;
        if (!$enabled) {
            $this->info('Auto-backup is disabled in admin settings. Skipping.');
            return self::SUCCESS;
        }

        $this->info('Creating full automatic backup...');
        try {
            $result = $backups->createBackup(BackupService::TYPE_FULL);
            $this->info("Backup created: {$result['filename']}");
        } catch (Throwable $e) {
            $this->error('Auto-backup failed: ' . $e->getMessage());
            \Log::error('Auto-backup failed', ['error' => $e->getMessage()]);
            return self::FAILURE;
        }

        $keep = (int) $this->option('keep');
        $deleted = $backups->pruneOldBackups($keep);
        if ($deleted > 0) {
            $this->info("Pruned {$deleted} old backup(s) (keeping last {$keep}).");
        }

        return self::SUCCESS;
    }
}
