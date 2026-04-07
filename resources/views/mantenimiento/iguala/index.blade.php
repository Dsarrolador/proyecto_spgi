@extends('layouts.app')

@section('content')
<div class="container mt-4">

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Iguala</h3>

    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevo">
      <i class="bi bi-plus-circle"></i> Nuevo
    </button>
  </div>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  @if($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="card shadow-sm">
    <div class="card-body p-0">
      <table class="table table-bordered table-hover mb-0 align-middle">
        <thead class="table-dark">
          <tr>
            <th style="width:80px;">ID</th>
            <th>Nombre</th>
            <th>Descripción</th>
            <th style="width:90px;">Activo</th>
            <th style="width:210px;">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse($igualas as $i)
            <tr>
              <td>{{ $i->id }}</td>
              <td>{{ $i->nombre }}</td>
              <td>{{ $i->descripcion ?? '-' }}</td>
              <td class="text-center">
                @if($i->activo)
                  <span class="badge bg-success">Sí</span>
                @else
                  <span class="badge bg-secondary">No</span>
                @endif
              </td>
              <td class="text-center">
                <button class="btn btn-warning btn-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#modalEditar{{ $i->id }}">
                  <i class="bi bi-pencil-square"></i> Editar
                </button>

                <form action="{{ route('mantenimiento.iguala.destroy', $i->id) }}"
                      method="POST"
                      class="d-inline"
                      onsubmit="return confirm('¿Eliminar esta iguala?')">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-danger btn-sm">
                    <i class="bi bi-trash"></i> Eliminar
                  </button>
                </form>
              </td>
            </tr>

            <!-- Modal editar -->
            <div class="modal fade" id="modalEditar{{ $i->id }}" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <form method="POST" action="{{ route('mantenimiento.iguala.update', $i->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="modal-header bg-warning">
                      <h5 class="modal-title">Editar Iguala</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                      <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="nombre" class="form-control" value="{{ $i->nombre }}" required>
                      </div>

                      <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="3">{{ $i->descripcion }}</textarea>
                      </div>

                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="activo" id="act{{ $i->id }}" {{ $i->activo ? 'checked' : '' }}>
                        <label class="form-check-label" for="act{{ $i->id }}">Activo</label>
                      </div>
                    </div>

                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                      <button type="submit" class="btn btn-success">Guardar</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>

          @empty
            <tr>
              <td colspan="5" class="text-center text-muted p-4">No hay igualas registradas.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal nuevo -->
<div class="modal fade" id="modalNuevo" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="{{ route('mantenimiento.iguala.store') }}">
        @csrf

        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title">Nueva Iguala</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
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

          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="activo" id="activoNuevo" checked>
            <label class="form-check-label" for="activoNuevo">Activo</label>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Crear</button>
        </div>

      </form>
    </div>
  </div>
</div>
@endsection
