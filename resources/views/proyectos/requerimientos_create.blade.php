@extends('layouts.app')

@section('page_title', 'Nuevo Requerimiento')

@section('content')

<style>
  .spgi-bg{ background: transparent !important; padding: 24px 0; }

  .spgi-toolbar{
    background: var(--bg-surface-glass); border: 1px solid var(--border-main);
    border-radius: 20px; box-shadow: var(--shadow-main); backdrop-filter: blur(16px); padding: 20px;
  }

  .spgi-title{ font-weight: 800; font-size: 1.4rem; color: var(--text-main); letter-spacing: -.5px; margin:0; }
  .spgi-subtitle{ color: var(--text-muted); font-size: .95rem; margin-top: 4px; }

  .btn-spgi{
    background: linear-gradient(135deg, var(--spgi-primary), #2563eb);
    border: 0; color: #fff !important; min-height:46px; border-radius:14px; padding:0 24px;
    box-shadow: 0 10px 25px var(--spgi-primary-glow); font-weight:700;
  }
  .btn-spgi:hover{ filter: brightness(1.1); transform: translateY(-1px); }

  .btn-soft{
    background: var(--bg-surface); color: var(--text-main);
    border: 1px solid var(--border-main); border-radius: 12px;
  }
  .btn-soft:hover{ background: rgba(var(--spgi-primary), 0.05); transform: translateY(-1px); }

  .spgi-card{
    background: var(--bg-surface-glass); border: 1px solid var(--border-main);
    border-radius: 20px; box-shadow: var(--shadow-main); backdrop-filter: blur(16px); overflow: hidden;
  }
  .spgi-card-header{
    padding: 20px; border-bottom: 1px solid var(--border-main);
    display:flex; justify-content:space-between; align-items:center; gap:12px;
  }
  .spgi-card-body{ padding: 24px; }

  .spgi-label{ font-size: .75rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; }

  .spgi-control{
    background: var(--bg-surface) !important; color: var(--text-main) !important;
    border-radius:12px !important; border:1px solid var(--border-main) !important;
    padding:.75rem 1rem !important; box-shadow: none !important;
  }
  .spgi-control:focus{ border-color: var(--spgi-primary) !important; }
</style>
</style>

<div class="spgi-bg">
  <div class="container">

    {{-- TOPBAR --}}
    <div class="spgi-toolbar mb-3 d-flex justify-content-between align-items-center flex-wrap gap-3">
      <div>
        <h2 class="spgi-title">Nuevo Requerimiento</h2>
        <div class="spgi-subtitle">
          Proyecto: <b>{{ $proyecto->nombre }}</b>
        </div>
      </div>

      <div class="toolbar-actions">
        {{-- ✅ vuelve al listado de requerimientos del proyecto --}}
        <a href="{{ route('proyectos.requerimientos.index', $proyecto->id) }}" class="btn btn-soft">
          <i class="bi bi-arrow-left"></i> Volver
        </a>

        <button form="form-req" type="submit" class="btn btn-spgi">
          <i class="bi bi-save"></i> Guardar
        </button>
      </div>
    </div>

    {{-- FORM CARD --}}
    <div class="spgi-card">
      <div class="spgi-card-header">
        <strong>Datos del Requerimiento</strong>
        <div class="small text-muted">El cliente y contacto se heredarán automáticamente del proyecto.</div>
      </div>

      <div class="spgi-card-body">

        @if ($errors->any())
          <div class="alert alert-danger rounded-4">
            <ul class="mb-0">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        @if(isset($parent))
          <div class="alert alert-info rounded-4 mb-4">
            <i class="bi bi-info-circle-fill me-1"></i> Creando este requerimiento como una sub-tarea del requerimiento: <strong>#{{ $parent->id }} - {{ \Illuminate\Support\Str::limit($parent->texto_imagen ?: $parent->descripcion, 100) }}</strong>
          </div>
        @endif

        <form id="form-req"
              method="POST"
              action="{{ route('proyectos.requerimientos.store', $proyecto->id) }}"
              enctype="multipart/form-data">
          @csrf
          @if(isset($parent))
            <input type="hidden" name="parent_id" value="{{ $parent->id }}">
          @endif

          <div class="row g-3">

            <div class="col-12">
              <label class="spgi-label">Descripción</label>
              <textarea name="texto_imagen"
                        class="form-control spgi-control"
                        rows="6"
                        required>{{ old('texto_imagen') }}</textarea>
            </div>

            <div class="col-md-4">
              <label class="spgi-label">Estado</label>
              <select name="estado_id" class="form-select spgi-control">
                @foreach(($estados ?? collect()) as $e)
                  <option value="{{ $e->id }}" {{ old('estado_id', 1) == $e->id ? 'selected' : '' }}>{{ $e->nombre }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-md-8">
              <label class="spgi-label">Adjunto (foto/pdf)</label>
              <input type="file" name="foto" class="form-control spgi-control" accept="image/*,application/pdf">
              <div class="text-muted small mt-1">Máx 30MB. Formatos: jpg, png, webp, pdf.</div>
            </div>

          </div>
        </form>

      </div>
    </div>

  </div>
</div>

@endsection