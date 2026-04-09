@extends('layouts.app')

@section('page_title', 'Clientes')

@section('content')

<style>
  :root{
    --spgi-primary:#0d6efd;
    --spgi-primary-2:#2b7bff;
    --spgi-ink:#0f172a;
    --spgi-muted:#64748b;
    --spgi-border: rgba(15, 23, 42, .10);
  }

  body{
    background:
      radial-gradient(900px 400px at 20% 10%, rgba(13,110,253,.14), transparent 60%),
      radial-gradient(900px 450px at 85% 20%, rgba(168,85,247,.12), transparent 55%),
      radial-gradient(900px 450px at 70% 90%, rgba(34,197,94,.10), transparent 55%),
      linear-gradient(135deg, rgba(13,110,253,.10), rgba(168,85,247,.08) 45%, rgba(34,197,94,.08));
    background-attachment: fixed;
  }

  .spgi-bg{
    background: transparent !important;
    padding: 12px 0 24px 0;
  }

  .spgi-toolbar{
    background: rgba(255,255,255,.92);
    border: 1px solid var(--spgi-border);
    border-radius: 18px;
    box-shadow: 0 18px 45px rgba(2, 6, 23, .10);
    backdrop-filter: blur(6px);
    padding: 16px;
  }

  .spgi-toolbar .toolbar-top{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:16px;
    flex-wrap:wrap;
  }

  .spgi-title{
    font-weight: 800;
    color: var(--spgi-ink);
    letter-spacing: -.3px;
    margin: 0;
    font-size: 1.5rem;
  }

  .spgi-actions-top{
    display:flex;
    align-items:center;
    gap:12px;
    flex-wrap:wrap;
    width: 100%;
    justify-content:flex-end;
  }

  .spgi-search-form{
    display:flex;
    gap:10px;
    align-items:center;
    flex-wrap:wrap;
  }

  .spgi-input{
    height: 44px;
    border-radius: 12px;
    border: 1px solid var(--spgi-border);
    box-shadow: 0 8px 20px rgba(2,6,23,.05);
    min-width: 260px;
  }

  .btn-spgi{
    background: linear-gradient(135deg, var(--spgi-primary), var(--spgi-primary-2));
    border: 0;
    color: #fff !important;
    height:44px;
    border-radius:12px;
    padding:0 14px;
    white-space:nowrap;
    box-shadow: 0 10px 24px rgba(2,6,23,.07);
  }

  .btn-spgi:hover{
    filter: brightness(.98);
    transform: translateY(-1px);
  }

  .btn-outline-spgi{
    height:44px;
    border-radius:12px;
    padding:0 14px;
    white-space:nowrap;
    border: 1px solid rgba(13,110,253,.35);
    box-shadow: 0 10px 24px rgba(2,6,23,.07);
    background: #fff;
  }

  .spgi-table-wrap{
    padding: 0;
  }

  .spgi-table-box{
    background: rgba(255,255,255,.92);
    border: 1px solid var(--spgi-border);
    border-radius: 18px;
    box-shadow: 0 18px 45px rgba(2, 6, 23, .10);
    overflow: hidden;
    backdrop-filter: blur(6px);
  }

  .spgi-table{
    margin-bottom: 0;
    background: #fff;
  }

  .spgi-table thead{
    background: #0b1220;
  }

  .spgi-table thead th{
    color: #fff;
    border-color: rgba(255,255,255,.12) !important;
    font-weight: 700;
    letter-spacing: .2px;
    white-space: nowrap;
    text-align: center;
  }

  .spgi-table tbody td{
    border-color: rgba(15,23,42,.08) !important;
    font-weight: 400;
    vertical-align: middle;
  }

  .acciones .btn{
    width: 38px;
    height: 38px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    border-radius: 10px;
  }

  .acciones form{
    margin:0;
  }

  .col-acciones{
    width: 220px;
    white-space: nowrap;
  }

  .spgi-mobile-list{
    display: none;
  }

  .spgi-client-card{
    background: rgba(255,255,255,.95);
    border: 1px solid var(--spgi-border);
    border-radius: 18px;
    box-shadow: 0 12px 30px rgba(2,6,23,.08);
    padding: 14px;
  }

  .spgi-client-card + .spgi-client-card{
    margin-top: 14px;
  }

  .spgi-client-head{
    display:flex;
    align-items:flex-start;
    justify-content:space-between;
    gap:12px;
    margin-bottom: 10px;
  }

  .spgi-client-name{
    font-size: 1rem;
    font-weight: 800;
    color: var(--spgi-ink);
    margin: 0;
    line-height: 1.25;
  }

  .spgi-badge{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    min-width: 34px;
    height: 34px;
    border-radius: 999px;
    background: rgba(13,110,253,.10);
    color: var(--spgi-primary);
    font-weight: 700;
    padding: 0 10px;
    font-size: .85rem;
    white-space: nowrap;
  }

  .spgi-client-grid{
    display:grid;
    grid-template-columns: 1fr;
    gap:10px;
    margin-bottom: 12px;
  }

  .spgi-field{
    background:#fff;
    border:1px solid rgba(15,23,42,.07);
    border-radius:12px;
    padding:10px 12px;
  }

  .spgi-field-label{
    font-size: .78rem;
    font-weight: 700;
    color: var(--spgi-muted);
    text-transform: uppercase;
    letter-spacing: .4px;
    display:block;
    margin-bottom: 2px;
  }

  .spgi-field-value{
    color: var(--spgi-ink);
    font-weight: 600;
    word-break: break-word;
  }

  .spgi-card-actions{
    display:grid;
    grid-template-columns: repeat(2, 1fr);
    gap:8px;
  }

  .spgi-card-actions .btn,
  .spgi-card-actions form .btn{
    width: 100%;
    min-height: 42px;
    border-radius: 12px;
  }

  .spgi-empty{
    text-align:center;
    color: var(--spgi-muted);
    padding: 30px 15px;
    background: rgba(255,255,255,.9);
    border-radius: 16px;
    border: 1px solid var(--spgi-border);
  }

  .modal-content{
    border: 0;
    border-radius: 18px;
    overflow: hidden;
  }

  .modal-header{
    border-bottom: 0;
  }

  .modal-footer{
    border-top: 0;
  }

  @media (max-width: 991.98px){
    .spgi-title{
      font-size: 1.3rem;
    }

    .spgi-actions-top{
      justify-content: stretch;
    }

    .spgi-search-form{
      width: 100%;
    }

    .spgi-search-form .spgi-input{
      flex: 1 1 220px;
      min-width: 0;
    }
  }

  @media (max-width: 767.98px){
    .spgi-bg .container{
      padding-left: 0;
      padding-right: 0;
    }

    .spgi-toolbar{
      padding: 14px;
      border-radius: 16px;
    }

    .toolbar-top{
      align-items: stretch !important;
    }

    .spgi-title{
      font-size: 1.2rem;
    }

    .spgi-actions-top{
      width: 100%;
      justify-content: stretch;
      gap: 10px;
    }

    .spgi-search-form{
      width: 100%;
      flex-direction: column;
      align-items: stretch;
      gap: 10px;
    }

    .spgi-search-form .spgi-input{
      flex: 0 0 auto !important;
      width: 100%;
      min-width: 0;
      height: 42px !important;
      min-height: 42px !important;
      padding: 6px 12px;
      font-size: 14px;
      border-radius: 10px;
    }

    .btn-outline-spgi,
    .btn-spgi{
      width: 100%;
      min-width: 0;
    }

    .btn-outline-spgi{
      height: 42px !important;
      min-height: 42px !important;
      padding: 6px 12px;
      font-size: 14px;
      border-radius: 10px;
    }

    .spgi-table-desktop{
      display: none;
    }

    .spgi-mobile-list{
      display: block;
      margin-top: 14px;
    }

    .modal-dialog{
      margin: .75rem;
    }

    .modal-dialog.modal-xl,
    .modal-dialog.modal-lg{
      max-width: calc(100% - 1.5rem);
    }
  }

  @media (max-width: 576px){
    .spgi-toolbar{
      padding: 12px;
    }

    .spgi-search-form{
      gap: 8px;
    }

    .spgi-search-form .spgi-input{
      height: 38px !important;
      min-height: 38px !important;
      padding: 5px 10px;
      font-size: 13px;
    }

    .btn-outline-spgi{
      height: 38px !important;
      min-height: 38px !important;
      padding: 5px 10px;
      font-size: 13px;
    }
  }

  @media (min-width: 768px){
    .spgi-table-desktop{
      display: block;
    }
  }
