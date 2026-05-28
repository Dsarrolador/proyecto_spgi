@extends('layouts.app')

@section('page_title', 'Registrar Visita')

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
    max-width: 600px;
    margin: 0 auto;
  }
  
  .form-label { font-weight: 800; color: var(--text-muted); font-size: .75rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; }
  .form-control, .form-select {
    background: rgba(0,0,0,0.15) !important; color: white !important;
    border-radius: 14px !important; border: 1px solid var(--border-main) !important;
    padding: .85rem 1rem !important; box-shadow: none !important;
    font-size: 1rem !important;
  }
  .form-control:focus, .form-select:focus { border-color: var(--spgi-primary) !important; }

  .btn-spgi {
    background: linear-gradient(135deg, var(--spgi-primary), #2563eb);
    border: 0; color: #fff !important; min-height: 48px; border-radius: 14px; padding: 0 24px;
    box-shadow: 0 10px 25px var(--spgi-primary-glow); font-weight: 700;
  }
  .btn-spgi:hover { filter: brightness(1.1); transform: translateY(-1px); }

  @media (max-width: 575.98px) {
    .spgi-card { padding: 24px 20px; }
  }
</style>

<div class="spgi-bg">
  <div class="container">

    {{-- HEADER --}}
    <div class="text-center mb-4">
      <h4 class="fw-bold text-white mb-2"><i class="bi bi-geo-alt text-primary"></i> Registrar Nueva Visita</h4>
      <p class="text-muted small">Ingresa la información inicial de la visita de campo para iniciar el cuestionario.</p>
    </div>

    {{-- FORM --}}
    <div class="spgi-card">
      <form action="{{ route('visitas.store') }}" method="POST">
        @csrf

        <div class="row g-4">
          <div class="col-12">
            <label class="form-label">Persona / Entidad Visitada</label>
            <input type="text" name="nombre_visitado" class="form-control" placeholder="Ej. Empresa Intertek o Nombre del Cliente" value="{{ old('nombre_visitado') }}" required>
          </div>

          <div class="col-12 col-md-4">
            <label class="form-label">Correo Electrónico</label>
            <input type="email" name="correo_visitado" class="form-control" placeholder="ejemplo@correo.com" value="{{ old('correo_visitado') }}">
          </div>

          <div class="col-12 col-md-4">
            <label class="form-label">Nombre de quien Recibió</label>
            <input type="text" name="nombre_recibio" class="form-control" placeholder="Nombre del receptor" value="{{ old('nombre_recibio') }}">
          </div>

          <div class="col-12 col-md-4">
            <label class="form-label">Teléfono de quien Recibió</label>
            <input type="text" name="telefono_recibio" class="form-control" placeholder="Teléfono del receptor" value="{{ old('telefono_recibio') }}">
          </div>

          <div class="col-12">
            <label class="form-label">Plantilla de Cuestionario</label>
            <select name="template_id" class="form-select" required>
              <option value="">-- Seleccionar Cuestionario --</option>
              @foreach($templates as $t)
                <option value="{{ $t->id }}" {{ old('template_id') == $t->id ? 'selected' : '' }}>
                  {{ $t->nombre }}
                </option>
              @endforeach
            </select>
            <div class="form-text mt-2 text-muted-opacity" style="font-size: 0.75rem;">
              Selecciona las preguntas y checklist técnicos que responderás durante la visita.
            </div>
          </div>

          <div class="col-12 mt-5 d-flex gap-3 flex-column-reverse flex-sm-row">
            <a href="{{ route('visitas.index') }}" class="btn btn-outline-secondary w-100 rounded-pill d-flex align-items-center justify-content-center gap-2" style="min-height: 48px;">
              <i class="bi bi-x-circle"></i> Cancelar
            </a>
            <button type="submit" class="btn btn-spgi w-100 rounded-pill d-flex align-items-center justify-content-center gap-2">
              <i class="bi bi-arrow-right-circle"></i> Empezar Cuestionario
            </button>
          </div>
        </div>

      </form>
    </div>

  </div>
</div>
@endsection
