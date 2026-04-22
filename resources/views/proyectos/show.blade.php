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
</style>
</style>

<div class="spgi-bg">
  <div class="container">

    {{-- TOPBAR --}}
    <div class="spgi-toolbar mb-3 d-flex justify-content-between align-items-center flex-wrap gap-3">
      <div>
        <h2 class="spgi-title">Requerimientos del Proyecto</h2>
        <div class="spgi-subtitle">
          Proyecto: <b>{{ $proyecto->nombre }}</b>
        </div>
      </div>

      <div class="toolbar-actions">
        <a href="{{ route('proyectos.index') }}" class="btn btn-soft">
          <i class="bi bi-arrow-left"></i> Volver
        </a>

        {{-- ✅ BOTÓN NUEVO: Agregar requerimiento --}}
        <a href="{{ route('proyectos.requerimientos.create', $proyecto->id) }}" class="btn btn-spgi">
          <i class="bi bi-plus-lg"></i> Agregar requerimiento
        </a>
      </div>
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
                    <span class="badge-soft">{{ $r->estado ?? 'Pendiente' }}</span>
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

@endsection