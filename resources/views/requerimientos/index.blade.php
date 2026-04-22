@extends('layouts.app')

@section('page_title', 'Requerimientos')

@section('content')

<style>
  .spgi-bg{ padding: 12px 0 24px 0; }

  .btn-spgi{
    background: linear-gradient(135deg, var(--spgi-primary), #2563eb);
    border: 0; color: #fff !important; min-height:46px; border-radius:14px; padding:0 24px;
    box-shadow: 0 10px 25px var(--spgi-primary-glow); font-weight:700;
  }
  .btn-spgi:hover{ filter: brightness(1.1); transform: translateY(-1px); }

  .btn-outline-spgi{
    border: 1px solid var(--border-main); background: var(--bg-surface);
    color: var(--text-main); border-radius: 12px; height: 46px; font-weight: 700;
  }
  .btn-outline-spgi:hover{ background: rgba(var(--spgi-primary), 0.05); }

  .spgi-toolbar{
    background: var(--bg-surface-glass); border: 1px solid var(--border-main);
    border-radius: 20px; box-shadow: var(--shadow-main); backdrop-filter: blur(16px);
    padding: 24px; margin-bottom: 24px;
  }

  .toolbar-actions{ display:flex; justify-content:flex-end; gap:10px; flex-wrap:wrap; margin-bottom: 20px; }
  .toolbar-actions .btn{ min-height:46px; border-radius:14px; padding:0 24px; font-weight: 700; }

  .toolbar-selects{ display:flex; gap:12px; align-items:center; flex-wrap:wrap; margin:0; }
  .toolbar-selects .form-select{
    height:46px; border-radius:12px; border:1px solid var(--border-main);
    background-color: var(--bg-surface); color: var(--text-main); min-width:200px;
    box-shadow: none !important;
  }

  .spgi-table-box{
    background: var(--bg-surface-glass); border: 1px solid var(--border-main);
    border-radius: 22px; box-shadow: var(--shadow-main); overflow-x: auto; backdrop-filter: blur(16px);
  }

  .spgi-table{ margin-bottom: 0; min-width: 1000px; width: 100%; table-layout: fixed; }
  .spgi-table thead th{
    background: #0b1220; color: #fff; text-align:center;
    border-color: rgba(255,255,255,0.08) !important;
    font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; padding: 16px;
  }

  /* Column widths */
  .col-cliente { width: 180px; }
  .col-req { width: auto; }
  .col-asignado { width: 180px; }
  .col-estado { width: 140px; }
  .col-acciones { width: 160px; }

  .spgi-table tbody td{ 
    border-color: var(--border-main) !important; 
    color: var(--text-main); 
    padding: 16px; 
    word-break: break-word; 
    vertical-align: middle;
  }
  .spgi-table tbody tr:hover{ background: rgba(var(--spgi-primary), 0.05); }

  .acciones .btn{ width: 38px; height: 38px; display: inline-flex; align-items: center; justify-content: center; padding: 0; border-radius: 10px; }

  .preview-box{
    background: rgba(var(--text-main), 0.03); border: 1px solid var(--border-main);
    border-radius: 14px; padding: 12px; color: var(--text-main); font-size: .85rem;
    transition: all 0.2s ease;
  }
  .preview-box:hover{ background: rgba(var(--spgi-primary), 0.1); border-color: var(--spgi-primary); }

  .spgi-req-card{
    background: var(--bg-surface-glass); border: 1px solid var(--border-main);
    border-radius: 20px; box-shadow: var(--shadow-main); padding: 20px;
    backdrop-filter: blur(12px);
  }

  .spgi-req-card + .spgi-req-card{
    margin-top: 14px;
  }

  .spgi-req-head{
    display:flex;
    align-items:flex-start;
    justify-content:space-between;
    gap:12px;
    margin-bottom: 12px;
  }

  .spgi-req-title{
    margin:0;
    font-size:1rem;
    font-weight:800;
    color:var(--spgi-ink);
    line-height:1.25;
  }

  .spgi-field-value{
    font-size:.92rem;
    font-weight:600;
    color:var(--spgi-ink);
    line-height:1.45;
    word-break:break-word;
    cursor: pointer;
  }

  .spgi-field-value.truncated{
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .spgi-req-subtitle{
    color:var(--spgi-muted);
    font-size:.88rem;
    margin-top:4px;
    line-height:1.25;
  }

  .spgi-info-grid{
    display:grid;
    grid-template-columns:1fr;
    gap:10px;
    margin-bottom:12px;
  }

  .spgi-field{
    background: rgba(0,0,0,0.02);
    border: 1px solid var(--border-main);
    border-radius: 14px; padding: 12px;
  }
  [data-bs-theme="dark"] .spgi-field { background: rgba(255,255,255,0.03); }

  .spgi-field-label{
    font-size:.7rem; font-weight:800; color:var(--text-muted);
    text-transform:uppercase; letter-spacing:1px; margin-bottom:4px;
  }

  .spgi-badge{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    min-height:34px;
    padding:0 12px;
    border-radius:999px;
    font-size:.82rem;
    font-weight:700;
    white-space:nowrap;
  }

  .spgi-card-actions{
    display:grid;
    grid-template-columns: repeat(2, 1fr);
    gap:8px;
  }

  .spgi-card-actions .btn,
  .spgi-card-actions form .btn{
    width:100%;
    min-height:42px;
    border-radius:12px;
    display:flex;
    align-items:center;
    justify-content:center;
  }

  .spgi-empty{
    text-align:center;
    color:var(--spgi-muted);
    padding:30px 15px;
    background:rgba(255,255,255,.9);
    border-radius:16px;
    border:1px solid var(--spgi-border);
  }

  .modal-content{
    border:0;
    border-radius:18px;
    overflow:hidden;
  }

  .spgi-historial-box{
    max-height: 500px;
    overflow-y: auto;
    background: #f8fafc;
    padding: 20px;
  }

  .spgi-pagination-wrap{
    margin-top: 24px; display: flex; justify-content: center;
  }

  .spgi-pagination-wrap nav{
    width: auto !important;
    max-width: 100%;
  }

  .spgi-pagination-wrap svg,
  .spgi-pagination-wrap nav svg,
  .spgi-pagination-wrap .svg-inline--fa,
  .spgi-pagination-wrap [class*="pagination"] svg{
    width: 14px !important;
    height: 14px !important;
    min-width: 14px !important;
    min-height: 14px !important;
    max-width: 14px !important;
    max-height: 14px !important;
    display: inline-block !important;
    vertical-align: middle !important;
  }

  .spgi-pagination-wrap nav > div{
    width: auto !important;
  }

  .spgi-pagination-wrap nav .hidden{
    display: none !important;
  }

  .spgi-pagination-wrap nav p{
    margin-bottom: 0;
  }

  .spgi-pagination-wrap nav span,
  .spgi-pagination-wrap nav a{
    font-size: 14px;
  }

  @media (max-width: 991.98px){
    .toolbar-selects .form-select{
      min-width:0;
      flex: 1 1 260px;
    }
  }

  @media (max-width: 767.98px){
    .spgi-bg .container{
      padding-left:0;
      padding-right:0;
    }

    .spgi-toolbar{
      padding:14px;
      border-radius:16px;
    }

    .toolbar-actions{
      justify-content:stretch;
    }

    .toolbar-actions .btn{
      flex:1 1 100%;
      width:100%;
    }

    .toolbar-selects{
      flex-direction:column;
      align-items:stretch;
      gap:10px;
    }

    .toolbar-selects .form-select{
      width:100%;
      min-width:0;
      flex: 0 0 auto !important;
      height:42px !important;
      min-height:42px !important;
      padding:6px 12px;
      font-size:14px;
      border-radius:10px;
    }

    .spgi-table-desktop{
      display:none;
    }

    .spgi-mobile-list{
      display:block;
      margin-top:14px;
    }

    .spgi-card-actions{
      grid-template-columns:1fr;
    }

    .modal-dialog{
      margin:.75rem;
    }

    .modal-dialog.modal-lg,
    .modal-dialog.modal-xl{
      max-width: calc(100% - 1.5rem);
    }

    .spgi-historial-box{
      max-height: none;
      border-right: 0 !important;
      padding: 14px;
    }
  }

  @media (max-width: 576px){
    .spgi-toolbar{
      padding:12px;
    }

    .toolbar-selects .form-select{
      height:38px !important;
      min-height:38px !important;
      padding:5px 10px;
      font-size:13px;
    }
  }

  @media (min-width: 768px){
    .spgi-mobile-list{ display:none !important; }
    .spgi-table-desktop{ display:block; }
  }
</style>

<div class="spgi-bg">
  <div class="container">
    <div class="mb-4">
      <h3 class="fw-bold text-gradient">Requerimientos Industriales</h3>
      <p class="text-muted small mb-0">Gestión de requerimientos y soporte operativo.</p>
    </div>

    <div class="spgi-toolbar mb-3">

      <div class="toolbar-actions">
        <button type="button"
                class="btn btn-outline-secondary btn-outline-spgi"
                data-bs-toggle="modal"
                data-bs-target="#modalFiltrosAvanzados">
          <i class="bi bi-sliders me-1"></i> Filtros avanzados
        </button>

        <a href="{{ route('requerimientos.create') }}" class="btn btn-spgi">
          <i class="bi bi-plus-lg me-1"></i> Agregar
        </a>
      </div>

      <form action="{{ route('requerimientos.index') }}" method="GET" class="toolbar-selects">

        <select name="estado" class="form-select" onchange="this.form.submit()">
          <option value="">Todos (sin completados)</option>
          @foreach($estados as $e)
            <option value="{{ $e->id }}" {{ request('estado') == $e->id ? 'selected' : '' }}>{{ $e->nombre }}</option>
          @endforeach
          <option value="Todos" {{ request('estado') == 'Todos' ? 'selected' : '' }}>Mostrar Todos</option>
        </select>

        <select name="cliente_id" class="form-select" onchange="this.form.submit()">
          <option value="">Todos los clientes</option>
          @foreach(($clientes ?? collect()) as $c)
            <option value="{{ $c->id }}" {{ (string)request('cliente_id') === (string)$c->id ? 'selected' : '' }}>
              {{ $c->nombre }}
            </option>
          @endforeach
        </select>

        <select name="asignado_id" class="form-select" onchange="this.form.submit()">
          <option value="mios" {{ request('asignado_id', 'mios') === 'mios' ? 'selected' : '' }}>Mis requerimientos</option>
          <option value="todos" {{ request('asignado_id') === 'todos' ? 'selected' : '' }}>Todos</option>
          @foreach(($asignados ?? collect()) as $u)
            <option value="{{ $u->id }}" {{ (string)request('asignado_id') === (string)$u->id ? 'selected' : '' }}>
              {{ $u->name ?? $u->nombre ?? $u->email }}
            </option>
          @endforeach
        </select>

      </form>
    </div>

    <div class="spgi-table-wrap">

      <div class="spgi-table-desktop spgi-table-box">
          <table class="spgi-table table table-bordered align-middle">
            <thead>
              <tr>
                <th class="col-cliente">Cliente</th>
                <th class="col-req">Requerimiento</th>
                <th class="col-asignado">Asignado a</th>
                <th style="width: 120px;">Fecha</th>
                <th class="col-estado">Estado</th>
                @if($esAdmin || $esEncargado)
                  <th style="width: 140px;">Facturación</th>
                @endif
                <th class="col-acciones">Acciones</th>
              </tr>
            </thead>

            <tbody>
              @forelse ($requerimientos as $req)
                <tr>
                  <td class="td-cliente">
                    {{ $req->clienteRelation->nombre ?? 'Sin cliente asignado' }}
                  </td>

                  <td class="td-preview">
                    <div class="preview-box" title="{{ $req->texto_imagen ?? 'Sin descripción' }}">
                      {{ $req->texto_imagen ?? 'Sin descripción' }}
                    </div>
                  </td>

                  <td class="text-center">
                    <div class="d-flex flex-column align-items-center">
                      <span class="fw-bold">{{ $req->asignado?->name ?? 'Sin asignar' }}</span>
                      
                      @if($req->es_colaborativo)
                        <span class="badge bg-info-subtle text-info border border-info-subtle mt-1" style="font-size: 0.65rem;">
                          <i class="bi bi-people-fill me-1"></i> COLABORATIVO
                        </span>
                      @endif

                      @if($req->colaboradores->count() > 0)
                        <div class="mt-1">
                          <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle" 
                                title="Colaboradores: {{ $req->colaboradores->pluck('name')->implode(', ') }}" 
                                data-bs-toggle="tooltip">
                            <i class="bi bi-plus-circle me-1"></i> +{{ $req->colaboradores->count() }}
                          </span>
                        </div>
                      @endif

                      @if($req->user_id == auth()->id() && $req->asignado_user_id != auth()->id())
                        <small class="text-muted italic mt-1" style="font-size: 0.7rem;">(Creado por ti)</small>
                      @endif
                    </div>
                  </td>

                  <td class="text-center">
                    {{ optional($req->created_at)->format('d/m/Y') }}
                  </td>

                  <td class="text-center">
                    @php
                      $_color = optional($req->estadoRequerimiento)->color ?? 'bg-secondary';
                      $_nombre = optional($req->estadoRequerimiento)->nombre ?? 'Pendiente';
                    @endphp
                    @if(\Illuminate\Support\Str::startsWith($_color, '#'))
                      <span class="badge" style="background-color: {{ $_color }}; color: #fff;">{{ $_nombre }}</span>
                    @else
                      <span class="badge {{ $_color }}">{{ $_nombre }}</span>
                    @endif
                  </td>

                  @if($esAdmin || $esEncargado)
                  <td class="text-center">
                    @if((int)($req->facturado ?? 0) === 1)
                      <span class="badge bg-success">Facturado</span>
                    @else
                      <span class="badge bg-warning text-dark">No facturado</span>
                    @endif
                  </td>
                  @endif

                  <td>
                    <div class="d-flex justify-content-center gap-2 acciones flex-nowrap">

                      <a href="{{ route('requerimientos.show', $req->id) }}"
                         class="btn btn-primary btn-sm"
                         title="Ver requerimiento">
                        <i class="bi bi-eye"></i>
                      </a>

                      <button type="button"
                              class="btn btn-outline-info btn-sm"
                              onclick="openNovedadesModal({{ $req->id }}, '{{ addslashes($req->clienteRelation->nombre ?? 'Cliente no asignado') }}', {{ $req->cliente_id ?? 'null' }})"
                              title="Novedades">
                        <i class="bi bi-journal-text"></i>
                      </button>

                      <form action="{{ route('requerimientos.destroy', $req->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="btn btn-danger btn-sm"
                                title="Eliminar"
                                onclick="return confirm('¿Eliminar este requerimiento?')">
                          <i class="bi bi-trash"></i>
                        </button>
                      </form>

                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="{{ ($esAdmin || $esEncargado) ? 7 : 6 }}" class="text-center text-muted p-4">
                    No hay requerimientos registrados.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      <div class="spgi-mobile-list">
        @forelse ($requerimientos as $req)
          <div class="spgi-req-card">
            <div class="spgi-req-head">
              <div>
                <h5 class="spgi-req-title">
                  {{ $req->clienteRelation->nombre ?? 'Sin cliente asignado' }}
                </h5>
                <div class="spgi-req-subtitle">
                  Asignado a: {{ $req->asignado?->name ?? 'Sin asignar' }}
                  @if($req->user_id == auth()->id() && $req->asignado_user_id != auth()->id())
                    <span class="small text-muted italic">(Creado por ti)</span>
                  @endif
                  
                  @if($req->es_colaborativo)
                    <div class="mt-1">
                      <span class="badge bg-info-subtle text-info border border-info-subtle" style="font-size: 0.7rem;">
                        <i class="bi bi-people-fill me-1"></i> Colaborativo
                      </span>
                    </div>
                  @endif

                  @if($req->colaboradores->count() > 0)
                    <div class="mt-1 small text-info">
                       <i class="bi bi-person-plus-fill me-1"></i> 
                       Con: {{ $req->colaboradores->pluck('name')->implode(', ') }}
                    </div>
                  @endif
                </div>
              </div>

              @if($esAdmin || $esEncargado)
                @if((int)($req->facturado ?? 0) === 1)
                  <span class="spgi-badge bg-success text-white">Facturado</span>
                @else
                  <span class="spgi-badge bg-warning text-dark">No facturado</span>
                @endif
              @endif
            </div>

            <div class="spgi-info-grid">
              <div class="spgi-field">
                <span class="spgi-field-label">Requerimiento</span>
                <div class="spgi-field-value truncated" onclick="this.classList.toggle('truncated')">{{ $req->texto_imagen ?? 'Sin descripción' }}</div>
              </div>

              <div class="spgi-field">
                <span class="spgi-field-label">Fecha</span>
                <div class="spgi-field-value">{{ optional($req->created_at)->format('d/m/Y') }}</div>
              </div>

              <div class="spgi-field">
                <span class="spgi-field-label">Estado</span>
                <div class="spgi-field-value">
                  @php
                    $_colorMob = optional($req->estadoRequerimiento)->color ?? 'bg-secondary';
                    $_nombreMob = optional($req->estadoRequerimiento)->nombre ?? 'Pendiente';
                  @endphp
                  @if(\Illuminate\Support\Str::startsWith($_colorMob, '#'))
                    <span class="badge" style="background-color: {{ $_colorMob }}; color: #fff;">{{ $_nombreMob }}</span>
                  @else
                    <span class="badge {{ $_colorMob }}">{{ $_nombreMob }}</span>
                  @endif
                </div>
              </div>
            </div>

            <div class="spgi-card-actions">
              <a href="{{ route('requerimientos.show', $req->id) }}" class="btn btn-primary btn-sm">
                <i class="bi bi-eye me-1"></i> Ver
              </a>

              <button type="button"
                      class="btn btn-outline-info btn-sm"
                      onclick="openNovedadesModal({{ $req->id }}, '{{ addslashes($req->clienteRelation->nombre ?? 'Cliente no asignado') }}', {{ $req->cliente_id ?? 'null' }})">
                <i class="bi bi-journal-text me-1"></i> Novedades
              </button>

              <form action="{{ route('requerimientos.destroy', $req->id) }}" method="POST" class="w-100">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="btn btn-danger btn-sm"
                        onclick="return confirm('¿Eliminar este requerimiento?')">
                  <i class="bi bi-trash me-1"></i> Eliminar
                </button>
              </form>
            </div>
          </div>
        @empty
          <div class="spgi-empty">
            No hay requerimientos registrados.
          </div>
        @endforelse
      <div class="spgi-pagination-wrap">
        {{ $requerimientos->withQueryString()->links() }}
      </div>

    </div>

  </div>
</div>

<div class="modal fade" id="modalFiltrosAvanzados" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">

      <form method="GET" action="{{ route('requerimientos.index') }}">

        <div class="modal-header bg-dark text-white">
          <h5 class="modal-title">Filtros avanzados</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <div class="row g-3">

            <div class="col-12 col-md-6">
              <label class="form-label">Estado</label>
              <select name="estado" class="form-select">
                <option value="">Todos (sin completados)</option>
                @foreach($estados as $e)
                  <option value="{{ $e->id }}" {{ request('estado') == $e->id ? 'selected' : '' }}>{{ $e->nombre }}</option>
                @endforeach
                <option value="Todos" {{ request('estado') == 'Todos' ? 'selected' : '' }}>Mostrar Todos</option>
              </select>
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label">Cliente</label>
              <select name="cliente_id" class="form-select">
                <option value="">Todos los clientes</option>
                @foreach(($clientes ?? collect()) as $c)
                  <option value="{{ $c->id }}" {{ (string)request('cliente_id') === (string)$c->id ? 'selected' : '' }}>
                    {{ $c->nombre }}
                  </option>
                @endforeach
              </select>
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label">Asignado a</label>
              <select name="asignado_id" class="form-select">
                <option value="mios" {{ request('asignado_id', 'mios') === 'mios' ? 'selected' : '' }}>Mis requerimientos</option>
                <option value="todos" {{ request('asignado_id') === 'todos' ? 'selected' : '' }}>Todos</option>
                @foreach(($asignados ?? collect()) as $u)
                  <option value="{{ $u->id }}" {{ (string)request('asignado_id') === (string)$u->id ? 'selected' : '' }}>
                    {{ $u->name ?? $u->nombre ?? $u->email }}
                  </option>
                @endforeach
              </select>
            </div>

            @if($esAdmin || $esEncargado)
            <div class="col-12 col-md-6">
              <label class="form-label">Facturación</label>
              <select name="facturado" class="form-select">
                <option value="">Todos</option>
                <option value="1" {{ request('facturado') === '1' ? 'selected' : '' }}>Facturados</option>
                <option value="0" {{ request('facturado') === '0' ? 'selected' : '' }}>No facturados</option>
              </select>
            </div>
            @endif

            <div class="col-12 col-md-6">
              <label class="form-label">Categoría iguala</label>
              <select name="categoria_iguala" class="form-select">
                <option value="">Todas</option>
                @foreach($categoriasIguala as $plan)
                  <option value="{{ $plan->id }}" {{ (string)request('categoria_iguala') === (string)$plan->id ? 'selected' : '' }}>
                    {{ $plan->nombre }}
                  </option>
                @endforeach
                {{-- No quitamos las opciones legacy por si hay datos viejos --}}
                <option value="Cliente de iguala solo sistema" {{ request('categoria_iguala') == 'Cliente de iguala solo sistema' ? 'selected' : '' }}>Cliente de iguala solo sistema (viejo)</option>
                <option value="Cliente de iguala premium" {{ request('categoria_iguala') == 'Cliente de iguala premium' ? 'selected' : '' }}>Cliente de iguala premium (viejo)</option>
                <option value="Cliente de iguala avanzada" {{ request('categoria_iguala') == 'Cliente de iguala avanzada' ? 'selected' : '' }}>Cliente de iguala avanzada (viejo)</option>
                <option value="Cliente de iguala Basico" {{ request('categoria_iguala') == 'Cliente de iguala Basico' ? 'selected' : '' }}>Cliente de iguala Basico (viejo)</option>
                <option value="Cliente sin iguala" {{ request('categoria_iguala') == 'Cliente sin iguala' ? 'selected' : '' }}>Cliente sin iguala (viejo)</option>
              </select>
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label">Desde</label>
              <input type="date" name="desde" class="form-control" value="{{ request('desde') }}">
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label">Hasta</label>
              <input type="date" name="hasta" class="form-control" value="{{ request('hasta') }}">
            </div>

          </div>
        </div>

        <div class="modal-footer flex-column flex-sm-row">
          <a href="{{ route('requerimientos.index') }}" class="btn btn-light w-100 w-sm-auto">Limpiar</a>
          <button type="submit" class="btn btn-primary w-100 w-sm-auto">Buscar</button>
        </div>

      </form>

    </div>
  </div>
</div>

@foreach ($requerimientos as $req)
<div class="modal fade" id="modalEstado{{ $req->id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <form action="{{ route('requerimientos.update', $req->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="modal-header bg-secondary text-white">
          <h5 class="modal-title">Cambiar estado</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <input type="hidden" name="cliente_id" value="{{ $req->cliente_id }}">
          <input type="hidden" name="texto_imagen" value="{{ $req->texto_imagen }}">

          <label class="form-label">Seleccione el nuevo estado:</label>
          <select name="estado_id" class="form-select" required>
            @foreach($estados as $e)
              <option value="{{ $e->id }}" {{ $req->estado_id == $e->id ? 'selected' : '' }}>{{ $e->nombre }}</option>
            @endforeach
          </select>
        </div>

        <div class="modal-footer flex-column flex-sm-row">
          <button type="button" class="btn btn-secondary w-100 w-sm-auto" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success w-100 w-sm-auto">Actualizar</button>
        </div>

      </form>

    </div>
  </div>
</div>
@endforeach

<!-- MODAL DE NOVEDADES DINÁMICO (CATEGORIZADO) -->
<div class="modal fade" id="modalNovedadesDinamico" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content glass-card-premium border-0 overflow-hidden">
      
      <!-- Cabecera Dinámica -->
      <div class="modal-header border-0 p-4 d-flex justify-content-between align-items-center" id="modal-header-novedades" style="background: linear-gradient(135deg, var(--spgi-primary), #2563eb); transition: all 0.3s ease;">
        <div class="d-flex align-items-center gap-3">
            <button type="button" id="btn-back-dashboard-modal" class="btn btn-link text-white p-0 d-none" onclick="regresarAlDashboardModal()">
                <i class="bi bi-arrow-left fs-4"></i>
            </button>
            <div>
                <h5 class="modal-title text-white fw-bold mb-0" id="modal-dinamico-title">Novedades</h5>
                <small class="text-white text-opacity-75" id="modal-dinamico-client">Cliente</small>
            </div>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body p-0">
        
        <!-- DASHBOARD DE SELECCIÓN (DENTRO DEL MODAL) -->
        <div id="modal-dashboard-novedades" class="p-5 animate__animated animate__fadeIn">
            <div class="text-center mb-4">
                <h4 class="fw-bold text-gradient">¿Qué desea consultar?</h4>
                <p class="text-muted">Seleccione el tipo de seguimiento para continuar</p>
            </div>
            <div class="row g-4 justify-content-center">
                <div class="col-md-5">
                    <div class="glass-card-premium p-4 text-center h-100 cursor-pointer hover-scale border-top border-4 border-success" onclick="modalSwitchCategory('cliente')">
                        <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                            <i class="bi bi-people-fill fs-1 text-success"></i>
                        </div>
                        <h5 class="fw-bold mb-2 text-success">Novedades Clientes</h5>
                        <p class="small text-muted mb-0">Avances oficiales compartidos con el cliente.</p>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="glass-card-premium p-4 text-center h-100 cursor-pointer hover-scale border-top border-4 border-primary" onclick="modalSwitchCategory('interno')">
                        <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                            <i class="bi bi-shield-lock-fill fs-1 text-primary"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Notas Internas</h5>
                        <p class="small text-muted mb-0">Detalles técnicos y notas privadas para el equipo.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- CONTENIDO DE NOVEDADES (LISTA + FORM) -->
        <div id="modal-content-novedades" class="d-none animate__animated animate__fadeIn">
            <div class="row g-0">
                <!-- Historial -->
                <div class="col-12 col-md-7 border-end p-4 bg-light bg-opacity-50 overflow-auto" style="height: 500px;" id="modal-historial-list">
                    <!-- Los items se cargan aquí -->
                </div>

                <!-- Formulario -->
                <div class="col-12 col-md-5 p-4 d-flex flex-column" style="background: var(--bg-surface);">
                    <h6 class="fw-bold mb-3 small text-uppercase text-muted" id="modal-form-title">Agregar Seguimiento</h6>
                    <form id="modal-form-novedad-dinamico" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="requerimiento_id" id="modal-req-id">
                        <input type="hidden" name="cliente_id" id="modal-cliente-id">
                        <input type="hidden" name="tipo" id="modal-tipo-input">

                        <div class="mb-3">
                            <textarea name="novedad" class="form-control border-0 shadow-sm" rows="6" placeholder="Escribe aquí..." required style="border-radius: 14px; resize: none; background: var(--bg-surface);"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small text-muted">Adjuntar archivo (opcional)</label>
                            <input type="file" name="adjunto" id="modal-adjunto-input" class="form-control form-control-sm rounded-pill">
                        </div>

                        <div id="modal-progress-container" class="mb-3 d-none">
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold py-2 mt-auto" id="modal-btn-save">
                            <i class="bi bi-send-fill me-1"></i> Publicar Seguimiento
                        </button>
                    </form>
                </div>
            </div>
        </div>

      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
    // --- LÓGICA DEL MODAL DINÁMICO DE NOVEDADES ---
    let currentNovedadesData = [];
    const modalDinamico = new bootstrap.Modal(document.getElementById('modalNovedadesDinamico'));
    const modalHistorialList = document.getElementById('modal-historial-list');
    const modalBtnSave = document.getElementById('modal-btn-save');
    const modalForm = document.getElementById('modal-form-novedad-dinamico');
    
    window.openNovedadesModal = function(id, title, clientId) {
        // Reset modal state
        document.getElementById('modal-dinamico-title').innerText = "Novedades de:";
        document.getElementById('modal-dinamico-client').innerText = title;
        document.getElementById('modal-req-id').value = id;
        document.getElementById('modal-cliente-id').value = clientId;
        
        regresarAlDashboardModal();
        modalHistorialList.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Cargando historial...</p></div>';
        
        modalDinamico.show();

        // Cargar datos vía AJAX
        fetch(`/novedades/${id}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            currentNovedadesData = data;
            // No renderizamos aún, esperamos a que elija categoría
        })
        .catch(err => {
            modalHistorialList.innerHTML = '<p class="text-danger p-4">Error al cargar el historial.</p>';
        });
    }

    window.modalSwitchCategory = function(tipo) {
        document.getElementById('modal-dashboard-novedades').classList.add('d-none');
        document.getElementById('modal-content-novedades').classList.remove('d-none');
        document.getElementById('btn-back-dashboard-modal').classList.remove('d-none');
        
        const header = document.getElementById('modal-header-novedades');
        const formTitle = document.getElementById('modal-form-title');
        const tipoInput = document.getElementById('modal-tipo-input');
        
        tipoInput.value = tipo;
        
        if (tipo === 'interno') {
            header.style.background = 'linear-gradient(135deg, #3b82f6, #2563eb)';
            formTitle.innerText = "Agregar Nota Interna";
            formTitle.className = "fw-bold mb-3 small text-uppercase text-primary";
            modalBtnSave.className = "btn btn-primary w-100 rounded-pill fw-bold py-2 mt-auto";
        } else {
            header.style.background = 'linear-gradient(135deg, #10b981, #059669)';
            formTitle.innerText = "Agregar Seguimiento Cliente";
            formTitle.className = "fw-bold mb-3 small text-uppercase text-success";
            modalBtnSave.className = "btn btn-success w-100 rounded-pill fw-bold py-2 mt-auto";
        }

        renderFilteredNovedades(tipo);
    }

    window.regresarAlDashboardModal = function() {
        document.getElementById('modal-dashboard-novedades').classList.remove('d-none');
        document.getElementById('modal-content-novedades').classList.add('d-none');
        document.getElementById('btn-back-dashboard-modal').classList.add('d-none');
        document.getElementById('modal-header-novedades').style.background = 'linear-gradient(135deg, #1e293b, #0f172a)';
    }

    function renderFilteredNovedades(tipo) {
        const filtered = currentNovedadesData.filter(n => n.tipo === tipo);
        
        if (filtered.length === 0) {
            modalHistorialList.innerHTML = `
                <div class="text-center py-5 opacity-50">
                    <i class="bi bi-chat-left-dots fs-1 d-block mb-2"></i>
                    <p>No hay registros en esta categoría.</p>
                </div>`;
            return;
        }

        modalHistorialList.innerHTML = filtered.reverse().map(n => `
            <div class="glass-card-premium p-3 mb-3 border-0 animate__animated animate__fadeIn">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-bold small ${tipo === 'interno' ? 'text-primary' : 'text-success'}">
                        <i class="bi ${tipo === 'interno' ? 'bi-shield-lock' : 'bi-person'} me-1"></i>
                        ${n.user_name}
                    </span>
                    <small class="text-muted">${n.created_at}</small>
                </div>
                <p class="mb-2 small" style="white-space: pre-wrap; color: var(--text-main);">${n.novedad}</p>
                ${n.file_url ? `
                    <a href="${n.file_url}" class="btn btn-sm btn-outline-secondary py-1 px-3 rounded-pill" style="font-size: 0.7rem;">
                        <i class="bi bi-download me-1"></i> Descargar
                    </a>
                ` : ''}
            </div>
        `).join('');
    }

    if (modalForm) {
        modalForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const xhr = new XMLHttpRequest();
            const tipo = document.getElementById('modal-tipo-input').value;

            modalBtnSave.disabled = true;
            modalBtnSave.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Publicando...';

            xhr.open('POST', '{{ route("novedades.store") }}', true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

            xhr.upload.onprogress = function(e) {
                if (e.lengthComputable) {
                    const pct = Math.round((e.loaded / e.total) * 100);
                    const container = document.getElementById('modal-progress-container');
                    container.classList.remove('d-none');
                    container.querySelector('.progress-bar').style.width = pct + '%';
                }
            };

            xhr.onload = function() {
                modalBtnSave.disabled = false;
                modalBtnSave.innerHTML = '<i class="bi bi-send-fill me-1"></i> Publicar Seguimiento';
                document.getElementById('modal-progress-container').classList.add('d-none');

                if (xhr.status >= 200 && xhr.status < 300) {
                    const res = JSON.parse(xhr.responseText);
                    if (res.success) {
                        modalForm.reset();
                        // Actualizar data local y re-renderizar
                        currentNovedadesData.push({
                            id: res.novedad.id,
                            novedad: res.novedad.novedad,
                            user_name: res.user_name,
                            created_at: res.created_at,
                            file_url: res.file_url,
                            file_name: res.file_name,
                            tipo: res.tipo
                        });
                        renderFilteredNovedades(tipo);
                    }
                }
            };
            xhr.send(formData);
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Tooltips y otros
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        tooltipTriggerList.map(function (el) { return new bootstrap.Tooltip(el) });
        
        document.querySelectorAll('.preview-box').forEach(box => {
            box.addEventListener('click', function() { this.classList.toggle('expanded'); });
        });
    });
</script>
@endpush

@endsection