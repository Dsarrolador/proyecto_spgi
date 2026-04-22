@extends('layouts.app')

@section('content')
<style>
  .spgi-bg{ padding: 24px 0; }
  .spgi-title{ font-weight: 800; font-size: 1.6rem; color: var(--text-main); letter-spacing: -.5px; margin:0; }
  
  .btn-spgi{
    background: linear-gradient(135deg, var(--spgi-primary), #2563eb);
    border: 0; color: #fff !important; min-height:46px; border-radius:14px; padding:0 24px;
    box-shadow: 0 10px 25px var(--spgi-primary-glow); font-weight:700;
  }
  .btn-spgi:hover{ filter: brightness(1.1); transform: translateY(-1px); }

  .spgi-card{
    background: var(--bg-surface-glass); border: 1px solid var(--border-main);
    border-radius: 20px; box-shadow: var(--shadow-main); backdrop-filter: blur(16px);
    overflow: hidden;
  }
  
  .table-spgi{ margin: 0; }
  .table-spgi thead th{
    background: #0b1220; color:#fff; border-color: rgba(255,255,255,0.08) !important;
    font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; padding: 14px;
  }
  .table-spgi tbody td{ border-color: var(--border-main) !important; color: var(--text-main); padding: 14px; }
  .table-spgi tbody tr:hover{ background: rgba(var(--spgi-primary), 0.05); }

  /* Modal Styling */
  .modal-content{ 
    background: var(--bg-surface-glass); border: 1px solid var(--border-main);
    backdrop-filter: blur(20px); border-radius: 24px; color: var(--text-main);
  }
  .modal-header{ border-bottom: 1px solid var(--border-main); padding: 24px; }
  .modal-footer{ border-top: 1px solid var(--border-main); padding: 20px 24px; }
  
  .form-label{ font-weight: 800; color: var(--text-muted); font-size: .75rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; }
  .form-control, .form-select{
    background: rgba(var(--text-main), 0.02) !important; color: var(--text-main) !important;
    border-radius:12px !important; border:1px solid var(--border-main) !important;
    padding:.75rem 1rem !important; box-shadow: none !important;
  }
  .form-control:focus, .form-select:focus{ border-color: var(--spgi-primary) !important; }
</style>

<div class="spgi-bg">
  <div class="container">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
      <h4 class="spgi-title">Roles de Usuario</h4>

      <button class="btn btn-spgi" data-bs-toggle="modal" data-bs-target="#modalNuevoRol">
        <i class="bi bi-plus-circle me-1"></i> Nuevo Rol
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

    <div class="spgi-card">
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-spgi mb-0 align-middle">
            <thead>
              <tr>
                <th style="width:80px;">ID</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th style="width:110px;" class="text-center">Activo</th>
                <th style="width:190px;" class="text-center">Acciones</th>
              </tr>
            </thead>
            <tbody>
              @forelse($roles as $rol)
                <tr>
                  <td class="text-center">{{ $rol->id }}</td>
                  <td class="fw-bold">{{ $rol->nombre }}</td>
                  <td>{{ $rol->descripcion ?? '-' }}</td>
                  <td class="text-center">
                    @if($rol->activo)
                      <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill">Sí</span>
                    @else
                      <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-3 py-2 rounded-pill">No</span>
                    @endif
                  </td>
                  <td class="text-center">
                    <div class="d-flex justify-content-center gap-2 flex-wrap">
  
                      <button class="btn btn-warning btn-sm fw-bold px-3 rounded-pill"
                        data-bs-toggle="modal"
                        data-bs-target="#modalEditarRol{{ $rol->id }}">
                        <i class="bi bi-pencil-square me-1"></i> Editar
                      </button>
  
                      <form action="{{ route('mantenimiento.roles.destroy', $rol->id) }}" method="POST"
                            onsubmit="return confirm('¿Eliminar este rol?');">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm rounded-circle" style="width:32px; height:32px; padding:0;">
                          <i class="bi bi-trash"></i>
                        </button>
                      </form>
  
                    </div>
                  </td>
                </tr>



            @empty
              <tr>
                <td colspan="5" class="text-center text-muted py-4">No hay roles registrados.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- MODAL NUEVO --}}
  <div class="modal fade" id="modalNuevoRol" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">

        <form action="{{ route('mantenimiento.roles.store') }}" method="POST">
          @csrf

          <div class="modal-header">
            <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Nuevo rol</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Nombre</label>
                <input type="text" name="nombre" class="form-control" required>
              </div>

              <div class="col-md-6">
                <label class="form-label">Activo</label>
                <select name="activo" class="form-select" required>
                  <option value="1" selected>Sí</option>
                  <option value="0">No</option>
                </select>
              </div>

              <div class="col-12">
                <label class="form-label">Descripción</label>
                <textarea name="descripcion" class="form-control" rows="3"></textarea>
              </div>
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

</div>

@foreach($roles as $rol)
{{-- MODAL EDITAR --}}
<div class="modal fade" id="modalEditarRol{{ $rol->id }}" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">

      <form action="{{ route('mantenimiento.roles.update', $rol->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="modal-header">
          <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Editar rol</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Nombre</label>
              <input type="text" name="nombre" class="form-control" value="{{ $rol->nombre }}" required>
            </div>

            <div class="col-md-6">
              <label class="form-label">Activo</label>
              <select name="activo" class="form-select" required>
                <option value="1" {{ $rol->activo ? 'selected' : '' }}>Sí</option>
                <option value="0" {{ !$rol->activo ? 'selected' : '' }}>No</option>
              </select>
            </div>

            <div class="col-12">
              <label class="form-label">Descripción</label>
              <textarea name="descripcion" class="form-control" rows="3">{{ $rol->descripcion }}</textarea>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button class="btn btn-success">Guardar</button>
        </div>

      </form>

    </div>
  </div>
</div>
@endforeach

@endsection
