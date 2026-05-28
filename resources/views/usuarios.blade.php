@extends('layouts.app')

@section('page_title', 'Usuarios')

@section('content')

<style>
  .spgi-page{ padding: 12px 0 24px 0; }
  .spgi-head{ display:flex; justify-content:space-between; align-items:flex-start; gap:12px; flex-wrap:wrap; margin-bottom: 24px; }

  .page-title{
    font-size: 1.65rem; font-weight: 800; letter-spacing: -.5px; margin: 0;
    color: var(--text-main);
  }
  .page-sub{ color: var(--text-muted); font-size: .95rem; margin-top: 4px; }

  .badge-spgi{
    border-radius: 999px; padding: 8px 16px; font-weight: 700; font-size: .8rem;
    border: 1px solid var(--border-main); background: var(--bg-surface);
    color: var(--text-main); box-shadow: var(--shadow-main);
  }

  .btn-spgi{
    background: linear-gradient(135deg, var(--spgi-primary), #2563eb);
    border: 0; color: #fff !important; min-height:46px; border-radius:14px; padding:0 20px;
    box-shadow: 0 10px 25px var(--spgi-primary-glow); font-weight:700;
  }
  .btn-spgi:hover{ filter: brightness(1.1); transform: translateY(-1px); }

  .spgi-card{
    background: var(--bg-surface-glass); border: 1px solid var(--border-main);
    border-radius: 20px; box-shadow: var(--shadow-main); backdrop-filter: blur(16px);
    overflow: hidden;
  }
  .spgi-card .card-head{
    padding: 20px; border-bottom: 1px solid var(--border-main);
    display: flex; gap: 12px; align-items: center; justify-content: space-between; flex-wrap: wrap;
  }

  .search-group{
    display:flex; align-items:center; flex: 1 1 340px; min-width: 0;
    background: var(--bg-surface); border: 1px solid var(--border-main);
    border-radius:14px; overflow:hidden;
  }
  .search-group .search-icon{
    min-width:44px; height:44px; display:flex; align-items:center; justify-content:center;
    color: var(--text-muted);
  }
  .search-input{ background: transparent !important; color: var(--text-main) !important; border: 0 !important; }

  .table-spgi thead th{
    background: #0b1220; color:#fff; border-color: rgba(255,255,255,0.08) !important;
    font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;
  }
  .table-spgi tbody td{ border-color: var(--border-main) !important; color: var(--text-main); }
  .table-spgi tbody tr:hover{ background: rgba(var(--spgi-primary), 0.05); }

  .user-card{
    background: var(--bg-surface-glass); border: 1px solid var(--border-main);
    border-radius: 20px; box-shadow: var(--shadow-main); padding: 16px; backdrop-filter: blur(16px);
  }

  .user-field{
    background: rgba(var(--text-main), 0.02); border: 1px solid var(--border-main);
    border-radius: 14px; padding: 12px; margin-bottom:12px;
  }
  .user-field-label{
    font-size: .7rem; font-weight: 800; color: var(--text-muted);
    text-transform: uppercase; letter-spacing: .8px; margin-bottom: 4px;
  }
  .user-field-value{ color: var(--text-main); font-weight: 600; }

  @media (max-width: 767.98px){
    .search-input{ height:38px !important; min-height:38px !important; font-size:13px; padding:5px 10px; }
    .btn-search, .btn-clear{ width:100%; min-height:38px; font-size:13px; padding:5px 10px; }
    .desktop-users{ display:none; }
    .mobile-users{ display:block; padding: 0; }
    .modal-dialog{ margin:.75rem; }
    .modal-dialog.modal-lg{ max-width: calc(100% - 1.5rem); }
  }

  @media (min-width: 768px){
    .mobile-users{ display:none !important; }
    .desktop-users{ display:block; }
  }
</style>

<div class="spgi-page">
  <div class="container">

    <div class="spgi-head">
      <div>
        <h3 class="page-title">Usuarios registrados</h3>
        <div class="page-sub">Administra usuarios del sistema (crear, editar y eliminar).</div>
      </div>

      <div class="spgi-head-actions">
        <span class="badge-spgi">
          <i class="bi bi-people me-1"></i>
          Total: {{ $usuarios->count() ?? 0 }}
        </span>

        <button class="btn btn-spgi d-flex align-items-center"
                type="button"
                data-bs-toggle="modal"
                data-bs-target="#modalUsuario">
          <i class="bi bi-plus-lg me-1"></i> Agregar
        </button>
      </div>
    </div>

    @if(session('success'))
      <div class="alert alert-success text-center" id="alerta-exito">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
      </div>
    @endif

    @if(session('error'))
      <div class="alert alert-danger text-center">
        <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
      </div>
    @endif

    <div class="spgi-card">
      <div class="card-head">
        <form class="search-wrap m-0" action="{{ route('usuarios.index') }}" method="GET">
          <div class="search-group">
            <span class="search-icon">
              <i class="bi bi-search"></i>
            </span>
            <input class="form-control search-input"
                   type="search"
                   name="q"
                   value="{{ request('q') }}"
                   placeholder="Buscar usuario por nombre o correo...">
          </div>

          <button class="btn btn-outline-success btn-search" type="submit">
            Buscar
          </button>

          @if(request('q'))
            <a href="{{ route('usuarios.index') }}" class="btn btn-clear">
              Limpiar
            </a>
          @endif
        </form>
      </div>

      <div class="card-body-spgi">

        {{-- TABLA DESKTOP --}}
        <div class="desktop-users">
          <div class="table-responsive">
            <table class="table table-spgi table-bordered align-middle">
              <thead>
                <tr>
                  <th style="width: 35%;">Nombre</th>
                  <th>Correo</th>
                  <th>Rol</th>
                  <th>Cumpleaños</th>
                  <th class="text-center" style="width: 220px;">Acciones</th>
                </tr>
              </thead>

              <tbody>
              @forelse($usuarios as $u)
                <tr>
                  <td class="fw-semibold">{{ $u->name }}</td>
                  <td>
                    <i class="bi bi-envelope me-1 text-secondary"></i>
                    {{ $u->email }}
                  </td>
                  <td>
                    <span class="badge bg-secondary text-white">{{ $u->role->nombre ?? 'Sin Rol' }}</span>
                  </td>
                  <td>
                    @if($u->cumpleanos)
                      <span class="badge bg-warning text-dark">
                        <i class="bi bi-gift-fill me-1"></i>
                        {{ \Carbon\Carbon::parse($u->cumpleanos->fecha_nacimiento)->format('d/m/Y') }}
                      </span>
                    @else
                      -
                    @endif
                  </td>

                  <td class="text-center">
                    <div class="acciones">
                      <button class="btn btn-warning"
                              type="button"
                              data-bs-toggle="modal"
                              data-bs-target="#editarUsuario{{ $u->id }}">
                        <i class="bi bi-pencil-square"></i> Editar
                      </button>

                      <button class="btn btn-danger"
                              type="button"
                              data-bs-toggle="modal"
                              data-bs-target="#confirmarEliminar{{ $u->id }}">
                        <i class="bi bi-trash"></i> Eliminar
                      </button>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="3" class="text-center py-4 text-muted">
                    <i class="bi bi-inbox me-2"></i>Sin usuarios
                  </td>
                </tr>
              @endforelse
              </tbody>
            </table>
          </div>
        </div>

        {{-- TARJETAS MÓVIL --}}
        <div class="mobile-users">
          @forelse($usuarios as $u)
            <div class="user-card">
              <div class="user-card-head">
                <div>
                  <h5 class="user-name">{{ $u->name }}</h5>
                </div>
                <span class="user-chip">
                  <i class="bi bi-person me-1"></i> {{ $u->role->nombre ?? 'Usuario' }}
                </span>
              </div>

              <div class="user-field">
                <span class="user-field-label">Correo</span>
                <div class="user-field-value">{{ $u->email }}</div>
              </div>

              <div class="user-field">
                <span class="user-field-label">Cumpleaños</span>
                <div class="user-field-value">
                  @if($u->cumpleanos)
                    <span class="badge bg-warning text-dark">
                      <i class="bi bi-gift-fill me-1"></i>
                      {{ \Carbon\Carbon::parse($u->cumpleanos->fecha_nacimiento)->format('d/m/Y') }}
                    </span>
                  @else
                    -
                  @endif
                </div>
              </div>

              <div class="user-card-actions">
                <button class="btn btn-warning"
                        type="button"
                        data-bs-toggle="modal"
                        data-bs-target="#editarUsuario{{ $u->id }}">
                  <i class="bi bi-pencil-square"></i> Editar
                </button>

                <button class="btn btn-danger"
                        type="button"
                        data-bs-toggle="modal"
                        data-bs-target="#confirmarEliminar{{ $u->id }}">
                  <i class="bi bi-trash"></i> Eliminar
                </button>
              </div>
            </div>
          @empty
            <div class="p-4 text-center text-muted">
              <i class="bi bi-inbox me-2"></i>Sin usuarios
            </div>
          @endforelse
        </div>

      </div>
    </div>

  </div>
</div>

{{-- MODALES POR USUARIO --}}
@foreach($usuarios as $u)
  <div class="modal fade" id="confirmarEliminar{{ $u->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-danger">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            Confirmar eliminación
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body text-center">
          ¿Eliminar usuario <strong>{{ $u->name }}</strong>?
          <div class="text-muted mt-2" style="font-size:.92rem;">
            Esta acción no se puede deshacer.
          </div>
        </div>
        <div class="modal-footer flex-column flex-sm-row">
          <button type="button" class="btn btn-secondary w-100 w-sm-auto" data-bs-dismiss="modal">Cancelar</button>
          <form action="{{ route('usuarios.destroy', $u->id) }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">Eliminar</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="editarUsuario{{ $u->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-warning">
          <h5 class="modal-title">
            <i class="bi bi-pencil-square me-1"></i>
            Editar usuario
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <form action="{{ route('usuarios.update', $u->id) }}" method="POST">
          @csrf
          @method('PUT')

          <div class="modal-body">

            <label class="form-label fw-semibold">Nombre</label>
            <input type="text" class="form-control" name="name" value="{{ $u->name }}" required>

            <label class="form-label fw-semibold mt-3">Correo</label>
            <input type="email" class="form-control" name="email" value="{{ $u->email }}" required>

            <label class="form-label fw-semibold mt-3">Rol</label>
            <select class="form-select" name="cod_roleUser">
              <option value="">Selecciona un rol...</option>
              @foreach($roles as $r)
                <option value="{{ $r->id }}" {{ $u->cod_roleUser == $r->id ? 'selected' : '' }}>{{ $r->nombre }}</option>
              @endforeach
            </select>

            <label class="form-label fw-semibold mt-3">Cumpleaños</label>
            <input type="date" class="form-control" name="fecha_nacimiento" 
                   value="{{ $u->cumpleanos ? \Carbon\Carbon::parse($u->cumpleanos->fecha_nacimiento)->format('Y-m-d') : '' }}">

            <hr>

            <div class="alert alert-light border" style="border-radius: 12px;">
              <div class="fw-bold mb-1"><i class="bi bi-shield-lock me-1"></i> Seguridad</div>
              <div class="text-muted" style="font-size:.92rem;">
                Para cambiar la contraseña, escribe la nueva. Si no deseas cambiarla, déjala en blanco.
              </div>
            </div>

            <label class="form-label fw-semibold">Nueva contraseña</label>
            <input type="password" class="form-control" name="password">

            <label class="form-label fw-semibold mt-3">Confirmar nueva contraseña</label>
            <input type="password" class="form-control" name="password_confirmation">

          </div>

          <div class="modal-footer flex-column flex-sm-row">
            <button type="button" class="btn btn-secondary w-100 w-sm-auto" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-success w-100 w-sm-auto">Guardar</button>
          </div>

        </form>
      </div>
    </div>
  </div>
@endforeach

<div class="modal fade" id="modalUsuario" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">
          <i class="bi bi-person-plus me-2"></i> Agregar usuario
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <form action="{{ route('usuarios.store') }}" method="POST">
        @csrf

        <div class="modal-body">

          <label class="form-label fw-semibold">Nombre</label>
          <input type="text" class="form-control" name="name" required>

          <label class="form-label fw-semibold mt-3">Correo</label>
          <input type="email" class="form-control" name="email" required>

          <label class="form-label fw-semibold mt-3">Rol</label>
          <select class="form-select" name="cod_roleUser" required>
            <option value="">Selecciona un rol...</option>
            @foreach($roles as $r)
              <option value="{{ $r->id }}">{{ $r->nombre }}</option>
            @endforeach
          </select>

          <label class="form-label fw-semibold mt-3">Cumpleaños</label>
          <input type="date" class="form-control" name="fecha_nacimiento">

          <hr>

          <label class="form-label fw-semibold">Contraseña</label>
          <input type="password" class="form-control" name="password" required>

          <label class="form-label fw-semibold mt-3">Confirmar contraseña</label>
          <input type="password" class="form-control" name="password_confirmation" required>

        </div>

        <div class="modal-footer flex-column flex-sm-row">
          <button type="button" class="btn btn-secondary w-100 w-sm-auto" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success w-100 w-sm-auto">Guardar</button>
        </div>

      </form>

    </div>
  </div>
</div>

@push('scripts')
<script>
  const alerta = document.getElementById('alerta-exito');
  if (alerta) {
    setTimeout(() => {
      alerta.classList.add('opacity-0');
      setTimeout(() => alerta.remove(), 450);
    }, 2200);
  }
</script>
@endpush

@endsection