</style>

<div class="spgi-bg">
  <div class="container">

    <div class="spgi-toolbar mb-3">
      <div class="toolbar-top">
        <h3 class="spgi-title">Clientes</h3>

        <div class="spgi-actions-top">
          <form class="spgi-search-form" action="{{ route('clientes.index') }}" method="GET">
            <input class="form-control spgi-input"
                   type="search"
                   name="q"
                   value="{{ request('q') }}"
                   placeholder="Buscar cliente...">
            <button class="btn btn-outline-spgi" type="submit">
              <i class="bi bi-search me-1"></i> Buscar
            </button>
          </form>

          <a href="{{ route('clientes.create') }}" class="btn btn-spgi d-flex align-items-center justify-content-center">
            <i class="bi bi-person-plus me-1"></i> Agregar Cliente
          </a>
        </div>
      </div>
    </div>

    <div class="spgi-table-wrap">

      {{-- TABLA DESKTOP / TABLET --}}
      <div class="spgi-table-desktop spgi-table-box">
        <div class="table-responsive">
          <table class="table table-bordered align-middle spgi-table">
            <thead>
              <tr>
                <th>Nombre</th>
                <th>RNC</th>
                <th>Teléfono</th>
                <th>Clasificación</th>
                <th>Categoría</th>
                <th class="text-center col-acciones">Acciones</th>
              </tr>
            </thead>

            <tbody>
            @forelse($clientes as $cliente)
              <tr>
                <td>{{ $cliente->nombre }}</td>
                <td>{{ $cliente->rnc }}</td>
                <td>{{ $cliente->telefono_principal }}</td>
                <td class="text-center">{{ $cliente->clasificacion_negocio }}</td>
                <td>{{ $cliente->categoria?->categoria ?? 'Sin categoría' }}</td>

                <td class="text-center">
                  <div class="d-flex justify-content-center gap-2 acciones flex-nowrap">

                    <button class="btn btn-secondary btn-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#modalAgregarContacto{{ $cliente->id }}"
                            title="Agregar contacto">
                      <i class="bi bi-clipboard-plus"></i>
                    </button>

                    <button class="btn btn-info btn-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#modalContactosCliente{{ $cliente->id }}"
                            title="Ver contactos">
                      <i class="bi bi-eye"></i>
                    </button>

                    <button class="btn btn-warning btn-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#editarCliente{{ $cliente->id }}"
                            title="Editar cliente">
                      <i class="bi bi-pencil-square"></i>
                    </button>

                    <form action="{{ route('clientes.destroy', $cliente->id) }}"
                          method="POST"
                          class="d-inline">
                      @csrf
                      @method('DELETE')
                      <button type="submit"
                              onclick="return confirm('¿Eliminar este cliente?')"
                              class="btn btn-danger btn-sm"
                              title="Eliminar">
                        <i class="bi bi-trash"></i>
                      </button>
                    </form>

                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center text-muted py-4">
                  No hay clientes registrados.
                </td>
              </tr>
            @endforelse
            </tbody>
          </table>
        </div>
      </div>

      {{-- TARJETAS MÓVIL --}}
      <div class="spgi-mobile-list">
        @forelse($clientes as $cliente)
          <div class="spgi-client-card">
            <div class="spgi-client-head">
              <div>
                <h5 class="spgi-client-name">{{ $cliente->nombre }}</h5>
              </div>
              <span class="spgi-badge">
                {{ $cliente->clasificacion_negocio ?: '-' }}
              </span>
            </div>

            <div class="spgi-client-grid">
              <div class="spgi-field">
                <span class="spgi-field-label">RNC</span>
                <div class="spgi-field-value">{{ $cliente->rnc ?: 'No disponible' }}</div>
              </div>

              <div class="spgi-field">
                <span class="spgi-field-label">Teléfono</span>
                <div class="spgi-field-value">{{ $cliente->telefono_principal ?: 'No disponible' }}</div>
              </div>

              <div class="spgi-field">
                <span class="spgi-field-label">Categoría</span>
                <div class="spgi-field-value">{{ $cliente->categoria?->categoria ?? 'Sin categoría' }}</div>
              </div>
            </div>

            <div class="spgi-card-actions">
              <button class="btn btn-secondary btn-sm"
                      data-bs-toggle="modal"
                      data-bs-target="#modalAgregarContacto{{ $cliente->id }}">
                <i class="bi bi-clipboard-plus me-1"></i> Contacto
              </button>

              <button class="btn btn-info btn-sm"
                      data-bs-toggle="modal"
                      data-bs-target="#modalContactosCliente{{ $cliente->id }}">
                <i class="bi bi-eye me-1"></i> Ver
              </button>

              <button class="btn btn-warning btn-sm"
                      data-bs-toggle="modal"
                      data-bs-target="#editarCliente{{ $cliente->id }}">
                <i class="bi bi-pencil-square me-1"></i> Editar
              </button>

              <form action="{{ route('clientes.destroy', $cliente->id) }}"
                    method="POST"
                    class="w-100">
                @csrf
                @method('DELETE')
                <button type="submit"
                        onclick="return confirm('¿Eliminar este cliente?')"
                        class="btn btn-danger btn-sm">
                  <i class="bi bi-trash me-1"></i> Eliminar
                </button>
              </form>
            </div>
          </div>
        @empty
          <div class="spgi-empty">
            No hay clientes registrados.
          </div>
        @endforelse
      </div>

    </div>

  </div>
