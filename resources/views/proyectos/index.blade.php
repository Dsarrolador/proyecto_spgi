@extends('layouts.app')

@section('page_title', 'Proyectos')

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

  .spgi-toolbar .toolbar-actions{
    display:flex;
    justify-content:flex-end;
    gap:10px;
    flex-wrap:wrap;
    margin-bottom: 12px;
  }

  .spgi-toolbar .toolbar-actions .btn{
    min-height:44px;
    border-radius:12px;
    padding:0 14px;
    white-space:nowrap;
    box-shadow: 0 10px 24px rgba(2,6,23,.07);
    display: inline-flex;
    align-items: center;
    justify-content: center;
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
    color:#fff;
    border-color: rgba(255,255,255,.12) !important;
    font-weight: 700;
    letter-spacing: .2px;
    vertical-align: middle;
    white-space: nowrap;
    text-align: center;
  }

  .spgi-table tbody td{
    border-color: rgba(15,23,42,.08) !important;
    font-weight: 400;
    vertical-align: middle;
  }

  .col-fecha { width: 120px; }
  .col-estado{ width: 140px; }
  .col-acciones{ width: 180px; }

  .td-ellipsis{
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  .td-ellipsis .text-muted{
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
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

  .acciones{
    white-space: nowrap;
  }

  .spgi-mobile-list{
    display:none;
  }

  .spgi-project-card{
    background: rgba(255,255,255,.95);
    border: 1px solid var(--spgi-border);
    border-radius: 18px;
    box-shadow: 0 12px 30px rgba(2,6,23,.08);
    padding: 14px;
  }

  .spgi-project-card + .spgi-project-card{
    margin-top: 14px;
  }

  .spgi-project-head{
    display:flex;
    align-items:flex-start;
    justify-content:space-between;
    gap:12px;
    margin-bottom: 12px;
  }

  .spgi-project-title{
    margin:0;
    font-size:1rem;
    font-weight:800;
    color:var(--spgi-ink);
    line-height:1.25;
  }

  .spgi-project-subtitle{
    color:var(--spgi-muted);
    font-size:.88rem;
    margin-top:4px;
    line-height:1.25;
  }

  .spgi-badge-status{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    min-height:34px;
    padding:0 12px;
    border-radius:999px;
    font-size:.82rem;
    font-weight:700;
    white-space:nowrap;
    background:#e2e8f0;
    color:#0f172a;
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

  .spgi-card-actions{
    display:grid;
    grid-template-columns: repeat(3, 1fr);
    gap:8px;
  }

  .spgi-card-actions .btn{
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

    .spgi-toolbar .toolbar-actions{
      justify-content:stretch;
    }

    .spgi-toolbar .toolbar-actions .btn{
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

    .modal-dialog.modal-lg{
      max-width: calc(100% - 1.5rem);
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

    {{-- Toolbar --}}
    <div class="spgi-toolbar mb-3">

      <div class="toolbar-actions">
        <button type="button"
                class="btn btn-outline-secondary btn-outline-spgi"
                data-bs-toggle="modal"
                data-bs-target="#modalFiltrosAvanzadosProyectos">
          <i class="bi bi-sliders me-1"></i> Filtros avanzados
        </button>

        <a href="{{ route('proyectos.create') }}" class="btn btn-spgi">
          <i class="bi bi-plus-lg me-1"></i> Agregar
        </a>
      </div>

      <form action="{{ route('proyectos.index') }}" method="GET" class="toolbar-selects">
        <select name="estado" class="form-select" onchange="this.form.submit()">
          <option value="Todos">Todos</option>
          <option value="Activo" {{ request('estado') == 'Activo' ? 'selected' : '' }}>Activo</option>
          <option value="En progreso" {{ request('estado') == 'En progreso' ? 'selected' : '' }}>En progreso</option>
          <option value="Completado" {{ request('estado') == 'Completado' ? 'selected' : '' }}>Completado</option>
          <option value="Pausado" {{ request('estado') == 'Pausado' ? 'selected' : '' }}>Pausado</option>
          <option value="Cancelado" {{ request('estado') == 'Cancelado' ? 'selected' : '' }}>Cancelado</option>
        </select>

        <select name="cliente_id" class="form-select" onchange="this.form.submit()">
          <option value="">Todos los clientes</option>
          @foreach(($clientes ?? collect()) as $c)
            <option value="{{ $c->id }}" {{ (string)request('cliente_id') === (string)$c->id ? 'selected' : '' }}>
              {{ $c->nombre }}
            </option>
          @endforeach
        </select>

        <select name="encargado_id" class="form-select" onchange="this.form.submit()">
          <option value="">Todos los encargados</option>
          @foreach(($usuarios ?? collect()) as $u)
            <option value="{{ $u->id }}" {{ (string)request('encargado_id') === (string)$u->id ? 'selected' : '' }}>
              {{ $u->name ?? $u->nombre ?? $u->email }}
            </option>
          @endforeach
        </select>
      </form>
    </div>

    <div class="spgi-table-wrap">

      {{-- TABLA DESKTOP / TABLET --}}
      <div class="spgi-table-desktop spgi-table-box">
        <div class="table-responsive">
          <table class="table table-bordered align-middle mb-0 spgi-table">
            <thead class="text-center">
              <tr>
                <th>Proyecto</th>
                <th>Cliente</th>
                <th class="col-fecha">Inicio</th>
                <th class="col-estado">Estado</th>
                <th class="col-acciones">Acciones</th>
              </tr>
            </thead>

            <tbody>
              @forelse ($proyectos as $p)
                <tr>
                  <td class="td-ellipsis">
                    <div class="fw-semibold td-ellipsis">{{ $p->nombre }}</div>
                    <div class="text-muted small">{{ $p->tipo_proyecto }}</div>
                  </td>

                  <td class="td-ellipsis">
                    {{ optional($p->clienteRelation)->nombre ?? optional($p->cliente)->nombre ?? 'Sin cliente asignado' }}
                  </td>

                  <td class="text-center">
                    {{ optional($p->fecha_inicio)->format('d/m/Y') }}
                  </td>

                  <td class="text-center">
                    <span class="badge bg-secondary">{{ $p->estado ?? '—' }}</span>
                  </td>

                  <td class="text-center">
                    <div class="d-inline-flex gap-2 acciones">
                      <a href="{{ route('proyectos.show', $p->id) }}" class="btn btn-primary btn-sm" title="Ver proyecto">
                        <i class="bi bi-eye"></i>
                      </a>

                      <a href="{{ route('proyectos.edit', $p->id) }}" class="btn btn-warning btn-sm" title="Editar">
                        <i class="bi bi-pencil-square"></i>
                      </a>

                      <a href="{{ route('proyectos.requerimientos.index', $p->id) }}" class="btn btn-dark btn-sm" title="Requerimientos del proyecto">
                        <i class="bi bi-list-check"></i>
                      </a>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-center text-muted p-4">
                    No hay proyectos registrados.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      {{-- TARJETAS MÓVIL --}}
      <div class="spgi-mobile-list">
        @forelse ($proyectos as $p)
          <div class="spgi-project-card">
            <div class="spgi-project-head">
              <div>
                <h5 class="spgi-project-title">{{ $p->nombre }}</h5>
                <div class="spgi-project-subtitle">{{ $p->tipo_proyecto ?: 'Sin tipo de proyecto' }}</div>
              </div>

              <span class="spgi-badge-status">{{ $p->estado ?? '—' }}</span>
            </div>

            <div class="spgi-info-grid">
              <div class="spgi-field">
                <span class="spgi-field-label">Cliente</span>
                <div class="spgi-field-value">
                  {{ optional($p->clienteRelation)->nombre ?? optional($p->cliente)->nombre ?? 'Sin cliente asignado' }}
                </div>
              </div>

              <div class="spgi-field">
                <span class="spgi-field-label">Fecha de inicio</span>
                <div class="spgi-field-value">
                  {{ optional($p->fecha_inicio)->format('d/m/Y') ?: 'No definida' }}
                </div>
              </div>
            </div>

            <div class="spgi-card-actions">
              <a href="{{ route('proyectos.show', $p->id) }}" class="btn btn-primary btn-sm">
                <i class="bi bi-eye me-1"></i> Ver
              </a>

              <a href="{{ route('proyectos.edit', $p->id) }}" class="btn btn-warning btn-sm">
                <i class="bi bi-pencil-square me-1"></i> Editar
              </a>

              <a href="{{ route('proyectos.requerimientos.index', $p->id) }}" class="btn btn-dark btn-sm">
                <i class="bi bi-list-check me-1"></i> Requerimientos
              </a>
            </div>
          </div>
        @empty
          <div class="spgi-empty">
            No hay proyectos registrados.
          </div>
        @endforelse
      </div>

      <div class="mt-3">
        {{ $proyectos->links() }}
      </div>
    </div>

  </div>
</div>

<!-- MODAL FILTROS AVANZADOS -->
<div class="modal fade" id="modalFiltrosAvanzadosProyectos" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">

      <form method="GET" action="{{ route('proyectos.index') }}">
        <div class="modal-header bg-dark text-white">
          <h5 class="modal-title">Filtros avanzados</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <div class="row g-3">
            <div class="col-12 col-md-6">
              <label class="form-label">Estado</label>
              <select name="estado" class="form-select">
                <option value="Todos">Todos</option>
                <option value="Activo" {{ request('estado') == 'Activo' ? 'selected' : '' }}>Activo</option>
                <option value="En progreso" {{ request('estado') == 'En progreso' ? 'selected' : '' }}>En progreso</option>
                <option value="Completado" {{ request('estado') == 'Completado' ? 'selected' : '' }}>Completado</option>
                <option value="Pausado" {{ request('estado') == 'Pausado' ? 'selected' : '' }}>Pausado</option>
                <option value="Cancelado" {{ request('estado') == 'Cancelado' ? 'selected' : '' }}>Cancelado</option>
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
              <label class="form-label">Encargado</label>
              <select name="encargado_id" class="form-select">
                <option value="">Todos</option>
                @foreach(($usuarios ?? collect()) as $u)
                  <option value="{{ $u->id }}" {{ (string)request('encargado_id') === (string)$u->id ? 'selected' : '' }}>
                    {{ $u->name ?? $u->nombre ?? $u->email }}
                  </option>
                @endforeach
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
          <a href="{{ route('proyectos.index') }}" class="btn btn-light w-100 w-sm-auto">Limpiar</a>
          <button type="submit" class="btn btn-primary w-100 w-sm-auto">Buscar</button>
        </div>
      </form>

    </div>
  </div>
</div>

@endsection