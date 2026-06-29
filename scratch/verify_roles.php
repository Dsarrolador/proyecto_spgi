<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Models\User;

try {
    echo "=== VERIFICACIÓN DE ACCESOS ADMINISTRATIVOS POR USUARIO ===\n";
    foreach (User::with('role')->get() as $u) {
        $roleName = $u->role ? $u->role->nombre : 'Sin Rol';
        $esAdmin = $u->es_admin ? 'SÍ' : 'NO';
        $esEncargado = $u->es_encargado ? 'SÍ' : 'NO';
        $esAdministrativo = $u->es_administrativo ? 'SÍ' : 'NO';
        
        echo "Usuario: {$u->name} | Rol: {$roleName} (cod: {$u->cod_roleUser})\n";
        echo "  - es_admin: {$esAdmin}\n";
        echo "  - es_encargado: {$esEncargado}\n";
        echo "  - es_administrativo (NUEVO): {$esAdministrativo}\n";
        echo "---------------------------------------------------------\n";
    }
} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
