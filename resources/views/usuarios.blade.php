@extends('layouts.app')

@section('page_title', 'Usuarios')

@section('content')

<style>
  :root{
    --spgi-primary:#0d6efd;
    --spgi-primary-2:#2b7bff;
    --spgi-ink:#0f172a;
    --spgi-muted:#64748b;
    --spgi-border: rgba(15, 23, 42, .10);
    --spgi-radius: 16px;
    --spgi-shadow: 0 18px 45px rgba(2, 6, 23, .10);
  }

  body{
    background:
      radial-gradient(900px 400px at 20% 10%, rgba(13,110,253,.14), transparent 60%),
      radial-gradient(900px 450px at 85% 20%, rgba(168,85,247,.12), transparent 55%),
      radial-gradient(900px 450px at 70% 90%, rgba(34,197,94,.10), transparent 55%),
      linear-gradient(135deg, rgba(13,110,253,.10), rgba(168,85,247,.08) 45%, rgba(34,197,94,.08));
    background-attachment: fixed;
  }

  .spgi-page{
    padding: 12px 0 24px 0;
  }

  .spgi-head{
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    gap:12px;
    flex-wrap:wrap;
    margin-bottom: 14px;
  }

  .page-title{
    font-size: 1.65rem;
    font-weight: 800;
    letter-spacing: .2px;
    margin: 0;
    color: var(--spgi-ink);
  }

  .page-sub{
    color: var(--spgi-muted);
    font-size: .95rem;
    margin-top: 4px;
  }

  .spgi-head-actions{
    display:flex;
    align-items:center;
    gap:10px;
    flex-wrap:wrap;
  }

  .badge-spgi{
    border-radius: 999px;
    padding: 8px 12px;
    font-weight: 700;
    font-size: .82rem;
    border: 1px solid rgba(0,0,0,.08);
    background: rgba(255,255,255,.9);
    color: #495057;
    box-shadow: 0 10px 24px rgba(2,6,23,.07);
  }

  .btn-spgi{
    background: linear-gradient(135deg, var(--spgi-primary), var(--spgi-primary-2));
    border: 0;
    color: #fff !important;
    min-height:44px;
    border-radius:12px;
    padding:0 14px;
    white-space:nowrap;
    box-shadow: 0 10px 24px rgba(2,6,23,.07);
    font-weight:700;
  }

  .btn-spgi:hover{
    filter: brightness(.98);
    transform: translateY(-1px);
  }

  .spgi-card{
    border: 1px solid var(--spgi-border);
    border-radius: var(--spgi-radius);
    box-shadow: var(--spgi-shadow);
    background: rgba(255,255,255,.92);
    backdrop-filter: blur(6px);
    overflow: hidden;
  }

  .spgi-card .card-head{
    padding: 14px 16px;
    border-bottom: 1px solid rgba(0,0,0,.06);
    display: flex;
    gap: 12px;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
  }

  .spgi-card .card-body-spgi{
    padding: 0;
  }

  .search-wrap{
    display: flex;
    gap: 10px;
    align-items: center;
    flex-wrap: wrap;
    width: 100%;
  }

  .search-group{
    display:flex;
    align-items:center;
    flex: 1 1 340px;
    min-width: 0;
    background:#fff;
    border:1px solid var(--spgi-border);
    border-radius:12px;
    box-shadow: 0 8px 20px rgba(2,6,23,.05);
    overflow:hidden;
  }

  .search-group .search-icon{
    min-width:44px;
    height:44px;
    display:flex;
    align-items:center;
    justify-content:center;
    color:#64748b;
    background:#fff;
  }

  .search-input{
    border:0 !important;
    box-shadow:none !important;
    border-radius:0 !important;
    height:44px;
    min-width:0;
  }

  .search-input:focus{
    box-shadow:none !important;
  }

  .btn-search,
  .btn-clear{
    min-height:44px;
    border-radius:12px;
    padding:0 14px;
    white-space:nowrap;
    font-weight:700;
  }

  .btn-clear{
    border: 1px solid rgba(0,0,0,.08);
    background:#fff;
  }

  .table-spgi{
    margin: 0;
  }

  .table-spgi thead th{
    font-size: .92rem;
    letter-spacing: .2px;
    background: #0b1220;
    color:#fff;
    border-bottom: 1px solid rgba(255,255,255,.08) !important;
    padding: 12px 14px;
    vertical-align: middle;
  }

  .table-spgi tbody td{
    padding: 12px 14px;
    vertical-align: middle;
    border-color: rgba(15,23,42,.08) !important;
  }

  .table-spgi tbody tr:hover{
    background: #fbfcff;
  }

  .acciones{
    display: inline-flex;
    gap: 8px;
    align-items: center;
    justify-content: center;
    flex-wrap: wrap;
  }

  .acciones .btn{
    height: 38px;
    padding: 0 12px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    font-weight: 600;
    font-size: .88rem;
    white-space: nowrap;
  }

  .mobile-users{
    display:none;
  }

  .user-card{
    background: rgba(255,255,255,.95);
    border: 1px solid var(--spgi-border);
    border-radius: 18px;
    box-shadow: 0 12px 30px rgba(2,6,23,.08);
    padding: 14px;
  }

  .user-card + .user-card{
    margin-top: 14px;
  }

  .user-card-head{
    display:flex;
    align-items:flex-start;
    justify-content:space-between;
    gap:10px;
    margin-bottom: 10px;
  }

  .user-name{
    margin:0;
    font-size:1rem;
    font-weight:800;
    color:var(--spgi-ink);
    line-height:1.25;
  }

  .user-chip{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    min-height:34px;
    padding:0 12px;
    border-radius:999px;
    background: rgba(13,110,253,.10);
    color: var(--spgi-primary);
    font-size:.82rem;
    font-weight:700;
    white-space:nowrap;
  }

  .user-field{
    background:#fff;
    border:1px solid rgba(15,23,42,.07);
    border-radius:12px;
    padding:10px 12px;
    margin-bottom:12px;
  }

  .user-field-label{
    font-size: .78rem;
    font-weight: 700;
    color: var(--spgi-muted);
    text-transform: uppercase;
    letter-spacing: .4px;
    display:block;
    margin-bottom: 2px;
  }

  .user-field-value{
    color: var(--spgi-ink);
    font-weight: 600;
    word-break: break-word;
  }

  .user-card-actions{
    display:grid;
    grid-template-columns:1fr;
    gap:8px;
  }

  .user-card-actions .btn{
    width:100%;
    min-height:42px;
    border-radius:12px;
    display:flex;
    align-items:center;
    justify-content:center;
    gap:6px;
    font-weight:600;
  }

  #alerta-exito{
    border-radius: 12px;
  }

  .modal-content{
    border-radius: 16px;
    border: 1px solid rgba(0,0,0,.08);
    box-shadow: 0 18px 40px rgba(0,0,0,.12);
  }

  .modal-header{
    border-top-left-radius: 16px;
    border-top-right-radius: 16px;
  }

  .form-control,
  .form-select{
    border-radius: 12px;
  }

  .modal-footer .btn{
    border-radius: 12px;
    min-height: 40px;
    padding: 0 14px;
    font-weight: 600;
  }

  @media (max-width: 767.98px){
    .spgi-page .container{
      padding-left: 0;
      padding-right: 0;
    }

    .page-title{
      font-size: 1.25rem;
    }

    .page-sub{
      font-size: .9rem;
    }

    .spgi-head{
      align-items:stretch;
    }

    .spgi-head-actions{
      width:100%;
      justify-content:stretch;
    }

    .badge-spgi,
    .spgi-head-actions .btn{
      width:100%;
      justify-content:center;
    }

    .spgi-card .card-head{
      padding: 12px;
    }

    .search-wrap{
      flex-direction:column;
      align-items:stretch;
      gap:8px;
    }

    .search-group{
      flex: 0 0 auto !important;
      width:100%;
    }

    .search-group .search-icon{
      min-width:38px;
      height:38px;
    }

    .search-input{
      height:38px !important;
      min-height:38px !important;
      font-size:13px;
      padding:5px 10px;
    }

    .btn-search,
    .btn-clear{
      width:100%;
      min-height:38px;
      font-size:13px;
      padding:5px 10px;
    }

    .desktop-users{
      display:none;
    }

    .mobile-users{
      display:block;
      padding: 0;
    }

    .modal-dialog{
      margin:.75rem;
    }

    .modal-dialog.modal-lg{
      max-width: calc(100% - 1.5rem);
    }
  }

  @media (min-width: 768px){
    .desktop-users{
      display:block;
    }
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