<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Models\Roles;
use App\Models\RoleUser;

try {
    echo "--- Roles (de la tabla 'roles') ---\n";
    if (class_exists(Roles::class)) {
        foreach (Roles::all() as $r) {
            echo "ID: {$r->id} - Nombre: {$r->nombre}\n";
        }
    } else {
        echo "No existe la clase Roles.\n";
    }

    echo "\n--- Roles de Usuario (de la tabla 'role_user') ---\n";
    if (class_exists(RoleUser::class)) {
        foreach (RoleUser::all() as $ru) {
            echo "ID: {$ru->id} - Nombre: {$ru->nombre}\n";
        }
    } else {
        echo "No existe la clase RoleUser.\n";
    }
} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
