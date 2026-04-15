<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();


use App\Http\Controllers\RequerimientoClienteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

try {
    $user = User::first();
    if (!$user) {
        throw new \Exception("No users found in database.");
    }
    Auth::login($user);
    $controller = new RequerimientoClienteController();
    $request = Request::create('/requerimientos', 'GET');
    $response = $controller->index($request);
    echo "Response status: " . $response->status() . "\n";
} catch (\Throwable $e) {
    echo "Exception caught: " . get_class($e) . "\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
