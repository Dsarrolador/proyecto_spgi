@extends('layouts.app')

@section('page_title', 'Editar Requerimiento de Proyecto')

@section('content')

<style>
  :root{
    --spgi-primary:#0d6efd;
    --spgi-primary-2:#2b7bff;
    --spgi-ink:#0f172a;
    --spgi-muted:#64748b;
    --spgi-border: rgba(15, 23, 42, .10);
    --shadow: 0 18px 45px rgba(2, 6, 23, .10);
  }

  .spgi-bg{ background: transparent !important; padding: 24px 0; }

  .spgi-card{
    background: rgba(255,255,255,.92); border: 1px solid var(--spgi-border);
    border-radius: 20px; box-shadow: var(--shadow); backdrop-filter: blur(8px);
    overflow: hidden;
  }

  .card-head{
    padding: 20px 24px; border-bottom: 1px solid var(--spgi-border);
    background: linear-gradient(to right, rgba(13,110,253,.05), transparent);
  }

  .card-body-spgi{ padding: 28px; }

  .form-label{ font-weight: 700; color: var(--spgi-ink); margin-bottom: 8px; font-size: .92rem; }
  .form-control, .form-select{
    border-radius: 12px; border: 1px solid rgba(15,23,42,.12);
    padding: 10px 14px; transition: all .2s;
  }
  .form-control:focus, .form-select:focus{
    border-color: var(--spgi-primary); box-shadow: 0 0 0 4px rgba(13,110,253,.08);
  }

  .btn-spgi{
    background: linear-gradient(135deg, var(--spgi-primary), var(--spgi-primary-2));
    border: 0; color: #fff !important; min-height:48px; border-radius:12px; padding:0 24px;
    font-weight:700; display:inline-flex; align-items:center; gap:8px;
    box-shadow: 0 10px 20px rgba(13,110,253,.15);
  }
  .btn-spgi:hover{ filter: brightness(.98); transform: translateY(-1px); }

  .btn-soft{
    background: #f1f5f9; color: #475569; border: 1px solid rgba(0,0,0,.04);
    min-height:48px; border-radius:12px; padding:0 24px; font-weight:700;
  }
  .btn-soft:hover{ background:#e2e8f0; }

  .current-photo{
    padding: 10px; border: 1px solid var(--spgi-border); border-radius: 12px; background: #fff;
    display: inline-block; margin-top: 10px;
  }
  .current-photo img{ max-width: 150px; border-radius: 8px; }
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
                      <img src="{{ asset('storage/' . $r->foto) }}" alt="Captura actual">
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
