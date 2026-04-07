<?php

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Http\Controllers\RequerimientoClienteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

define('LARAVEL_START', microtime(true));

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$kernel->bootstrap();

try {
    $user = User::first();
    if (!$user) {
        echo "No users found.\n";
        exit;
    }
    Auth::login($user);

    // Create a dummy requirement to test relations
    $reqRecord = \App\Models\RequerimientoCliente::create([
        'cliente_id' => \App\Models\ClienteMaestro::first()->id ?? 0,
        'tipo_soporte_id' => \App\Models\TipoSoporte::first()->id ?? 0,
        'texto_imagen' => 'Test requirement',
        'estado_id' => \App\Models\EstadoRequerimiento::first()->id ?? 0,
        'user_id' => $user->id,
        'asignado_user_id' => $user->id,
    ]);

    $controller = new RequerimientoClienteController();
    $request = Request::create('/requerimientos', 'GET');
    $response = $controller->index($request);
    if ($response instanceof \Illuminate\View\View) {
        $html = $response->render();
        echo "View rendered successfully.\n";
    } else {
        echo "Response status: " . $response->status() . "\n";
    }

    // Clean up
    $reqRecord->delete();
} catch (\Throwable $e) {
    echo "Exception caught: " . get_class($e) . "\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