</div>

{{-- AGREGAR CONTACTO --}}
@foreach($clientes as $cliente)
<div class="modal fade" id="modalAgregarContacto{{ $cliente->id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <form action="{{ route('libreta_contacto.store') }}" method="POST">
        @csrf

        <div class="modal-header bg-dark text-white">
          <h5 class="modal-title">Agregar contacto</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <input type="hidden" name="codigo_cliente_maestro" value="{{ $cliente->id }}">

          <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input type="text" name="nombre" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Rol</label>
            <select name="codigo_rol" class="form-select">
              <option value="">-- Seleccionar --</option>
              @foreach($roles as $rol)
                <option value="{{ $rol->id }}">{{ $rol->nombre }}</option>
              @endforeach
            </select>
            <div class="form-text">Si no eliges, se asignará el rol por defecto (ID=1).</div>
          </div>

          <div class="mb-3">
            <label class="form-label">Teléfono</label>
            <input type="text" name="telefono" class="form-control">
          </div>

          <div class="mb-3">
            <label class="form-label">Correo</label>
            <input type="email" name="correo" class="form-control">
          </div>

          <div class="mb-3">
            <label class="form-label">Nota</label>
            <textarea name="nota" class="form-control" rows="3"></textarea>
          </div>
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

{{-- VER CONTACTOS --}}
@foreach($clientes as $cliente)
<div class="modal fade" id="modalContactosCliente{{ $cliente->id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header bg-info text-white">
        <h5 class="modal-title">
          <i class="bi bi-person-lines-fill me-2"></i>
          Contactos de {{ $cliente->nombre }}
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        @if($cliente->contactos->count())
          <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle text-center mb-0">
              <thead class="table-light">
                <tr>
                  <th>Nombre</th>
                  <th>Rol</th>
                  <th>Teléfono</th>
                  <th>Correo</th>
                  <th>Nota</th>
                  <th style="width:120px;">Acciones</th>
                </tr>
              </thead>
              <tbody>
                @foreach($cliente->contactos as $c)
                <tr>
                  <td>{{ $c->nombre }}</td>
                  <td>{{ $c->rol->nombre ?? 'Sin rol' }}</td>
                  <td>{{ $c->telefono }}</td>
                  <td>{{ $c->correo }}</td>
                  <td>{{ $c->nota }}</td>
                  <td>
                    <div class="d-flex justify-content-center gap-1 flex-wrap">

                      <button class="btn btn-warning btn-sm"
                              data-bs-toggle="modal"
                              data-bs-target="#modalEditarContacto{{ $c->id }}">
                        <i class="bi bi-pencil"></i>
                      </button>

                      <form action="{{ route('libreta_contacto.destroy', $c->id) }}"
                            method="POST"
                            onsubmit="return confirm('¿Eliminar este contacto?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">
                          <i class="bi bi-trash"></i>
                        </button>
                      </form>

                    </div>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          <p class="text-muted text-center mb-0">Este cliente no tiene contactos.</p>
        @endif
      </div>

    </div>
  </div>
