<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ClienteMaestroController;
use App\Http\Controllers\LibretaContactoController;
use App\Http\Controllers\RequerimientoClienteController;
use App\Http\Controllers\NovedadRequerimientoController;
use App\Http\Controllers\NovedadRequerimientoProyectoController;
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
use App\Http\Controllers\ProveedorController;

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
        
        // Novedades Lead
        Route::post('/leads/{lead}/novedades', [\App\Http\Controllers\NovedadLeadController::class, 'store'])->name('leads.novedades.store');
        Route::get('/leads/novedades/{novedad}/download', [\App\Http\Controllers\NovedadLeadController::class, 'download'])->name('leads.novedades.download');

        // Checklists
        Route::resource('checklists', \App\Http\Controllers\ChecklistTemplateController::class);
        Route::post('checklists/{checklist}/questions', [\App\Http\Controllers\ChecklistTemplateController::class, 'storeQuestion'])->name('checklists.questions.store');
        Route::delete('checklists/questions/{question}', [\App\Http\Controllers\ChecklistTemplateController::class, 'destroyQuestion'])->name('checklists.questions.destroy');
        Route::post('checklists/questions/{question}/answers', [\App\Http\Controllers\ChecklistTemplateController::class, 'storeAnswer'])->name('checklists.answers.store');
        Route::delete('checklists/answers/{answer}', [\App\Http\Controllers\ChecklistTemplateController::class, 'destroyAnswer'])->name('checklists.answers.destroy');

        Route::get('leads/{lead}/checklists/create', [\App\Http\Controllers\LeadChecklistController::class, 'create'])->name('leads.checklists.create');
        Route::post('leads/{lead}/checklists', [\App\Http\Controllers\LeadChecklistController::class, 'store'])->name('leads.checklists.store');
        Route::get('leads/{lead}/checklists/{checklist}/edit', [\App\Http\Controllers\LeadChecklistController::class, 'edit'])->name('leads.checklists.edit');
        Route::put('leads/{lead}/checklists/{checklist}', [\App\Http\Controllers\LeadChecklistController::class, 'update'])->name('leads.checklists.update');

        // Visitas de Campo
        Route::get('visitas/{id}/pdf', [\App\Http\Controllers\VisitaController::class, 'generarPdf'])->name('visitas.pdf');
        Route::post('visitas/{id}/enviar-correo', [\App\Http\Controllers\VisitaController::class, 'enviarCorreo'])->name('visitas.enviar-correo');
        Route::resource('visitas', \App\Http\Controllers\VisitaController::class);

        Route::get('/leads-calculos', [LeadController::class, 'indexCalculos'])->name('leads.indexCalculos');
        Route::get('/leads/file/{id}/download', [LeadController::class, 'downloadFile'])->name('leads.downloadFile');
        Route::get('/leads/serve-file', [LeadController::class, 'serveFile'])->name('leads.serveFile');
        Route::post('/leads/{id}/upload-files', [LeadController::class, 'uploadFiles'])->name('leads.uploadFiles');
        Route::delete('/leads/file/{file_id}', [LeadController::class, 'deleteFile'])->name('leads.deleteFile');
        Route::post('/leads/{id}/validar', [LeadController::class, 'validar'])->name('leads.validar');
        Route::post('/leads/{id}/aprobar', [LeadController::class, 'aprobar'])->name('leads.aprobar');
        Route::post('/leads/{id}/rechazar', [LeadController::class, 'rechazar'])->name('leads.rechazar');
        Route::get('/leads/{lead}/calculadora', [LeadController::class, 'calculadora'])->name('leads.calculadora');
        Route::post('/leads/{lead}/save-calculo', [LeadController::class, 'saveCalculo'])->name('leads.saveCalculo');
        Route::get('/leads/{lead}/calculations/{calc_id}', [LeadController::class, 'getCalculationDetails'])->name('leads.getCalculationDetails');
        Route::post('/leads/calculations/{id}/validar', [LeadController::class, 'validarCalculation'])->name('leads.validarCalculation');
        Route::delete('/leads/calculations/{calc_id}', [LeadController::class, 'deleteCalculation'])->name('leads.deleteCalculation');
        Route::post('/leads/{id}/update-status', [LeadController::class, 'updateStatus'])->name('leads.updateStatus');
        Route::post('/leads/{id}/convertir-ganado', [LeadController::class, 'convertirAGanado'])->name('leads.convertirGanado');
        Route::post('/leads/{id}/marcar-perdido', [LeadController::class, 'marcarPerdido'])->name('leads.marcarPerdido');

        // Administración Dashboard y Facturación
        Route::get('/administracion/bienvenido', function () {
            return view('administracion.bienvenido');
        })->name('administracion.bienvenido');

        Route::get('/administracion/comisiones-leads', [\App\Http\Controllers\ProyectoRentabilidadController::class, 'comisionesLeads'])->name('administracion.comisiones.leads');

        Route::get('/requerimientos/facturacion', [RequerimientoClienteController::class, 'facturacion'])
            ->name('requerimientos.facturacion');
        Route::post('/requerimientos/{id}/subir-factura', [RequerimientoClienteController::class, 'subirFactura'])
            ->name('requerimientos.subir-factura');
        Route::post('/requerimientos/{id}/toggle-facturado', [RequerimientoClienteController::class, 'toggleFacturado'])
            ->name('requerimientos.toggle-facturado');

        Route::resource('proveedores', ProveedorController::class);
        
        Route::post('tarifarios/tipo/ajax', [\App\Http\Controllers\TarifarioController::class, 'storeTipoAjax'])->name('tarifarios.tipo.ajax');
        Route::resource('tarifarios', \App\Http\Controllers\TarifarioController::class);

        // Rendición de Gastos
        Route::resource('rendiciones', \App\Http\Controllers\RendicionController::class)->except(['edit', 'update']);
        Route::put('rendiciones/{id}/general-info', [\App\Http\Controllers\RendicionController::class, 'updateGeneralInfo'])->name('rendiciones.general-info');
        Route::post('rendiciones/{id}/update-status', [\App\Http\Controllers\RendicionController::class, 'updateStatus'])->name('rendiciones.updateStatus');
        Route::post('rendiciones/metodo-pago/ajax', [\App\Http\Controllers\RendicionController::class, 'storeMetodoPago'])->name('rendiciones.metodo-pago.ajax');
        Route::post('rendiciones/{id}/gastos', [\App\Http\Controllers\RendicionController::class, 'storeGasto'])->name('rendiciones.gastos.store');
        Route::delete('rendiciones/{id}/gastos/{gasto_id}', [\App\Http\Controllers\RendicionController::class, 'deleteGasto'])->name('rendiciones.gastos.destroy');
        Route::get('rendiciones/{id}/pdf', [\App\Http\Controllers\RendicionController::class, 'generarPdf'])->name('rendiciones.pdf');

        // Planilla de Horas Extras
        Route::resource('horas-extras', \App\Http\Controllers\HoraExtraController::class);
        Route::get('horas-extras/{id}/pdf', [\App\Http\Controllers\HoraExtraController::class, 'generarPdf'])->name('horas-extras.pdf');
        Route::post('horas-extras/{id}/detalles', [\App\Http\Controllers\HoraExtraController::class, 'storeDetalle'])->name('horas-extras.detalles.store');
        Route::delete('horas-extras/{id}/detalles/{detalle_id}', [\App\Http\Controllers\HoraExtraController::class, 'deleteDetalle'])->name('horas-extras.detalles.destroy');
        Route::put('horas-extras/{id}/general', [\App\Http\Controllers\HoraExtraController::class, 'updateGeneral'])->name('horas-extras.general.update');
        Route::post('horas-extras/{id}/aprobar', [\App\Http\Controllers\HoraExtraController::class, 'aprobarPlanilla'])->name('horas-extras.aprobar');

        // Requerimientos Administrativos
        Route::resource('requerimientos-administrativos', \App\Http\Controllers\RequerimientoAdministrativoController::class);

        // Bitácora Administrativa de Clientes
        Route::prefix('administracion/bitacora-clientes')->name('administracion.bitacora-clientes.')->group(function () {
            Route::get('/', [\App\Http\Controllers\ClienteBitacoraAdminController::class, 'index'])->name('index');
            Route::get('/{cliente}', [\App\Http\Controllers\ClienteBitacoraAdminController::class, 'show'])->name('show');
            Route::post('/{cliente}/documentos', [\App\Http\Controllers\ClienteBitacoraAdminController::class, 'storeDocumento'])->name('documentos.store');
            Route::delete('/{cliente}/documentos/{id}', [\App\Http\Controllers\ClienteBitacoraAdminController::class, 'destroyDocumento'])->name('documentos.destroy');
            Route::get('/documentos/{id}/download', [\App\Http\Controllers\ClienteBitacoraAdminController::class, 'downloadDocumento'])->name('documentos.download');
            Route::post('/{cliente}/contactos', [\App\Http\Controllers\ClienteBitacoraAdminController::class, 'storeContacto'])->name('contactos.store');
            Route::delete('/{cliente}/contactos/{id}', [\App\Http\Controllers\ClienteBitacoraAdminController::class, 'destroyContacto'])->name('contactos.destroy');
        });

        // Análisis de Rentabilidad de Proyectos
        Route::prefix('administracion/rentabilidad')->name('administracion.rentabilidad.')->group(function () {
            Route::get('/', [\App\Http\Controllers\ProyectoRentabilidadController::class, 'index'])->name('index');
            Route::get('/{proyecto}', [\App\Http\Controllers\ProyectoRentabilidadController::class, 'show'])->name('show');
            Route::post('/{proyecto}/update-comision', [\App\Http\Controllers\ProyectoRentabilidadController::class, 'updateComision'])->name('update-comision');
            
            // Rutas para filas
            Route::post('/{proyecto}/proyecciones', [\App\Http\Controllers\ProyectoRentabilidadController::class, 'storeProyeccion'])->name('proyecciones.store');
            Route::delete('/proyecciones/{id}', [\App\Http\Controllers\ProyectoRentabilidadController::class, 'destroyProyeccion'])->name('proyecciones.destroy');
            
            Route::post('/{proyecto}/gastos', [\App\Http\Controllers\ProyectoRentabilidadController::class, 'storeGasto'])->name('gastos.store');
            Route::delete('/gastos/{id}', [\App\Http\Controllers\ProyectoRentabilidadController::class, 'destroyGasto'])->name('gastos.destroy');
            
            Route::post('/{proyecto}/horas-extras', [\App\Http\Controllers\ProyectoRentabilidadController::class, 'storeHoraExtra'])->name('horas-extras.store');
            Route::delete('/horas-extras/{id}', [\App\Http\Controllers\ProyectoRentabilidadController::class, 'destroyHoraExtra'])->name('horas-extras.destroy');
        });
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
    | 👤 USUARIOS Y AUDITORIA
    |--------------------------------------------------------------------------
    */
    Route::resource('usuarios', UsuarioController::class)->middleware(\App\Http\Middleware\CheckAdmin::class);
    Route::get('/auditoria', [\App\Http\Controllers\AuditLogController::class, 'index'])->name('auditoria.index')->middleware(\App\Http\Middleware\CheckAdminOrEncargado::class);

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
    | 📝 NOVEDADES DE REQUERIMIENTOS DE PROYECTOS
    |--------------------------------------------------------------------------
    |*/
    Route::get('/proyectos-novedades/{requerimiento}', [\App\Http\Controllers\NovedadRequerimientoProyectoController::class, 'index'])
        ->name('proyectos-novedades.index');

    Route::post('/proyectos-novedades', [\App\Http\Controllers\NovedadRequerimientoProyectoController::class, 'store'])
        ->name('proyectos-novedades.store');

    Route::get('/proyectos-novedades/download/{novedad}', [\App\Http\Controllers\NovedadRequerimientoProyectoController::class, 'download'])
        ->name('proyectos-novedades.download');

    Route::patch('/proyectos-novedades/{novedad}', [\App\Http\Controllers\NovedadRequerimientoProyectoController::class, 'update'])
        ->name('proyectos-novedades.update');

    Route::delete('/proyectos-novedades/{novedad}', [\App\Http\Controllers\NovedadRequerimientoProyectoController::class, 'destroy'])
        ->name('proyectos-novedades.destroy');

    /*
    |--------------------------------------------------------------------------
    | 🚚 CONDUCES
    |--------------------------------------------------------------------------
    */
    Route::post('/conduces', [\App\Http\Controllers\ConduceController::class, 'store'])
        ->name('conduces.store');

    Route::get('/conduces/{id}/pdf', [\App\Http\Controllers\ConduceController::class, 'generatePdf'])
        ->name('conduces.pdf');

    /*
    |--------------------------------------------------------------------------
    | 🛠️ MANTENIMIENTO
    |--------------------------------------------------------------------------
    */
    Route::prefix('mantenimiento')->name('mantenimiento.')->group(function () {

        // Tipo de soporte
        Route::post('tipo-soporte/ajax', [TipoSoporteController::class, 'storeAjax'])->name('tipo-soporte.ajax');
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

    Route::post('requerimientos-proyecto/{requerimientos_proyecto}/tareas', [\App\Http\Controllers\RequerimientoProyectoController::class, 'storeTarea'])->name('requerimientos-proyecto.tareas.store');
    Route::post('requerimientos-proyecto-tareas/{id}/toggle', [\App\Http\Controllers\RequerimientoProyectoController::class, 'toggleTarea'])->name('requerimientos-proyecto-tareas.toggle');
    Route::delete('requerimientos-proyecto-tareas/{id}', [\App\Http\Controllers\RequerimientoProyectoController::class, 'destroyTarea'])->name('requerimientos-proyecto-tareas.destroy');

    Route::post('requerimientos-cliente/{requerimiento_cliente}/tareas', [RequerimientoClienteController::class, 'storeTarea'])->name('requerimientos-cliente.tareas.store');
    Route::post('requerimientos-cliente-tareas/{id}/toggle', [RequerimientoClienteController::class, 'toggleTarea'])->name('requerimientos-cliente-tareas.toggle');
    Route::delete('requerimientos-cliente-tareas/{id}', [RequerimientoClienteController::class, 'destroyTarea'])->name('requerimientos-cliente-tareas.destroy');

    Route::get('proyectos/requerimientos/{requerimientos_proyecto}/novedades', [NovedadRequerimientoProyectoController::class, 'index'])->name('proyectos.requerimientos.novedades.index');
    Route::post('proyectos/requerimientos/novedades', [NovedadRequerimientoProyectoController::class, 'store'])->name('proyectos.requerimientos.novedades.store');
    Route::put('proyectos/requerimientos/novedades/{id}', [NovedadRequerimientoProyectoController::class, 'update'])->name('proyectos.requerimientos.novedades.update');
    Route::delete('proyectos/requerimientos/novedades/{id}', [NovedadRequerimientoProyectoController::class, 'destroy'])->name('proyectos.requerimientos.novedades.destroy');
    Route::get('proyectos/requerimientos/novedades/{id}/download', [NovedadRequerimientoProyectoController::class, 'download'])->name('proyectos.requerimientos.novedades.download');

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
        Route::put('/documento/{id}', [ClienteEntornoController::class, 'updateDocumento'])->name('documento.update');
        Route::get('/documento/{id}/download', [ClienteEntornoController::class, 'downloadDocumento'])->name('documento.download');
        Route::delete('/documento/{id}', [ClienteEntornoController::class, 'destroyDocumento'])->name('documento.destroy');
        
        // Inventario
        Route::post('/equipo', [ClienteEntornoController::class, 'storeEquipo'])->name('equipo.store');
        Route::put('/equipo/{id}', [ClienteEntornoController::class, 'updateEquipo'])->name('equipo.update');
        Route::delete('/equipo/{id}', [ClienteEntornoController::class, 'destroyEquipo'])->name('equipo.destroy');
        Route::post('/equipo/{id}/duplicate', [ClienteEntornoController::class, 'duplicateEquipo'])->name('equipo.duplicate');
    });
});

/*
|--------------------------------------------------------------------------
| Portal de Clientes
|--------------------------------------------------------------------------
*/
Route::prefix('portal')->name('cliente.')->group(function () {
    Route::get('/login', [App\Http\Controllers\Cliente\ClienteLoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [App\Http\Controllers\Cliente\ClienteLoginController::class, 'login']);
    Route::post('/logout', [App\Http\Controllers\Cliente\ClienteLoginController::class, 'logout'])->name('logout');

    Route::middleware(['auth:cliente'])->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Cliente\ClienteDashboardController::class, 'index'])->name('dashboard');
        Route::get('/historial', [App\Http\Controllers\Cliente\ClienteDashboardController::class, 'historial'])->name('historial');
        Route::get('/requerimientos/{id}', [App\Http\Controllers\Cliente\ClienteDashboardController::class, 'showRequerimiento'])->name('requerimientos.show');
        Route::post('/requerimientos/{id}/novedad', [App\Http\Controllers\Cliente\ClienteDashboardController::class, 'storeNovedad'])->name('requerimientos.novedad.store');
        Route::get('/novedades', [App\Http\Controllers\Cliente\ClienteDashboardController::class, 'novedades'])->name('novedades');
    });
});