@extends('layouts.app')

@section('content')
<div class="container py-4">

  <div class="d-flex align-items-center justify-content-between mb-3">
    <h3 class="mb-0 fw-bold">Categorías</h3>

    <!-- Botón Nuevo -->
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevo">
      <i class="bi bi-plus-circle me-1"></i> Nuevo
    </button>
  </div>

  <div class="card shadow-sm">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-dark">
            <tr>
              <th style="width:80px;">ID</th>
              <th>Nombre</th>
              <th style="width:260px;">Descripción</th>
              <th style="width:120px;" class="text-center">Activo</th>
              <th style="width:200px;" class="text-center">Acciones</th>
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
                <td class="text-muted">{{ $c->id }}</td>
                <td class="fw-semibold">{{ $nombre }}</td>
                <td class="text-muted">{{ $descripcion ?: '-' }}</td>
                <td class="text-center">
                  <span class="badge {{ $activo ? 'bg-success' : 'bg-secondary' }}">
                    {{ $activo ? 'Sí' : 'No' }}
                  </span>
                </td>
                <td class="text-center">
                  <!-- Editar -->
                  <button class="btn btn-warning btn-sm me-1"
                          data-bs-toggle="modal"
                          data-bs-target="#modalEditar{{ $c->id }}">
                    <i class="bi bi-pencil-square me-1"></i> Editar
                  </button>

                  <!-- Eliminar -->
                  <form action="{{ route('mantenimiento.categorias.destroy', $c->id) }}"
                        method="POST" class="d-inline"
                        onsubmit="return confirm('¿Eliminar esta categoría?');">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger btn-sm">
                      <i class="bi bi-trash"></i>
                    </button>
                  </form>
                </td>
              </tr>

              <!-- Modal Editar -->
              <div class="modal fade" id="modalEditar{{ $c->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <form action="{{ route('mantenimiento.categorias.update', $c->id) }}" method="POST">
                      @csrf
                      @method('PUT')

                      <div class="modal-header">
                        <h5 class="modal-title">Editar Categoría</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>

                      <div class="modal-body">
                        <div class="mb-3">
                          <label class="form-label">Nombre</label>
                          <input type="text" name="nombre" class="form-control" value="{{ $nombre }}" required>
                        </div>

                        <div class="mb-3">
                          <label class="form-label">Descripción</label>
                          <textarea name="descripcion" class="form-control" rows="3">{{ $descCol ? ($c->{$descCol} ?? '') : '' }}</textarea>
                        </div>

                        <div class="mb-2">
                          <label class="form-label">Activo</label>
                          <select name="activo" class="form-select" {{ $activeCol ? '' : 'disabled' }}>
                            <option value="1" {{ $activo ? 'selected' : '' }}>Sí</option>
                            <option value="0" {{ !$activo ? 'selected' : '' }}>No</option>
                          </select>
                          @if(!$activeCol)
                            <small class="text-muted">Tu tabla no tiene columna de activo; se asume “Sí”.</small>
                          @endif
                        </div>
                      </div>

                      <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                        <button class="btn btn-primary">Guardar</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>

            @empty
              <tr>
                <td colspan="5" class="text-center text-muted py-4">No hay categorías registradas.</td>
              </tr>
            @endforelse
          </tbody>

        </table>
      </div>
    </div>
  </div>

  <!-- Modal Nuevo -->
  <div class="modal fade" id="modalNuevo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form action="{{ route('mantenimiento.categorias.store') }}" method="POST">
          @csrf

          <div class="modal-header">
            <h5 class="modal-title">Nueva Categoría</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Nombre</label>
              <input type="text" name="nombre" class="form-control" required>
            </div>

            <div class="mb-3">
              <label class="form-label">Descripción</label>
              <textarea name="descripcion" class="form-control" rows="3"></textarea>
            </div>

            <div class="mb-2">
              <label class="form-label">Activo</label>
              <select name="activo" class="form-select" {{ $activeCol ? '' : 'disabled' }}>
                <option value="1" selected>Sí</option>
                <option value="0">No</option>
              </select>
              @if(!$activeCol)
                <small class="text-muted">Tu tabla no tiene columna de activo; se asume “Sí”.</small>
              @endif
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
            <button class="btn btn-primary">Crear</button>
          </div>

        </form>
      </div>
    </div>
  </div>

</div>
@endsection