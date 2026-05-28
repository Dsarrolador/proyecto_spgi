@extends('layouts.app')

@section('page_title', 'Detalle del Requerimiento de Proyecto')

@section('content')

@php
    $fotoPrincipalUrl = !empty($r->foto)
        ? route('storage.proxy', ['path' => $r->foto])
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
    background: var(--bg-surface); border: 1px solid var(--border-main);
    border-radius: 20px; padding: 24px; height: 100%;
    transition: all 0.3s ease;
  }
  .spgi-section:hover { border-color: var(--spgi-primary); background: var(--bg-surface-glass); }

  .spgi-label{
    color: var(--text-muted); font-size: .75rem; text-transform: uppercase;
    letter-spacing: 1px; font-weight: 800; margin-bottom: 8px;
  }
  .spgi-value{ color: var(--text-main); font-weight: 700; font-size: 1.05rem; }

  .spgi-text-box{
    border: 1px solid var(--border-main); border-radius: 16px;
    background: var(--bg-surface); padding: 20px; color: var(--text-main);
    white-space: pre-wrap; line-height: 1.7; font-size: 0.95rem;
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
  }

  .btn-evidence{
    min-height: 54px; border-radius: 16px; width: 100%;
    display: flex; align-items: center; justify-content: flex-start; gap: 14px;
    padding: 0 24px; border: 1px solid var(--border-main);
    background: var(--bg-surface-glass); color: var(--text-main);
    font-weight: 700; transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    box-shadow: var(--shadow-main);
  }
  .btn-evidence:hover{
    border-color: var(--spgi-primary); transform: translateY(-3px);
    background: rgba(var(--spgi-primary), 0.05); color: var(--spgi-primary);
  }
  .btn-evidence i{ font-size: 1.4rem; opacity: 0.8; }

  .no-evidence{
    padding: 24px; border: 2px dashed var(--border-main); border-radius: 20px;
    text-align: center; color: var(--text-muted); font-weight: 600;
  }

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
  .novedad-item:hover{ background: var(--bg-surface); border-radius: 16px; }
</style>

