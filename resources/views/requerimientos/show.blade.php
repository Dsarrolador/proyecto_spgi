@extends('layouts.app')

@section('page_title', 'Detalle del Requerimiento')

@section('content')

@php
    $fotoPrincipalUrl = !empty($requerimiento->foto)
        ? route('storage.proxy', ['path' => $requerimiento->foto])
        : null;
@endphp

<style>
  .spgi-page{ padding: 12px 0 24px 0; }
  .spgi-header{ display:flex; justify-content:space-between; align-items:center; gap:16px; margin-bottom:32px; flex-wrap:wrap; }

  .spgi-title{ margin:0; font-weight:900; color:var(--text-main); letter-spacing:-1px; font-size:1.8rem; }
  .spgi-subtitle{ margin:4px 0 0 0; color:var(--text-muted); font-size:1rem; }

  .spgi-btn-action, .spgi-btn-back{
    min-height:46px; border-radius:14px; padding:0 24px;
    display:inline-flex; align-items:center; justify-content:center;
    font-weight: 700; transition: all 0.3s ease;
  }
  .btn-warning.spgi-btn-action{ background: #f59e0b; color: #000; border: 0; box-shadow: 0 10px 20px rgba(245, 158, 11, 0.2); }
  .btn-warning.spgi-btn-action:hover{ background: #d97706; transform: translateY(-2px); color: #000; }
  
  .btn-secondary.spgi-btn-action, .spgi-btn-back{ 
    background: var(--bg-surface); color: var(--text-main); border: 1px solid var(--border-main);
  }
  .btn-secondary.spgi-btn-action:hover, .spgi-btn-back:hover{ background: rgba(var(--spgi-primary), 0.05); transform: translateY(-2px); }

  .spgi-card{
    background: var(--bg-surface-glass); border: 1px solid var(--border-main);
    border-radius: 24px; box-shadow: var(--shadow-main); backdrop-filter: blur(16px);
    overflow: hidden;
  }
  .spgi-card-body{ padding: 32px; }

  .spgi-section{
    background: rgba(var(--text-main), 0.04); border: 1px solid var(--border-main);
    border-radius: 20px; padding: 24px; height: 100%;
  }

  .spgi-label{
    color: var(--text-muted); font-size: .75rem; text-transform: uppercase;
    letter-spacing: 1px; font-weight: 800; margin-bottom: 8px;
  }
  .spgi-value{ color: var(--text-main); font-weight: 700; font-size: 1.05rem; }

  .spgi-text-box{
    border: 1px solid var(--border-main); border-radius: 16px;
    background: rgba(0,0,0,0.2); padding: 20px; color: var(--text-main);
    white-space: pre-wrap; line-height: 1.7; font-size: 0.95rem;
  }

  .spgi-image-wrap{ position: relative; border-radius: 20px; overflow: hidden; border: 1px solid var(--border-main); background: #000; }
  .spgi-image{ width: 100%; max-height: 520px; object-fit: contain; }

  .spgi-thumb{
    width: 140px; height: 140px; object-fit: cover; border-radius: 16px;
    border: 1px solid var(--border-main); background: var(--bg-surface);
    cursor: pointer; transition: all 0.3s ease;
  }
  .spgi-thumb:hover{ transform: scale(1.05); border-color: var(--spgi-primary); box-shadow: 0 10px 20px var(--spgi-primary-glow); }

  .spgi-btn-save{
    min-height: 52px; border-radius: 16px; padding: 0 32px;
    background: var(--spgi-primary); color: #fff; border: 0; font-weight: 800;
    box-shadow: 0 10px 25px var(--spgi-primary-glow); transition: all 0.3s ease;
  }
  .spgi-btn-save:hover{ filter: brightness(1.1); transform: translateY(-2px); }

  @media (max-width: 768px){
    .spgi-header-actions{ width: 100%; flex-direction: column; }
    .spgi-btn-action, .spgi-btn-back{ width: 100%; }
    .spgi-card-body{ padding: 20px; }
  }

  /* Timeline */
  .novedad-item{ 
    border-bottom: 1px solid var(--border-main); padding: 20px; transition: all 0.2s ease;
  }
  .novedad-item:hover{ background: rgba(var(--spgi-primary), 0.04); }
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
                  <div class="w-100">
                    <div class="spgi-label">Estado y Colaboración</div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
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

                      @if($requerimiento->es_colaborativo)
                        <span class="badge bg-info-subtle text-info border border-info-subtle spgi-badge">
                          <i class="bi bi-people-fill me-1"></i> Colaborativo
                        </span>
                      @endif
                    </div>

                    @if($requerimiento->colaboradores->count() > 0)
                      <div class="mt-3">
                        <div class="spgi-label small">Colaboradores adicionales</div>
                        <div class="d-flex flex-wrap gap-2">
                          @foreach($requerimiento->colaboradores as $colab)
                            <span class="badge border fw-normal" style="background: rgba(var(--text-main), 0.05); color: var(--text-main);">
                              <i class="bi bi-person me-1"></i> {{ $colab->name }}
                            </span>
                          @endforeach
                        </div>
                      </div>
                    @endif
                  </div>

                  <div class="text-md-end flex-shrink-0">
                    <div class="spgi-label">Responsables</div>
                    <div class="spgi-value">
                      <div class="small text-muted mb-1">Creado por:</div>
                      <i class="bi bi-person-circle me-1"></i>
                      {{ optional($requerimiento->user)->name ?? 'Sistema' }}
                    </div>
                    @if($requerimiento->asignado)
                      <div class="spgi-value mt-2">
                        <div class="small text-muted mb-1">Asignado a:</div>
                        <i class="bi bi-person-check-fill me-1 text-primary"></i>
                        {{ $requerimiento->asignado->name }}
                      </div>
                    @endif
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

                    <img
                      src="{{ $fotoPrincipalUrl }}"
                      class="spgi-image"
                      alt="Foto principal del requerimiento"
                      onclick="abrirModalImagen('{{ $fotoPrincipalUrl }}')"
                      onerror="this.style.display='none'; document.getElementById('error-foto-principal').classList.remove('d-none');"
                    >

                    <div id="error-foto-principal" class="alert alert-warning mt-2 mb-0 d-none">
                      No se pudo cargar la imagen principal desde el servidor externo.
                    </div>
                  </div>
                @endif

                @if(isset($requerimiento->imagenes) && $requerimiento->imagenes->count())
                  <div class="{{ !empty($requerimiento->foto) ? 'mt-4' : 'mt-2' }}">
                    <div class="small text-muted mb-2">Imágenes adicionales</div>

                    <div class="spgi-gallery">
                      @foreach($requerimiento->imagenes as $index => $img)
                        @php
                          $fotoAdicionalUrl = !empty($img->imagen)
                              ? route('storage.proxy', ['path' => $img->imagen])
                              : null;
                        @endphp

                        @if(!empty($img->imagen))
                          <div>
                            <img
                              src="{{ $fotoAdicionalUrl }}"
                              class="spgi-thumb"
                              alt="Imagen adicional"
                              onclick="abrirModalImagen('{{ $fotoAdicionalUrl }}')"
                              onerror="this.style.display='none'; document.getElementById('error-foto-adicional-{{ $index }}').classList.remove('d-none');"
                            >

                            <div id="error-foto-adicional-{{ $index }}" class="alert alert-warning mt-2 mb-0 d-none py-2">
                              No se pudo cargar una imagen adicional.
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

    <!-- SECCIÓN DE NOVEDADES / SEGUIMIENTOS -->
    <div id="novedades" class="mt-5">

      {{-- Alertas de seguimientos --}}
      @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert" id="alert-novedad-ok">
          <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      @endif
      @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show mb-3" role="alert">
          <i class="bi bi-exclamation-triangle me-2"></i>{{ session('warning') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      @endif
      @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
          <i class="bi bi-x-circle me-2"></i>{{ session('error') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      @endif

      <div class="spgi-card">
        <div class="spgi-card-body p-4">
          <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
            <h5 class="fw-bold mb-0" style="color: var(--text-main);">
              <i class="bi bi-journal-text me-2"></i> Seguimientos y Novedades
            </h5>
            <span class="badge bg-primary rounded-pill">{{ $requerimiento->novedades->count() }} Notas</span>
          </div>

          <!-- Listado de Novedades -->
          <div class="novedades-timeline mb-4">
            @forelse($requerimiento->novedades as $nov)
              <div class="novedad-item mb-4 pb-3 border-bottom position-relative">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 0.75rem; background: rgba(59, 130, 246, 0.15); color: #3b82f6; border: 1px solid rgba(59, 130, 246, 0.2);">
                      {{ strtoupper(substr($nov->user->name ?? 'U', 0, 1)) }}
                    </div>
                    <div>
                      <span class="fw-bold small d-block">{{ $nov->user->name ?? 'Usuario' }}</span>
                      <small class="text-muted" style="font-size: 0.7rem;">{{ $nov->created_at->format('d/m/Y h:i A') }}</small>
                    </div>
                  </div>
                </div>
                <div class="ps-1">
                  <p class="mb-2" style="white-space: pre-wrap; font-size: 0.95rem; color: var(--text-main);">{{ $nov->novedad }}</p>
                  @if($nov->adjunto)
                    <a href="{{ route('novedades.download', $nov->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 mt-1" style="font-size: 0.8rem;">
                      <i class="bi bi-download me-1"></i> {{ $nov->nombre_original ?? 'Descargar Adjunto' }}
                    </a>
                  @endif
                </div>
              </div>
            @empty
              <div class="text-center py-5 text-muted">
                <i class="bi bi-chat-left-dots fs-1 d-block mb-3 opacity-25"></i>
                <p>Aún no hay seguimientos registrados para este requerimiento.</p>
              </div>
            @endforelse
          </div>

          <!-- Formulario para agregar Novedad -->
          <div class="p-4 rounded-4 border" style="background: rgba(var(--text-main), 0.04); border-color: var(--border-main) !important;">
            <h6 class="fw-bold mb-3 small text-uppercase text-muted letter-spacing-1">Agregar Seguimiento</h6>
            <form action="{{ route('novedades.store') }}" method="POST" enctype="multipart/form-data">
              @csrf
              <input type="hidden" name="requerimiento_id" value="{{ $requerimiento->id }}">
              <input type="hidden" name="cliente_id" value="{{ $requerimiento->cliente_id }}">
              
              <div class="mb-3">
                <textarea name="novedad" class="form-control border-0" rows="4" placeholder="Escribe aquí el detalle del seguimiento..." style="border-radius: 12px; resize: none; background: var(--bg-surface); color: var(--text-main);" required></textarea>
              </div>
              
              <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="col-md-6">
                  <input type="file" name="adjunto" class="form-control form-control-sm rounded-pill px-3">
                  <div class="form-text mt-1 ms-2" style="font-size: 0.7rem;">Imagen o documento (máx 30MB)</div>
                </div>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold spgi-btn-save">
                  <i class="bi bi-send-fill me-1"></i> Publicar Seguimiento
                </button>
              </div>
            </form>
          </div>

        </div>
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