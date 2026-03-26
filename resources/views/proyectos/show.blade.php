@extends('layouts.app')

@section('page_title', 'Requerimientos del Proyecto')

@section('content')

<style>
  :root{
    --spgi-primary:#0d6efd;
    --spgi-primary-2:#2b7bff;
    --spgi-ink:#0f172a;
    --spgi-muted:#64748b;
    --spgi-border: rgba(15, 23, 42, .10);
    --shadow: 0 18px 45px rgba(2, 6, 23, .10);
    --shadowSoft: 0 10px 24px rgba(2, 6, 23, .07);
  }

  body{
    background:
      radial-gradient(900px 400px at 20% 10%, rgba(13,110,253,.14), transparent 60%),
      radial-gradient(900px 450px at 85% 20%, rgba(168,85,247,.12), transparent 55%),
      radial-gradient(900px 450px at 70% 90%, rgba(34,197,94,.10), transparent 55%),
      linear-gradient(135deg, rgba(13,110,253,.10), rgba(168,85,247,.08) 45%, rgba(34,197,94,.08));
    background-attachment: fixed;
  }

  .spgi-bg{ background: transparent !important; padding: 24px 0; }

  .btn-spgi{
    background: linear-gradient(135deg, var(--spgi-primary), var(--spgi-primary-2));
    border: 0;
    color: #fff !important;
  }
  .btn-spgi:hover{ filter: brightness(.98); transform: translateY(-1px); }

  .btn-soft{
    background: #eef2ff;
    color: #1e40af;
    border: 1px solid rgba(30,64,175,.12);
  }
  .btn-soft:hover{ background:#e0e7ff; transform: translateY(-1px); }

  .spgi-toolbar{
    background: rgba(255,255,255,.92);
    border: 1px solid var(--spgi-border);
    border-radius: 18px;
    box-shadow: var(--shadow);
    backdrop-filter: blur(6px);
    padding: 16px;
  }

  .spgi-title{
    font-weight: 800;
    font-size: 1.2rem;
    color: var(--spgi-ink);
    margin:0;
    line-height: 1.15;
  }
  .spgi-subtitle{
    color: var(--spgi-muted);
    font-size: .9rem;
    margin-top: 4px;
  }

  .toolbar-actions{
    display:flex;
    justify-content:flex-end;
    gap:10px;
    flex-wrap:wrap;
  }

  .toolbar-actions .btn{
    height:44px;
    border-radius:12px;
    padding:0 14px;
    white-space:nowrap;
    box-shadow: var(--shadowSoft);
    display:inline-flex;
    align-items:center;
    gap:.5rem;
    font-weight:700;
  }

  .spgi-card{
    margin-top: 14px;
    background: rgba(255,255,255,.92);
    border: 1px solid var(--spgi-border);
    border-radius: 18px;
    box-shadow: var(--shadow);
    backdrop-filter: blur(6px);
    overflow: hidden;
  }

  .spgi-card-header{
    padding: 14px 16px;
    border-bottom: 1px solid var(--spgi-border);
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:12px;
  }

  .spgi-card-body{ padding: 16px; }

  .spgi-table{
    width:100%;
    border-collapse: separate;
    border-spacing: 0;
    table-layout: fixed;
  }
  .spgi-table thead{
    background:#0b1220;
  }
  .spgi-table thead th{
    color:#fff;
    font-weight:700;
    border-bottom: 1px solid rgba(255,255,255,.12);
    padding: 12px 10px;
    white-space: nowrap;
  }
  .spgi-table tbody td{
    padding: 12px 10px;
    border-bottom: 1px solid rgba(15,23,42,.08);
    vertical-align: middle;
  }

  .td-ellipsis{
    overflow:hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  .badge-soft{
    background: rgba(15,23,42,.06);
    color: #334155;
    border: 1px solid rgba(15,23,42,.10);
    font-weight: 700;
    border-radius: 999px;
    padding: .35rem .6rem;
    font-size: .78rem;
    display:inline-flex;
    align-items:center;
    gap:.35rem;
  }

  .acciones .btn{
    width: 38px;
    height: 38px;
    border-radius: 10px;
    padding: 0;
    display:inline-flex;
    align-items:center;
    justify-content:center;
  }

  .col-fecha{ width: 130px; }
  .col-estado{ width: 140px; }
  .col-acciones{ width: 160px; }

  .empty{
    background: #fff;
    border: 1px solid var(--spgi-border);
    border-radius: 14px;
    padding: 16px;
    box-shadow: 0 10px 24px rgba(2,6,23,.06);
    color: var(--spgi-muted);
  }
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
                      <a href="{{ route('proyectos.requerimientos.show', [$proyecto->id, $r->id]) }}"
                         class="btn btn-primary btn-sm" title="Ver">
                        <i class="bi bi-eye"></i>
                      </a>

                      {{-- Editar --}}
                      <a href="{{ route('proyectos.requerimientos.edit', [$proyecto->id, $r->id]) }}"
                         class="btn btn-warning btn-sm" title="Editar">
                        <i class="bi bi-pencil-square"></i>
                      </a>

                      {{-- Eliminar --}}
                      <form method="POST"
                            action="{{ route('proyectos.requerimientos.destroy', [$proyecto->id, $r->id]) }}"
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