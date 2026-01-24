<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = App\Models\User::withTrashed()
    ->where(function($q) {
        $q->where('email', 'like', '%kakslauttanen%')
          ->orWhere('role', 'company')
          ->orWhere('role', 'manager')
          ->orWhere('role', 'contracted_client');
    })
    ->get(['id', 'name', 'email', 'role', 'deleted_at']);

echo "=== Users with company/manager roles ===\n";
foreach ($users as $user) {
    echo "ID: {$user->id}\n";
    echo "Name: {$user->name}\n";
    echo "Email: {$user->email}\n";
    echo "Role: {$user->role}\n";
    echo "Deleted: " . ($user->deleted_at ? $user->deleted_at : 'No') . "\n";
    echo "---\n";
}

if ($users->isEmpty()) {
    echo "No users found with these roles!\n";
}
