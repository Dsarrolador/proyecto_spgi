@extends('layouts.app')

@section('page_title', 'Editar Requerimiento de Proyecto')

@section('content')

<style>
  .spgi-bg{ background: transparent !important; padding: 24px 0; }

  .spgi-card{
    background: var(--bg-surface-glass); border: 1px solid var(--border-main);
    border-radius: 24px; box-shadow: var(--shadow-main); backdrop-filter: blur(16px);
    overflow: hidden;
  }

  .card-head{
    padding: 24px; border-bottom: 1px solid var(--border-main);
    background: linear-gradient(to right, rgba(var(--spgi-primary), 0.05), transparent);
  }

  .card-body-spgi{ padding: 32px; }

  .form-label{ font-weight: 800; color: var(--text-main); margin-bottom: 10px; font-size: .85rem; text-transform: uppercase; letter-spacing: 1px; }
  .form-control, .form-select{
    background: var(--bg-surface) !important; color: var(--text-main) !important;
    border-radius:12px !important; border:1px solid var(--border-main) !important;
    padding:.75rem 1rem !important; box-shadow: none !important;
  }
  .form-control:focus, .form-select:focus{ border-color: var(--spgi-primary) !important; }

  .btn-spgi{
    background: linear-gradient(135deg, var(--spgi-primary), #2563eb);
    border: 0; color: #fff !important; min-height:50px; border-radius:14px; padding:0 32px;
    font-weight:700; display:inline-flex; align-items:center; gap:8px;
    box-shadow: 0 10px 25px var(--spgi-primary-glow);
  }
  .btn-spgi:hover{ filter: brightness(1.1); transform: translateY(-1px); }

  .btn-soft{
    background: var(--bg-surface); color: var(--text-main); border: 1px solid var(--border-main);
    min-height:50px; border-radius:14px; padding:0 24px; font-weight:700;
  }
  .btn-soft:hover{ background: rgba(var(--spgi-primary), 0.05); }

  .current-photo{
    padding: 12px; border: 1px solid var(--border-main); border-radius: 16px; background: var(--bg-surface);
    display: inline-block; margin-top: 10px; box-shadow: var(--shadow-main);
  }
  .current-photo img{ max-width: 180px; border-radius: 12px; transition: transform 0.3s ease; }
  .current-photo img:hover{ transform: scale(1.05); }
</style>

<div class="spgi-bg">
  <div class="container">

    <div class="row justify-content-center">
      <div class="col-lg-9">

        <div class="d-flex justify-content-between align-items-center mb-4">
          <div>
            <h2 class="fw-800 m-0" style="color:var(--spgi-ink)">Editar Requerimiento</h2>
            <p class="text-muted m-0">Actualiza los detalles del requerimiento para: <b>{{ $proyecto->nombre }}</b></p>
          </div>
          <a href="{{ route('proyectos.show', $proyecto->id) }}" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="bi bi-x-lg"></i> Cancelar
          </a>
        </div>

        @if ($errors->any())
          <div class="alert alert-danger mb-4 rounded-3">
            <ul class="mb-0">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <div class="spgi-card">
          <div class="card-head">
            <h5 class="m-0 fw-bold"><i class="bi bi-pencil-square me-2"></i> Formulario de Edición</h5>
          </div>

          <form action="{{ route('requerimientos_proyecto.update', $r->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="card-body-spgi">
              <div class="row g-4">
                {{-- Cliente --}}
                <div class="col-md-6">
                  <label class="form-label">Cliente</label>
                  <select name="cliente_id" class="form-select">
                    <option value="">(Seleccione si es diferente)</option>
                    @foreach($clientes as $c)
                      <option value="{{ $c->id }}" {{ $r->cliente_id == $c->id ? 'selected' : '' }}>
                        {{ $c->nombre }}
                      </option>
                    @endforeach
                  </select>
                </div>

                {{-- Tipo de Soporte --}}
                <div class="col-md-6">
                  <label class="form-label">Tipo de Soporte</label>
                  <select name="tipo_soporte_id" class="form-select">
                    @foreach($tiposSoporte as $ts)
                      <option value="{{ $ts->id }}" {{ $r->tipo_soporte_id == $ts->id ? 'selected' : '' }}>
                        {{ $ts->nombre }}
                      </option>
                    @endforeach
                  </select>
                </div>

                {{-- Estado --}}
                <div class="col-md-6">
                  <label class="form-label">Estado de Requerimiento</label>
                  <select name="estado_id" class="form-select">
                    @foreach($estados as $e)
                      <option value="{{ $e->id }}" {{ $r->estado_id == $e->id ? 'selected' : '' }}>
                        {{ $e->nombre }}
                      </option>
                    @endforeach
                  </select>
                </div>

                {{-- Descripción --}}
                <div class="col-12">
                  <label class="form-label">Descripción / Texto Requerimiento <span class="text-danger">*</span></label>
                  <textarea name="texto_imagen" rows="5" class="form-control" required placeholder="Escribe aquí el requerimiento...">{{ $r->texto_imagen ?: $r->descripcion }}</textarea>
                </div>

                {{-- Foto / Captura --}}
                <div class="col-12">
                  <label class="form-label">Archivo / Captura (Opcional)</label>
                  <input type="file" name="foto" class="form-control">
                  @if($r->foto)
                    <div class="mt-2 text-muted small">Captura actual:</div>
                    <div class="current-photo">
                      <img src="{{ route('storage.proxy', ['path' => $r->foto]) }}" alt="Captura actual">
                    </div>
                  @endif
                </div>
              </div>
            </div>

            <div class="card-footer bg-light border-top p-4 d-flex justify-content-end gap-3 rounded-bottom-20">
              <a href="{{ route('proyectos.show', $proyecto->id) }}" class="btn btn-soft">
                Cancelar
              </a>
              <button type="submit" class="btn btn-spgi">
                <i class="bi bi-save"></i> Guardar Cambios
              </button>
            </div>
          </form>
        </div>

      </div>
    </div>

  </div>
</div>

@endsection
