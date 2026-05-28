@extends('layouts.app')

@section('page_title', 'Realizar Evaluación Técnica')

@section('content')

<style>
  .spgi-bg{ padding: 12px 0 24px 0; }

  .spgi-card{
    background: var(--bg-surface-glass); border: 1px solid var(--border-main);
    border-radius: 24px; box-shadow: var(--shadow-main); backdrop-filter: blur(16px);
    padding: 28px; margin-bottom: 24px;
  }

  .question-item{
    background: rgba(var(--bg-surface), 0.2); border: 1px solid var(--border-main);
    border-radius: 18px; padding: 24px; margin-bottom: 24px; transition: all 0.2s ease;
  }
  .question-item:hover{ border-color: rgba(16, 185, 129, 0.2); }

  .btn-spgi{
    background: linear-gradient(135deg, #10b981, #059669);
    border: 0; color: #fff !important; min-height:48px; border-radius:14px; padding:0 32px;
    box-shadow: 0 10px 25px rgba(16, 185, 129, 0.2); font-weight:700;
  }
  .btn-spgi:hover{ filter: brightness(1.1); transform: translateY(-1px); }

  .form-label{ font-weight: 600; color: var(--text-main); margin-bottom: 8px; font-size: 0.9rem; }
  .form-control{
    height:42px; border-radius:10px; border:1px solid var(--border-main);
    background-color: var(--bg-surface); color: var(--text-main);
    box-shadow: none !important; transition: all 0.2s ease;
  }
  .form-control:focus{ border-color: #10b981; background-color: var(--bg-surface); color: var(--text-main); }
  
  textarea.form-control{ height: auto; }

  .score-sticky-card {
    position: sticky;
    top: 24px;
  }

  .form-check-input:checked {
    background-color: #10b981;
    border-color: #10b981;
  }
</style>

@php
    $existingAnswers = $checklist->answers->keyBy('question_id');
@endphp

<div class="spgi-bg">
  <div class="container">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold text-gradient">Evaluación Técnica: {{ $checklist->template->nombre }}</h1>
            <p class="text-muted mb-0">Completando cuestionario para el lead: <strong>{{ $lead->nombre }}</strong>.</p>
        </div>
        <a href="{{ route('leads.show', $lead->id) }}" class="btn btn-outline-secondary rounded-pill">
            <i class="bi bi-arrow-left me-2"></i> Volver al Lead
        </a>
    </div>

    <form action="{{ route('leads.checklists.update', [$lead->id, $checklist->id]) }}" method="POST">
      @csrf
      @method('PUT')

      <div class="row">
        <!-- Preguntas del Cuestionario -->
        <div class="col-lg-8">
          @foreach($checklist->template->questions as $index => $question)
            @php
                $userAnswer = $existingAnswers->get($question->id);
            @endphp
            <div class="question-item">
              <div class="d-flex align-items-start gap-2 mb-3">
                <span class="badge bg-success rounded-pill px-2 py-1" style="font-size: 0.75rem;">Q{{ $index + 1 }}</span>
                <h5 class="fw-bold text-white mb-0" style="font-size: 1.1rem; line-height: 1.4;">{{ $question->pregunta }}</h5>
              </div>

              <!-- Opciones de Respuesta -->
              <div class="mb-4 ps-3">
                @if($question->predefinedAnswers->count() == 0)
                  <!-- Respuesta abierta -->
                  <div class="mb-2">
                    <label class="form-label text-muted">Respuesta abierta</label>
                    <input type="text" 
                           name="respuestas[{{ $question->id }}][respuesta]" 
                           class="form-control" 
                           placeholder="Escribe la respuesta aquí..." 
                           value="{{ $userAnswer ? $userAnswer->respuesta_seleccionada : '' }}" 
                           required>
                    <input type="hidden" name="respuestas[{{ $question->id }}][puntos]" id="points_{{ $question->id }}" value="0">
                  </div>
                @else
                  <!-- Opciones Predeterminadas (Radio Buttons) -->
                  @foreach($question->predefinedAnswers as $answer)
                    <div class="form-check mb-2">
                      <input class="form-check-input question-option-radio" 
                             type="radio" 
                             name="respuestas[{{ $question->id }}][respuesta]" 
                             id="opt_{{ $answer->id }}" 
                             value="{{ $answer->respuesta }}"
                             data-points="{{ $answer->puntos }}"
                             data-question-id="{{ $question->id }}"
                             {{ ($userAnswer && $userAnswer->respuesta_seleccionada == $answer->respuesta) ? 'checked' : '' }}
                             required>
                      <label class="form-check-label text-white" for="opt_{{ $answer->id }}">
                        {{ $answer->respuesta }} 
                        <span class="badge bg-success ms-2" style="font-size: 0.7rem; opacity: 0.85;">+{{ $answer->puntos }} pts</span>
                      </label>
                    </div>
                  @endforeach
                  
                  <!-- Input oculto para rastrear puntos de esta pregunta -->
                  <input type="hidden" 
                         name="respuestas[{{ $question->id }}][puntos]" 
                         id="points_{{ $question->id }}" 
                         value="{{ $userAnswer ? $userAnswer->puntos : 0 }}">
                @endif
              </div>

              <!-- Observaciones y Recomendaciones por Pregunta -->
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label text-muted">Observaciones / Hallazgo</label>
                  <input type="text" 
                         name="respuestas[{{ $question->id }}][observaciones]" 
                         class="form-control" 
                         placeholder="Ej: Servidor físico antiguo sin soporte..." 
                         value="{{ $userAnswer ? $userAnswer->observaciones : '' }}">
                </div>
                <div class="col-md-6">
                  <label class="form-label text-muted">Recomendación técnica</label>
                  <input type="text" 
                         name="respuestas[{{ $question->id }}][recomendacion]" 
                         class="form-control" 
                         placeholder="Ej: Migrar a Azure o actualizar hardware..." 
                         value="{{ $userAnswer ? $userAnswer->recomendacion : '' }}">
                </div>
              </div>
            </div>
          @endforeach
        </div>

        <!-- Panel de Puntuación Real-Time y Estado -->
        <div class="col-lg-4">
          <div class="spgi-card score-sticky-card">
            <h5 class="fw-bold mb-3 text-white"><i class="bi bi-speedometer2 me-2"></i>Diagnóstico Comercial</h5>
            <hr class="border-secondary opacity-20 mb-4">

            <!-- Puntos Totales -->
            <div class="text-center py-4 rounded-4 mb-4" style="background: rgba(255,255,255,0.03); border: 1px solid var(--border-main);">
              <div class="text-muted small text-uppercase fw-bold mb-1">Puntuación Total</div>
              <div class="display-3 fw-bold text-gradient text-success" id="total-points-display">0</div>
              <div class="text-muted small mt-1">puntos acumulados</div>
            </div>

            <!-- Estado del Cliente -->
            <div class="d-flex align-items-center justify-content-between mb-4">
              <span class="text-muted fw-bold">Estado Sugerido:</span>
              <span id="status-badge-display" class="badge bg-success px-3 py-2 fs-6 rounded-pill">Estable</span>
            </div>

            <!-- Acción sugerida final -->
            <div class="mb-4">
              <label for="accion_sugerida" class="form-label">Acción Comercial Sugerida / Siguiente Paso</label>
              <textarea name="accion_sugerida" id="accion_sugerida" rows="3" class="form-control" placeholder="Ej: Agendar demo técnica, cotizar pack de seguridad, etc.">{{ $checklist->accion_sugerida }}</textarea>
            </div>

            <div class="d-grid">
              <button type="submit" class="btn btn-spgi">
                <i class="bi bi-check-circle me-1"></i> Guardar Cuestionario
              </button>
            </div>
          </div>
        </div>
      </div>
    </form>

  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
      // Listen to changes on radio buttons
      const radios = document.querySelectorAll('.question-option-radio');
      radios.forEach(radio => {
          radio.addEventListener('change', function() {
              const questionId = this.dataset.questionId;
              const points = this.dataset.points;
              document.getElementById('points_' + questionId).value = points;
              
              calculateTotalPoints();
          });
      });

      function calculateTotalPoints() {
          let total = 0;
          const pointsInputs = document.querySelectorAll('input[name$="[puntos]"]');
          pointsInputs.forEach(input => {
              total += parseInt(input.value) || 0;
          });
          document.getElementById('total-points-display').innerText = total;
          
          // Determine status text
          let statusText = 'Óptimo';
          let statusClass = 'bg-success';
          if (total < 30) {
              statusText = 'Crítico';
              statusClass = 'bg-danger';
          } else if (total < 45) {
              statusText = 'Regular';
              statusClass = 'bg-warning text-dark';
          }
          
          const statusBadge = document.getElementById('status-badge-display');
          statusBadge.innerText = statusText;
          statusBadge.className = 'badge ' + statusClass + ' px-3 py-2 fs-6 rounded-pill';
      }
      
      // Run on page load
      calculateTotalPoints();
  });
</script>

@endsection
