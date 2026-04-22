@extends('layouts.app')

@section('page_title', 'Requerimientos Comerciales')

@section('content')
<style>
    .glass-card {
        background: var(--bg-surface-glass);
        border: 1px solid var(--border-main);
        border-radius: 24px;
        box-shadow: var(--shadow-main);
        backdrop-filter: blur(20px);
        padding: 30px;
    }
    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 999px;
        font-weight: 800;
        font-size: 0.7rem;
        text-transform: uppercase;
    }
    .status-pendiente { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
    .status-realizado { background: rgba(16, 185, 129, 0.1); color: #10b981; }
    .status-cancelado { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
</style>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold text-gradient">Requerimientos Comerciales</h1>
            <p class="text-muted mb-0">Gestión exclusiva de solicitudes para el área de Ventas.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('leads.bienvenido') }}" class="btn btn-outline-secondary rounded-pill px-4 fw-bold">
                <i class="bi bi-arrow-left me-1"></i> Volver
            </a>
            <a href="{{ route('lead-requirements.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold">
                <i class="bi bi-plus-lg me-1"></i> Agregar Requerimiento
            </a>
        </div>
    </div>

    <div class="glass-card mb-4">
        <form action="{{ route('lead-requirements.index') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label small fw-bold">Filtrar por Lead</label>
                <select name="lead_id" class="form-select rounded-pill">
                    <option value="">Todos los leads</option>
                    @foreach($leads as $l)
                        <option value="{{ $l->id }}" {{ request('lead_id') == $l->id ? 'selected' : '' }}>{{ $l->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold">Estado</label>
                <select name="status" class="form-select rounded-pill">
                    <option value="">Cualquier estado</option>
                    <option value="Pendiente" {{ request('status') == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                    <option value="Realizado" {{ request('status') == 'Realizado' ? 'selected' : '' }}>Realizado</option>
                    <option value="Cancelado" {{ request('status') == 'Cancelado' ? 'selected' : '' }}>Cancelado</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-spgi w-100 rounded-pill">
                    <i class="bi bi-filter me-1"></i> Filtrar
                </button>
            </div>
        </form>
    </div>

    <style>
        .table-fixed { table-layout: fixed; min-width: 900px; }
        .col-lead { width: 180px; }
        .col-desc { width: auto; }
        .col-user { width: 180px; }
        .col-stat { width: 140px; }
        .col-date { width: 120px; }
        .col-acts { width: 120px; }
        .text-wrap-break { word-break: break-word; white-space: normal; }
    </style>

    <div class="glass-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle table-fixed">
                <thead>
                    <tr style="border-bottom: 2px solid var(--border-main);">
                        <th class="text-muted small fw-bold col-lead">LEAD / PROSPECTO</th>
                        <th class="text-muted small fw-bold col-desc">DESCRIPCIÓN</th>
                        <th class="text-muted small fw-bold col-user">ASIGNADO A</th>
                        <th class="text-muted small fw-bold col-stat">ESTADO</th>
                        <th class="text-muted small fw-bold col-date">CREACIÓN</th>
                        <th class="text-muted small fw-bold text-end col-acts">ACCIONES</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requirements as $r)
                        <tr style="border-bottom: 1px solid rgba(var(--text-main), 0.05);">
                            <td>
                                <a href="{{ route('leads.show', $r->lead_id) }}" class="fw-bold text-decoration-none">
                                    {{ $r->lead->nombre }}
                                </a>
                            </td>
                            <td class="text-wrap-break">
                                {{ $r->descripcion }}
                            </td>
                            <td>
                                @if($r->asignado)
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-primary text-white rounded-circle d-grid place-items-center small fw-bold" style="width: 24px; height: 24px;">
                                            {{ substr($r->asignado->name, 0, 1) }}
                                        </div>
                                        <span>{{ $r->asignado->name }}</span>
                                    </div>
                                @else
                                    <span class="text-muted italic small">Sin asignar</span>
                                @endif
                            </td>
                            <td>
                                @php $sClass = 'status-' . strtolower($r->estado); @endphp
                                <span class="status-pill {{ $sClass }}">
                                    <i class="bi bi-circle-fill" style="font-size: 0.4rem;"></i> {{ $r->estado }}
                                </span>
                            </td>
                            <td class="small text-muted">{{ $r->created_at->format('d/m/Y') }}</td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('lead-requirements.edit', $r->id) }}" class="btn btn-sm btn-outline-primary rounded-circle">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('lead-requirements.destroy', $r->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle" onclick="return confirm('¿Eliminar este requerimiento?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-clipboard-x fs-2 d-block mb-2"></i>
                                No se encontraron requerimientos comerciales.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $requirements->links() }}
        </div>
    </div>
</div>
@endsection
