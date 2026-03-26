@extends('layouts.app')

@section('page_title', 'Mantenimiento de Estados de Requerimientos')

@section('content')

<style>
  :root{
    --spgi-primary:#0d6efd;
    --spgi-primary-2:#2b7bff;
    --spgi-ink:#0f172a;
    --spgi-muted:#64748b;
    --spgi-border: rgba(15, 23, 42, .10);
    --spgi-radius: 16px;
    --spgi-shadow: 0 18px 45px rgba(2, 6, 23, .10);
  }

  body{
    background:
      radial-gradient(900px 400px at 20% 10%, rgba(13,110,253,.14), transparent 60%),
      radial-gradient(900px 450px at 85% 20%, rgba(168,85,247,.12), transparent 55%),
      radial-gradient(900px 450px at 70% 90%, rgba(34,197,94,.10), transparent 55%),
      linear-gradient(135deg, rgba(13,110,253,.10), rgba(168,85,247,.08) 45%, rgba(34,197,94,.08));
    background-attachment: fixed;
  }

  .spgi-page{ padding: 12px 0 24px 0; }
  .spgi-head{ display:flex; justify-content:space-between; align-items:flex-start; gap:12px; flex-wrap:wrap; margin-bottom: 14px; }
  .page-title{ font-size: 1.65rem; font-weight: 800; letter-spacing: .2px; margin: 0; color: var(--spgi-ink); }
  .page-sub{ color: var(--spgi-muted); font-size: .95rem; margin-top: 4px; }
  .spgi-head-actions{ display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
  
  .badge-spgi{
    border-radius: 999px; padding: 8px 12px; font-weight: 700; font-size: .82rem;
    border: 1px solid rgba(0,0,0,.08); background: rgba(255,255,255,.9); color: #495057; box-shadow: 0 10px 24px rgba(2,6,23,.07);
  }

  .btn-spgi{
    background: linear-gradient(135deg, var(--spgi-primary), var(--spgi-primary-2));
    border: 0; color: #fff !important; min-height:44px; border-radius:12px; padding:0 14px; white-space:nowrap;
    box-shadow: 0 10px 24px rgba(2,6,23,.07); font-weight:700;
  }
  .btn-spgi:hover{ filter: brightness(.98); transform: translateY(-1px); }

  .spgi-card{ border: 1px solid var(--spgi-border); border-radius: var(--spgi-radius); box-shadow: var(--spgi-shadow); background: rgba(255,255,255,.92); backdrop-filter: blur(6px); overflow: hidden; }
  .spgi-card .card-head{ padding: 14px 16px; border-bottom: 1px solid rgba(0,0,0,.06); display: flex; gap: 12px; align-items: center; justify-content: space-between; flex-wrap: wrap; }
  .spgi-card .card-body-spgi{ padding: 0; }

  .table-spgi{ margin: 0; }
  .table-spgi thead th{ font-size: .92rem; letter-spacing: .2px; background: #0b1220; color:#fff; border-bottom: 1px solid rgba(255,255,255,.08) !important; padding: 12px 14px; vertical-align: middle; }
  .table-spgi tbody td{ padding: 12px 14px; vertical-align: middle; border-color: rgba(15,23,42,.08) !important; }
  .table-spgi tbody tr:hover{ background: #fbfcff; }

  .acciones{ display: inline-flex; gap: 8px; align-items: center; justify-content: center; flex-wrap: wrap; }
  .acciones .btn{ height: 38px; padding: 0 12px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; gap: 6px; font-weight: 600; font-size: .88rem; white-space: nowrap; }

  .modal-content{ border-radius: 16px; border: 1px solid rgba(0,0,0,.08); box-shadow: 0 18px 40px rgba(0,0,0,.12); }
  .modal-header{ border-top-left-radius: 16px; border-top-right-radius: 16px; }
  .form-control{ border-radius: 12px; }

  @media (max-width: 767.98px){
    .spgi-head{ align-items:stretch; }
    .spgi-head-actions{ width:100%; justify-content:stretch; }
    .badge-spgi, .spgi-head-actions .btn{ width:100%; justify-content:center; }
  }
</style>

<div class="spgi-page">
  <div class="container">

    <div class="spgi-head">
      <div>
        <h3 class="page-title">Estados de Requerimientos</h3>
        <div class="page-sub">Administra los estados de flujo de trabajo.</div>
      </div>

      <div class="spgi-head-actions">
        <span class="badge-spgi">
          <i class="bi bi-tags me-1"></i>
          Total: {{ count($estados) }}
        </span>

        <button class="btn btn-spgi d-flex align-items-center" type="button" data-bs-toggle="modal" data-bs-target="#modalEstado">
          <i class="bi bi-plus-lg me-1"></i> Agregar Estado
        </button>
      </div>
    </div>

    @if(session('success'))
      <div class="alert alert-success text-center" id="alerta-exito">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
      </div>
    @endif

    @if(session('error'))
      <div class="alert alert-danger text-center">
        <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
      </div>
    @endif

    <div class="spgi-card">
      <div class="card-body-spgi">
        <div class="table-responsive">
          <table class="table table-spgi table-bordered align-middle">
            <thead>
              <tr>
                <th style="width: 10%;">ID</th>
                <th style="width: 40%;">Nombre</th>
                <th style="width: 30%;">Color (Badges)</th>
                <th class="text-center" style="width: 20%;">Acciones</th>
              </tr>
            </thead>
            <tbody>
            @foreach($estados as $e)
              <tr>
                <td class="fw-semibold text-center">{{ $e->id }}</td>
                <td class="fw-bold">{{ $e->nombre }}</td>
                 <td>
                    @if(Str::startsWith($e->color, '#'))
                        <span class="badge" style="background-color: {{ $e->color }}; color: #fff;">{{ $e->color }}</span>
                    @else
                        <span class="badge {{ $e->color }}">{{ $e->color ?? 'N/A' }}</span>
                    @endif
                 </td>
                <td class="text-center">
                  <div class="acciones">
                    <button class="btn btn-warning" type="button" data-bs-toggle="modal" data-bs-target="#editarEstado{{ $e->id }}" style="width: 36px; height: 36px; padding: 0;" title="Editar">
                      <i class="bi bi-pencil-square"></i>
                    </button>

                    <button class="btn btn-danger" type="button" data-bs-toggle="modal" data-bs-target="#confirmarEliminar{{ $e->id }}" style="width: 36px; height: 36px; padding: 0;" title="Eliminar">
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>
</div>

@foreach($estados as $e)
  <div class="modal fade" id="confirmarEliminar{{ $e->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-danger">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> Confirmar Eliminación
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body text-center">
          ¿Eliminar el estado <strong>{{ $e->nombre }}</strong>?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <form action="{{ route('mantenimiento.estados-requerimiento.destroy', $e->id) }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">Eliminar</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="editarEstado{{ $e->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-warning">
          <h5 class="modal-title"><i class="bi bi-pencil-square me-1"></i> Editar Estado</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form action="{{ route('mantenimiento.estados-requerimiento.update', $e->id) }}" method="POST">
          @csrf
          @method('PUT')
          <div class="modal-body">
            <div class="mb-3">
                <label class="form-label fw-semibold">Nombre</label>
                <input type="text" class="form-control" name="nombre" value="{{ $e->nombre }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Color</label>
                <div class="d-flex gap-2 align-items-center">
                    <input type="color" class="form-control form-control-color" value="{{ Str::startsWith($e->color, '#') ? $e->color : '#0d6efd' }}" onchange="document.getElementById('colorEdit{{ $e->id }}').value = this.value" style="width: 50px; height: 38px; padding: 4px;">
                    <input type="text" class="form-control" name="color" id="colorEdit{{ $e->id }}" value="{{ $e->color }}" placeholder="Ej: bg-success o #ff0000">
                </div>
                <small class="text-muted">Elige con el panel o escribe clase Bootstrap (ej: `bg-success`).</small>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-success">Guardar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endforeach

<div class="modal fade" id="modalEstado" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title"><i class="bi bi-plus-lg me-2"></i> Agregar Estado</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('mantenimiento.estados-requerimiento.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label fw-semibold">Nombre</label>
            <input type="text" class="form-control" name="nombre" required>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Color</label>
            <div class="d-flex gap-2 align-items-center">
                <input type="color" class="form-control form-control-color" value="#0d6efd" onchange="document.getElementById('colorCreate').value = this.value" style="width: 50px; height: 38px; padding: 4px;">
                <input type="text" class="form-control" name="color" id="colorCreate" placeholder="Ej: bg-primary o #0d6efd">
            </div>
            <small class="text-muted">Elige con el panel o escribe clase Bootstrap.</small>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>

@push('scripts')
<script>
  const alerta = document.getElementById('alerta-exito');
  if (alerta) {
    setTimeout(() => {
      alerta.style.transition = "opacity 0.4s";
      alerta.style.opacity = '0';
      setTimeout(() => alerta.remove(), 400);
    }, 3000);
  }
</script>
@endpush
@endsection
