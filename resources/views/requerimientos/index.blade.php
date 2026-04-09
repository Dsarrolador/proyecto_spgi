@extends('layouts.app')

@section('page_title', 'Requerimientos')

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

  .btn-spgi{
    background: linear-gradient(135deg, var(--spgi-primary), var(--spgi-primary-2));
    border: 0;
    color: #fff !important;
  }

  .btn-spgi:hover{
    filter: brightness(.98);
    transform: translateY(-1px);
  }

  .btn-outline-spgi{
    border: 1px solid rgba(13,110,253,.35);
    background: #fff;
  }

  .spgi-toolbar{
    background: rgba(255,255,255,.92);
    border: 1px solid var(--spgi-border);
    border-radius: 18px;
    box-shadow: 0 18px 45px rgba(2, 6, 23, .10);
    backdrop-filter: blur(6px);
    padding: 16px;
  }

  .toolbar-actions{
    display:flex;
    justify-content:flex-end;
    gap:10px;
    flex-wrap:wrap;
    margin-bottom: 12px;
  }

  .toolbar-actions .btn{
    min-height:44px;
    border-radius:12px;
    padding:0 14px;
    white-space:nowrap;
    box-shadow: 0 10px 24px rgba(2,6,23,.07);
    display:inline-flex;
    align-items:center;
    justify-content:center;
  }

  .toolbar-selects{
    display:flex;
    gap:12px;
    align-items:center;
    flex-wrap:wrap;
    margin:0;
  }

  .toolbar-selects .form-select{
    height:44px;
    border-radius:12px;
    border:1px solid var(--spgi-border);
    box-shadow: 0 8px 20px rgba(2,6,23,.05);
    min-width:240px;
    font-weight:400;
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
    border-color: rgba(255,255,255,.12) !important;
    font-weight: 700;
    letter-spacing: .2px;
    color: #fff;
    text-align:center;
    vertical-align: middle;
    white-space: nowrap;
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
    margin: 0;
  }

  .col-acciones{
    min-width: 160px;
    width: 160px;
    white-space: nowrap;
  }

  .col-fecha{ width: 130px; }
  .col-estado{ width: 140px; }
  .col-facturacion{ width: 140px; }

  .td-cliente{
    min-width: 260px;
    white-space: normal;
    word-break: break-word;
    font-weight: 400;
  }

  .td-preview{
    min-width: 280px;
    max-width: 340px;
    width: 340px;
  }

  .preview-box{
    background: #f8fafc;
    border: 1px solid rgba(15,23,42,.08);
    border-radius: 12px;
    padding: 10px 12px;
    color: #334155;
    font-size: .84rem;
    line-height: 1.4;
    word-break: break-word;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    min-height: 64px;
    cursor: pointer;
    transition: all 0.2s ease;
  }

  .preview-box.expanded{
    -webkit-line-clamp: unset !important;
    max-height: none !important;
    display: block;
  }

  .preview-box:hover{
    background: #eef4ff;
    border-color: rgba(13,110,253,.20);
    box-shadow: 0 4px 12px rgba(13,110,253,.08);
  }

  .spgi-mobile-list{
    display:none;
  }

  .spgi-req-card{
    background: rgba(255,255,255,.95);
    border: 1px solid var(--spgi-border);
    border-radius: 18px;
    box-shadow: 0 12px 30px rgba(2,6,23,.08);
    padding: 14px;
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
    background:#fff;
    border:1px solid rgba(15,23,42,.07);
    border-radius:12px;
    padding:10px 12px;
  }

  .spgi-field-label{
    font-size:.78rem;
    font-weight:700;
    color:var(--spgi-muted);
    text-transform:uppercase;
    letter-spacing:.4px;
    display:block;
    margin-bottom:2px;
  }

  .spgi-field-value{
    color:var(--spgi-ink);
    font-weight:600;
    word-break:break-word;
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
    margin-top: 16px;
    display: flex;
    justify-content: center;
    overflow-x: auto;
    padding-bottom: 4px;
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
    .spgi-table-desktop{
      display:block;
    }
  }
</style>

