@extends('layouts.app')

@section('page_title', 'Requerimientos Administrativos')

@section('content')
<style>
  .admin-bg { padding: 12px 0 24px 0; }
  .admin-toolbar {
    background: var(--bg-surface-glass); border: 1px solid var(--border-main);
    border-radius: 20px; box-shadow: var(--shadow-main); backdrop-filter: blur(16px); padding: 20px;
  }
  .admin-title { font-weight: 800; color: var(--text-main); letter-spacing: -.5px; margin: 0; font-size: 1.6rem; }
  
  .btn-admin {
    background: linear-gradient(135deg, var(--spgi-primary), #2563eb);
    border: 0; color: #fff !important; height:46px; border-radius:14px; padding:0 24px;
    box-shadow: 0 10px 25px var(--spgi-primary-glow); font-weight: 700;
  }
  .btn-admin:hover { filter: brightness(1.1); transform: translateY(-1px); }

  .btn-outline-admin {
    height:46px; border-radius:12px; padding:0 20px;
    border: 1px solid var(--border-main); background: var(--bg-surface); color: var(--text-main);
  }

  .admin-table-box {
    background: var(--bg-surface-glass); border: 1px solid var(--border-main);
    border-radius: 20px; box-shadow: var(--shadow-main); overflow: hidden; backdrop-filter: blur(16px);
  }
  
  .table-admin { margin-bottom: 0; background: transparent; }
  .table-admin thead th {
    background: #0b1220; color: #fff; border-color: rgba(255,255,255,0.08) !important;
    font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; padding: 14px;
  }
  .table-admin tbody td { border-color: var(--border-main) !important; color: var(--text-main); padding: 14px; }

  .badge-prioridad {
    padding: 6px 12px; border-radius: 8px; font-weight: 700; font-size: 0.75rem; text-transform: uppercase;
  }
  .prio-alta { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
  .prio-media { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
  .prio-baja { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }

  .badge-estado {
    padding: 6px 12px; border-radius: 20px; font-weight: 800; font-size: 0.75rem; text-transform: uppercase;
  }
  .est-pendiente { background: rgba(245, 158, 11, 0.15); color: #f59e0b; }
  .est-proceso { background: rgba(59, 130, 246, 0.15); color: #3b82f6; }
  .est-completado { background: rgba(16, 185, 129, 0.15); color: #10b981; }
  .est-cancelado { background: rgba(107, 114, 128, 0.15); color: #6b7280; }
</style>

<div class="admin-bg">
  <div class="container">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold text-gradient">Requerimientos Administrativos</h1>
            <p class="text-muted mb-0">Gestión de tareas y requerimientos internos de administración.</p>
        </div>
        <a href="{{ route('administracion.bienvenido') }}" class="btn btn-outline-secondary rounded-pill">
            <i class="bi bi-arrow-left me-2"></i> Volver al Panel
        </a>
    </div>

    <div class="admin-toolbar mb-4">
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <form action="{{ route('requerimientos-administrativos.index') }}" method="GET" class="d-flex gap-2 flex-wrap flex-grow-1">
          <input type="text" name="search" class="form-control bg-transparent text-white" placeholder="Buscar título o detalle..." value="{{ request('search') }}" style="max-width: 250px; border-color: var(--border-main);">
          
          <select name="estado" class="form-select bg-transparent text-white" onchange="this.form.submit()" style="max-width: 180px; border-color: var(--border-main);">
            <option value="" style="background:#1e293b;">Todos los estados</option>
            <option value="Pendiente" {{ request('estado') == 'Pendiente' ? 'selected' : '' }} style="background:#1e293b;">Pendiente</option>
            <option value="En Proceso" {{ request('estado') == 'En Proceso' ? 'selected' : '' }} style="background:#1e293b;">En Proceso</option>
            <option value="Completado" {{ request('estado') == 'Completado' ? 'selected' : '' }} style="background:#1e293b;">Completado</option>
            <option value="Cancelado" {{ request('estado') == 'Cancelado' ? 'selected' : '' }} style="background:#1e293b;">Cancelado</option>
          </select>

          <select name="prioridad" class="form-select bg-transparent text-white" onchange="this.form.submit()" style="max-width: 180px; border-color: var(--border-main);">
            <option value="" style="background:#1e293b;">Todas las prioridades</option>
            <option value="Alta" {{ request('prioridad') == 'Alta' ? 'selected' : '' }} style="background:#1e293b;">Alta</option>
            <option value="Media" {{ request('prioridad') == 'Media' ? 'selected' : '' }} style="background:#1e293b;">Media</option>
            <option value="Baja" {{ request('prioridad') == 'Baja' ? 'selected' : '' }} style="background:#1e293b;">Baja</option>
          </select>
          
          <button type="submit" class="btn btn-outline-admin"><i class="bi bi-search"></i></button>
        </form>

        <a href="{{ route('requerimientos-administrativos.create') }}" class="btn btn-admin d-flex align-items-center gap-2">
          <i class="bi bi-plus-lg"></i> Nuevo Requerimiento
        </a>
      </div>
    </div>

    <div class="admin-table-box">
      <div class="table-responsive">
        <table class="table table-admin align-middle text-center">
          <thead>
            <tr>
              <th class="text-start ps-4">Título</th>
              <th>Prioridad</th>
              <th>Estado</th>
              <th>Creado por</th>
              <th>Asignado a</th>
              <th>Fecha Límite</th>
              <th style="width: 140px;">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @forelse($requerimientos as $req)
              <tr>
                <td class="text-start ps-4 fw-bold">
                  <div class="d-flex align-items-center flex-wrap gap-2">
                    <span>{{ $req->titulo }}</span>
                    @if($req->es_recurrente)
                      <span class="badge rounded-pill px-2 py-1" style="font-size: 0.7rem; background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3);" title="Recurrente">
                        <i class="bi bi-arrow-repeat me-1"></i>{{ $req->frecuencia }}
                      </span>
                    @endif
                  </div>
                  <small class="text-muted text-truncate d-inline-block mb-0 mt-1" style="max-width: 300px;">{{ $req->descripcion ?? 'Sin descripción' }}</small>
                  @if($req->es_recurrente && $req->proxima_fecha_ejecucion)
                    <div class="text-muted small mt-1" style="font-size: 0.75rem; font-weight: normal;">
                      <i class="bi bi-calendar2-event text-success me-1"></i>Próx. ejecución: <span class="text-white">{{ $req->proxima_fecha_ejecucion->format('d/m/Y') }}</span>
                    </div>
                  @endif
                </td>
                <td>
                  @php
                    $prioClass = 'prio-' . strtolower(str_replace('é', 'e', $req->prioridad));
                  @endphp
                  <span class="badge-prioridad {{ $prioClass }}">{{ $req->prioridad }}</span>
                </td>
                <td>
                  @php
                    $estClass = 'est-' . strtolower(str_replace(' ', '', $req->estado));
                  @endphp
                  <span class="badge-estado {{ $estClass }}">{{ $req->estado }}</span>
                </td>
                <td>{{ $req->user->name ?? '---' }}</td>
                <td>
                  @if($req->asignado)
                    <div class="d-flex align-items-center justify-content-center gap-1">
                      <div class="bg-primary text-white rounded-circle d-grid place-items-center" style="width: 24px; height: 24px; font-size: 0.75rem; font-weight: 800;">
                        {{ strtoupper(substr($req->asignado->name, 0, 1)) }}
                      </div>
                      <span>{{ $req->asignado->name }}</span>
                    </div>
                  @else
                    <span class="text-muted small">Sin asignar</span>
                  @endif
                </td>
                <td>
                  @if($req->fecha_limite)
                    <span class="{{ $req->fecha_limite->isPast() && $req->estado != 'Completado' ? 'text-danger fw-bold' : '' }}">
                      {{ $req->fecha_limite->format('d/m/Y') }}
                    </span>
                  @else
                    <span class="text-muted">---</span>
                  @endif
                </td>
                <td>
                  <div class="d-flex justify-content-center gap-2">
                    <a href="{{ route('requerimientos-administrativos.edit', $req->id) }}" class="btn btn-sm btn-warning" title="Editar">
                      <i class="bi bi-pencil-square"></i>
                    </a>
                    <form action="{{ route('requerimientos-administrativos.destroy', $req->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Seguro que deseas eliminar este requerimiento administrativo?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                        <i class="bi bi-trash"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="py-5 text-muted">
                  <i class="bi bi-journal-x fs-2 d-block mb-2"></i> No hay requerimientos registrados.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <div class="mt-4">
      {{ $requerimientos->withQueryString()->links() }}
    </div>

  </div>
</div>
@endsection
