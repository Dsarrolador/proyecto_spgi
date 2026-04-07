@extends('layouts.app')

@section('content')
<div class="container py-4">

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0">Editar tipo de soporte</h4>
    <a href="{{ route('mantenimiento.tipo-soporte.index') }}" class="btn btn-secondary">
      <i class="bi bi-arrow-left"></i> Volver
    </a>
  </div>

  <div class="card shadow-sm">
    <div class="card-body">
      <form method="POST" action="{{ route('mantenimiento.tipo-soporte.update', $tipo->id) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
          <label class="form-label fw-semibold">Nombre</label>
          <input type="text" name="nombre"
                 value="{{ old('nombre', $tipo->nombre) }}"
                 class="form-control @error('nombre') is-invalid @enderror">
          @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
          <label class="form-label fw-semibold">Descripción</label>
          <textarea name="descripcion" class="form-control" rows="3">{{ old('descripcion', $tipo->descripcion) }}</textarea>
        </div>

        <div class="form-check mb-3">
          <input class="form-check-input" type="checkbox" name="activo" id="activo"
                 {{ old('activo', $tipo->activo) ? 'checked' : '' }}>
          <label class="form-check-label" for="activo">Activo</label>
        </div>

        <button class="btn btn-primary">
          <i class="bi bi-save me-1"></i> Actualizar
        </button>
      </form>
    </div>
  </div>

</div>
@endsection