<div class="spgi-page">
  <div class="container">

    <div class="spgi-header">
      <div>
        <h4 class="spgi-title">Detalle del Requerimiento de Proyecto</h4>
        <p class="spgi-subtitle">Proyecto: <b>{{ $r->proyecto->nombre }}</b></p>
      </div>

      <div class="spgi-header-actions d-flex gap-2 gap-md-3 flex-wrap">
        <button type="button"
                class="btn btn-secondary spgi-btn-action"
                data-bs-toggle="modal"
                data-bs-target="#modalEstado">
          <i class="bi bi-flag me-1"></i> Cambiar estado
        </button>

        <a href="{{ route('requerimientos_proyecto.edit', $r->id) }}"
           class="btn btn-warning spgi-btn-action">
          <i class="bi bi-pencil-square me-1"></i> Editar
        </a>

        <a href="{{ route('proyectos.show', $r->id_proyecto) }}" class="btn btn-secondary spgi-btn-back">
          <i class="bi bi-arrow-left me-1"></i> Volver al proyecto
        </a>
      </div>
    </div>

    <div class="spgi-card">
      <div class="spgi-card-body">

        <form method="POST" action="{{ route('requerimientos_proyecto.update', $r->id) }}">
          @csrf
          @method('PUT')

          <input type="hidden" name="cliente_id" value="{{ $r->cliente_id }}">
          <input type="hidden" name="texto_imagen" value="{{ $r->texto_imagen }}">
          <input type="hidden" name="estado_id" value="{{ $r->estado_id }}">

          <div class="row g-3 g-md-4">

            <!-- CLIENTE -->
            <div class="col-12 col-md-6">
              <div class="spgi-section">
                <div class="spgi-label">Cliente</div>
                <div class="spgi-value">
                  {{ optional($r->cliente)->nombre ?? 'Sin cliente asignado' }}
                </div>
              </div>
            </div>

            <!-- ESTADO Y COLABORACIÓN -->
            <div class="col-12 col-md-6">
              <div class="spgi-section h-100">
                <div class="d-flex flex-column flex-md-row justify-content-md-between align-items-md-start gap-2">
                  <div class="w-100">
                    <div class="spgi-label">Estado y Colaboración</div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                      @php
                        $estadoNombre = optional($r->estadoRequerimiento)->nombre ?? 'Pendiente';
                        $badge = optional($r->estadoRequerimiento)->color ?? 'bg-secondary';
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

                      @if($r->es_colaborativo)
                        <span class="badge bg-info-subtle text-info border border-info-subtle spgi-badge">
                          <i class="bi bi-people-fill me-1"></i> Colaborativo
                        </span>
                      @endif
                    </div>

                    @if($r->colaboradores->count() > 0)
                      <div class="mt-3">
                        <div class="spgi-label small">Colaboradores adicionales</div>
                        <div class="d-flex flex-wrap gap-2">
                          @foreach($r->colaboradores as $colab)
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
                      {{ optional($r->user)->name ?? 'Sistema' }}
                    </div>
                    @if($r->asignado)
                      <div class="spgi-value mt-2">
                        <div class="small text-muted mb-1">Asignado a:</div>
                        <i class="bi bi-person-check-fill me-1 text-primary"></i>
                        {{ $r->asignado->name }}
                      </div>
                    @endif
                  </div>
                </div>
              </div>
            </div>

            <!-- TIPO SOPORTE -->
            <div class="col-12 col-md-6">
              <div class="spgi-section">
                <div class="spgi-label">Tipo de soporte</div>
                <div class="spgi-value">
                  {{ optional($r->tipoSoporte)->nombre ?? 'Sin tipo de soporte' }}
                </div>
              </div>
            </div>

            <!-- CONTACTO -->
            <div class="col-12 col-md-6">
              <div class="spgi-section">
                <div class="spgi-label">Contacto</div>
                @if($r->contacto)
                  <div class="spgi-contact-box">
                    <div class="fw-semibold">{{ $r->contacto->nombre }}</div>
                    <div class="small mt-2 d-flex flex-column flex-md-row gap-2 gap-md-4">
                      @if(!empty($r->contacto->telefono))
                        <span><i class="bi bi-telephone me-1"></i> {{ $r->contacto->telefono }}</span>
                      @endif
                      @if(!empty($r->contacto->correo))
                        <span><i class="bi bi-envelope me-1"></i> {{ $r->contacto->correo }}</span>
                      @endif
                    </div>
                  </div>
                @else
                  <div class="text-muted">Sin contacto asignado</div>
                @endif
              </div>
            </div>

            <!-- DESCRIPCIÓN -->
            <div class="col-12">
              <div class="spgi-section">
                <div class="spgi-label">Requerimiento</div>
                <div class="spgi-text-box">
                  {{ $r->texto_imagen ?? 'Sin descripción' }}
                </div>
              </div>
            </div>

            <!-- EVIDENCIAS -->
            <div class="col-12">
              <div class="spgi-section">
                <div class="spgi-label">Evidencias</div>
                <div class="row g-3">
                    @php
                        $hasPrincipal = !empty($r->foto);
                        $hasAdicionales = isset($r->imagenes) && $r->imagenes->count() > 0;
                    @endphp

                    @if($hasPrincipal)
                        <div class="col-12 col-md-6">
                            <button type="button" class="btn-evidence glass-card-premium" onclick="abrirModalPrincipal('{{ $fotoPrincipalUrl }}')">
                                <i class="bi bi-image-fill icon-float"></i>
                                <span>Ver imagen principal</span>
                            </button>
                        </div>
                    @endif

                    @if($hasAdicionales)
                        <div class="col-12 col-md-6">
                            <button type="button" class="btn-evidence glass-card-premium" onclick="abrirModalAdicionales()">
                                <i class="bi bi-images icon-float"></i>
                                <span>Ver imágenes adicionales</span>
                            </button>
                        </div>
                    @endif

                    @if(!$hasPrincipal && !$hasAdicionales)
                        <div class="col-12">
                            <div class="no-evidence">
                                <i class="bi bi-cloud-slash fs-3 d-block mb-2 opacity-50"></i>
                                Sin evidencias adjuntas
                            </div>
                        </div>
                    @endif
                </div>
              </div>
            </div>

            <!-- TIEMPO / REGISTRO -->
            <div class="col-12 col-md-6">
              <div class="spgi-section">
                <div class="spgi-label">Creado</div>
                <div class="spgi-value">
                  {{ optional($r->created_at)->timezone('America/Santo_Domingo')->format('d/m/Y h:i A') }}
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

    <!-- SECCIÓN DE NOVEDADES / SEGUIMIENTOS DE PROYECTO -->
    <div id="novedades" class="mt-5">

      <div class="spgi-card">
        <div class="spgi-card-body p-4">
          <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
            <h5 class="fw-bold mb-0" id="novedades-header-title" style="color: var(--text-main);">
              <i class="bi bi-journal-text me-2"></i> Seguimientos y Novedades del Proyecto
            </h5>
            <div id="header-badges">
                <span class="badge bg-success rounded-pill">{{ $r->novedades->where('tipo', 'interno')->count() }} Clientes</span>
                <span class="badge bg-primary rounded-pill">{{ $r->novedades->where('tipo', 'cliente')->count() }} Internas</span>
            </div>
            
            <button type="button" id="btn-back-to-dashboard" class="btn btn-sm btn-outline-secondary d-none rounded-pill px-3" onclick="showNovedadesDashboard()">
                <i class="bi bi-arrow-left me-1"></i> Volver al menú
            </button>
          </div>

          <!-- DASHBOARD DE SELECCIÓN DE NOVEDADES -->
          <div id="novedades-dashboard" class="row g-4 mb-4 animate__animated animate__fadeIn">
            <div class="col-md-6">
                <div class="glass-card-premium p-4 text-center h-100 cursor-pointer hover-scale" onclick="switchNovedadesCategory('cliente')" style="border-left: 5px solid #3b82f6;">
                    <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-shield-lock-fill fs-2 text-primary"></i>
                    </div>
                    <h5 class="fw-bold text-gradient mb-2">Notas Internas</h5>
                    <p class="small text-muted mb-0">Detalles técnicos, procesos y notas privadas de proyectos.</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="glass-card-premium p-4 text-center h-100 cursor-pointer hover-scale" onclick="switchNovedadesCategory('interno')" style="border-left: 5px solid #10b981;">
                    <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-people-fill fs-2 text-success"></i>
                    </div>
                    <h5 class="fw-bold text-success mb-2">Seguimientos Clientes</h5>
                    <p class="small text-muted mb-0">Avances y notificaciones compartidas con el cliente en proyectos.</p>
                </div>
            </div>
          </div>

          <!-- CONTENEDOR DE LISTADO Y FORMULARIO -->
          <div id="novedades-content-area" class="d-none">
            
            <!-- Listado de Novedades (Filtrado vía JS) -->
            <div class="novedades-timeline mb-4" id="novedades-list">
              @foreach($r->novedades->sortByDesc('created_at') as $nov)
                <div class="novedad-item mb-4 pb-3 border-bottom position-relative novelty-card" data-tipo="{{ $nov->tipo }}">
                  <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="d-flex align-items-center gap-2">
                      <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold" 
                           style="width: 32px; height: 32px; font-size: 0.75rem; 
                                  background: {{ $nov->tipo === 'cliente' ? 'rgba(59, 130, 246, 0.15)' : 'rgba(16, 185, 129, 0.15)' }}; 
                                  color: {{ $nov->tipo === 'cliente' ? '#3b82f6' : '#10b981' }}; 
                                  border: 1px solid {{ $nov->tipo === 'cliente' ? 'rgba(59, 130, 246, 0.2)' : 'rgba(16, 185, 129, 0.2)' }};">
                        {{ strtoupper(substr($nov->user->name ?? 'U', 0, 1)) }}
                      </div>
                      <div>
                        <span class="fw-bold small d-block">{{ $nov->user->name ?? 'Usuario' }}</span>
                        <small class="text-muted" style="font-size: 0.7rem;">{{ $nov->created_at->format('d/m/Y h:i A') }}</small>
                      </div>
                    </div>
                    
                    <div class="dropdown">
                      <button class="btn btn-sm btn-light rounded-circle shadow-sm p-0 border-0" type="button" data-bs-toggle="dropdown" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; background: var(--bg-surface);">
                        <i class="bi bi-three-dots-vertical fs-5 text-muted"></i>
                      </button>
                      <ul class="dropdown-menu dropdown-menu-end shadow border-0 p-1" style="min-width: 140px; border-radius: 12px; z-index: 1070;">
                        <li><a class="dropdown-item rounded-2 py-2" href="javascript:void(0)" onclick="editNovedadStatic({{ $nov->id }}, \`{{ addslashes($nov->novedad) }}\`)"><i class="bi bi-pencil me-2 text-warning"></i> Editar</a></li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li><a class="dropdown-item rounded-2 py-2 text-danger" href="javascript:void(0)" onclick="deleteNovedadStatic({{ $nov->id }}, this)"><i class="bi bi-trash me-2"></i> Eliminar</a></li>
                      </ul>
                    </div>
                  </div>
                  <div class="ps-1">
                    <p class="mb-2" style="white-space: pre-wrap; font-size: 0.95rem; color: var(--text-main);" id="novedad-static-text-{{ $nov->id }}">{{ $nov->novedad }}</p>
                    @if($nov->adjunto)
                      <a href="{{ route('proyectos-novedades.download', $nov->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 mt-1" style="font-size: 0.8rem;">
                        <i class="bi bi-download me-1"></i> {{ $nov->nombre_original ?? 'Descargar Adjunto' }}
                      </a>
                    @endif
                  </div>
                </div>
              @endforeach
              
              <div id="no-novedades-msg" class="text-center py-5 text-muted d-none">
                <i class="bi bi-chat-left-dots fs-1 d-block mb-3 opacity-25"></i>
                <p>Aún no hay seguimientos en esta categoría de proyectos.</p>
              </div>
            </div>

            <!-- Formulario para agregar Novedad de Proyecto -->
            <div id="novedad-form-wrapper" class="p-4 rounded-4 border animate__animated animate__fadeInUp" style="background: var(--bg-surface); border-color: var(--border-main) !important; box-shadow: var(--shadow-main);">
              <h6 class="fw-bold mb-3 small text-uppercase text-muted letter-spacing-1" id="form-novedad-title">Agregar Seguimiento</h6>
              <form id="form-novedad" action="{{ route('proyectos-novedades.store') }}" method="POST" enctype="multipart/form-data" data-no-loader="true">
                @csrf
                <input type="hidden" name="requerimiento_proyecto_id" value="{{ $r->id }}">
                <input type="hidden" name="cliente_id" value="{{ $r->cliente_id ?: $r->proyecto->cliente_id }}">
                <input type="hidden" name="tipo" id="input-novedad-tipo" value="cliente">
                
                <div class="mb-3">
                  <textarea name="novedad" id="textarea-novedad" class="form-control border-0" rows="4" placeholder="Escribe aquí el detalle..." style="border-radius: 12px; resize: none; background: var(--bg-surface); color: var(--text-main);" required></textarea>
                </div>
              
              <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="col-md-6">
                  <input type="file" name="adjunto" id="adjunto-novedad" class="form-control form-control-sm rounded-pill px-3">
                  <div class="form-text mt-1 ms-2" style="font-size: 0.7rem;">Imagen o documento (máx 30MB)</div>
                </div>
                <button type="submit" id="btn-submit-novedad" class="btn btn-primary rounded-pill px-4 fw-bold spgi-btn-save">
                  <i class="bi bi-send-fill me-1"></i> Publicar Seguimiento
                </button>
              </div>

              <!-- Barra de progreso AJAX -->
              <div id="progress-container-novedad" class="mt-3 d-none">
                <div class="d-flex justify-content-between mb-1">
                    <span class="small text-muted" id="progress-text">Subiendo archivos...</span>
                    <span class="small fw-bold text-primary" id="progress-percent">0%</span>
                </div>
                <div class="progress" style="height: 10px; border-radius: 10px; background: rgba(var(--text-main), 0.1);">
                  <div id="progress-bar-novedad" class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 0%"></div>
                </div>
              </div>
            </form>
          </div>
        </div>

      </div>
    </div>

  </div>
