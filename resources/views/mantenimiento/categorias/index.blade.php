@extends('layouts.app')

@section('page_title', 'Mantenimiento: Categorías')

@section('content')
<div class="container-fluid py-4">

  <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h4 class="fw-bold mb-1">Gestión de Categorías</h4>
        <p class="text-muted small mb-0">Clasificación sistemática para la organización de requerimientos y tareas.</p>
    </div>

    <!-- Botón Nuevo -->
    <button class="btn btn-spgi" data-bs-toggle="modal" data-bs-target="#modalNuevo">
      <i class="bi bi-plus-lg me-2"></i> Nueva Categoría
    </button>
  </div>

  <div class="spgi-card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-spgi align-middle mb-0">
          <thead>
            <tr>
              <th style="width:80px;">ID</th>
              <th>Nombre de Categoría</th>
              <th>Descripción Informativa</th>
              <th style="width:120px;" class="text-center">Estado</th>
              <th style="width:180px;" class="text-center">Acciones</th>
            </tr>
          </thead>

          <tbody>
            @forelse($categorias as $c)
              @php
                $nombre = $c->{$nameCol};
                $descripcion = $descCol ? ($c->{$descCol} ?? '-') : '-';
                $activo = $activeCol ? (int)($c->{$activeCol} ?? 1) : 1;
              @endphp
              <tr>
                <td class="text-muted fw-mono">#{{ $c->id }}</td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-pill bg-primary bg-opacity-10 px-2 py-1">
                            <i class="bi bi-tag-fill text-primary small"></i>
                        </div>
                        <span class="fw-bold">{{ $nombre }}</span>
                    </div>
                </td>
                <td class="text-muted small">{{ Str::limit($descripcion, 80) }}</td>
                <td class="text-center">
                  @if($activo)
                    <span class="badge rounded-pill" style="background: rgba(34, 197, 94, 0.15); color: #22c55e; border: 1px solid rgba(34, 197, 94, 0.2);">Activo</span>
                  @else
                    <span class="badge rounded-pill" style="background: rgba(148, 163, 184, 0.15); color: #94a3b8; border: 1px solid rgba(148, 163, 184, 0.2);">Inactivo</span>
                  @endif
                </td>
                <td class="text-center">
                  <div class="d-flex gap-2 justify-content-center">
                    <!-- Editar -->
                    <button class="btn btn-sm btn-outline-warning border-0 rounded-circle"
                            data-bs-toggle="modal"
                            data-bs-target="#modalEditar{{ $c->id }}" title="Editar">
                      <i class="bi bi-pencil-square fs-5"></i>
                    </button>

                    <!-- Eliminar -->
                    <form action="{{ route('mantenimiento.categorias.destroy', $c->id) }}"
                          method="POST" class="d-inline"
                          onsubmit="return confirm('¿Eliminar esta categoría?');">
                      @csrf
                      @method('DELETE')
                      <button class="btn btn-sm btn-outline-danger border-0 rounded-circle" title="Eliminar">
                        <i class="bi bi-trash3 fs-5"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>


            @empty
              <tr>
                <td colspan="5" class="text-center py-5">
                    <div class="text-muted">
                        <i class="bi bi-tags fs-1 d-block mb-3 opacity-25"></i>
                        No hay categorías registradas.
                    </div>
                </td>
              </tr>
            @endforelse
          </tbody>

        </table>
      </div>
    </div>
  </div>

  <!-- Modal Nuevo -->
  <div class="modal fade" id="modalNuevo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form action="{{ route('mantenimiento.categorias.store') }}" method="POST">
          @csrf

          <div class="modal-header">
            <h5 class="modal-title fw-bold">Nueva Categoría</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body p-4">
            <div class="mb-3">
              <label class="form-label">Nombre</label>
              <input type="text" name="nombre" class="form-control" required placeholder="Ej: Redes y Seguridad">
            </div>

            <div class="mb-3">
              <label class="form-label">Descripción</label>
              <textarea name="descripcion" class="form-control" rows="3" placeholder="Opcional..."></textarea>
            </div>

            <div class="mb-0">
              <label class="form-label">Estado Inicial</label>
              <select name="activo" class="form-select" {{ $activeCol ? '' : 'disabled' }}>
                <option value="1" selected>Activo</option>
                <option value="0">Inactivo</option>
              </select>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal">Cancelar</button>
            <button class="btn btn-spgi">Crear Categoría</button>
          </div>

        </form>
      </div>
    </div>
  </div>

@foreach($categorias as $c)
  @php
    $nombre = $c->{$nameCol};
    $activo = $activeCol ? (int)($c->{$activeCol} ?? 1) : 1;
  @endphp
  <!-- Modal Editar -->
  <div class="modal fade" id="modalEditar{{ $c->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form action="{{ route('mantenimiento.categorias.update', $c->id) }}" method="POST">
          @csrf
          @method('PUT')

          <div class="modal-header">
            <h5 class="modal-title fw-bold">
                <i class="bi bi-pencil-fill me-2 text-warning"></i>Editar Categoría
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body p-4">
            <div class="mb-3">
              <label class="form-label">Nombre</label>
              <input type="text" name="nombre" class="form-control" value="{{ $nombre }}" required>
            </div>

            <div class="mb-3">
              <label class="form-label">Descripción</label>
              <textarea name="descripcion" class="form-control" rows="3">{{ $descCol ? ($c->{$descCol} ?? '') : '' }}</textarea>
            </div>

            <div class="mb-0">
              <label class="form-label">Estado de la Categoría</label>
              <select name="activo" class="form-select" {{ $activeCol ? '' : 'disabled' }}>
                <option value="1" {{ $activo ? 'selected' : '' }}>Habilitada</option>
                <option value="0" {{ !$activo ? 'selected' : '' }}>Deshabilitada</option>
              </select>
              @if(!$activeCol)
                <small class="text-muted mt-2 d-block text-warning small">
                    <i class="bi bi-info-circle me-1"></i> Esta tabla no admite gestión de estado.
                </small>
              @endif
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal">Cancelar</button>
            <button class="btn btn-spgi">Guardar Cambios</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endforeach

@endsection