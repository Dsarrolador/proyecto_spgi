@extends('layouts.app')

@section('page_title', 'Detalle del Requerimiento')

@section('content')

@php
    $fotoPrincipalUrl = !empty($requerimiento->foto)
        ? '/storage/' . ltrim($requerimiento->foto, '/')
        : null;
@endphp

<style>
  :root{
    --spgi-primary:#0d6efd;
    --spgi-primary-2:#2b7bff;
    --spgi-ink:#0f172a;
    --spgi-muted:#64748b;
    --spgi-border: rgba(15, 23, 42, .10);
  }

  body{
    background:
      radial-gradient(900px 400px at 20% 10%, rgba(13,110,253,.14), transparent 60%),
      radial-gradient(900px 450px at 85% 20%, rgba(168,85,247,.12), transparent 55%),
      radial-gradient(900px 450px at 70% 90%, rgba(34,197,94,.10), transparent 55%),
      linear-gradient(135deg, rgba(13,110,253,.10), rgba(168,85,247,.08) 45%, rgba(34,197,94,.08));
    background-attachment: fixed;
  }

  .spgi-page{
    padding: 12px 0 24px 0;
  }

  .spgi-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:12px;
    margin-bottom:16px;
    flex-wrap:wrap;
  }

  .spgi-title{
    margin:0;
    font-weight:800;
    color:var(--spgi-ink);
    letter-spacing:-.3px;
    font-size:1.45rem;
  }

  .spgi-subtitle{
    margin:4px 0 0 0;
    color:var(--spgi-muted);
    font-size:.92rem;
  }

  .spgi-header-actions{
    display:flex;
    align-items:center;
    gap:10px;
    flex-wrap:wrap;
  }

  .spgi-btn-back,
  .spgi-btn-action{
    min-height:44px;
    border-radius:12px;
    padding:0 14px;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    white-space:nowrap;
    box-shadow: 0 10px 24px rgba(2,6,23,.07);
  }

  .spgi-card{
    background: rgba(255,255,255,.92);
    border: 1px solid var(--spgi-border);
    border-radius: 20px;
    box-shadow: 0 18px 45px rgba(2, 6, 23, .10);
    backdrop-filter: blur(6px);
    overflow: hidden;
  }

  .spgi-card-body{
    padding: 22px;
  }

  .spgi-section{
    background: rgba(255,255,255,.72);
    border: 1px solid rgba(15, 23, 42, .08);
    border-radius: 16px;
    padding: 16px;
    height: 100%;
  }

  .spgi-label{
    color: var(--spgi-muted);
    font-size: .78rem;
    text-transform: uppercase;
    letter-spacing: .4px;
    font-weight: 700;
    margin-bottom: 6px;
  }

  .spgi-value{
    color: var(--spgi-ink);
    font-weight: 600;
    word-break: break-word;
  }

  .spgi-badge{
    font-size: .82rem;
    padding: .5rem .8rem;
    border-radius: 999px;
  }

  .spgi-contact-box,
  .spgi-text-box{
    border: 1px solid rgba(15, 23, 42, .08);
    border-radius: 14px;
    background: #f8fafc;
    padding: 14px;
  }

  .spgi-text-box{
    color: var(--spgi-ink);
    white-space: pre-wrap;
  }

  .spgi-image-wrap{
    margin-top: 10px;
  }

  .spgi-image{
    width: 100%;
    max-height: 420px;
    object-fit: contain;
    border-radius: 16px;
    border: 1px solid rgba(15, 23, 42, .10);
    background: #fff;
    cursor: pointer;
    display:block;
  }

  .spgi-gallery{
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin-top: 12px;
  }

  .spgi-thumb{
    width: 160px;
    height: 160px;
    object-fit: cover;
    border-radius: 14px;
    border: 1px solid rgba(15, 23, 42, .10);
    background: #fff;
    cursor: pointer;
    transition: transform .15s ease, box-shadow .15s ease;
    display:block;
  }

  .spgi-thumb:hover{
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(2,6,23,.10);
  }

  .spgi-footer-actions{
    margin-top: 6px;
  }

  .spgi-btn-save{
    min-height: 46px;
    border-radius: 12px;
    padding: 0 16px;
    box-shadow: 0 10px 24px rgba(2,6,23,.07);
  }

  @media (max-width: 767.98px){
    .spgi-page .container{
      padding-left: 0;
      padding-right: 0;
    }

    .spgi-title{
      font-size: 1.2rem;
    }

    .spgi-header{
      align-items:stretch;
    }

    .spgi-header-actions{
      width:100%;
      flex-direction:column;
      align-items:stretch;
    }

    .spgi-btn-back,
    .spgi-btn-action{
      width:100%;
    }

    .spgi-card-body{
      padding: 14px;
    }

    .spgi-section{
      padding: 14px;
      border-radius: 14px;
    }

    .spgi-btn-save{
      width: 100%;
    }

    .spgi-thumb{
      width: 100%;
      height: auto;
      max-height: 260px;
      object-fit: contain;
    }
  }
