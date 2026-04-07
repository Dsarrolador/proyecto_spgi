@extends('layouts.app')

@section('content')
<div class="container py-4">

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0">Nuevo tipo de soporte</h4>
    <a href="{{ route('mantenimiento.tipo-soporte.index') }}" class="btn btn-secondary">
      <i class="bi bi-arrow-left"></i> Volver
    </a>
  </div>

  @if ($errors->any())
    <div class="alert alert-danger">
      <b>Hay errores:</b>
      <ul class="mb-0">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="card shadow-sm">
    <div class="card-body">

      <form method="POST" action="{{ route('mantenimiento.tipo-soporte.store') }}">
        @csrf

        <div class="mb-3">
          <label class="form-label fw-semibold">Nombre</label>
          <input type="text"
                 name="nombre"
                 value="{{ old('nombre') }}"
                 class="form-control @error('nombre') is-invalid @enderror"
                 placeholder="Ej: Soporte técnico hardware básico interno"
                 required>
          @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
          <label class="form-label fw-semibold">Descripción (opcional)</label>
          <textarea name="descripcion"
                    class="form-control @error('descripcion') is-invalid @enderror"
                    rows="3">{{ old('descripcion') }}</textarea>
          @error('descripcion') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="form-check mb-3">
          <input class="form-check-input"
                 type="checkbox"
                 name="activo"
                 id="activo"
                 value="1"
                 {{ old('activo', 1) ? 'checked' : '' }}>
          <label class="form-check-label" for="activo">Activo</label>
        </div>

        <button type="submit" class="btn btn-primary">
          <i class="bi bi-save me-1"></i> Guardar
        </button>

      </form>

    </div>
  </div>

</div>
@endsection
