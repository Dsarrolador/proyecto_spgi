@extends('layouts.app')

@section('page_title', 'Reporte de Visita')

@section('content')
<style>
  .spgi-bg { padding: 24px 0; }
  
  .spgi-card {
    background: var(--bg-surface-glass);
    border: 1px solid var(--border-main);
    border-radius: 24px;
    padding: 32px;
    box-shadow: var(--shadow-main);
    backdrop-filter: blur(20px);
    margin-bottom: 24px;
  }

  .status-show-box {
    text-align: center;
    padding: 24px;
    border-radius: 20px;
    border: 1px solid var(--border-main);
    background: rgba(255,255,255,0.02);
  }

  .status-badge-lg {
    padding: 8px 24px;
    border-radius: 999px;
    font-weight: 900;
    font-size: 1rem;
    text-transform: uppercase;
    display: inline-block;
  }

  .status-critico { background: rgba(239, 68, 68, 0.15); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2); }
  .status-regular { background: rgba(245, 158, 11, 0.15); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.2); }
  .status-estable, .status-optimo { background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.2); }

  .report-question-item {
    background: rgba(255,255,255,0.02);
    border: 1px solid var(--border-main);
    border-radius: 20px;
    padding: 24px;
    margin-bottom: 20px;
  }

  .report-meta-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
  }

  @media (max-width: 767.98px) {
    .report-meta-grid {
      grid-template-columns: 1fr;
    }
    .spgi-card {
      padding: 24px 18px;
    }
    .report-question-item {
      padding: 18px 16px;
    }
  }
</style>

