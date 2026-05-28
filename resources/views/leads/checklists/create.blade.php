@extends('layouts.app')

@section('page_title', 'Asignar Cuestionario Técnico a Lead')

@section('content')

<style>
  .spgi-bg{ padding: 12px 0 24px 0; }

  .spgi-card{
    background: var(--bg-surface-glass); border: 1px solid var(--border-main);
    border-radius: 24px; box-shadow: var(--shadow-main); backdrop-filter: blur(16px);
    padding: 32px;
  }

  .btn-spgi{
    background: linear-gradient(135deg, #10b981, #059669);
    border: 0; color: #fff !important; min-height:46px; border-radius:14px; padding:0 24px;
    box-shadow: 0 10px 25px rgba(16, 185, 129, 0.2); font-weight:700;
  }
  .btn-spgi:hover{ filter: brightness(1.1); transform: translateY(-1px); }

  .form-label{ font-weight: 600; color: var(--text-main); margin-bottom: 8px; }
  .form-select{
    height:46px; border-radius:12px; border:1px solid var(--border-main);
    background-color: var(--bg-surface); color: var(--text-main);
    box-shadow: none !important; transition: all 0.2s ease;
  }
  .form-select:focus{ border-color: #10b981; background-color: var(--bg-surface); color: var(--text-main); }
</style>

<div class="spgi-bg">
  <div class="container" style="max-width: 600px;">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold text-gradient">Asignar Cuestionario</h1>
            <p class="text-muted mb-0">Selecciona la plantilla de cuestionario para evaluar a <strong>{{ $lead->nombre }}</strong>.</p>
        </div>
        <a href="{{ route('leads.show', $lead->id) }}" class="btn btn-outline-secondary rounded-pill">
            <i class="bi bi-arrow-left me-2"></i> Cancelar
        </a>
    </div>

    @if ($errors->any())
      <div class="alert alert-danger alert-dismissible fade show rounded-4 mb-4" role="alert">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif

    <div class="spgi-card">
      <form action="{{ route('leads.checklists.store', $lead->id) }}" method="POST">
        @csrf

        <div class="mb-4">
          <label for="template_id" class="form-label">Selecciona la Plantilla de Evaluación <span class="text-danger">*</span></label>
          <select name="template_id" id="template_id" class="form-select" required>
            <option value="" disabled selected>-- Elige una plantilla --</option>
            @foreach($templates as $temp)
              <option value="{{ $temp->id }}">{{ $temp->nombre }} ({{ $temp->questions()->count() }} preguntas)</option>
            @endforeach
          </select>
        </div>

        <div class="d-grid mt-4">
          <button type="submit" class="btn btn-spgi">
            Iniciar Evaluación <i class="bi bi-play-fill ms-1"></i>
          </button>
        </div>
      </form>
    </div>

  </div>
</div>

@endsection