</div>

<!-- MODAL CAMBIAR ESTADO -->
<div class="modal fade" id="modalEstado" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <form action="{{ route('requerimientos_proyecto.update', $r->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="modal-header bg-secondary text-white">
          <h5 class="modal-title">Cambiar estado</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <input type="hidden" name="cliente_id" value="{{ $r->cliente_id }}">
          <input type="hidden" name="texto_imagen" value="{{ $r->texto_imagen }}">

          <label class="form-label">Seleccione el nuevo estado:</label>
          <select name="estado_id" class="form-select" required>
            @foreach($estados as $e)
              <option value="{{ $e->id }}" {{ $r->estado_id == $e->id ? 'selected' : '' }}>
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

<!-- MODAL IMÁGENES COMPLETO -->
<div class="modal fade" id="modalImagenGeneral" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-fullscreen">
    <div class="modal-content border-0" style="background: rgba(0,0,0,0.95);">
      <div class="modal-header border-0 p-4">
        <h5 class="modal-title text-white fw-bold" id="modalTitle">Vista de Evidencia</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body p-0 d-flex align-items-center justify-content-center">
        <div id="modalContentContainer" class="w-100 h-100 p-3 overflow-auto d-flex flex-column align-items-center justify-content-start gap-4">
            <!-- Content will be injected here -->
        </div>
      </div>
    </div>
  </div>
