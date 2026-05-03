<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Backup\BackupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class BackupController extends Controller
{
    public function __construct(private BackupService $backups) {}

    public function index()
    {
        $items = $this->backups->listBackups();
        $autoBackupEnabled = (bool) \App\Services\CompanySettingService::get('auto_backup_enabled', 1);

        return view('admin.backup', [
            'backups' => $items,
            'autoBackupEnabled' => $autoBackupEnabled,
        ]);
    }

    public function create(Request $request)
    {
        $request->validate([
            'type' => 'required|in:full,db',
        ]);

        try {
            $result = $this->backups->createBackup($request->input('type'));
            return response()->json([
                'success' => true,
                'message' => 'Backup created successfully.',
                'backup' => [
                    'filename' => $result['filename'],
                    'type' => $result['type'],
                ],
            ]);
        } catch (Throwable $e) {
            Log::error('Backup creation failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to create backup: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function download(string $filename): BinaryFileResponse
    {
        $absolute = $this->backups->getAbsolutePath($filename);
        abort_unless(file_exists($absolute), 404, 'Backup file not found.');
        return response()->download($absolute, $filename);
    }

    public function delete(Request $request, string $filename)
    {
        $request->validate(['password' => 'required|string']);

        if (!Hash::check($request->input('password'), $request->user()->password)) {
            return response()->json(['success' => false, 'message' => 'Incorrect password.'], 403);
        }

        try {
            $this->backups->deleteBackup($filename);
            return response()->json(['success' => true, 'message' => 'Backup deleted.']);
        } catch (Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function restore(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|mimes:sql,zip|max:512000',
            'password' => 'required|string',
            'confirm' => 'required|string|in:RESTORE',
        ]);

        if (!Hash::check($request->input('password'), $request->user()->password)) {
            return response()->json(['success' => false, 'message' => 'Incorrect password.'], 403);
        }

        try {
            $uploaded = $request->file('backup_file');
            $tempPath = $uploaded->getRealPath();

            $result = $this->backups->restoreFromUpload($tempPath, true);

            return response()->json([
                'success' => true,
                'message' => 'Restore completed successfully. A safety backup of your previous data was created.',
                'safety_backup' => $result['safety_backup'],
                'restored_files' => $result['restored_files'],
            ]);
        } catch (Throwable $e) {
            Log::error('Backup restore failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Restore failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function toggleAuto(Request $request)
    {
        $request->validate(['enabled' => 'required|boolean']);
        \App\Services\CompanySettingService::set(
            'auto_backup_enabled',
            $request->boolean('enabled') ? 1 : 0,
            'integer',
            'Whether weekly automatic backups run on schedule'
        );
        return response()->json(['success' => true]);
    }
}
