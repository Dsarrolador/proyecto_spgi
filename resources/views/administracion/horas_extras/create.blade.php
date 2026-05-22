@extends('layouts.app')

@section('page_title', 'Crear Planilla de Horas Extras')

@section('content')
<style>
  .spgi-bg{ padding: 12px 0 24px 0; }
  .spgi-card{
    background: var(--bg-surface-glass); border: 1px solid var(--border-main);
    border-radius: 24px; box-shadow: var(--shadow-main); backdrop-filter: blur(16px);
    padding: 40px; max-width: 600px; margin: 0 auto;
  }
</style>

<div class="spgi-bg">
  <div class="container">
    <div class="d-flex align-items-center justify-content-between mb-4 max-width-600 mx-auto" style="max-width: 600px;">
        <div>
            <h1 class="h3 mb-1 fw-bold text-gradient">Nueva Planilla</h1>
            <p class="text-muted mb-0">Inicializa un nuevo registro de horas extras.</p>
        </div>
        <a href="{{ route('horas-extras.index') }}" class="btn btn-outline-secondary rounded-pill">
            <i class="bi bi-arrow-left me-2"></i> Cancelar
        </a>
    </div>

    <div class="spgi-card animate__animated animate__fadeInUp">
      <form action="{{ route('horas-extras.store') }}" method="POST">
        @csrf
        
        <div class="mb-4">
          <label for="titulo" class="form-label fw-bold">Título o Descripción de la Planilla</label>
          <input type="text" class="form-control @error('titulo') is-invalid @enderror" id="titulo" name="titulo" value="{{ old('titulo', 'Planilla de Horas Extras - ' . date('F Y')) }}" placeholder="Ej: Horas Extras - Mayo 2026" required>
          @error('titulo')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
          <small class="text-muted mt-1 d-block">Asigna un nombre descriptivo para identificar este reporte.</small>
        </div>

        <div class="mb-4">
          <label for="fecha_registro" class="form-label fw-bold">Fecha de Registro</label>
          <input type="date" class="form-control @error('fecha_registro') is-invalid @enderror" id="fecha_registro" name="fecha_registro" value="{{ old('fecha_registro', date('Y-m-d')) }}" required>
          @error('fecha_registro')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="d-flex gap-3 mt-5">
          <button type="submit" class="btn btn-primary rounded-pill w-100 py-2.5 fw-bold">
            <i class="bi bi-check-lg me-1"></i> Inicializar Planilla
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