</div>
@endforeach

{{-- EDITAR CONTACTO --}}
@foreach($clientes as $cliente)
  @foreach($cliente->contactos as $c)
  <div class="modal fade" id="modalEditarContacto{{ $c->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">

        <form method="POST" action="{{ route('libreta_contacto.update', $c->id) }}">
          @csrf
          @method('PUT')

          <div class="modal-header bg-warning">
            <h5 class="modal-title">
              <i class="bi bi-pencil-square me-1"></i> Editar contacto
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body text-start">
            <div class="row g-3">

              <div class="col-12 col-md-8">
                <label class="form-label fw-bold">Nombre</label>
                <input type="text" name="nombre" class="form-control" value="{{ $c->nombre }}" required>
              </div>

              <div class="col-12 col-md-4">
                <label class="form-label fw-bold">Rol</label>
                <select name="codigo_rol" class="form-select" required>
                  @foreach($roles as $rol)
                    <option value="{{ $rol->id }}" {{ (string)$rol->id === (string)$c->codigo_rol ? 'selected' : '' }}>
                      {{ $rol->nombre }}
                    </option>
                  @endforeach
                </select>
              </div>

              <div class="col-12 col-md-6">
                <label class="form-label fw-bold">Teléfono</label>
                <input type="text" name="telefono" class="form-control" value="{{ $c->telefono }}">
              </div>

              <div class="col-12 col-md-6">
                <label class="form-label fw-bold">Correo</label>
                <input type="email" name="correo" class="form-control" value="{{ $c->correo }}">
              </div>

              <div class="col-12">
                <label class="form-label fw-bold">Nota</label>
                <textarea name="nota" class="form-control" rows="3">{{ $c->nota }}</textarea>
              </div>

            </div>
          </div>

          <div class="modal-footer flex-column flex-sm-row">
            <button type="button" class="btn btn-secondary w-100 w-sm-auto" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-success w-100 w-sm-auto">Guardar cambios</button>
          </div>

        </form>

      </div>
    </div>
  </div>
  @endforeach
