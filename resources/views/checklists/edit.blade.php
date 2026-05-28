@extends('layouts.app')

@section('page_title', 'Configurar Plantilla de Cuestionario')

@section('content')

<style>
  .spgi-bg{ padding: 12px 0 24px 0; }

  .spgi-card{
    background: var(--bg-surface-glass); border: 1px solid var(--border-main);
    border-radius: 24px; box-shadow: var(--shadow-main); backdrop-filter: blur(16px);
    padding: 24px; margin-bottom: 24px;
  }

  .question-card{
    background: rgba(var(--bg-surface), 0.3); border: 1px solid var(--border-main);
    border-radius: 18px; padding: 20px; margin-bottom: 20px; transition: all 0.2s ease;
  }
  .question-card:hover{ border-color: rgba(16, 185, 129, 0.3); }

  .btn-spgi{
    background: linear-gradient(135deg, #10b981, #059669);
    border: 0; color: #fff !important; min-height:46px; border-radius:12px; padding:0 24px;
    box-shadow: 0 10px 25px rgba(16, 185, 129, 0.2); font-weight:700;
  }
  .btn-spgi:hover{ filter: brightness(1.1); transform: translateY(-1px); }

  .btn-spgi-sm{
    background: linear-gradient(135deg, #10b981, #059669);
    border: 0; color: #fff !important; min-height:36px; border-radius:10px; padding:0 16px;
    font-size: 0.85rem; font-weight:700;
  }
  .btn-spgi-sm:hover{ filter: brightness(1.1); }

  .form-label{ font-weight: 600; color: var(--text-main); margin-bottom: 8px; }
  .form-control, .form-select{
    height:46px; border-radius:12px; border:1px solid var(--border-main);
    background-color: var(--bg-surface); color: var(--text-main);
    box-shadow: none !important; transition: all 0.2s ease;
  }
  .form-control:focus, .form-select:focus{ border-color: #10b981; background-color: var(--bg-surface); color: var(--text-main); }
  
  .form-control-sm{ height: 38px; border-radius: 10px; font-size: 0.875rem; }

  textarea.form-control{ height: auto; }

  .predefined-answer-badge {
    background: rgba(255,255,255,0.05);
    border: 1px solid var(--border-main);
    color: var(--text-main);
    padding: 8px 12px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin-right: 8px;
    margin-bottom: 8px;
    font-size: 0.85rem;
  }

  .puntos-badge {
    background: rgba(16, 185, 129, 0.1);
    color: #10b981;
    font-weight: bold;
    padding: 2px 6px;
    border-radius: 6px;
    font-size: 0.75rem;
  }
</style>

<div class="spgi-bg">
  <div class="container">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold text-gradient">Configurar Plantilla</h1>
            <p class="text-muted mb-0">Define las preguntas y las respuestas predeterminadas de tu cuestionario técnico.</p>
        </div>
        <a href="{{ route('checklists.index') }}" class="btn btn-outline-secondary rounded-pill">
            <i class="bi bi-arrow-left me-2"></i> Volver a Plantillas
        </a>
    </div>

    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4" role="alert">
        <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif

    <div class="row">
      <!-- Datos Generales -->
      <div class="col-lg-4">
        <div class="spgi-card">
          <h5 class="fw-bold mb-3 text-white"><i class="bi bi-info-circle me-2"></i>Datos Generales</h5>
          <hr class="border-secondary opacity-20 mb-4">

          <form action="{{ route('checklists.update', $checklist->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
              <label for="nombre" class="form-label">Nombre de la Plantilla</label>
              <input type="text" name="nombre" id="nombre" class="form-control" value="{{ $checklist->nombre }}" required>
            </div>

            <div class="mb-4">
              <label for="descripcion" class="form-label">Descripción</label>
              <textarea name="descripcion" id="descripcion" rows="3" class="form-control">{{ $checklist->descripcion }}</textarea>
            </div>

            <button type="submit" class="btn btn-spgi w-100">
              Guardar Cambios
            </button>
          </form>
        </div>

        <div class="spgi-card">
          <h5 class="fw-bold mb-3 text-white"><i class="bi bi-plus-circle me-2"></i>Agregar Pregunta</h5>
          <hr class="border-secondary opacity-20 mb-4">

          <form action="{{ route('checklists.questions.store', $checklist->id) }}" method="POST">
            @csrf
            <div class="mb-4">
              <label for="pregunta" class="form-label">Pregunta / Criterio de Evaluación <span class="text-danger">*</span></label>
              <textarea name="pregunta" id="pregunta" rows="3" class="form-control" placeholder="Ej: ¿Cuenta con backup de datos fuera del sitio?" required></textarea>
            </div>
            
            <button type="submit" class="btn btn-spgi w-100">
              <i class="bi bi-plus-lg me-1"></i> Agregar Pregunta
            </button>
          </form>
        </div>
      </div>

      <!-- Preguntas y Respuestas -->
      <div class="col-lg-8">
        <div class="spgi-card">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <h5 class="fw-bold mb-0 text-white"><i class="bi bi-question-circle me-2"></i>Preguntas Configuradas</h5>
            <span class="badge bg-primary rounded-pill px-3">{{ $checklist->questions->count() }} Preguntas</span>
          </div>
          <hr class="border-secondary opacity-20 mb-4">

          @if($checklist->questions->count() == 0)
            <div class="text-center py-5 text-muted">
              <i class="bi bi-patch-question fs-1 mb-3 d-block text-secondary"></i>
              <p class="mb-0">Aún no hay preguntas. Utiliza el formulario de la izquierda para agregar la primera.</p>
            </div>
          @else
            @foreach($checklist->questions as $index => $question)
              <div class="question-card">
                <div class="d-flex align-items-start justify-content-between mb-3">
                  <div>
                    <span class="fw-bold text-success me-2">#{{ $index + 1 }}</span>
                    <span class="fw-bold text-white fs-6">{{ $question->pregunta }}</span>
                  </div>
                  
                  <form action="{{ route('checklists.questions.destroy', $question->id) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar esta pregunta y todas sus opciones?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger border-0 rounded-circle" style="width: 32px; height: 32px;" title="Eliminar Pregunta">
                      <i class="bi bi-trash"></i>
                    </button>
                  </form>
                </div>

                <!-- Respuestas Predeterminadas -->
                <div class="mb-3">
                  <div class="text-muted small fw-bold mb-2">Respuestas / Opciones Disponibles:</div>
                  @if($question->predefinedAnswers->count() == 0)
                    <p class="text-muted small mb-2 italic">Sin opciones predeterminadas. (El usuario tendrá que responder de forma abierta).</p>
                  @else
                    <div class="d-flex flex-wrap">
                      @foreach($question->predefinedAnswers as $answer)
                        <div class="predefined-answer-badge">
                          <span>{{ $answer->respuesta }}</span>
                          <span class="puntos-badge">+{{ $answer->puntos }} pts</span>
                          <form action="{{ route('checklists.answers.destroy', $answer->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar esta opción?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-close btn-close-white" style="font-size: 0.65rem;" aria-label="Close"></button>
                          </form>
                        </div>
                      @endforeach
                    </div>
                  @endif
                </div>

                <!-- Agregar Nueva Respuesta Predeterminada -->
                <div class="mt-3 p-3 rounded-4" style="background: rgba(255,255,255,0.02); border: 1px dashed var(--border-main);">
                  <form action="{{ route('checklists.answers.store', $question->id) }}" method="POST" class="row g-2 align-items-center">
                    @csrf
                    <div class="col-md-7">
                      <input type="text" name="respuesta" class="form-control form-control-sm" placeholder="Texto de la respuesta (Ej: Sí, Completo)" required>
                    </div>
                    <div class="col-md-3">
                      <select name="puntos" class="form-select form-control-sm" required>
                        <option value="5">5 Puntos (Excelente)</option>
                        <option value="3">3 Puntos (Regular)</option>
                        <option value="0" selected>0 Puntos (Malo/No aplica)</option>
                      </select>
                    </div>
                    <div class="col-md-2">
                      <button type="submit" class="btn btn-spgi-sm w-100">
                        <i class="bi bi-plus-lg"></i> Opción
                      </button>
                    </div>
                  </form>
                </div>
              </div>
            @endforeach
          @endif
        </div>
      </div>
    </div>

  </div>
</div>

@endsection
