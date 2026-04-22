@extends('layouts.app')

@section('page_title', 'Proyectos')

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
    padding: 24px; display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap;
  }

  .toolbar-selects{ display: flex; gap: 12px; flex-wrap: wrap; }
  .toolbar-selects .form-select{
    height:46px; border-radius:12px; border:1px solid var(--border-main);
    background-color: var(--bg-surface); color: var(--text-main); min-width:200px;
    box-shadow: none !important;
  }

  .spgi-table-box{
    background: var(--bg-surface-glass); border: 1px solid var(--border-main);
    border-radius: 20px; box-shadow: var(--shadow-main); overflow: hidden; backdrop-filter: blur(16px);
  }

  .spgi-table{ margin-bottom: 0; }
  .spgi-table thead th{
    background: #0b1220; color:#fff; border-color: rgba(255,255,255,.08) !important;
    font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; padding: 16px;
  }
  .spgi-table tbody td{ border-color: var(--border-main) !important; color: var(--text-main); padding: 16px; }
  .spgi-table tbody tr:hover{ background: rgba(var(--spgi-primary), 0.05); }

  .badge-status{ padding: 8px 12px; border-radius: 10px; font-weight: 800; font-size: 0.75rem; text-transform: uppercase; }

  /* MÓVIL STYLES */
  .spgi-project-card{
    background: var(--bg-surface-glass); border: 1px solid var(--border-main);
    border-radius: 20px; box-shadow: var(--shadow-main); padding: 16px; backdrop-filter: blur(16px);
    margin-bottom: 14px;
  }
  .spgi-project-head{
    display:flex; align-items:flex-start; justify-content:space-between; gap:12px; margin-bottom: 12px;
  }
  .spgi-project-title{
    margin:0; font-size:1.05rem; font-weight:800; color:var(--text-main); line-height:1.25;
  }
  .spgi-project-subtitle{
    color: var(--text-muted); font-size: 0.8rem; margin-top: 2px;
  }
  .spgi-badge-status{
    padding: 6px 12px; border-radius: 8px; font-weight: 800; font-size: 0.7rem; text-transform: uppercase;
    background: rgba(var(--text-main), 0.1); color: var(--text-main); display: inline-block; text-align: center;
  }
  .spgi-info-grid{
    display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px;
    background: rgba(var(--text-main), 0.02); border: 1px solid var(--border-main);
    border-radius: 14px; padding: 12px;
  }
  .spgi-field-label{
    display: block; font-size: .65rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; margin-bottom: 2px; letter-spacing: 0.5px;
  }
  .spgi-field-value{
    font-size: .85rem; font-weight: 600; color: var(--text-main);
  }
  .spgi-card-actions{
    display: flex; gap: 8px;
  }
  .spgi-card-actions .btn{
    flex: 1; padding: 8px; font-size: 0.8rem; border-radius: 10px; font-weight: 700;
  }

  @media (max-width: 767.98px){
    .spgi-toolbar{ flex-direction: column; align-items: stretch; }
    .toolbar-actions .btn, .toolbar-selects .form-select{ width: 100%; }
    .spgi-table-desktop{ display: none; }
    .spgi-mobile-list{ display: block; padding: 0 10px; }
  }

  @media (min-width: 768px){
    .spgi-mobile-list{ display: none !important; }
    .spgi-table-desktop{ display: block; }
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