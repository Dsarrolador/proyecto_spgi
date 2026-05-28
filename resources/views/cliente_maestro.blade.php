@extends('layouts.app')

@section('page_title', 'Clientes')

@section('content')

<style>
  .spgi-bg{ background: transparent !important; padding: 12px 0 24px 0; }

  .spgi-toolbar{
    background: var(--bg-surface-glass); border: 1px solid var(--border-main);
    border-radius: 20px; box-shadow: var(--shadow-main); backdrop-filter: blur(16px); padding: 20px;
  }

  .spgi-title{ font-weight: 800; color: var(--text-main); letter-spacing: -.5px; margin: 0; font-size: 1.6rem; }

  .spgi-input{
    height: 46px; border-radius: 12px; border: 1px solid var(--border-main);
    background-color: var(--bg-surface); color: var(--text-main); min-width: 260px;
  }

  .btn-spgi{
    background: linear-gradient(135deg, var(--spgi-primary), #2563eb);
    border: 0; color: #fff !important; height:46px; border-radius:14px; padding:0 24px;
    box-shadow: 0 10px 25px var(--spgi-primary-glow); font-weight: 700;
  }
  .btn-spgi:hover{ filter: brightness(1.1); transform: translateY(-1px); }

  .btn-outline-spgi{
    height:46px; border-radius:12px; padding:0 20px;
    border: 1px solid var(--border-main); background: var(--bg-surface); color: var(--text-main);
  }

  .spgi-table-box{
    background: var(--bg-surface-glass); border: 1px solid var(--border-main);
    border-radius: 20px; box-shadow: var(--shadow-main); overflow: hidden; backdrop-filter: blur(16px);
  }

  .spgi-table{ margin-bottom: 0; background: transparent; }
  .spgi-table thead th{
    background: #0b1220; color: #fff; border-color: rgba(255,255,255,0.08) !important;
    font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;
  }
  .spgi-table tbody td{ border-color: var(--border-main) !important; color: var(--text-main); }

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
    .spgi-mobile-list{ display: none !important; }
    .spgi-table-desktop{ display: block; }
  }
</style>

<div class="spgi-bg">
  <div class="container">

    <div class="spgi-toolbar mb-4">
      <div class="spgi-card p-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <h3 class="spgi-title mb-1">Directorio de Clientes</h3>
                <p class="text-muted small mb-0">Gestión centralizada de socios comerciales y entidades.</p>
            </div>

            <div class="d-flex align-items-center gap-3 flex-wrap">
              <form class="spgi-search-form d-flex gap-2" action="{{ route('clientes.index') }}" method="GET">
                <input class="form-control spgi-input"
                       type="search"
                       name="q"
                       value="{{ request('q') }}"
                       placeholder="Buscar por nombre o RNC...">
                <button class="btn btn-outline-spgi px-3" type="submit">
                  <i class="bi bi-search"></i>
                </button>
              </form>

              <a href="{{ route('clientes.create') }}" class="btn btn-spgi">
                <i class="bi bi-person-plus me-2"></i> Nuevo Cliente
              </a>
            </div>
        </div>
      </div>
    </div>

    <div class="spgi-table-wrap">

      {{-- TABLA DESKTOP / TABLET --}}
      <div class="spgi-table-desktop spgi-card">
        <div class="table-responsive">
          <table class="table table-spgi align-middle">
            <thead>
              <tr>
                <th class="ps-4">Nombre Comercial</th>
                <th>RNC</th>
                <th>Teléfono</th>
                <th class="text-center">Clasif.</th>
                <th>Categoría</th>
                <th class="text-center col-acciones pe-4">Acciones</th>
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

                    <a href="{{ route('clientes.entorno.show', $cliente->id) }}"
                       class="btn btn-dark btn-sm"
                       title="Directorio de Entorno">
                      <i class="bi bi-gear-wide-connected"></i>
                    </a>

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
              <a href="{{ route('clientes.entorno.show', $cliente->id) }}"
                 class="btn btn-dark btn-sm">
                <i class="bi bi-gear-wide-connected me-1"></i> Entorno
              </a>

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
            <label class="form-label">Cumpleaños</label>
            <input type="date" name="fecha_nacimiento" class="form-control">
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

              <div class="col-12 col-md-6">
                <label class="form-label fw-bold">Cumpleaños</label>
                <input type="date" name="fecha_nacimiento" class="form-control" value="{{ $c->fecha_nacimiento }}">
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