<div class="spgi-bg">
  <div class="container">

    {{-- ALERTAS --}}
    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="border-radius: 12px;">
        <strong>Listo:</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
      </div>
    @endif

    @if($errors->any())
      <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert" style="border-radius: 12px;">
        <strong>Error:</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
      </div>
    @endif

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
      <div>
        <h4 class="spgi-title">Reporte de Visita Técnica</h4>
        <p class="text-muted small mb-0">Resumen y evaluación del cuestionario aplicado durante la visita.</p>
      </div>

      <div class="d-flex gap-2">
        <button type="button" class="btn btn-info text-white rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalEnviarCorreo">
          <i class="bi bi-envelope-paper me-1"></i> Enviar Correo
        </button>
        <a href="{{ route('visitas.pdf', $visita->id) }}" target="_blank" class="btn btn-danger text-white rounded-pill px-4">
          <i class="bi bi-file-earmark-pdf me-1"></i> Exportar PDF
        </a>
        <a href="{{ route('visitas.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
          <i class="bi bi-list-ul me-1"></i> Listado
        </a>
        <a href="{{ route('visitas.edit', $visita->id) }}" class="btn btn-warning text-dark rounded-pill px-4">
          <i class="bi bi-pencil-square me-1"></i> Editar Cuestionario
        </a>
      </div>
    </div>

    <div class="row g-4">
      
      {{-- INFORMACIÓN DE LA VISITA --}}
      <div class="col-lg-5 col-xl-4">
        <div class="spgi-card h-100">
          <h5 class="fw-bold text-white mb-4"><i class="bi bi-info-circle me-2 text-primary"></i>Datos Generales</h5>
          
          <div class="mb-4">
            <label class="text-muted small text-uppercase fw-bold d-block mb-1">Entidad / Persona Visitada</label>
            <div class="text-white fw-bold fs-5">{{ $visita->nombre_visitado }}</div>
          </div>

          <div class="mb-3">
            <label class="text-muted small text-uppercase fw-bold d-block mb-1">Correo Electrónico</label>
            <div class="text-white">{{ $visita->correo_visitado ?? 'No registrado' }}</div>
          </div>

          <div class="mb-3">
            <label class="text-muted small text-uppercase fw-bold d-block mb-1">Recibió en Terreno (Nombre)</label>
            <div class="text-white">{{ $visita->nombre_recibio ?? 'No especificado' }}</div>
          </div>

          <div class="mb-3">
            <label class="text-muted small text-uppercase fw-bold d-block mb-1">Recibió en Terreno (Teléfono)</label>
            <div class="text-white">{{ $visita->telefono_recibio ?? 'No registrado' }}</div>
          </div>

          <div class="mb-3">
            <label class="text-muted small text-uppercase fw-bold d-block mb-1">Cuestionario Aplicado</label>
            <span class="badge bg-secondary text-white">{{ $visita->template->nombre ?? 'N/A' }}</span>
          </div>

          <div class="mb-3">
            <label class="text-muted small text-uppercase fw-bold d-block mb-1">Fecha y Hora</label>
            <div class="text-white small">{{ \Carbon\Carbon::parse($visita->created_at)->format('d/m/Y h:i A') }}</div>
          </div>

          <div class="mb-4">
            <label class="text-muted small text-uppercase fw-bold d-block mb-1">Registrado por</label>
            <div class="text-white-50 small">{{ $visita->user->name ?? 'Sistema' }}</div>
          </div>

          <hr class="border-secondary opacity-25 my-4">

          <div class="status-show-box">
            <label class="text-muted small text-uppercase fw-bold d-block mb-2">Diagnóstico Resultante</label>
            <span class="status-badge-lg status-{{ strtolower(str_replace(['ó', 'á', 'é', 'í', 'ú'], ['o', 'a', 'e', 'i', 'u'], $visita->estado_cliente ?? 'estable')) }}">
              {{ $visita->estado_cliente ?? 'Estable' }}
            </span>
            <div class="display-4 fw-black text-warning mt-3 mb-1">{{ $visita->total_puntos }}</div>
            <div class="text-muted small">puntos obtenidos</div>
          </div>
        </div>
      </div>

      {{-- RESPUESTAS Y PLAN DE ACCIÓN --}}
      <div class="col-lg-7 col-xl-8">
        
        {{-- PLAN DE ACCIÓN --}}
        <div class="spgi-card">
          <h5 class="fw-bold text-white mb-3"><i class="bi bi-compass-fill text-primary me-2"></i>Plan de Acción Comercial</h5>
          <div class="p-3 rounded-4 bg-black bg-opacity-25 border border-secondary border-opacity-10 text-white" style="line-height: 1.6; font-size: 0.95rem;">
            {!! nl2br(e($visita->accion_sugerida ?? 'No se ha detallado ningún plan de acción.')) !!}
          </div>
        </div>

        {{-- DETALLE DE RESPUESTAS --}}
        <div class="spgi-card">
          <h5 class="fw-bold text-white mb-4"><i class="bi bi-card-checklist text-primary me-2"></i>Respuestas del Cuestionario</h5>

          @php
              $answersMap = $visita->respuestas->keyBy('question_id');
          @endphp

          @foreach($visita->template->questions as $idx => $question)
            @php
                $ans = $answersMap->get($question->id);
            @endphp
            <div class="report-question-item">
              <div class="d-flex align-items-start gap-2 mb-3">
                <span class="badge bg-primary px-2 py-1 rounded-pill" style="font-size: 0.75rem;">Q{{ $idx + 1 }}</span>
                <h6 class="fw-bold text-white mb-0" style="font-size: 1rem; line-height: 1.4;">{{ $question->pregunta }}</h6>
              </div>

              <div class="p-3 rounded-3 bg-black bg-opacity-20 border border-secondary border-opacity-10 mb-3 d-flex justify-content-between align-items-center">
                <div>
                  <span class="text-muted small d-block mb-1">Respuesta Seleccionada:</span>
                  <span class="text-white fw-bold">{{ $ans ? $ans->respuesta_seleccionada : 'Sin contestar' }}</span>
                </div>
                <div class="text-end">
                  <span class="badge bg-success" style="font-size: 0.8rem;">+{{ $ans ? $ans->puntos : 0 }} pts</span>
                </div>
              </div>

              @if($ans && ($ans->observaciones || $ans->recomendacion))
                <div class="row g-2 pt-2 border-top border-secondary border-opacity-10">
                  @if($ans->observaciones)
                    <div class="col-md-6">
                      <span class="text-muted small d-block" style="font-size: 0.7rem;">Observación / Hallazgo:</span>
                      <span class="text-white-50 small">{!! e($ans->observaciones) !!}</span>
                    </div>
                  @endif
                  @if($ans->recomendacion)
                    <div class="col-md-6">
                      <span class="text-muted small d-block" style="font-size: 0.7rem;">Recomendación Técnica:</span>
                      <span class="text-white-50 small">{!! e($ans->recomendacion) !!}</span>
                    </div>
                  @endif
                </div>
              @endif
            </div>
          @endforeach
        </div>

      </div>

    </div>

  </div>
</div>

{{-- MODAL ENVIAR CORREO --}}
<div class="modal fade" id="modalEnviarCorreo" tabindex="-1" aria-labelledby="modalEnviarCorreoLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="background: var(--bg-surface-glass); border: 1px solid var(--border-main); backdrop-filter: blur(20px);">
      <div class="modal-header border-secondary border-opacity-25">
        <h5 class="modal-title text-white" id="modalEnviarCorreoLabel"><i class="bi bi-envelope-paper me-2 text-info"></i>Enviar Reporte por Correo</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <form action="{{ route('visitas.enviar-correo', $visita->id) }}" method="POST">
        @csrf
        <div class="modal-body">
          <p class="text-white-50 small mb-4">Confirme el correo electrónico del destinatario para enviar el reporte de la visita en formato PDF.</p>

          <div class="mb-3">
            <label class="form-label text-muted small text-uppercase fw-bold">Correo del Destinatario</label>
            <input type="email" name="destinatario" class="form-control bg-transparent text-white border-secondary border-opacity-50" value="{{ $visita->correo_visitado }}" required>
          </div>
        </div>
        <div class="modal-footer border-secondary border-opacity-25">
          <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary rounded-pill px-4">
            <i class="bi bi-send-fill me-1"></i> Enviar Ahora
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
