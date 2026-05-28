@extends('layouts.app')

@section('page_title', 'Requerimientos del Proyecto')

@section('content')

<style>
  .spgi-bg{ background: transparent !important; padding: 24px 0; }

  .btn-spgi{
    background: linear-gradient(135deg, var(--spgi-primary), #2563eb);
    border: 0; color: #fff !important; min-height:46px; border-radius:14px; padding:0 20px;
    box-shadow: 0 10px 25px var(--spgi-primary-glow); font-weight:700;
  }
  .btn-spgi:hover{ filter: brightness(1.1); transform: translateY(-1px); }

  .btn-soft{
    background: var(--bg-surface); color: var(--text-main);
    border: 1px solid var(--border-main); border-radius: 12px;
  }
  .btn-soft:hover{ background: rgba(var(--spgi-primary), 0.05); transform: translateY(-1px); }

  .spgi-toolbar{
    background: var(--bg-surface-glass); border: 1px solid var(--border-main);
    border-radius: 20px; box-shadow: var(--shadow-main); backdrop-filter: blur(16px); padding: 20px;
  }

  .spgi-title{ font-weight: 800; font-size: 1.4rem; color: var(--text-main); letter-spacing: -.5px; margin:0; }
  .spgi-subtitle{ color: var(--text-muted); font-size: .95rem; margin-top: 4px; }

  .spgi-card{
    background: var(--bg-surface-glass); border: 1px solid var(--border-main);
    border-radius: 20px; box-shadow: var(--shadow-main); backdrop-filter: blur(16px); overflow: hidden;
  }
  .spgi-card-header{
    padding: 16px 20px; border-bottom: 1px solid var(--border-main);
    display:flex; justify-content:space-between; align-items:center; gap:12px;
  }

  .spgi-table{ width:100%; border-collapse: separate; border-spacing: 0; }
  .spgi-table thead th{
    background:#0b1220; color:#fff; font-weight:700; font-size: 0.75rem;
    text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid rgba(255,255,255,.08);
    padding: 12px 14px;
  }
  .spgi-table tbody td{
    padding: 14px; border-bottom: 1px solid var(--border-main); color: var(--text-main);
    vertical-align: middle;
  }

  .badge-soft{
    background: rgba(var(--text-main), 0.05); color: var(--text-main);
    border: 1px solid var(--border-main); font-weight: 700; border-radius: 8px;
    padding: 6px 12px; font-size: .8rem;
  }

  .toolbar-selects {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    background: var(--bg-surface-glass);
    padding: 16px;
    border-radius: 16px;
    border: 1px solid var(--border-main);
    box-shadow: var(--shadow-main);
    backdrop-filter: blur(12px);
    margin-top: 15px;
    width: 100%;
  }

  .toolbar-selects .form-select {
    flex: 1;
    min-width: 180px;
    background-color: var(--bg-surface);
    border: 1px solid rgba(255,255,255,0.1);
    color: var(--text-main);
    border-radius: 12px;
    height: 46px;
    font-size: 0.9rem;
    font-weight: 500;
  }
</style>

<div class="spgi-bg">
  <div class="container">

    {{-- TOPBAR --}}
    <div class="spgi-toolbar mb-3 d-flex flex-column gap-3">
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 w-100">
        <div>
          <h2 class="spgi-title">Requerimientos del Proyecto</h2>
          <div class="spgi-subtitle">
            Proyecto: <b>{{ $proyecto->nombre }}</b>
          </div>
        </div>

        <div class="toolbar-actions d-flex gap-2">
          <a href="{{ route('proyectos.index') }}" class="btn btn-soft d-flex align-items-center gap-2">
            <i class="bi bi-arrow-left"></i> Volver
          </a>

          <button type="button"
                  class="btn btn-soft d-flex align-items-center gap-2"
                  data-bs-toggle="modal"
                  data-bs-target="#modalFiltrosAvanzadosReqProyecto">
            <i class="bi bi-sliders"></i> Filtros avanzados
          </button>

          {{-- ✅ BOTÓN NUEVO: Agregar requerimiento --}}
          <a href="{{ route('proyectos.requerimientos.create', $proyecto->id) }}" class="btn btn-spgi d-flex align-items-center gap-2">
            <i class="bi bi-plus-lg"></i> Agregar requerimiento
          </a>
        </div>
      </div>

      {{-- FILTROS RÁPIDOS --}}
      <form action="{{ route('proyectos.show', $proyecto->id) }}" method="GET" class="toolbar-selects">
        
        <select name="estado" class="form-select" onchange="this.form.submit()">
          <option value="">Todos (sin completados)</option>
          @foreach($estados as $e)
            <option value="{{ $e->id }}" {{ (string)request('estado') === (string)$e->id ? 'selected' : '' }}>{{ $e->nombre }}</option>
          @endforeach
          <option value="Todos" {{ request('estado') == 'Todos' ? 'selected' : '' }}>Mostrar Todos</option>
          <option value="Eliminados" {{ request('estado') == 'Eliminados' ? 'selected' : '' }} style="color: #dc3545; font-weight: bold;">🗑️ Eliminados</option>
        </select>

        <select name="prioridad" class="form-select" onchange="this.form.submit()">
          <option value="">Cualquier Prioridad</option>
          <option value="5" {{ request('prioridad') == '5' ? 'selected' : '' }} style="color: #dc3545; font-weight: bold;">5 - Muy Urgente</option>
          <option value="4" {{ request('prioridad') == '4' ? 'selected' : '' }} style="color: #ffc107; font-weight: bold;">4 - Urgente</option>
          <option value="3" {{ request('prioridad') == '3' ? 'selected' : '' }}>3 - Media</option>
          <option value="2" {{ request('prioridad') == '2' ? 'selected' : '' }}>2 - Baja</option>
          <option value="1" {{ request('prioridad') == '1' ? 'selected' : '' }}>1 - Muy Baja</option>
        </select>

        <select name="asignado_id" class="form-select" onchange="this.form.submit()">
          <option value="mios" {{ request('asignado_id', 'mios') === 'mios' ? 'selected' : '' }}>Mis requerimientos</option>
          <option value="todos" {{ request('asignado_id') === 'todos' ? 'selected' : '' }}>Todos</option>
          @foreach(($usuarios ?? collect()) as $u)
            <option value="{{ $u->id }}" {{ (string)request('asignado_id') === (string)$u->id ? 'selected' : '' }}>
              {{ $u->name ?? $u->nombre ?? $u->email }}
            </option>
          @endforeach
        </select>

      </form>

    </div>

    {{-- CARD LISTADO --}}
    <div class="spgi-card">
      <div class="spgi-card-header">
        <div class="fw-bold" style="color:var(--spgi-ink)">Listado</div>
        <div class="small text-muted">
          {{ method_exists($requerimientos, 'total') ? $requerimientos->total() : 0 }} registro(s)
        </div>
      </div>

      <div class="spgi-card-body">

        @if(session('success'))
          <div class="alert alert-success rounded-4">{{ session('success') }}</div>
        @endif

        @if($requerimientos->count() === 0)
          <div class="empty">
            Este proyecto no tiene requerimientos registrados.
          </div>
        @else

          <table class="spgi-table">
            <thead>
              <tr>
                <th>Descripción</th>
                <th class="col-fecha text-center">Fecha</th>
                <th class="col-estado text-center">Estado</th>
                <th class="col-acciones text-center">Acciones</th>
              </tr>
            </thead>

            <tbody>
              @foreach($requerimientos as $r)
                <tr>
                  <td class="td-ellipsis">
                    {{ $r->texto_imagen ?? $r->descripcion ?? '—' }}
                  </td>

                  <td class="text-center">
                    {{ optional($r->created_at)->format('d/m/Y') }}
                  </td>

                  <td class="text-center">
                    @php
                      $_color = $r->estado_id == 6 ? 'bg-danger' : (optional($r->estadoRequerimiento)->color ?? 'bg-secondary');
                      $_nombre = $r->estado_id == 6 ? 'Eliminado' : (optional($r->estadoRequerimiento)->nombre ?? 'Pendiente');
                    @endphp
                    @if(\Illuminate\Support\Str::startsWith($_color, '#'))
                      <span class="badge spgi-badge" style="background-color: {{ $_color }}; color: #fff;">{{ $_nombre }}</span>
                    @else
                      <span class="badge {{ $_color }} spgi-badge">{{ $_nombre }}</span>
                    @endif
                  </td>

                  <td class="text-center">
                    <div class="d-inline-flex gap-2 acciones">
                      {{-- Ver --}}
                      <a href="{{ route('requerimientos_proyecto.show', $r->id) }}"
                         class="btn btn-primary btn-sm" title="Ver">
                        <i class="bi bi-eye"></i>
                      </a>

                      {{-- Editar --}}
                      <a href="{{ route('requerimientos_proyecto.edit', $r->id) }}"
                         class="btn btn-warning btn-sm" title="Editar">
                        <i class="bi bi-pencil-square"></i>
                      </a>

                      {{-- Eliminar --}}
                      <form method="POST"
                            action="{{ route('requerimientos_proyecto.destroy', $r->id) }}"
                            onsubmit="return confirm('¿Eliminar este requerimiento?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm" title="Eliminar">
                          <i class="bi bi-trash"></i>
                        </button>
                      </form>
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>

          <div class="mt-3">
            {{ $requerimientos->links() }}
          </div>

        @endif
      </div>
    </div>

  </div>
</div>

<div class="modal fade" id="modalFiltrosAvanzadosReqProyecto" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form method="GET" action="{{ route('proyectos.show', $proyecto->id) }}">
        <div class="modal-header bg-dark text-white border-0" style="border-radius: 18px 18px 0 0;">
          <h5 class="modal-title"><i class="bi bi-sliders me-2"></i>Filtros avanzados</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body p-4 bg-light">
          <div class="row g-3">
            <div class="col-12 col-md-6">
              <label class="form-label fw-bold text-muted small">Estado</label>
              <select name="estado" class="form-select shadow-sm border-0">
                <option value="">Todos</option>
                @foreach($estados as $e)
                  <option value="{{ $e->id }}" {{ (string)request('estado') === (string)$e->id ? 'selected' : '' }}>{{ $e->nombre }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label fw-bold text-muted small">Prioridad</label>
              <select name="prioridad" class="form-select shadow-sm border-0">
                <option value="">Cualquier Prioridad</option>
                <option value="5" {{ request('prioridad') == '5' ? 'selected' : '' }} class="text-danger fw-bold">5 - Muy Urgente</option>
                <option value="4" {{ request('prioridad') == '4' ? 'selected' : '' }} class="text-warning fw-bold">4 - Urgente</option>
                <option value="3" {{ request('prioridad') == '3' ? 'selected' : '' }}>3 - Media</option>
                <option value="2" {{ request('prioridad') == '2' ? 'selected' : '' }}>2 - Baja</option>
                <option value="1" {{ request('prioridad') == '1' ? 'selected' : '' }}>1 - Muy Baja</option>
              </select>
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label fw-bold text-muted small">Asignado a</label>
              <select name="asignado_id" class="form-select shadow-sm border-0">
                <option value="">Cualquiera</option>
                <option value="mios" {{ request('asignado_id', 'mios') === 'mios' ? 'selected' : '' }}>Mis requerimientos</option>
                <option value="todos" {{ request('asignado_id') === 'todos' ? 'selected' : '' }}>Todos</option>
                @foreach(($usuarios ?? collect()) as $u)
                  <option value="{{ $u->id }}" {{ (string)request('asignado_id') === (string)$u->id ? 'selected' : '' }}>
                    {{ $u->name }}
                  </option>
                @endforeach
              </select>
            </div>

            @if(auth()->user()->es_administrativo)
            <div class="col-12 col-md-6">
              <label class="form-label fw-bold text-muted small">Facturación</label>
              <select name="facturado" class="form-select shadow-sm border-0">
                <option value="">Todos</option>
                <option value="1" {{ request('facturado') === '1' ? 'selected' : '' }}>Facturados</option>
                <option value="0" {{ request('facturado') === '0' ? 'selected' : '' }}>No facturados</option>
              </select>
            </div>
            @endif

            <div class="col-12 col-md-6">
              <label class="form-label fw-bold text-muted small">Desde</label>
              <input type="date" name="desde" class="form-control shadow-sm border-0" value="{{ request('desde') }}">
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label fw-bold text-muted small">Hasta</label>
              <input type="date" name="hasta" class="form-control shadow-sm border-0" value="{{ request('hasta') }}">
            </div>
          </div>
        </div>

        <div class="modal-footer bg-white border-0 flex-column flex-sm-row rounded-bottom-4">
          <a href="{{ route('proyectos.show', $proyecto->id) }}" class="btn btn-light w-100 w-sm-auto rounded-pill px-4">Limpiar</a>
          <button type="submit" class="btn btn-primary w-100 w-sm-auto rounded-pill px-4 shadow-sm">Aplicar Filtros</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection