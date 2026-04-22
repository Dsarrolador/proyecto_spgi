@extends('layouts.app')

@section('page_title', 'Agregar Cliente')

@section('content')
<style>
  .spgi-bg{ padding: 24px 0; }
  .spgi-title{ font-weight: 800; font-size: 1.6rem; color: var(--text-main); letter-spacing: -.5px; margin:0; }
  
  .btn-spgi{
    background: linear-gradient(135deg, var(--spgi-primary), #2563eb);
    border: 0; color: #fff !important; min-height:46px; border-radius:14px; padding:0 24px;
    box-shadow: 0 10px 25px var(--spgi-primary-glow); font-weight:700;
  }
  .btn-spgi:hover{ filter: brightness(1.1); transform: translateY(-1px); }

  .btn-soft{
    background: var(--bg-surface); color: var(--text-main); border: 1px solid var(--border-main);
    min-height:46px; border-radius:14px; padding:0 24px; font-weight:700;
  }
  .btn-soft:hover{ background: rgba(var(--spgi-primary), 0.05); }

  .spgi-card{
    background: var(--bg-surface-glass); border: 1px solid var(--border-main);
    border-radius: 20px; box-shadow: var(--shadow-main); backdrop-filter: blur(16px);
    overflow: hidden; margin-top: 24px;
  }
  .spgi-card-body{ padding: 32px; }

  .form-label{ font-weight: 800; color: var(--text-muted); font-size: .75rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; }
  .form-control, .form-select{
    background: rgba(var(--text-main), 0.02) !important; color: var(--text-main) !important;
    border-radius:12px !important; border:1px solid var(--border-main) !important;
    padding:.75rem 1rem !important; box-shadow: none !important;
  }
  .form-control:focus, .form-select:focus{ border-color: var(--spgi-primary) !important; }
</style>

<div class="spgi-bg">
  <div class="container">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
      <h3 class="spgi-title">
        <i class="bi bi-person-plus me-2"></i> Agregar Cliente
      </h3>
    </div>

    {{-- 🔹 FORMULARIO PRINCIPAL --}}
    <div class="spgi-card">
      <div class="spgi-card-body">

        <form action="{{ route('clientes.store') }}" method="POST">
          @csrf

          <div class="row">

            <!-- 🧾 Nombre -->
            <div class="col-md-6 mb-4">
              <label class="form-label">Nombre</label>
              <input type="text" name="nombre" class="form-control" required value="{{ old('nombre') }}">
            </div>

            <!-- 🆔 RNC -->
            <div class="col-md-6 mb-4">
              <label class="form-label">RNC</label>
              <input type="text" name="rnc" class="form-control" value="{{ old('rnc') }}">
            </div>

            <!-- ☎ Teléfono -->
            <div class="col-md-6 mb-4">
              <label class="form-label">Teléfono</label>
              <input type="text" name="telefono_principal" class="form-control" value="{{ old('telefono_principal') }}">
            </div>

            <!-- 🏷 Clasificación -->
            <div class="col-md-6 mb-4">
              <label class="form-label">Clasificación</label>
              <select name="clasificacion_negocio" class="form-select">
                <option value="">-- Selecciona una opción --</option>
                <option value="A" {{ old('clasificacion_negocio') == 'A' ? 'selected' : '' }}>A</option>
                <option value="B" {{ old('clasificacion_negocio') == 'B' ? 'selected' : '' }}>B</option>
                <option value="C" {{ old('clasificacion_negocio') == 'C' ? 'selected' : '' }}>C</option>
                <option value="D" {{ old('clasificacion_negocio') == 'D' ? 'selected' : '' }}>D</option>
              </select>
            </div>

            <!-- 🗂 Categoría -->
            <div class="col-md-6 mb-4">
              <label class="form-label">Categoría</label>
              <select name="clasificacion_interna" class="form-select">
                <option value="">-- Selecciona una categoría --</option>
                @foreach ($categorias as $categoria)
                  <option value="{{ $categoria->id }}" {{ (string)old('clasificacion_interna') === (string)$categoria->id ? 'selected' : '' }}>
                    {{ $categoria->categoria }}
                  </option>
                @endforeach
              </select>
            </div>

            <!-- 🧾 Categoría iguala -->
            <div class="col-md-6 mb-4">
              <label class="form-label">Categoría iguala</label>
              <select name="categoria_iguala_id" class="form-select">
                <option value="">-- Selecciona una opción --</option>
                @foreach($categoriasIguala as $plan)
                  <option value="{{ $plan->id }}" {{ old('categoria_iguala_id') == $plan->id ? 'selected' : '' }}>
                    {{ $plan->nombre }}
                  </option>
                @endforeach
              </select>
            </div>

            <!-- 📝 Notas -->
            <div class="col-md-12 mb-4">
              <label class="form-label">Notas</label>
              <textarea name="notas" class="form-control" rows="3">{{ old('notas') }}</textarea>
            </div>

            <!-- 📍 Dirección -->
            <div class="col-md-12 mb-4">
              <label class="form-label">Dirección</label>
              <input type="text" name="direccion_escrita" class="form-control" value="{{ old('direccion_escrita') }}">
            </div>

          </div>

          <!-- 🔹 BOTONES -->
          <div class="text-end mt-4">
            <a href="{{ route('clientes.index') }}" class="btn btn-soft me-2">
              <i class="bi bi-x-circle me-1"></i> Cancelar
            </a>
            <button type="submit" class="btn btn-spgi">
              <i class="bi bi-save me-1"></i> Guardar Cliente
            </button>
          </div>

        </form>

      </div>
    </div>

  </div>
</div>
@endsection