<div class="spgi-bg">
  <div class="container">

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
        <div class="table-responsive spgi-table">
          <table class="table table-bordered align-middle mb-0">
            <thead>
              <tr>
                <th>Cliente</th>
                <th>Requerimiento</th>
                <th>Asignado a</th>
                <th class="col-fecha">Fecha</th>
                <th class="col-estado">Estado</th>
                @if($esAdmin || $esEncargado)
                  <th class="col-facturacion">Facturación</th>
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
                    {{ $req->asignado?->name ?? 'Sin asignar' }}
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
                              class="btn btn-info btn-sm"
                              data-bs-toggle="modal"
                              data-bs-target="#modalNovedades{{ $req->id }}"
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
                      class="btn btn-info btn-sm"
                      data-bs-toggle="modal"
                      data-bs-target="#modalNovedades{{ $req->id }}">
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

@foreach ($requerimientos as $req)
<div class="modal fade" id="modalNovedades{{ $req->id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header bg-info text-white">
        <h5 class="modal-title">
          Novedades de: {{ $req->clienteRelation->nombre ?? 'Cliente no asignado' }}
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <div class="row g-0">

          <div class="col-12 col-md-7 border-end spgi-historial-box">
            <h5 class="fw-bold mb-3">Historial</h5>

            <div id="historial-container-{{ $req->id }}">
              @forelse ($req->novedades->sortBy('created_at') as $item)
                <div class="mb-3 d-flex flex-column" id="novedad-wrapper-{{ $item->id }}">
                  <div class="d-flex justify-content-between align-items-center mb-1 gap-2">
                    <span class="fw-bold text-primary small">
                      @if($item->adjunto)
                        <i class="bi bi-paperclip me-1 text-success"></i>
                      @endif
                      {{ $item->user->name ?? 'Usuario' }}
                    </span>
                    <div class="dropdown">
                      <button class="btn btn-link btn-sm text-muted p-0" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-three-dots"></i>
                      </button>
                      <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                          <button class="dropdown-item small"
                                  type="button"
                                  onclick="habilitarEdicion({{ $item->id }})">
                            <i class="bi bi-pencil me-1"></i> Editar
                          </button>
                        </li>
                        <li>
                          <button class="dropdown-item text-danger small"
                                  type="button"
                                  onclick="eliminarNovedad({{ $item->id }}, {{ $req->id }})">
                            <i class="bi bi-trash me-1"></i> Eliminar
                          </button>
                        </li>
                      </ul>
                    </div>
                  </div>

                  <div class="p-3 rounded-3 shadow-sm bg-white" style="border: 1px solid var(--spgi-border);">
                    <p class="mb-1 small" id="novedad-texto-{{ $item->id }}" style="white-space: pre-wrap;">{{ $item->novedad }}</p>

                    <div id="novedad-edit-{{ $item->id }}" class="d-none">
                      <textarea class="form-control form-control-sm mb-2" id="novedad-area-{{ $item->id }}"></textarea>
                      <div class="d-flex gap-2 flex-wrap">
                        <button type="button" class="btn btn-primary btn-sm py-0 px-2" onclick="guardarEdicion({{ $item->id }})" style="font-size: 0.7rem;">Guardar</button>
                        <button type="button" class="btn btn-light btn-sm py-0 px-2" onclick="cancelarEdicion({{ $item->id }})" style="font-size: 0.7rem;">Cancelar</button>
                      </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-2 gap-2 flex-wrap">
                      <span class="text-muted" style="font-size: 0.7rem;">
                        {{ $item->created_at->format('d/m/Y H:i') }}
                      </span>
                      @if($item->adjunto)
                        <a href="{{ route('novedades.download', $item->id) }}"
                           class="btn btn-sm btn-outline-primary py-0 px-2"
                           style="font-size: 0.7rem;">
                          <i class="bi bi-download"></i> Descargar
                        </a>
                      @endif
                    </div>
                  </div>
                </div>
              @empty
                <p class="text-muted text-center py-4 no-novedades">Aún no hay novedades registradas.</p>
              @endforelse
            </div>
          </div>

          <div class="col-12 col-md-5 p-4">
            <h5 class="fw-bold mb-3">Nueva Novedad</h5>

            <form id="form-novedad-{{ $req->id }}" onsubmit="enviarNovedad(event, {{ $req->id }})" enctype="multipart/form-data">
              @csrf
              <input type="hidden" name="requerimiento_id" value="{{ $req->id }}">
              <input type="hidden" name="cliente_id" value="{{ $req->cliente_id }}">

              <div class="mb-3">
                <textarea name="novedad" class="form-control" rows="5" placeholder="Escribe aquí..." required></textarea>
              </div>

              <div class="mb-3">
                <label class="form-label small text-muted">Adjuntar archivo (opcional)</label>
                <input type="file" name="adjunto" class="form-control form-control-sm">
                <small class="text-muted">Máximo 30MB.</small>
              </div>

              <button type="submit" class="btn btn-primary w-100" id="btn-save-{{ $req->id }}">
                <i class="bi bi-send-fill me-1"></i> Guardar Novedad
              </button>
            </form>
          </div>

        </div>
      </div>

    </div>
  </div>
