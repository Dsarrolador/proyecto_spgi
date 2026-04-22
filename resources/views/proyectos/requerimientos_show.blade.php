@extends('layouts.app')

@section('page_title', 'Detalle de Requerimiento')

@section('content')

<style>
  .spgi-bg{ background: transparent !important; padding: 24px 0; }

  .btn-spgi{
    background: linear-gradient(135deg, var(--spgi-primary), #2563eb);
    border: 0; color: #fff !important; min-height:46px; border-radius:14px; padding:0 24px;
    font-weight:700; display:inline-flex; align-items:center; gap:8px;
    box-shadow: 0 10px 25px var(--spgi-primary-glow);
  }
  .btn-spgi:hover{ filter: brightness(1.1); transform: translateY(-1px); }

  .spgi-card{
    background: var(--bg-surface-glass); border: 1px solid var(--border-main);
    border-radius: 20px; box-shadow: var(--shadow-main); backdrop-filter: blur(16px);
    overflow: hidden; margin-bottom: 24px;
  }

  .card-head{
    padding: 20px 24px; border-bottom: 1px solid var(--border-main);
    display: flex; justify-content: space-between; align-items: center;
  }

  .card-body-spgi{ padding: 24px; }

  .info-label{ font-size: .75rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 6px; }
  .info-value{ font-size: 1.1rem; color: var(--text-main); font-weight: 600; margin-bottom: 24px; }

  .description-box{
    background: rgba(var(--text-main), 0.02); border: 1px solid var(--border-main);
    border-radius: 16px; padding: 24px; color: var(--text-main); line-height: 1.7; font-size: 1.05rem;
  }

  .badge-status{
    padding: 8px 16px; border-radius: 999px; font-weight: 700; font-size: .85rem;
    background: rgba(var(--spgi-primary), 0.1); color: var(--spgi-primary); border: 1px solid var(--border-main);
  }

  .photo-container{
    border-radius: 20px; overflow: hidden; border: 1px solid var(--border-main);
    box-shadow: var(--shadow-main); margin-top: 10px;
  }
  .photo-container img{ width: 100%; height: auto; display: block; transition: transform 0.3s ease; }
  .photo-container img:hover{ transform: scale(1.02); }
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
                <img src="{{ route('storage.proxy', ['path' => $r->foto]) }}" alt="Captura">
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
