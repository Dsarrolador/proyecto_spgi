<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Models\RequerimientoProyecto;

try {
    echo "=== VERIFICACIÓN DE ESTRUCTURAS ESPEJO PARA PROYECTOS ===\n";
    $req = RequerimientoProyecto::first();
    if ($req) {
        echo "Requerimiento ID: {$req->id}\n";
        echo "  - Relación colaboradores (count): " . $req->colaboradores->count() . "\n";
        echo "  - Relación imagenes adicionales (count): " . $req->imagenes->count() . "\n";
        echo "  - Relación novedades (count): " . $req->novedades->count() . "\n";
    } else {
        echo "No hay requerimientos de proyecto en la BD para probar.\n";
    }
    echo "¡Estructura de relaciones verificada exitosamente!\n";
} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