</div>
@endforeach

@push('scripts')
<script>
function enviarNovedad(event, reqId) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);
    const btn = document.getElementById('btn-save-' + reqId);

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Guardando...';

    fetch('{{ route("novedades.store") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const container = document.getElementById('historial-container-' + reqId);
            const noNovedades = container.querySelector('.no-novedades');
            if (noNovedades) noNovedades.remove();

            const html = `
                <div class="mb-3 d-flex flex-column" id="novedad-wrapper-${data.novedad.id}">
                    <div class="d-flex justify-content-between align-items-center mb-1 gap-2">
                        <span class="fw-bold text-primary small">${data.user_name}</span>
                        <div class="dropdown">
                            <button class="btn btn-link btn-sm text-muted p-0" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <button class="dropdown-item small" type="button" onclick="habilitarEdicion(${data.novedad.id})">
                                        <i class="bi bi-pencil me-1"></i> Editar
                                    </button>
                                </li>
                                <li>
                                    <button class="dropdown-item text-danger small" type="button" onclick="eliminarNovedad(${data.novedad.id}, ${reqId})">
                                        <i class="bi bi-trash me-1"></i> Eliminar
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="p-3 rounded-3 shadow-sm bg-white" style="border: 1px solid var(--spgi-border);">
                        <p class="mb-1 small" id="novedad-texto-${data.novedad.id}" style="white-space: pre-wrap;">${data.novedad.novedad}</p>
                        <div id="novedad-edit-${data.novedad.id}" class="d-none">
                            <textarea class="form-control form-control-sm mb-2" id="novedad-area-${data.novedad.id}"></textarea>
                            <div class="d-flex gap-2 flex-wrap">
                                <button type="button" class="btn btn-primary btn-sm py-0 px-2" onclick="guardarEdicion(${data.novedad.id})" style="font-size: 0.7rem;">Guardar</button>
                                <button type="button" class="btn btn-light btn-sm py-0 px-2" onclick="cancelarEdicion(${data.novedad.id})" style="font-size: 0.7rem;">Cancelar</button>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-2 gap-2 flex-wrap">
                            <span class="text-muted" style="font-size: 0.7rem;">${data.created_at}</span>
                            ${data.file_url ? `
                                <a href="${data.file_url}" target="_blank" download="${data.file_name}" class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size: 0.7rem;">
                                    <i class="bi bi-download"></i> Descargar
                                </a>
                            ` : ''}
                        </div>
                    </div>
                </div>`;

            container.insertAdjacentHTML('beforeend', html);
            form.reset();
            container.scrollTop = container.scrollHeight;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Ocurrió un error al guardar la novedad.');
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-send-fill me-1"></i> Guardar Novedad';
    });
}

function habilitarEdicion(id) {
    document.getElementById('novedad-texto-' + id).classList.add('d-none');
    const area = document.getElementById('novedad-area-' + id);
    area.value = document.getElementById('novedad-texto-' + id).innerText;
    document.getElementById('novedad-edit-' + id).classList.remove('d-none');
}

function cancelarEdicion(id) {
    document.getElementById('novedad-texto-' + id).classList.remove('d-none');
    document.getElementById('novedad-edit-' + id).classList.add('d-none');
}

function guardarEdicion(id) {
    const nuevaNovedad = document.getElementById('novedad-area-' + id).value;

    fetch(`/novedades/${id}`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ novedad: nuevaNovedad })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('novedad-texto-' + id).innerText = nuevaNovedad;
            cancelarEdicion(id);
        }
    })
    .catch(error => alert('Error al actualizar'));
}

function eliminarNovedad(id, reqId) {
    if (!confirm('¿Eliminar esta novedad?')) return;

    fetch(`/novedades/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('novedad-wrapper-' + id).remove();
            const container = document.getElementById('historial-container-' + reqId);
            if (container.children.length === 0) {
                container.innerHTML = '<p class="text-muted text-center py-4 no-novedades">Aún no hay novedades registradas.</p>';
            }
        }
    })
    .catch(error => alert('Error al eliminar'));
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.preview-box').forEach(box => {
        box.addEventListener('click', function() {
            this.classList.toggle('expanded');
        });
    });
});
</script>
@endpush

@endsection