</style>

<div class="spgi-page">
  <div class="container">

    <div class="spgi-header">
      <div>
        <h4 class="spgi-title">Detalle del Requerimiento</h4>
        <p class="spgi-subtitle">Consulta y actualiza la información del requerimiento.</p>
      </div>

      <div class="spgi-header-actions">
        <button type="button"
                class="btn btn-secondary spgi-btn-action"
                data-bs-toggle="modal"
                data-bs-target="#modalEstado">
          <i class="bi bi-flag me-1"></i> Cambiar estado
        </button>

        <a href="{{ route('requerimientos.edit', $requerimiento->id) }}"
           class="btn btn-warning spgi-btn-action">
          <i class="bi bi-pencil-square me-1"></i> Editar
        </a>

        <a href="{{ route('requerimientos.index') }}" class="btn btn-secondary spgi-btn-back">
          <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
      </div>
    </div>

    <div class="spgi-card">
      <div class="spgi-card-body">

        <form method="POST" action="{{ route('requerimientos.update', $requerimiento->id) }}">
          @csrf
          @method('PUT')

          <input type="hidden" name="cliente_id" value="{{ $requerimiento->cliente_id }}">
          <input type="hidden" name="texto_imagen" value="{{ $requerimiento->texto_imagen }}">
          <input type="hidden" name="estado_id" value="{{ $requerimiento->estado_id }}">

          <div class="row g-3 g-md-4">

            <div class="col-12 col-md-6">
              <div class="spgi-section">
                <div class="spgi-label">Cliente</div>
                <div class="spgi-value">
                  {{ optional($requerimiento->clienteRelation)->nombre ?? 'Sin cliente' }}
                </div>
              </div>
            </div>

            <div class="col-12 col-md-6">
              <div class="spgi-section h-100">
                <div class="d-flex flex-column flex-md-row justify-content-md-between align-items-md-start gap-2">
                  <div>
                    <div class="spgi-label">Estado</div>
                    @php
                      $estadoNombre = optional($requerimiento->estadoRequerimiento)->nombre ?? 'Pendiente';
                      $badge = optional($requerimiento->estadoRequerimiento)->color ?? 'bg-secondary';
                    @endphp

                    @if(is_string($badge) && \Illuminate\Support\Str::startsWith($badge, '#'))
                      <span class="badge spgi-badge" style="background-color: {{ $badge }}; color: #fff;">
                        {{ $estadoNombre }}
                      </span>
                    @else
                      <span class="badge {{ $badge }} spgi-badge">
                        {{ $estadoNombre }}
                      </span>
                    @endif
                  </div>

                  <div class="text-md-end">
                    <div class="spgi-label">Creado por</div>
                    <div class="spgi-value">
                      <i class="bi bi-person-circle me-1"></i>
                      {{ optional($requerimiento->user)->name ?? 'Sistema' }}
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-12">
              <div class="spgi-section">
                <div class="spgi-label">Tipo de soporte</div>
                <div class="spgi-value">
                  {{ optional($requerimiento->tipoSoporte)->nombre ?? 'Sin tipo de soporte' }}
                </div>
              </div>
            </div>

            <div class="col-12">
              <div class="spgi-section">
                <div class="spgi-label">Contacto</div>

                @php $contacto = $requerimiento->contactoRelation ?? null; @endphp

                @if($contacto)
                  <div class="spgi-contact-box">
                    <div class="fw-semibold">{{ $contacto->nombre }}</div>

                    <div class="small mt-2 d-flex flex-column flex-md-row gap-2 gap-md-4">
                      @if(!empty($contacto->telefono))
                        <span><i class="bi bi-telephone me-1"></i> {{ $contacto->telefono }}</span>
                      @endif

                      @if(!empty($contacto->correo))
                        <span><i class="bi bi-envelope me-1"></i> {{ $contacto->correo }}</span>
                      @endif
                    </div>
                  </div>
                @else
                  <div class="text-muted">Sin contacto asignado</div>
                @endif
              </div>
            </div>

            <div class="col-12">
              <div class="spgi-section">
                <div class="spgi-label">Requerimiento</div>
                <div class="spgi-text-box">
                  {{ $requerimiento->texto_imagen ?? 'Sin descripción' }}
                </div>
              </div>
            </div>

            <div class="col-12">
              <div class="spgi-section">
                <div class="spgi-label">Evidencias</div>

                @if(!empty($requerimiento->foto))
                  <div class="spgi-image-wrap">
                    <div class="small text-muted mb-2">Imagen principal</div>

                    @if(file_exists(storage_path('app/public/' . $requerimiento->foto)))
                      <img
                        src="{{ $fotoPrincipalUrl }}"
                        class="spgi-image"
                        alt="Foto principal del requerimiento"
                        onclick="abrirModalImagen('{{ $fotoPrincipalUrl }}')"
                        onerror="this.style.display='none'; document.getElementById('error-foto-principal').classList.remove('d-none');"
                      >

                      <div id="error-foto-principal" class="alert alert-warning mt-2 mb-0 d-none">
                        No se pudo cargar la imagen principal desde la ruta pública.
                      </div>
                    @else
                      <div class="alert alert-warning mt-2 mb-0">
                        No se encontró la imagen principal en storage/app/public/{{ $requerimiento->foto }}
                      </div>
                    @endif
                  </div>
                @endif

                @if(isset($requerimiento->imagenes) && $requerimiento->imagenes->count())
                  <div class="{{ !empty($requerimiento->foto) ? 'mt-4' : 'mt-2' }}">
                    <div class="small text-muted mb-2">Imágenes adicionales</div>

                    <div class="spgi-gallery">
                      @foreach($requerimiento->imagenes as $index => $img)
                        @php
                          $fotoAdicionalUrl = !empty($img->imagen)
                              ? '/storage/' . ltrim($img->imagen, '/')
                              : null;
                        @endphp

                        @if(!empty($img->imagen) && file_exists(storage_path('app/public/' . $img->imagen)))
                          <div>
                            <img
                              src="{{ $fotoAdicionalUrl }}"
                              class="spgi-thumb"
                              alt="Imagen adicional"
                              onclick="abrirModalImagen('{{ $fotoAdicionalUrl }}')"
                              onerror="this.style.display='none'; document.getElementById('error-foto-adicional-{{ $index }}').classList.remove('d-none');"
                            >

                            <div id="error-foto-adicional-{{ $index }}" class="alert alert-warning mt-2 mb-0 d-none py-2">
                              No se pudo cargar una imagen adicional desde la ruta pública.
                            </div>
                          </div>
                        @endif
                      @endforeach
                    </div>
                  </div>
                @endif

                @if(empty($requerimiento->foto) && (!isset($requerimiento->imagenes) || !$requerimiento->imagenes->count()))
                  <div class="text-muted mt-2">No hay imágenes adjuntas.</div>
                @endif
              </div>
            </div>

            <div class="col-12 col-md-6">
              <div class="spgi-section">
                <div class="spgi-label">Creado</div>
                <div class="spgi-value">
                  {{ optional($requerimiento->created_at)->timezone('America/Santo_Domingo')->format('d/m/Y h:i A') }}
                </div>
              </div>
            </div>

            <div class="col-12">
              <div class="spgi-footer-actions">
                <button type="submit" class="btn btn-primary spgi-btn-save">
                  <i class="bi bi-save me-1"></i> Guardar cambios
                </button>
              </div>
            </div>

          </div>
        </form>

      </div>
    </div>

  </div>
