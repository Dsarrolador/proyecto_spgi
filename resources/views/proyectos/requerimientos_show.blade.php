@extends('layouts.app')

@section('page_title', 'Detalle de Requerimiento')

@section('content')

<style>
  :root{
    --spgi-primary:#0d6efd;
    --spgi-primary-2:#2b7bff;
    --spgi-ink:#0f172a;
    --spgi-muted:#64748b;
    --spgi-border: rgba(15, 23, 42, .10);
    --shadow: 0 18px 45px rgba(2, 6, 23, .10);
  }

  .spgi-bg{ background: transparent !important; padding: 24px 0; }

  .btn-spgi{
    background: linear-gradient(135deg, var(--spgi-primary), var(--spgi-primary-2));
    border: 0; color: #fff !important; min-height:44px; border-radius:12px; padding:0 20px;
    font-weight:700; display:inline-flex; align-items:center; gap:8px;
  }
  .btn-spgi:hover{ filter: brightness(.98); transform: translateY(-1px); }

  .spgi-card{
    background: rgba(255,255,255,.92); border: 1px solid var(--spgi-border);
    border-radius: 20px; box-shadow: var(--shadow); backdrop-filter: blur(8px);
    overflow: hidden; margin-bottom: 24px;
  }

  .card-head{
    padding: 20px 24px; border-bottom: 1px solid var(--spgi-border);
    display: flex; justify-content: space-between; align-items: center;
  }

  .card-body-spgi{ padding: 24px; }

  .info-label{ font-size: .82rem; font-weight: 700; color: var(--spgi-muted); text-transform: uppercase; letter-spacing: .5px; margin-bottom: 6px; }
  .info-value{ font-size: 1.05rem; color: var(--spgi-ink); font-weight: 600; margin-bottom: 20px; }

  .description-box{
    background: #f8fafc; border: 1px solid rgba(15,23,42,.05); border-radius: 14px;
    padding: 20px; color: #334155; line-height: 1.6; font-size: 1rem;
  }

  .badge-status{
    padding: 6px 14px; border-radius: 999px; font-weight: 700; font-size: .85rem;
    background: rgba(13,110,253,.08); color: var(--spgi-primary); border: 1px solid rgba(13,110,253,.15);
  }

  .photo-container{
    border-radius: 16px; overflow: hidden; border: 1px solid var(--spgi-border);
    box-shadow: 0 10px 25px rgba(0,0,0,.05); margin-top: 10px;
  }
  .photo-container img{ width: 100%; height: auto; display: block; }
</style>

<div class="spgi-bg">
  <div class="container">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h2 class="fw-800 m-0" style="color:var(--spgi-ink)">Detalle de Requerimiento</h2>
        <p class="text-muted m-0">Proyecto: <b>{{ $r->proyecto->nombre }}</b></p>
      </div>
      <a href="{{ route('proyectos.show', $r->id_proyecto) }}" class="btn btn-outline-secondary rounded-pill px-4">
        <i class="bi bi-arrow-left"></i> Volver al listado
      </a>
    </div>

    <div class="row">
      <div class="col-lg-8">
        <div class="spgi-card">
          <div class="card-head">
            <h5 class="m-0 fw-bold"><i class="bi bi-card-text me-2"></i> Descripción</h5>
          </div>
          <div class="card-body-spgi">
            <div class="description-box mb-4">
              {{ $r->texto_imagen ?: ($r->descripcion ?: 'Sin descripción') }}
            </div>

            @if($r->foto)
              <div class="info-label">Archivo / Captura Adjunta:</div>
              <div class="photo-container">
                <img src="{{ asset('storage/' . $r->foto) }}" alt="Captura">
              </div>
            @endif
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="spgi-card">
          <div class="card-head">
            <h5 class="m-0 fw-bold"><i class="bi bi-info-circle me-2"></i> Información</h5>
          </div>
          <div class="card-body-spgi">
            <div class="info-label">Estado:</div>
            <div class="info-value">
              <span class="badge-status">{{ $r->estadoRequerimiento->nombre ?? $r->estado ?? 'Pendiente' }}</span>
            </div>

            <div class="info-label">Cliente:</div>
            <div class="info-value">{{ $r->cliente->nombre ?? 'N/A' }}</div>

            <div class="info-label">Contacto:</div>
            <div class="info-value">{{ $r->contacto->nombre ?? 'N/A' }}</div>

            <div class="info-label">Tipo de Soporte:</div>
            <div class="info-value">{{ $r->tipoSoporte->nombre ?? 'N/A' }}</div>

            <div class="info-label">Registrado por:</div>
            <div class="info-value">{{ $r->user->name ?? 'Sistema' }}</div>

            <div class="info-label">Fecha de Registro:</div>
            <div class="info-value">{{ optional($r->created_at)->format('d/m/Y H:i') }}</div>

            <hr class="my-4" style="opacity:.08">

            <div class="d-grid gap-2">
              <a href="{{ route('requerimientos_proyecto.edit', $r->id) }}" class="btn btn-warning text-white fw-bold py-2 rounded-3">
                <i class="bi bi-pencil-square me-1"></i> Editar Requerimiento
              </a>
              <form action="{{ route('requerimientos_proyecto.destroy', $r->id) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar este requerimiento?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger w-100 fw-bold py-2 rounded-3 mt-1">
                  <i class="bi bi-trash me-1"></i> Eliminar
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

@endsection
