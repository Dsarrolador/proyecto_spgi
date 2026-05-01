@extends('layouts.app')

@section('page_title', 'Bitácora de Movimientos')

@section('content')
<style>
    .glass-card-premium.no-hover:hover {
        transform: none !important;
        border-color: var(--border-main) !important;
    }
    .glass-card-premium.no-hover::before {
        display: none !important;
    }
    .table tbody tr:hover {
        background-color: transparent !important;
    }
</style>
<div class="container-fluid p-0">
    <!-- FILTROS -->
    <div class="glass-card-premium p-4 mb-4">
        <form method="GET" action="{{ route('auditoria.index') }}" class="row g-3">
            <div class="col-md-2">
                <label class="form-label text-muted small fw-bold">Usuario / Correo</label>
                <input type="text" name="user_name" class="form-control" value="{{ request('user_name') }}" placeholder="Buscar...">
            </div>
            <div class="col-md-2">
                <label class="form-label text-muted small fw-bold">Acción</label>
                <select name="action" class="form-select">
                    <option value="">Todas</option>
                    <option value="login" {{ request('action') == 'login' ? 'selected' : '' }}>Login</option>
                    <option value="logout" {{ request('action') == 'logout' ? 'selected' : '' }}>Logout</option>
                    <option value="failed_login" {{ request('action') == 'failed_login' ? 'selected' : '' }}>Login Fallido</option>
                    <option value="created" {{ request('action') == 'created' ? 'selected' : '' }}>Creación</option>
                    <option value="updated" {{ request('action') == 'updated' ? 'selected' : '' }}>Actualización</option>
                    <option value="deleted" {{ request('action') == 'deleted' ? 'selected' : '' }}>Eliminación</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label text-muted small fw-bold">Módulo</label>
                <input type="text" name="module" class="form-control" value="{{ request('module') }}" placeholder="Ej. ClienteMaestro">
            </div>
            <div class="col-md-2">
                <label class="form-label text-muted small fw-bold">Desde</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label text-muted small fw-bold">Hasta</label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-2 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary w-100 rounded-pill">
                    <i class="bi bi-search"></i> Buscar
                </button>
                <a href="{{ route('auditoria.index') }}" class="btn btn-light rounded-pill px-3" title="Limpiar Filtros">
                    <i class="bi bi-eraser"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- TABLA DE LOGS -->
    <div class="glass-card-premium no-hover p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead>
                    <tr>
                        <th class="ps-4">Fecha / Hora</th>
                        <th>Usuario</th>
                        <th>Acción</th>
                        <th>Descripción</th>
                        <th>Módulo</th>
                        <th>Dispositivo / IP</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td class="ps-4 text-nowrap">
                            <div class="fw-bold">{{ $log->created_at->format('d/m/Y') }}</div>
                            <small class="text-muted">{{ $log->created_at->format('h:i A') }}</small>
                        </td>
                        <td>
                            <div class="fw-bold">{{ $log->user_name ?? 'Desconocido' }}</div>
                        </td>
                        <td>
                            @if($log->action == 'login')
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2">Login</span>
                            @elseif($log->action == 'logout')
                                <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 py-2">Logout</span>
                            @elseif($log->action == 'failed_login')
                                <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-2">Fallido</span>
                            @elseif($log->action == 'created')
                                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2">Creado</span>
                            @elseif($log->action == 'updated')
                                <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-2">Editado</span>
                            @elseif($log->action == 'deleted')
                                <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-2">Eliminado</span>
                            @else
                                <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-3 py-2">{{ ucfirst($log->action) }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="small" style="max-width: 450px; white-space: normal; line-height: 1.4;">
                                @php $targetUrl = $log->getTargetUrl(); @endphp
                                @if($targetUrl && $log->action !== 'deleted')
                                    <a href="{{ $targetUrl }}" class="text-decoration-none fw-medium" style="color: var(--text-main); transition: opacity 0.2s;" onmouseover="this.style.opacity='0.7'" onmouseout="this.style.opacity='1'">
                                        <i class="bi bi-box-arrow-up-right text-primary me-2" style="font-size: 0.8rem;"></i>
                                        {{ $log->description }}
                                    </a>
                                @else
                                    <span class="text-muted-light">{{ $log->description }}</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="small text-muted">{{ $log->module }}</span>
                            @if($log->model_id)
                                <br><small class="text-muted">ID: {{ $log->model_id }}</small>
                            @endif
                        </td>
                        <td>
                            <div class="small fw-bold">
                                <i class="bi bi-pc-display me-1"></i> {{ $log->device_type }}
                            </div>
                            <div class="small text-muted" title="{{ $log->browser }} en {{ $log->os }}">
                                {{ $log->ip_address }}
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                            No se encontraron registros de auditoría que coincidan con los filtros.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
        <div class="card-footer border-0 bg-transparent p-3 d-flex justify-content-end">
            {{ $logs->links('pagination::bootstrap-4') }}
        </div>
        @endif
    </div>
</div>
@endsection
