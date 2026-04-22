<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ClienteMaestroController;
use App\Http\Controllers\LibretaContactoController;
use App\Http\Controllers\RequerimientoClienteController;
use App\Http\Controllers\NovedadRequerimientoController;
use App\Http\Controllers\TipoSoporteController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\CategoriaIgualaController;
use App\Http\Controllers\Mantenimiento\EstadoRequerimientoController;
use App\Http\Controllers\ProyectoController;
use App\Http\Controllers\RequerimientoProyectoController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\WikiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RoleUserController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\LeadRequirementController;
use App\Http\Controllers\CatEquipoController;
use App\Http\Controllers\CatTipoEquipoController;
use App\Http\Controllers\ClienteEntornoController;

/*
|--------------------------------------------------------------------------
| 🔐 RUTAS PÚBLICAS
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| 🔒 RUTAS PRIVADAS (AUTH)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    Route::get('/seleccion', function () {
        return view('seleccion');
    })->name('seleccion');

    Route::get('/bienvenido', function () {
        return view('bienvenido');
    })->name('bienvenido');

    Route::middleware('comercial')->group(function () {
        Route::get('/comerciales/bienvenido', [LeadController::class, 'bienvenido'])->name('leads.bienvenido');
        Route::get('/comerciales/reportes', [LeadController::class, 'reportes'])->name('leads.reportes');
        Route::resource('lead-requirements', LeadRequirementController::class);
        Route::resource('leads', LeadController::class);

        // Administración Dashboard y Facturación
        Route::get('/administracion/bienvenido', function () {
            return view('administracion.bienvenido');
        })->name('administracion.bienvenido');

        Route::get('/requerimientos/facturacion', [RequerimientoClienteController::class, 'facturacion'])
            ->name('requerimientos.facturacion');
        Route::post('/requerimientos/{id}/subir-factura', [RequerimientoClienteController::class, 'subirFactura'])
            ->name('requerimientos.subir-factura');
        Route::post('/requerimientos/{id}/toggle-facturado', [RequerimientoClienteController::class, 'toggleFacturado'])
            ->name('requerimientos.toggle-facturado');
    });

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/iguala-control', [DashboardController::class, 'igualaControl'])->name('dashboard.iguala-control')->middleware('comercial');
    Route::get('/api/cliente-metrics/{id}', [DashboardController::class, 'getClienteMetrics'])->name('api.cliente-metrics');

    /*
    |--------------------------------------------------------------------------
    | 🔔 NOTIFICACIONES
    |--------------------------------------------------------------------------
    */
    Route::get('/api/notificaciones/unread', [NotificacionController::class, 'getUnread'])->name('api.notificaciones.unread');
    Route::get('/notificaciones', [NotificacionController::class, 'index'])->name('notificaciones.index');
    Route::post('/api/notificaciones/{id}/read', [NotificacionController::class, 'markAsRead'])->name('api.notificaciones.read');
    Route::delete('/api/notificaciones/delete-all', [NotificacionController::class, 'destroyAll'])->name('api.notificaciones.destroyAll');
    Route::delete('/api/notificaciones/{id}', [NotificacionController::class, 'destroy'])->name('api.notificaciones.destroy');
    Route::get('/notificaciones/admin', [NotificacionController::class, 'adminPanel'])->name('notificaciones.admin')->middleware('comercial');
    Route::post('/notificaciones/send', [NotificacionController::class, 'send'])->name('notificaciones.send')->middleware('comercial');

    /*
    |--------------------------------------------------------------------------
    | 📦 STORAGE PROXY (FTP)
    |--------------------------------------------------------------------------
    */
    Route::get('/storage-proxy/{path}', [\App\Http\Controllers\FileProxyController::class, 'stream'])
        ->where('path', '.*')
        ->name('storage.proxy');

    /*
    |--------------------------------------------------------------------------
    | 📚 WIKI
    |--------------------------------------------------------------------------
    */
    Route::get('/wiki', [WikiController::class, 'index'])->name('wiki.index');
    Route::post('/wiki', [WikiController::class, 'store'])->name('wiki.store');
    Route::put('/wiki/{wikiDocument}', [WikiController::class, 'update'])->name('wiki.update');
    Route::get('/wiki/{wikiDocument}/download', [WikiController::class, 'download'])->name('wiki.download');
    Route::delete('/wiki/{wikiDocument}', [WikiController::class, 'destroy'])->name('wiki.destroy');
    Route::post('/wiki/{wikiDocument}/approve', [WikiController::class, 'approve'])->name('wiki.approve');
    Route::post('/wiki/sync-paths', [WikiController::class, 'syncPaths'])->name('wiki.sync-paths');

    /*
    |--------------------------------------------------------------------------
    | 👤 USUARIOS
    |--------------------------------------------------------------------------
    */
    Route::resource('usuarios', UsuarioController::class)->middleware(\App\Http\Middleware\CheckAdmin::class);

    /*
    |--------------------------------------------------------------------------
    | 🏢 CLIENTES
    |--------------------------------------------------------------------------
    */
    Route::resource('clientes', ClienteMaestroController::class);

    Route::get('/clientes/{cliente}/contactos', [ClienteMaestroController::class, 'contactos'])
        ->name('clientes.contactos');

    /*
    |--------------------------------------------------------------------------
    | 📒 LIBRETA DE CONTACTOS
    |--------------------------------------------------------------------------
    */
    Route::resource('libreta_contacto', LibretaContactoController::class);

    /*
    |--------------------------------------------------------------------------
    | 🧾 REQUERIMIENTOS (CLIENTES)
    |--------------------------------------------------------------------------
    */
    Route::get('/requerimientos/facturacion', [RequerimientoClienteController::class, 'facturacion'])
        ->name('requerimientos.facturacion');
    Route::post('/requerimientos/{id}/subir-factura', [RequerimientoClienteController::class, 'subirFactura'])
        ->name('requerimientos.subir-factura');
    Route::post('/requerimientos/{id}/toggle-facturado', [RequerimientoClienteController::class, 'toggleFacturado'])
        ->name('requerimientos.toggle-facturado');

    Route::resource('requerimientos', RequerimientoClienteController::class);

    /*
    |--------------------------------------------------------------------------
    | ✅ PROYECTOS
    |--------------------------------------------------------------------------
    */
    Route::resource('proyectos', ProyectoController::class);
    Route::get('/proyectos/download/{proyecto}', [ProyectoController::class, 'download'])
        ->name('proyectos.download');

    /*
    |--------------------------------------------------------------------------
    | 📝 NOVEDADES DE REQUERIMIENTOS
    |--------------------------------------------------------------------------
    */
    Route::get('/novedades/{requerimiento}', [NovedadRequerimientoController::class, 'index'])
        ->name('novedades.index');

    Route::post('/novedades', [NovedadRequerimientoController::class, 'store'])
        ->name('novedades.store');

    Route::get('/novedades/download/{novedad}', [NovedadRequerimientoController::class, 'download'])
        ->name('novedades.download');

    Route::patch('/novedades/{novedad}', [NovedadRequerimientoController::class, 'update'])
        ->name('novedades.update');

    Route::delete('/novedades/{novedad}', [NovedadRequerimientoController::class, 'destroy'])
        ->name('novedades.destroy');

    /*
    |--------------------------------------------------------------------------
    | 🛠️ MANTENIMIENTO
    |--------------------------------------------------------------------------
    */
    Route::prefix('mantenimiento')->name('mantenimiento.')->group(function () {

        // Tipo de soporte
        Route::resource('tipo-soporte', TipoSoporteController::class)
            ->parameters(['tipo-soporte' => 'tipo_soporte'])
            ->names('tipo-soporte');

        // Roles
        Route::resource('roles', RolesController::class)
            ->parameters(['roles' => 'rol'])
            ->names('roles');

        // Roles de Usuario
        Route::resource('roles-usuario', RoleUserController::class)
            ->parameters(['roles-usuario' => 'role_user'])
            ->names('roles-usuario');

        // Iguala
        Route::get('/iguala', [CategoriaIgualaController::class, 'index'])
            ->name('iguala.index');

        Route::post('/iguala', [CategoriaIgualaController::class, 'store'])
            ->name('iguala.store');

        Route::put('/iguala/{id}', [CategoriaIgualaController::class, 'update'])
            ->name('iguala.update');

        Route::delete('/iguala/{id}', [CategoriaIgualaController::class, 'destroy'])
            ->name('iguala.destroy');

        // Categorías
        Route::get('/categorias', [CategoriaController::class, 'index'])
            ->name('categorias.index');

        Route::post('/categorias', [CategoriaController::class, 'store'])
            ->name('categorias.store');

        Route::put('/categorias/{id}', [CategoriaController::class, 'update'])
            ->name('categorias.update');

        Route::delete('/categorias/{id}', [CategoriaController::class, 'destroy'])
            ->name('categorias.destroy');

        // Estados de Requerimientos
        Route::resource('estados-requerimiento', EstadoRequerimientoController::class)
            ->except(['create', 'show', 'edit'])
            ->parameters(['estados-requerimiento' => 'estado_requerimiento'])
            ->names('estados-requerimiento');

        // Equipos
        Route::resource('equipos', CatEquipoController::class);
        Route::resource('tipos-equipo', CatTipoEquipoController::class);
    });

    /*
    |--------------------------------------------------------------------------
    | 🧩 REQUERIMIENTOS POR PROYECTO
    |--------------------------------------------------------------------------
    */
    Route::prefix('proyectos/{proyecto}')->name('proyectos.')->group(function () {
        Route::get('requerimientos', [RequerimientoProyectoController::class, 'index'])
            ->name('requerimientos.index');

        Route::get('requerimientos/create', [RequerimientoProyectoController::class, 'create'])
            ->name('requerimientos.create');

        Route::post('requerimientos', [RequerimientoProyectoController::class, 'store'])
            ->name('requerimientos.store');
    });

    /*
    |--------------------------------------------------------------------------
    | 🧩 ACCIONES POR ID
    |--------------------------------------------------------------------------
    */
    Route::resource('requerimientos-proyecto', RequerimientoProyectoController::class)
        ->only(['show', 'edit', 'update', 'destroy'])
        ->names([
            'show' => 'requerimientos_proyecto.show',
            'edit' => 'requerimientos_proyecto.edit',
            'update' => 'requerimientos_proyecto.update',
            'destroy' => 'requerimientos_proyecto.destroy',
        ]);

    /*
    |--------------------------------------------------------------------------
    | 🧩 ENTORNO DE CLIENTES
    |--------------------------------------------------------------------------
    */
    Route::prefix('clientes/{cliente}/entorno')->name('clientes.entorno.')->group(function () {
        Route::get('/', [ClienteEntornoController::class, 'show'])->name('show');
        
        // AnyDesk
        Route::post('/anydesk', [ClienteEntornoController::class, 'storeAnydesk'])->name('anydesk.store');
        Route::put('/anydesk/{id}', [ClienteEntornoController::class, 'updateAnydesk'])->name('anydesk.update');
        Route::delete('/anydesk/{id}', [ClienteEntornoController::class, 'destroyAnydesk'])->name('anydesk.destroy');
        
        // Bitácora
        Route::post('/bitacora', [ClienteEntornoController::class, 'storeBitacora'])->name('bitacora.store');
        Route::put('/bitacora/{id}', [ClienteEntornoController::class, 'updateBitacora'])->name('bitacora.update');
        Route::delete('/bitacora/{id}', [ClienteEntornoController::class, 'destroyBitacora'])->name('bitacora.destroy');
        
        // Documentos
        Route::post('/documento', [ClienteEntornoController::class, 'storeDocumento'])->name('documento.store');
        Route::get('/documento/{id}/download', [ClienteEntornoController::class, 'downloadDocumento'])->name('documento.download');
        Route::delete('/documento/{id}', [ClienteEntornoController::class, 'destroyDocumento'])->name('documento.destroy');
        
        // Inventario
        Route::post('/equipo', [ClienteEntornoController::class, 'storeEquipo'])->name('equipo.store');
        Route::put('/equipo/{id}', [ClienteEntornoController::class, 'updateEquipo'])->name('equipo.update');
        Route::delete('/equipo/{id}', [ClienteEntornoController::class, 'destroyEquipo'])->name('equipo.destroy');
    });
});