</div>

<script>
    window.deleteNovedadStatic = function(id, btn) {
        if (!confirm('¿Seguro que desea eliminar este seguimiento?')) return;
        
        fetch(`/proyectos-novedades/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const item = btn.closest('.novedad-item');
                item.classList.add('animate__animated', 'animate__fadeOutRight');
                setTimeout(() => item.remove(), 500);
            }
        });
    }

    window.editNovedadStatic = function(id, currentText) {
        const newText = prompt('Editar seguimiento:', currentText);
        if (newText === null || newText.trim() === '' || newText === currentText) return;

        fetch(`/proyectos-novedades/${id}`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ novedad: newText })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById(`novedad-static-text-${id}`).innerText = newText;
            }
        });
    }

  document.addEventListener('DOMContentLoaded', function() {
    const modalElement = document.getElementById('modalImagenGeneral');
    const modalTitle = document.getElementById('modalTitle');
    const modalContent = document.getElementById('modalContentContainer');
    let bsModal = null;

    function getModal() {
        if (!bsModal) {
            bsModal = new bootstrap.Modal(modalElement);
        }
        return bsModal;
    }

    window.abrirModalPrincipal = function(src) {
        modalTitle.innerText = "Imagen Principal";
        modalContent.innerHTML = `<img src="${src}" class="img-fluid rounded shadow-lg" style="max-height: 90vh; object-fit: contain;">`;
        getModal().show();
    }

    window.abrirModalAdicionales = function() {
        modalTitle.innerText = "Imágenes Adicionales";
        let html = '';
        @if(isset($r->imagenes))
            @foreach($r->imagenes as $img)
                @php $url = route('storage.proxy', ['path' => $img->imagen]); @endphp
                html += `<img src="{{ $url }}" class="img-fluid rounded shadow-lg mb-4" style="max-height: 85vh; object-fit: contain;">`;
            @endforeach
        @endif
        modalContent.innerHTML = html;
        getModal().show();
    }

    // --- MANEJO DE NOVEDADES VÍA AJAX ---
    const formNovedad = document.getElementById('form-novedad');
    const novedadesList = document.getElementById('novedades-list');
    const btnSubmit = document.getElementById('btn-submit-novedad');
    const progressContainer = document.getElementById('progress-container-novedad');
    const progressBar = document.getElementById('progress-bar-novedad');
    const progressPercent = document.getElementById('progress-percent');
    const novedadesDashboard = document.getElementById('novedades-dashboard');
    const contentArea = document.getElementById('novedades-content-area');
    const btnBack = document.getElementById('btn-back-to-dashboard');
    const formTitle = document.getElementById('form-novedad-title');
    const inputTipo = document.getElementById('input-novedad-tipo');
    const headerBadges = document.getElementById('header-badges');
    const headerTitle = document.getElementById('novedades-header-title');

    window.switchNovedadesCategory = function(tipo) {
        if (inputTipo) inputTipo.value = tipo;
        if (novedadesDashboard) novedadesDashboard.classList.add('d-none');
        if (contentArea) contentArea.classList.remove('d-none');
        if (btnBack) btnBack.classList.remove('d-none');
        if (headerBadges) headerBadges.classList.add('d-none');

        if (tipo === 'cliente') {
            if (headerTitle) headerTitle.innerHTML = '<i class="bi bi-shield-lock-fill me-2 text-primary"></i> Notas Internas';
            if (formTitle) formTitle.innerText = "Agregar Nota Interna";
            if (formTitle) formTitle.className = "fw-bold mb-3 small text-uppercase text-primary";
            if (btnSubmit) btnSubmit.className = "btn btn-primary rounded-pill px-4 fw-bold spgi-btn-save";
        } else {
            if (headerTitle) headerTitle.innerHTML = '<i class="bi bi-people-fill me-2 text-success"></i> Seguimientos Clientes';
            if (formTitle) formTitle.innerText = "Agregar Seguimiento Cliente";
            if (formTitle) formTitle.className = "fw-bold mb-3 small text-uppercase text-success";
            if (btnSubmit) btnSubmit.className = "btn btn-success rounded-pill px-4 fw-bold spgi-btn-save";
        }

        filterNovedadesList(tipo);
    }

    window.showNovedadesDashboard = function() {
        novedadesDashboard.classList.remove('d-none');
        contentArea.classList.add('d-none');
        btnBack.classList.add('d-none');
        headerBadges.classList.remove('d-none');
        headerTitle.innerHTML = '<i class="bi bi-journal-text me-2"></i> Seguimientos y Novedades del Proyecto';
    }

    function filterNovedadesList(tipo) {
        let visibleCount = 0;
        document.querySelectorAll('.novelty-card').forEach(card => {
            if (card.getAttribute('data-tipo') === tipo) {
                card.classList.remove('d-none');
                visibleCount++;
            } else {
                card.classList.add('d-none');
            }
        });

        const noMsg = document.getElementById('no-novedades-msg');
        if (visibleCount === 0) {
            noMsg.classList.remove('d-none');
        } else {
            noMsg.classList.add('d-none');
        }
    }

    if (formNovedad) {
        formNovedad.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const xhr = new XMLHttpRequest();
            const currentTipo = inputTipo.value;

            btnSubmit.disabled = true;
            const originalBtnHtml = btnSubmit.innerHTML;
            btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Publicando...';
            
            if (document.getElementById('adjunto-novedad').files.length > 0) {
                progressContainer.classList.remove('d-none');
            }

            xhr.open('POST', this.action, true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

            xhr.upload.onprogress = function(e) {
                if (e.lengthComputable) {
                    const percent = Math.round((e.loaded / e.total) * 100);
                    progressBar.style.width = percent + '%';
                    progressPercent.innerText = percent + '%';
                }
            };

            xhr.onload = function() {
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = originalBtnHtml;
                progressContainer.classList.add('d-none');
                progressBar.style.width = '0%';

                if (xhr.status >= 200 && xhr.status < 300) {
                    const res = JSON.parse(xhr.responseText);
                    
                    if (res.success) {
                        formNovedad.reset();
                        inputTipo.value = currentTipo;

                        const newItem = document.createElement('div');
                        newItem.className = 'novedad-item mb-4 pb-3 border-bottom position-relative novelty-card animate__animated animate__fadeIn';
                        newItem.setAttribute('data-tipo', res.tipo);
                        
                        const accentColor = res.tipo === 'interno' ? '#3b82f6' : '#10b981';
                        const accentBg = res.tipo === 'interno' ? 'rgba(59, 130, 246, 0.15)' : 'rgba(16, 185, 129, 0.15)';
                        const accentBorder = res.tipo === 'interno' ? 'rgba(59, 130, 246, 0.2)' : 'rgba(16, 185, 129, 0.2)';

                        let adjuntoHtml = '';
                        if (res.file_url) {
                            adjuntoHtml = `
                                <a href="${res.file_url}" class="btn btn-sm btn-outline-primary rounded-pill px-3 mt-1" style="font-size: 0.8rem;">
                                    <i class="bi bi-download me-1"></i> ${res.file_name}
                                </a>
                            `;
                        }

                        newItem.innerHTML = `
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-uppercase" 
                                         style="width: 32px; height: 32px; font-size: 0.75rem; background: ${accentBg}; color: ${accentColor}; border: 1px solid ${accentBorder};">
                                        ${res.user_name.charAt(0)}
                                    </div>
                                    <div>
                                        <span class="fw-bold small d-block">${res.user_name}</span>
                                        <small class="text-muted" style="font-size: 0.7rem;">${res.created_at}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="ps-1">
                                <p class="mb-2" style="white-space: pre-wrap; font-size: 0.95rem; color: var(--text-main);">${res.novedad.novedad}</p>
                                ${adjuntoHtml}
                            </div>
                        `;

                        document.getElementById('no-novedades-msg').classList.add('d-none');

                        if (novedadesList.firstChild) {
                            novedadesList.insertBefore(newItem, novedadesList.firstChild);
                        } else {
                            novedadesList.appendChild(newItem);
                        }

                        newItem.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                } else {
                    alert('Error en el servidor.');
                }
            };

            xhr.onerror = function() {
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = originalBtnHtml;
                progressContainer.classList.add('d-none');
                alert('Error de conexión. Inténtalo de nuevo.');
            };

            xhr.send(formData);
        });
    }
  });
</script>

@endsection