</div>

<div class="modal fade" id="modalEstado" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <form action="{{ route('requerimientos.update', $requerimiento->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="modal-header bg-secondary text-white">
          <h5 class="modal-title">Cambiar estado</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <input type="hidden" name="cliente_id" value="{{ $requerimiento->cliente_id }}">
          <input type="hidden" name="texto_imagen" value="{{ $requerimiento->texto_imagen }}">

          <label class="form-label">Seleccione el nuevo estado:</label>
          <select name="estado_id" class="form-select" required>
            @foreach($estados as $e)
              <option value="{{ $e->id }}" {{ $requerimiento->estado_id == $e->id ? 'selected' : '' }}>
                {{ $e->nombre }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success">
            <i class="bi bi-check2-circle me-1"></i> Actualizar estado
          </button>
        </div>

      </form>

    </div>
  </div>
</div>

<div class="modal fade" id="modalImagenGeneral" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Vista de imagen</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body text-center">
        <img id="imagenModalPreview" src="" alt="Vista previa" class="img-fluid rounded">
      </div>
    </div>
  </div>
</div>

<script>
  function abrirModalImagen(src) {
    const img = document.getElementById('imagenModalPreview');
    img.src = src;

    const modal = new bootstrap.Modal(document.getElementById('modalImagenGeneral'));
    modal.show();
  }
</script>

@endsection