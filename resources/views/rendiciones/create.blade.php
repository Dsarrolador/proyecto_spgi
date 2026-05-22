@extends('layouts.app')

@section('page_title', 'Nueva Rendición')

@section('content')
<style>
  .spgi-bg{ padding: 24px 0; }
  .glass-form{
    background: var(--bg-surface-glass); border: 1px solid var(--border-main);
    border-radius: 24px; box-shadow: var(--shadow-main); backdrop-filter: blur(20px);
    padding: 32px; max-width: 600px; margin: 0 auto;
  }
  .form-label{ font-weight: 600; color: var(--text-main); font-size: 0.9rem; }
  .form-control{
    height: 48px; border-radius: 12px; border: 1px solid var(--border-main);
    background: var(--bg-surface); color: var(--text-main); transition: all 0.2s;
  }
  .form-control:focus{
    border-color: var(--spgi-primary); box-shadow: 0 0 0 4px var(--spgi-primary-glow);
  }
  .btn-spgi{
    background: linear-gradient(135deg, #3b82f6, #2563eb); border: 0; color: #fff;
    min-height: 48px; border-radius: 14px; padding: 0 32px; font-weight: 700;
    box-shadow: 0 10px 25px rgba(59, 130, 246, 0.2); transition: all 0.3s;
  }
  .btn-spgi:hover{ transform: translateY(-2px); box-shadow: 0 15px 35px rgba(59, 130, 246, 0.3); color: #fff; }
</style>

<div class="spgi-bg">
  <div class="container">
    <div class="d-flex align-items-center justify-content-between mb-4 max-w-600 mx-auto" style="max-width: 600px;">
        <div>
            <h1 class="h3 mb-1 fw-bold text-gradient">Nueva Rendición</h1>
            <p class="text-muted mb-0">Crea un reporte agrupador para ir ingresando tus gastos.</p>
        </div>
        <a href="{{ route('rendiciones.index') }}" class="btn btn-outline-secondary rounded-pill">
            <i class="bi bi-arrow-left me-2"></i> Cancelar
        </a>
    </div>

    <div class="glass-form animate__animated animate__fadeInUp">
        <form action="{{ route('rendiciones.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Título o Descripción de la Rendición <span class="text-danger">*</span></label>
                <input type="text" name="titulo" class="form-control" placeholder="Ej. Gastos de Viaje Julio 2025 o Combustible/Uber" required value="{{ old('titulo') }}">
                @error('titulo') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="mb-4">
                <label class="form-label">Reportado por (Encargado) <span class="text-danger">*</span></label>
                <select name="user_id" class="form-select select2-user" style="height: 48px; border-radius: 12px;" required>
                    <option value="">-- Seleccione un Encargado --</option>
                    @foreach($usuarios as $u)
                        <option value="{{ $u->id }}" {{ old('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                    @endforeach
                </select>
                @error('user_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-spgi">
                    <i class="bi bi-arrow-right me-2"></i> Continuar
                </button>
            </div>
        </form>
    </div>
  </div>
</div>
@endsection