@endforeach

{{-- EDITAR CLIENTE --}}
@foreach($clientes as $cliente)
<div class="modal fade" id="editarCliente{{ $cliente->id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">

      <form method="POST" action="{{ route('clientes.update', $cliente->id) }}">
        @csrf
        @method('PUT')

        <div class="modal-header bg-warning">
          <h5 class="modal-title">Editar cliente</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <div class="row g-3">

            <div class="col-12 col-md-6">
              <label class="form-label">Nombre</label>
              <input type="text" name="nombre" class="form-control"
                     value="{{ $cliente->nombre }}" required>
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label">RNC</label>
              <input type="text" name="rnc" class="form-control"
                     value="{{ $cliente->rnc }}">
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label">Teléfono</label>
              <input type="text" name="telefono_principal" class="form-control"
                     value="{{ $cliente->telefono_principal }}">
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label">Clasificación</label>
              <select name="clasificacion_negocio" class="form-select">
                <option value="">-- Selecciona una opción --</option>
                <option value="A" {{ $cliente->clasificacion_negocio=='A'?'selected':'' }}>A</option>
                <option value="B" {{ $cliente->clasificacion_negocio=='B'?'selected':'' }}>B</option>
                <option value="C" {{ $cliente->clasificacion_negocio=='C'?'selected':'' }}>C</option>
                <option value="D" {{ $cliente->clasificacion_negocio=='D'?'selected':'' }}>D</option>
              </select>
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label">Categoría</label>
              <select name="clasificacion_interna" class="form-select">
                <option value="">-- Sin categoría --</option>
                @foreach($categorias as $cat)
                  <option value="{{ $cat->id }}"
                    {{ (string)$cliente->clasificacion_interna === (string)$cat->id ? 'selected' : '' }}>
                    {{ $cat->categoria }}
                  </option>
                @endforeach
              </select>
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label">Categoría iguala</label>
              <select name="categoria_iguala_id" class="form-select">
                <option value="">-- Selecciona una opción --</option>
                @foreach($categoriasIguala as $plan)
                  <option value="{{ $plan->id }}"
                    {{ (string)$cliente->categoria_iguala_id === (string)$plan->id ? 'selected' : '' }}>
                    {{ $plan->nombre }}
                  </option>
                @endforeach
              </select>
            </div>

            <div class="col-12">
              <label class="form-label">Dirección</label>
              <input type="text" name="direccion_escrita" class="form-control"
                     value="{{ $cliente->direccion_escrita }}">
            </div>

            <div class="col-12">
              <label class="form-label">Notas</label>
              <textarea name="notas" class="form-control" rows="3">{{ $cliente->notas }}</textarea>
            </div>

          </div>
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

@endsection