@extends('layouts.app')

@section('page_title', 'Mantenimiento: Roles de Usuario')

@section('content')
<div class="container-fluid py-4">

  <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h4 class="fw-bold mb-1">Estructura de Roles</h4>
        <p class="text-muted small mb-0">Gestión de jerarquías y perfiles de acceso para el personal del sistema.</p>
    </div>

    <button class="btn btn-spgi" data-bs-toggle="modal" data-bs-target="#modalNuevoRol">
      <i class="bi bi-shield-lock me-2"></i> Nuevo Rol
    </button>
  </div>

  @if(session('success'))
    <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4" style="background: rgba(34, 197, 94, 0.1); color: #22c55e;">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
    </div>
  @endif

  @if(session('error'))
    <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
    </div>
  @endif

  @if($errors->any())
    <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4">
      <ul class="mb-0 small fw-bold">
        @foreach($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="spgi-card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-spgi mb-0 align-middle">
          <thead>
            <tr>
              <th style="width:80px;" class="ps-4">ID</th>
              <th>Denominación del Rol</th>
              <th style="width:200px;" class="text-center">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @forelse($roles as $rol)
              <tr>
                <td class="ps-4 text-muted fw-mono">#{{ $rol->id }}</td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-pill bg-primary bg-opacity-10 px-3 py-1">
                            <i class="bi bi-person-badge text-primary small me-1"></i>
                            <span class="fw-bold">{{ $rol->nombre }}</span>
                        </div>
                    </div>
                </td>
                <td class="text-center">
                  <div class="d-flex justify-content-center gap-2">
                    <button class="btn btn-sm btn-outline-warning border-0 rounded-circle"
                      data-bs-toggle="modal"
                      data-bs-target="#modalEditarRol{{ $rol->id }}" title="Editar Rol">
                      <i class="bi bi-pencil-square fs-5"></i>
                    </button>

                    <form action="{{ route('mantenimiento.roles-usuario.destroy', $rol->id) }}" method="POST"
                          onsubmit="return confirm('¿Eliminar este rol del sistema?');">
                      @csrf
                      @method('DELETE')
                      <button class="btn btn-sm btn-outline-danger border-0 rounded-circle" title="Eliminar Rol">
                        <i class="bi bi-trash3 fs-5"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>


            @empty
              <tr>
                <td colspan="3" class="text-center py-5">
                    <div class="text-muted opacity-50">
                        <i class="bi bi-shield-exclamation fs-1 d-block mb-3"></i>
                        No hay roles definidos en la base de datos.
                    </div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- MODAL NUEVO --}}
  <div class="modal fade" id="modalNuevoRol" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form action="{{ route('mantenimiento.roles-usuario.store') }}" method="POST">
          @csrf

          <div class="modal-header">
            <h5 class="modal-title fw-bold">
                <i class="bi bi-shield-plus me-2 text-primary"></i>Nuevo Rol de Usuario
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body p-4">
            <div class="mb-0">
              <label class="form-label fw-bold">Nombre del Rol</label>
              <input type="text" name="nombre" class="form-control" placeholder="Ej: Auditor Externo" required>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-spgi">Registrar Rol</button>
          </div>
        </form>
      </div>
    </div>
  </div>

</div>

@foreach($roles as $rol)
{{-- MODAL EDITAR --}}
<div class="modal fade" id="modalEditarRol{{ $rol->id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form action="{{ route('mantenimiento.roles-usuario.update', $rol->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="modal-header">
          <h5 class="modal-title fw-bold">
              <i class="bi bi-pencil-fill me-2 text-warning"></i>Editar Rol
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body p-4">
          <div class="mb-0">
            <label class="form-label fw-bold">Nombre del Rol</label>
            <input type="text" name="nombre" class="form-control" value="{{ $rol->nombre }}" required>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-spgi">Guardar Cambios</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endforeach

@endsection
