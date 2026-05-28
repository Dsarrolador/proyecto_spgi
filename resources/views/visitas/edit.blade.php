@extends('layouts.app')

@section('page_title', 'Ejecutar Cuestionario de Visita')

@section('content')

<style>
  .spgi-bg { padding: 12px 0 32px 0; }

  /* Header pegajoso optimizado para móvil */
  .sticky-stats-bar {
    position: sticky;
    top: 70px;
    z-index: 100;
    background: rgba(15, 23, 42, 0.85);
    border: 1px solid var(--border-main);
    backdrop-filter: blur(16px);
    border-radius: 16px;
    padding: 14px 20px;
    margin-bottom: 24px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.25);
  }

  .spgi-card {
    background: var(--bg-surface-glass);
    border: 1px solid var(--border-main);
    border-radius: 24px;
    box-shadow: var(--shadow-main);
    backdrop-filter: blur(16px);
    padding: 24px;
    margin-bottom: 24px;
  }

  /* Tarjetas de preguntas táctiles */
  .question-card {
    background: rgba(30, 41, 59, 0.4);
    border: 1px solid var(--border-main);
    border-radius: 20px;
    padding: 24px;
    margin-bottom: 20px;
    transition: all 0.25s ease;
  }
  .question-card.active-focus {
    border-color: rgba(37, 99, 235, 0.4);
    box-shadow: 0 0 15px rgba(37, 99, 235, 0.1);
  }

  /* Botones táctiles de respuesta (Pills grandes) */
  .tactile-option-label {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: rgba(255,255,255,0.03);
    border: 1px solid var(--border-main);
    border-radius: 14px;
    padding: 14px 18px;
    margin-bottom: 10px;
    cursor: pointer;
    font-weight: 600;
    color: var(--text-muted);
    transition: all 0.2s ease;
    user-select: none;
    -webkit-user-select: none;
  }

  .tactile-option-label:hover {
    background: rgba(255,255,255,0.06);
    color: white;
  }

  /* Ocultar los inputs de radio reales */
  .hidden-radio-input {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
  }

  /* Opcion seleccionada */
  .hidden-radio-input:checked + .tactile-option-label {
    background: rgba(37, 99, 235, 0.15);
    border-color: var(--spgi-primary);
    color: white;
    box-shadow: 0 0 12px rgba(37, 99, 235, 0.2);
  }

  .hidden-radio-input:checked + .tactile-option-label .check-indicator {
    background: var(--spgi-primary);
    border-color: var(--spgi-primary);
    color: white;
  }

  .check-indicator {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    border: 2px solid var(--border-main);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    color: transparent;
    transition: all 0.2s ease;
    flex-shrink: 0;
  }

  /* Sección de observaciones expandible */
  .notes-toggler {
    background: transparent;
    border: none;
    color: var(--spgi-primary);
    font-size: 0.85rem;
    font-weight: 700;
    padding: 4px 0;
    display: flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    margin-top: 10px;
    transition: color 0.2s ease;
  }
  .notes-toggler:hover {
    color: #3b82f6;
  }

  .btn-spgi-submit {
    background: linear-gradient(135deg, var(--spgi-primary), #2563eb);
    border: 0; color: #fff !important; min-height: 52px; border-radius: 16px; padding: 0 32px;
    box-shadow: 0 10px 25px var(--spgi-primary-glow); font-weight: 800;
    font-size: 1.05rem;
    transition: all 0.2s ease;
  }
  .btn-spgi-submit:hover { filter: brightness(1.1); transform: translateY(-1px); }

  /* Barra de progreso */
  .custom-progress {
    height: 6px;
    background: rgba(255,255,255,0.08);
    border-radius: 99px;
    overflow: hidden;
  }
  .custom-progress-bar {
    height: 100%;
    background: linear-gradient(90deg, var(--spgi-primary), #10b981);
    width: 0%;
    transition: width 0.3s ease;
  }

  @media (max-width: 991.98px) {
    .sticky-stats-bar {
      top: 60px;
      padding: 10px 15px;
    }
  }

  @media (max-width: 575.98px) {
    .question-card {
      padding: 18px 16px;
    }
    .tactile-option-label {
      padding: 12px 14px;
      font-size: 0.9rem;
    }
  }
</style>

@php
    $existingAnswers = $visita->respuestas->keyBy('question_id');
@endphp

<div class="spgi-bg">
  <div class="container">

    {{-- VOLVER --}}
    <div class="d-flex align-items-center justify-content-between mb-3">
      <div>
        <h4 class="fw-bold text-white mb-0">Visita: {{ $visita->nombre_visitado }}</h4>
        <p class="text-muted small mb-0 d-none d-sm-block">Cuestionario: <strong>{{ $visita->template->nombre }}</strong></p>
      </div>
      <a href="{{ route('visitas.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
        <i class="bi bi-x-circle me-1"></i> Salir
      </a>
    </div>

    {{-- BARRA ESTADÍSTICAS FLOTANTE (Optimizado para Tablet/Móvil) --}}
    <div class="sticky-stats-bar">
      <div class="row align-items-center g-2 g-md-3">
        <div class="col-6 col-md-3 text-start">
          <div class="text-muted small text-uppercase fw-bold" style="font-size: 0.65rem;">Progreso</div>
          <div class="text-white fw-extrabold" style="font-size: 0.95rem;">
            <span id="answered-count-display">0</span> / {{ $visita->template->questions->count() }} <small class="text-muted">listo</small>
          </div>
        </div>

        <div class="col-6 col-md-3 text-end text-md-center">
          <div class="text-muted small text-uppercase fw-bold" style="font-size: 0.65rem;">Puntos Totales</div>
          <div class="text-warning fw-extrabold" style="font-size: 0.95rem;">
            <span id="live-points-display" class="fs-5">0</span> <small class="text-muted-opacity">pts</small>
          </div>
        </div>

        <div class="col-6 col-md-3 text-start text-md-center">
          <div class="text-muted small text-uppercase fw-bold" style="font-size: 0.65rem;">Diagnóstico</div>
          <span id="live-status-badge" class="badge bg-success rounded-pill px-3 py-1" style="font-size: 0.75rem;">Estable</span>
        </div>

        <div class="col-6 col-md-3 text-end d-none d-md-block">
          <div class="custom-progress">
            <div class="custom-progress-bar" id="progress-bar-el"></div>
          </div>
        </div>
      </div>
      <div class="custom-progress d-md-none mt-2">
        <div class="custom-progress-bar" id="mobile-progress-bar-el"></div>
      </div>
    </div>

    {{-- FORMULARIO CUESTIONARIO --}}
    <form action="{{ route('visitas.update', $visita->id) }}" method="POST">
      @csrf
      @method('PUT')

      <div class="row g-4">
        <div class="col-lg-8">
          @foreach($visita->template->questions as $index => $question)
            @php
                $userAnswer = $existingAnswers->get($question->id);
            @endphp
            <div class="question-card" id="q_card_{{ $question->id }}">
              <div class="d-flex align-items-start gap-2 mb-3">
                <span class="badge bg-primary rounded-pill px-2 py-1" style="font-size: 0.75rem;">{{ $index + 1 }}</span>
                <h5 class="fw-bold text-white mb-0 fs-6" style="line-height: 1.4;">{{ $question->pregunta }}</h5>
              </div>

              <!-- OPCIONES DE RESPUESTA TÁCTILES -->
              <div class="mb-3">
                @if($question->predefinedAnswers->count() == 0)
                  <!-- Respuesta abierta -->
                  <div class="mb-2">
                    <input type="text" 
                           name="respuestas[{{ $question->id }}][respuesta]" 
                           class="form-control" 
                           placeholder="Ingresa la respuesta..." 
                           value="{{ $userAnswer ? $userAnswer->respuesta_seleccionada : '' }}" 
                           required
                           data-question-id="{{ $question->id }}"
                           oninput="checkOpenAnswered(this)">
                    <input type="hidden" name="respuestas[{{ $question->id }}][puntos]" id="points_{{ $question->id }}" value="0">
                  </div>
                @else
                  <!-- Opciones Grandes (Táctiles) -->
                  @foreach($question->predefinedAnswers as $answer)
                    <div class="position-relative">
                      <input class="hidden-radio-input question-option-radio" 
                             type="radio" 
                             name="respuestas[{{ $question->id }}][respuesta]" 
                             id="opt_{{ $answer->id }}" 
                             value="{{ $answer->respuesta }}"
                             data-points="{{ $answer->puntos }}"
                             data-question-id="{{ $question->id }}"
                             {{ ($userAnswer && $userAnswer->respuesta_seleccionada == $answer->respuesta) ? 'checked' : '' }}
                             required>
                      <label class="tactile-option-label" for="opt_{{ $answer->id }}">
                        <span class="text-white-85">{{ $answer->respuesta }}</span>
                        <div class="d-flex align-items-center gap-2">
                          <span class="badge rounded" style="background: rgba(255,255,255,0.06); font-size: 0.65rem; color: #10b981;">+{{ $answer->puntos }} pts</span>
                          <div class="check-indicator"><i class="bi bi-check-lg"></i></div>
                        </div>
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

              <!-- Observaciones y Recomendaciones Colapsables (móvil) -->
              <div>
                <button type="button" class="notes-toggler" onclick="toggleNotes('notes_box_{{ $question->id }}', this)">
                  <i class="bi bi-plus-circle"></i>
                  <span>Observaciones / Recomendación</span>
                </button>
                <div class="collapse-notes-section d-none mt-3" id="notes_box_{{ $question->id }}">
                  <div class="row g-2">
                    <div class="col-12 col-md-6">
                      <label class="form-label text-muted" style="font-size: 0.65rem;">Hallazgo / Observación</label>
                      <input type="text" 
                             name="respuestas[{{ $question->id }}][observaciones]" 
                             class="form-control form-control-sm" 
                             placeholder="Observaciones de campo" 
                             value="{{ $userAnswer ? $userAnswer->observaciones : '' }}">
                    </div>
                    <div class="col-12 col-md-6">
                      <label class="form-label text-muted" style="font-size: 0.65rem;">Recomendación técnica</label>
                      <input type="text" 
                             name="respuestas[{{ $question->id }}][recomendacion]" 
                             class="form-control form-control-sm" 
                             placeholder="Recomendación técnica" 
                             value="{{ $userAnswer ? $userAnswer->recomendacion : '' }}">
                    </div>
                  </div>
                </div>
              </div>

            </div>
          @endforeach
        </div>

        {{-- Diagnóstico y Envío --}}
        <div class="col-lg-4">
          <div class="spgi-card">
            <h5 class="fw-bold mb-3 text-white"><i class="bi bi-check2-square me-2"></i>Finalizar Visita</h5>
            <hr class="border-secondary opacity-25 mb-4">

            <div class="mb-4">
              <label for="accion_sugerida" class="form-label">Plan de Acción / Siguiente Paso</label>
              <textarea name="accion_sugerida" id="accion_sugerida" rows="4" class="form-control" placeholder="Ej. Agendar seguimiento técnico, enviar cotización de upgrade, etc.">{{ $visita->accion_sugerida }}</textarea>
              <div class="form-text mt-2" style="font-size: 0.72rem;">Indica los compromisos o planes de acción resultantes de la visita técnica.</div>
            </div>

            <div class="d-grid mt-4">
              <button type="submit" class="btn btn-spgi-submit d-flex align-items-center justify-content-center gap-2">
                <i class="bi bi-cloud-arrow-up-fill"></i> Guardar Cuestionario
              </button>
            </div>
          </div>
        </div>
      </div>
    </form>

  </div>
</div>

<script>
  function toggleNotes(boxId, button) {
      const box = document.getElementById(boxId);
      const icon = button.querySelector('i');
      if (box.classList.contains('d-none')) {
          box.classList.remove('d-none');
          icon.className = 'bi bi-dash-circle';
      } else {
          box.classList.add('d-none');
          icon.className = 'bi bi-plus-circle';
      }
  }

  function checkOpenAnswered(input) {
      const qId = input.dataset.questionId;
      const pointsInput = document.getElementById('points_' + qId);
      if (input.value.trim() !== "") {
          pointsInput.value = "0"; // Abierto no suma pts por defecto
      }
      calculateStats();
  }

  function calculateStats() {
      const questionsCount = {{ $visita->template->questions->count() }};
      let answeredCount = 0;
      let totalPoints = 0;

      // 1. Recorrer preguntas de radio
      const radioGroups = {};
      const radios = document.querySelectorAll('.question-option-radio');
      radios.forEach(radio => {
          radioGroups[radio.name] = true;
          if (radio.checked) {
              const qId = radio.dataset.questionId;
              const pts = parseInt(radio.dataset.points) || 0;
              document.getElementById('points_' + qId).value = pts;
              
              // Agregar foco visual a la tarjeta seleccionada
              const card = document.getElementById('q_card_' + qId);
              if (card) card.classList.add('active-focus');
          }
      });

      // Contar radios respondidos
      Object.keys(radioGroups).forEach(groupName => {
          const checked = document.querySelector(`input[name="${groupName}"]:checked`);
          if (checked) {
              answeredCount++;
          }
      });

      // Contar inputs de texto respondidos
      const textInputs = document.querySelectorAll('input[type="text"][required]');
      textInputs.forEach(input => {
          if (input.value.trim() !== "") {
              answeredCount++;
          }
      });

      // Sumar todos los puntos ocultos
      const pointsInputs = document.querySelectorAll('input[name$="[puntos]"]');
      pointsInputs.forEach(input => {
          totalPoints += parseInt(input.value) || 0;
      });

      // Actualizar interfaz flotante
      document.getElementById('answered-count-display').innerText = answeredCount;
      document.getElementById('live-points-display').innerText = totalPoints;

      // Porcentaje de progreso
      const progressPercent = Math.min(100, Math.round((answeredCount / questionsCount) * 100)) || 0;
      document.getElementById('progress-bar-el').style.width = progressPercent + '%';
      document.getElementById('mobile-progress-bar-el').style.width = progressPercent + '%';

      // Diagnóstico sugerido
      let statusText = 'Óptimo';
      let statusClass = 'bg-success';
      if (totalPoints < 30) {
          statusText = 'Crítico';
          statusClass = 'bg-danger';
      } else if (totalPoints < 45) {
          statusText = 'Regular';
          statusClass = 'bg-warning text-dark';
      }

      const statusBadge = document.getElementById('live-status-badge');
      statusBadge.innerText = statusText;
      statusBadge.className = 'badge ' + statusClass + ' px-3 py-1 rounded-pill';
  }

  document.addEventListener('DOMContentLoaded', function() {
      const radios = document.querySelectorAll('.question-option-radio');
      radios.forEach(radio => {
          radio.addEventListener('change', function() {
              const qId = this.dataset.questionId;
              
              // Quitar foco visual de otros labels del mismo grupo
              const radiosInGroup = document.querySelectorAll(`input[name="respuestas[${qId}][respuesta]"]`);
              radiosInGroup.forEach(r => {
                  const card = document.getElementById('q_card_' + qId);
                  if (card) card.classList.remove('active-focus');
              });

              calculateStats();
          });
      });

      // Calcular estadísticas iniciales al cargar
      calculateStats();
  });
</script>

@endsection
