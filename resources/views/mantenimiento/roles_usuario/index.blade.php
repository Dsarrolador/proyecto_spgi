@extends('layouts.app')

@section('content')
<div class="container py-4">

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0">Roles de Usuario</h4>

    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevoRol">
      <i class="bi bi-plus-circle"></i> Nuevo
    </button>
  </div>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
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
      <div class="table-responsive">
        <table class="table table-bordered table-striped mb-0 align-middle">
          <thead class="table-light">
            <tr>
              <th style="width:80px;">ID</th>
              <th>Nombre</th>
              <th style="width:190px;" class="text-center">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @forelse($roles as $rol)
              <tr>
                <td class="text-center">{{ $rol->id }}</td>
                <td class="fw-semibold">{{ $rol->nombre }}</td>
                <td class="text-center">
                  <div class="d-flex justify-content-center gap-2 flex-wrap">

                    <button class="btn btn-warning btn-sm"
                      data-bs-toggle="modal"
                      data-bs-target="#modalEditarRol{{ $rol->id }}">
                      <i class="bi bi-pencil-square"></i> Editar
                    </button>

                    <form action="{{ route('mantenimiento.roles-usuario.destroy', $rol->id) }}" method="POST"
                          onsubmit="return confirm('¿Eliminar este rol?');">
                      @csrf
                      @method('DELETE')
                      <button class="btn btn-danger btn-sm">
                        <i class="bi bi-trash"></i>
                      </button>
                    </form>

                  </div>
                </td>
              </tr>

              {{-- MODAL EDITAR --}}
              <div class="modal fade" id="modalEditarRol{{ $rol->id }}" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                  <div class="modal-content">

                    <form action="{{ route('mantenimiento.roles-usuario.update', $rol->id) }}" method="POST">
                      @csrf
                      @method('PUT')

                      <div class="modal-header bg-warning">
                        <h5 class="modal-title">Editar rol de usuario</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>

                      <div class="modal-body">
                        <div class="mb-3">
                          <label class="form-label">Nombre</label>
                          <input type="text" name="nombre" class="form-control" value="{{ $rol->nombre }}" required>
                        </div>
                      </div>

                      <div class="modal-footer">
                        <button class="btn btn-light border" data-bs-dismiss="modal">Cancelar</button>
                        <button class="btn btn-success">Guardar</button>
                      </div>

                    </form>

                  </div>
                </div>
              </div>

            @empty
              <tr>
                <td colspan="3" class="text-center text-muted py-4">No hay roles de usuario registrados.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- MODAL NUEVO --}}
  <div class="modal fade" id="modalNuevoRol" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">

        <form action="{{ route('mantenimiento.roles-usuario.store') }}" method="POST">
          @csrf

          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title">Nuevo rol de usuario</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Nombre</label>
              <input type="text" name="nombre" class="form-control" placeholder="Ej: Soporte" required>
            </div>
          </div>

          <div class="modal-footer">
            <button class="btn btn-light border" data-bs-dismiss="modal">Cancelar</button>
            <button class="btn btn-success">Guardar</button>
          </div>

        </form>

      </div>
    </div>
  </div>

</div>
@endsection
