@extends('layouts.app')

@section('page_title', 'Mantenimiento: Estados de Requerimientos')

@section('content')

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h4 class="fw-bold mb-1">Estados de Requerimiento</h4>
            <p class="text-muted small mb-0">Gestión de las etapas del ciclo de vida de los requerimientos técnicos.</p>
        </div>

        <div class="d-flex align-items-center gap-3">
            <span class="badge rounded-pill px-3 py-2" style="background: rgba(var(--text-main), 0.05); border: 1px solid var(--border-main); color: var(--text-muted); font-weight: 700;">
                <i class="bi bi-tags me-1"></i> Total: {{ count($estados) }}
            </span>

            <button class="btn btn-spgi" type="button" data-bs-toggle="modal" data-bs-target="#modalEstado">
                <i class="bi bi-plus-lg me-2"></i> Nuevo Estado
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4" id="alerta-exito" style="background: rgba(34, 197, 94, 0.1); color: #22c55e;">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        </div>
    @endif

    <div class="spgi-card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-spgi align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width: 80px;">ID</th>
                            <th>Nombre del Estado</th>
                            <th style="width: 200px;">Visualización (Badge)</th>
                            <th class="text-center" style="width: 180px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($estados as $e)
                        <tr>
                            <td class="text-muted fw-mono">#{{ $e->id }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle" style="width: 10px; height: 10px; background: {{ Str::startsWith($e->color, '#') ? $e->color : 'currentColor' }}"></div>
                                    <span class="fw-bold">{{ $e->nombre }}</span>
                                </div>
                            </td>
                             <td>
                                @if(Str::startsWith($e->color, '#'))
                                    <span class="badge rounded-pill px-3 py-2 fw-bold" style="background-color: {{ $e->color }}; color: #fff; box-shadow: 0 4px 12px {{ $e->color }}44;">
                                        {{ $e->nombre }}
                                    </span>
                                @else
                                    <span class="badge rounded-pill px-3 py-2 {{ $e->color }}">{{ $e->nombre }}</span>
                                @endif
                             </td>
                            <td class="text-center">
                                <div class="d-flex gap-2 justify-content-center">
                                    <button class="btn btn-sm btn-outline-warning border-0 rounded-circle" type="button" data-bs-toggle="modal" data-bs-target="#editarEstado{{ $e->id }}" title="Editar">
                                        <i class="bi bi-pencil-square fs-5"></i>
                                    </button>

                                    <button class="btn btn-sm btn-outline-danger border-0 rounded-circle" type="button" data-bs-toggle="modal" data-bs-target="#confirmarEliminar{{ $e->id }}" title="Eliminar">
                                        <i class="bi bi-trash3 fs-5"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

@foreach($estados as $e)
    <!-- Modal Eliminar -->
    <div class="modal fade" id="confirmarEliminar{{ $e->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-exclamation-triangle-fill me-2 text-danger"></i>Eliminar Estado
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 text-center">
                   <p class="mb-0">¿Estás seguro de que deseas eliminar el estado <strong>{{ $e->nombre }}</strong>?</p>
                   <small class="text-muted d-block mt-2">Esta acción podría afectar a los requerimientos que actualmente usan este estado.</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                    <form action="{{ route('mantenimiento.estados-requerimiento.destroy', $e->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger px-4 rounded-pill">Confirmar Eliminación</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Editar -->
    <div class="modal fade" id="editarEstado{{ $e->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-pencil-fill me-2 text-warning"></i>Editar Estado
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('mantenimiento.estados-requerimiento.update', $e->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4">
                        <div class="mb-4">
                            <label class="form-label fw-bold">Nombre del Estado</label>
                            <input type="text" class="form-control" name="nombre" value="{{ $e->nombre }}" required>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-bold">Identidad Visual (Color)</label>
                            <div class="d-flex gap-2 align-items-center mb-2">
                                <input type="color" class="form-control form-control-color" value="{{ Str::startsWith($e->color, '#') ? $e->color : '#3b82f6' }}" onchange="document.getElementById('colorEdit{{ $e->id }}').value = this.value" style="width: 50px; height: 46px; padding: 4px; border-radius: 12px; border: 1px solid var(--border-main);">
                                <input type="text" class="form-control" name="color" id="colorEdit{{ $e->id }}" value="{{ $e->color }}" placeholder="Ej: bg-success o #ff0000">
                            </div>
                            <div class="alert alert-info border-0 p-2 rounded-3 mb-0" style="background: rgba(59, 130, 246, 0.05); color: var(--spgi-primary); font-size: 0.75rem;">
                                <i class="bi bi-info-circle me-1"></i> Puedes usar un selector de color hexadecimal o una clase de Bootstrap (ex: <code>bg-primary</code>).
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-spgi">Actualizar Estado</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

<!-- Modal Nuevo -->
<div class="modal fade" id="modalEstado" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-plus-lg me-2 text-primary"></i>Nuevo Estado
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('mantenimiento.estados-requerimiento.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-4">
                        <label class="form-label fw-bold">Nombre del Estado</label>
                        <input type="text" class="form-control" name="nombre" placeholder="Ej: En Producción" required>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-bold">Color Distintivo</label>
                        <div class="d-flex gap-2 align-items-center mb-2">
                            <input type="color" class="form-control form-control-color" value="#3b82f6" onchange="document.getElementById('colorCreate').value = this.value" style="width: 50px; height: 46px; padding: 4px; border-radius: 12px; border: 1px solid var(--border-main);">
                            <input type="text" class="form-control" name="color" id="colorCreate" placeholder="Ej: bg-primary o #3b82f6">
                        </div>
                        <small class="text-muted d-block">Define cómo se verá el componente visual en las listas.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-spgi">Crear Estado</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const alerta = document.getElementById('alerta-exito');
        if (alerta) {
            setTimeout(() => {
                alerta.style.transition = "all 0.4s cubic-bezier(0.4, 0, 0.2, 1)";
                alerta.style.opacity = '0';
                alerta.style.transform = "translateY(-10px)";
                setTimeout(() => alerta.remove(), 400);
            }, 3000);
        }
    });
</script>
@endpush
@endsection
