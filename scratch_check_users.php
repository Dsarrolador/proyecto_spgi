<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Models\User;

try {
    echo "--- Usuarios del Sistema ---\n";
    foreach (User::with('role')->get() as $u) {
        $roleName = $u->role ? $u->role->nombre : 'Sin Rol';
        echo "ID: {$u->id} - Nombre: {$u->name} - Email: {$u->email} - Rol ID (cod_roleUser): {$u->cod_roleUser} ({$roleName})\n";
    }
} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
