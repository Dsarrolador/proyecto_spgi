@extends('layouts.app')

@section('page_title', 'Detalle de Requerimiento')

@section('content')

<style>
  .spgi-bg{ background: transparent !important; padding: 24px 0; }

  .btn-spgi{
    background: linear-gradient(135deg, var(--spgi-primary), #2563eb);
    border: 0; color: #fff !important; min-height:46px; border-radius:14px; padding:0 24px;
    font-weight:700; display:inline-flex; align-items:center; gap:8px;
    box-shadow: 0 10px 25px var(--spgi-primary-glow);
  }
  .btn-spgi:hover{ filter: brightness(1.1); transform: translateY(-1px); }

  .spgi-card{
    background: var(--bg-surface-glass); border: 1px solid var(--border-main);
    border-radius: 20px; box-shadow: var(--shadow-main); backdrop-filter: blur(16px);
    overflow: hidden; margin-bottom: 24px;
  }

  .card-head{
    padding: 20px 24px; border-bottom: 1px solid var(--border-main);
    display: flex; justify-content: space-between; align-items: center;
  }

  .card-body-spgi{ padding: 24px; }

  .info-label{ font-size: .75rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 6px; }
  .info-value{ font-size: 1.1rem; color: var(--text-main); font-weight: 600; margin-bottom: 24px; }

  .description-box{
    background: rgba(var(--text-main), 0.02); border: 1px solid var(--border-main);
    border-radius: 16px; padding: 24px; color: var(--text-main); line-height: 1.7; font-size: 1.05rem;
  }

  .badge-status{
    padding: 8px 16px; border-radius: 999px; font-weight: 700; font-size: .85rem;
    background: rgba(var(--spgi-primary), 0.1); color: var(--spgi-primary); border: 1px solid var(--border-main);
  }

  .photo-container{
    border-radius: 20px; overflow: hidden; border: 1px solid var(--border-main);
    box-shadow: var(--shadow-main); margin-top: 10px;
  }
  .photo-container img{ width: 100%; height: auto; display: block; transition: transform 0.3s ease; }
  .photo-container img:hover{ transform: scale(1.02); }

  /* Pulse button alert */
  .pulse-button {
    animation: pulse-red 2s infinite;
    box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7);
    color: #fff !important;
    background-color: #dc3545 !important;
    border-color: #dc3545 !important;
  }
  @keyframes pulse-red {
    0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7); }
    70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(220, 53, 69, 0); }
    100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
  }

  /* Pulse dot */
  .pulse-dot {
    display: inline-block;
    width: 12px;
    height: 12px;
    background-color: #dc3545;
    border-radius: 50%;
    margin-right: 8px;
    vertical-align: middle;
    animation: pulse-dot-anim 1.5s infinite;
    box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7);
  }
  @keyframes pulse-dot-anim {
    0% { transform: scale(0.9); box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7); }
    70% { transform: scale(1.2); box-shadow: 0 0 0 6px rgba(220, 53, 69, 0); }
    100% { transform: scale(0.9); box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
  }
  .glass-card-premium {
    background: var(--bg-surface-glass);
    border: 1px solid var(--border-main);
    backdrop-filter: blur(16px);
    box-shadow: var(--shadow-main);
  }
  .cursor-pointer { cursor: pointer; }
  .hover-scale { transition: transform 0.2s ease; }
  .hover-scale:hover { transform: scale(1.03); }
</style>

<div class="spgi-bg">
  <div class="container">

    @php
      $mostrarAlertaRoja = ($r->user_id === auth()->id() && $r->notas_last_user_id && $r->notas_last_user_id !== auth()->id() && !$r->notas_seen);
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
      <div>
        <h2 class="fw-800 m-0" style="color:var(--spgi-ink)">
          @if($mostrarAlertaRoja)
            <span class="pulse-dot" title="Modificado por otro usuario" id="pulse-dot-{{ $r->id }}"></span>
          @endif
          Detalle de Requerimiento
        </h2>
        <p class="text-muted m-0">Proyecto: <b>{{ $r->proyecto->nombre }}</b></p>
      </div>
      <div class="d-flex gap-2">
        @if($r->parent_id)
          <a href="{{ route('requerimientos_proyecto.show', $r->parent_id) }}" class="btn btn-outline-info rounded-pill px-4">
            <i class="bi bi-arrow-up-circle-fill me-1"></i> Requerimiento Padre (#{{ $r->parent_id }})
          </a>
        @endif
        <a href="{{ route('proyectos.show', $r->id_proyecto) }}" class="btn btn-outline-secondary rounded-pill px-4">
          <i class="bi bi-arrow-left"></i> Volver al listado
        </a>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-8">
        <div class="spgi-card">
          <div class="card-head">
            <h5 class="m-0 fw-bold"><i class="bi bi-card-text me-2"></i> Descripción</h5>
          </div>
          <div class="card-body-spgi">
            <div class="description-box mb-4">
              {{ $r->texto_imagen ?: ($r->descripcion ?: 'Sin descripción') }}
            </div>

            @if($r->foto)
              <div class="info-label">Archivo / Captura Adjunta:</div>
              <div class="photo-container">
                <img src="{{ route('storage.proxy', ['path' => $r->foto]) }}" alt="Captura">
              </div>
            @endif
          </div>
        </div>

        {{-- SECCIÓN DE SUB-REQUERIMIENTOS (Solo si no es hijo) --}}
        @if(!$r->parent_id)
        <div class="spgi-card">
          <div class="card-head d-flex justify-content-between align-items-center">
            <h5 class="m-0 fw-bold"><i class="bi bi-list-task me-2"></i> Sub-requerimientos / Sub-tareas</h5>
            <a href="{{ route('proyectos.requerimientos.create', [$r->id_proyecto, 'parent_id' => $r->id]) }}" class="btn btn-primary btn-sm rounded-pill px-3">
              <i class="bi bi-plus-lg me-1"></i> Agregar Sub-requerimiento
            </a>
          </div>
          <div class="card-body-spgi">
            @if($r->subRequerimientos->isEmpty())
              <p class="text-muted small mb-0 text-center py-4">No hay sub-requerimientos registrados para esta tarea.</p>
            @else
              <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                  <thead class="table-light">
                    <tr>
                      <th>Descripción</th>
                      <th class="text-center">Estado</th>
                      <th class="text-end">Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($r->subRequerimientos as $sub)
                    <tr>
                      <td class="small">{{ \Illuminate\Support\Str::limit($sub->texto_imagen ?: $sub->descripcion, 80) }}</td>
                      <td class="text-center">
                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle small">
                          {{ $sub->estadoRequerimiento->nombre ?? $sub->estado ?? 'Pendiente' }}
                        </span>
                      </td>
                      <td class="text-end">
                        <a href="{{ route('requerimientos_proyecto.show', $sub->id) }}" class="btn btn-sm btn-outline-primary" title="Ver">
                          <i class="bi bi-eye"></i>
                        </a>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            @endif
          </div>
        </div>
        @endif
      </div>

      <div class="col-lg-4">
        <div class="spgi-card">
          <div class="card-head">
            <h5 class="m-0 fw-bold"><i class="bi bi-info-circle me-2"></i> Información</h5>
          </div>
          <div class="card-body-spgi">
            <div class="info-label">Estado:</div>
            <div class="info-value">
              <span class="badge-status">{{ $r->estadoRequerimiento->nombre ?? $r->estado ?? 'Pendiente' }}</span>
            </div>

            <div class="info-label">Cliente:</div>
            <div class="info-value">{{ $r->cliente->nombre ?? 'N/A' }}</div>

            <div class="info-label">Contacto:</div>
            <div class="info-value">{{ $r->contacto->nombre ?? 'N/A' }}</div>

            <div class="info-label">Tipo de Soporte:</div>
            <div class="info-value">{{ $r->tipoSoporte->nombre ?? 'N/A' }}</div>

            <div class="info-label">Registrado por:</div>
            <div class="info-value">{{ $r->user->name ?? 'Sistema' }}</div>

            <div class="info-label">Fecha de Registro:</div>
            <div class="info-value">{{ optional($r->created_at)->format('d/m/Y H:i') }}</div>

            <hr class="my-4" style="opacity:.08">

            <div class="d-grid gap-2 mb-2">
              <button type="button"
                      class="btn {{ $mostrarAlertaRoja ? 'pulse-button' : 'btn-outline-info' }} fw-bold py-2 rounded-3"
                      onclick="openNotesProyectoModal({{ $r->id }}, '{{ addslashes($r->proyecto->nombre) }}')"
                      id="btn-notes-{{ $r->id }}">
                <i class="bi bi-journal-text me-1"></i> Novedades / Notas
              </button>
            </div>

            <div class="d-grid gap-2">
              <a href="{{ route('requerimientos_proyecto.edit', $r->id) }}" class="btn btn-warning text-white fw-bold py-2 rounded-3">
                <i class="bi bi-pencil-square me-1"></i> Editar Requerimiento
              </a>
              <form action="{{ route('requerimientos_proyecto.destroy', $r->id) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar este requerimiento?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger w-100 fw-bold py-2 rounded-3 mt-1">
                  <i class="bi bi-trash me-1"></i> Eliminar
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

{{-- MODAL DE NOVEDADES DINÁMICO PROYECTO --}}
<div class="modal fade" id="modalNovedadesDinamicoProyecto" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content glass-card-premium border-0 overflow-hidden" style="border-radius: 18px;">
      
      <!-- Cabecera Dinámica -->
      <div class="modal-header border-0 p-4 d-flex justify-content-between align-items-center" id="modal-header-novedades-proj" style="background: linear-gradient(135deg, #1e293b, #0f172a); transition: all 0.3s ease;">
        <div class="d-flex align-items-center gap-3">
            <button type="button" id="btn-back-dashboard-modal-proj" class="btn btn-link text-white p-0 d-none" onclick="regresarAlDashboardModalProj()">
                <i class="bi bi-arrow-left fs-4"></i>
            </button>
            <div>
                <h5 class="modal-title text-white fw-bold mb-0" id="modal-dinamico-title-proj">Novedades de Proyecto</h5>
                <small class="text-white text-opacity-75" id="modal-dinamico-project-subtitle">Proyecto: ...</small>
            </div>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body p-0">
        
        <!-- DASHBOARD DE SELECCIÓN (DENTRO DEL MODAL) -->
        <div id="modal-dashboard-novedades-proj" class="p-5 animate__animated animate__fadeIn">
            <div class="text-center mb-4">
                <h4 class="fw-bold text-gradient">¿Qué desea consultar o editar?</h4>
                <p class="text-muted">Seleccione la categoría de notas para este requerimiento</p>
            </div>
            <div class="row g-4 justify-content-center">
                <div class="col-md-5">
                    <div class="glass-card-premium p-4 text-center h-100 cursor-pointer hover-scale border-top border-4 border-primary" onclick="modalSwitchCategoryProj('interno')">
                        <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                            <i class="bi bi-shield-lock-fill fs-1 text-primary"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Notas Internas</h5>
                        <p class="small text-muted mb-0">Detalles técnicos y notas privadas para el equipo.</p>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="glass-card-premium p-4 text-center h-100 cursor-pointer hover-scale border-top border-4 border-success" onclick="modalSwitchCategoryProj('cliente')">
                        <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                            <i class="bi bi-people-fill fs-1 text-success"></i>
                        </div>
                        <h5 class="fw-bold mb-2 text-success">Novedades Clientes</h5>
                        <p class="small text-muted mb-0">Avances oficiales compartidos con el cliente.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- CONTENIDO DE NOVEDADES (LISTA + FORM) -->
        <div id="modal-content-novedades-proj" class="d-none animate__animated animate__fadeIn">
            <div class="row g-0">
                <!-- Historial -->
                <div class="col-12 col-md-7 border-end p-4 overflow-auto" style="height: 500px; background: var(--bg-master); border-color: var(--border-main) !important; padding-bottom: 100px !important;" id="modal-historial-list-proj">
                    <!-- Los items se cargan aquí -->
                </div>

                <!-- Formulario -->
                <div class="col-12 col-md-5 p-4 d-flex flex-column" style="background: var(--bg-surface); border-color: var(--border-main) !important;">
                    <h6 class="fw-bold mb-3 small text-uppercase text-muted" id="modal-form-title-proj">Agregar Seguimiento</h6>
                    <form id="modal-form-notes-proyecto" enctype="multipart/form-data" data-no-loader="true">
                        @csrf
                        <input type="hidden" name="requerimiento_proyecto_id" id="modal-notes-req-id">
                        <input type="hidden" name="cliente_id" id="modal-notes-cliente-id">
                        <input type="hidden" name="tipo" id="modal-notes-tipo">

                        <div class="mb-3">
                            <textarea name="novedad" id="modal-notes-textarea" class="form-control border-0 shadow-sm" rows="6" placeholder="Escribe aquí..." required style="border-radius: 14px; resize: none; background: var(--bg-master); color: var(--text-main);"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small text-muted">Adjuntar archivo (opcional)</label>
                            <input type="file" name="adjunto" id="modal-notes-adjunto" class="form-control form-control-sm rounded-pill">
                        </div>

                        <div id="modal-progress-container-proj" class="mb-3 d-none">
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold py-2 mt-auto" id="modal-btn-save-proj">
                            <i class="bi bi-send-fill me-1"></i> Publicar Seguimiento
                        </button>
                    </form>
                </div>
            </div>
        </div>

      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
    // --- LÓGICA DEL MODAL DINÁMICO DE NOVEDADES PROYECTO ---
    let projectNotesData = [];
    const notesModal = new bootstrap.Modal(document.getElementById('modalNovedadesDinamicoProyecto'));
    const modalHistorialListProj = document.getElementById('modal-historial-list-proj');
    const modalBtnSaveProj = document.getElementById('modal-btn-save-proj');
    const modalFormProj = document.getElementById('modal-form-notes-proyecto');
    
    window.openNotesProyectoModal = function(id, projectTitle) {
        document.getElementById('modal-dinamico-project-subtitle').innerText = "Proyecto: " + projectTitle;
        document.getElementById('modal-notes-req-id').value = id;
        
        regresarAlDashboardModalProj();
        modalHistorialListProj.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Cargando historial...</p></div>';
        
        notesModal.show();

        // Cargar datos de novedades de proyecto vía AJAX
        fetch(`/proyectos/requerimientos/${id}/novedades`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            projectNotesData = data;
            
            // Si este requerimiento tenía alerta roja, la quitamos visualmente al instante
            const btnNotes = document.getElementById(`btn-notes-${id}`);
            if (btnNotes && btnNotes.classList.contains('pulse-button')) {
                btnNotes.classList.remove('pulse-button', 'btn-danger');
                btnNotes.classList.add('btn-outline-info');
            }
            const pulseDot = document.getElementById(`pulse-dot-${id}`);
            if (pulseDot) {
                pulseDot.remove();
            }
        })
        .catch(err => {
            modalHistorialListProj.innerHTML = '<p class="text-danger p-4">Error al cargar el historial de novedades.</p>';
        });
    }

    window.modalSwitchCategoryProj = function(tipo) {
        document.getElementById('modal-dashboard-novedades-proj').classList.add('d-none');
        document.getElementById('modal-content-novedades-proj').classList.remove('d-none');
        document.getElementById('btn-back-dashboard-modal-proj').classList.remove('d-none');
        
        const header = document.getElementById('modal-header-novedades-proj');
        const formTitle = document.getElementById('modal-form-title-proj');
        const tipoInput = document.getElementById('modal-notes-tipo');
        
        tipoInput.value = tipo;
        
        if (tipo === 'interno') {
            header.style.background = 'linear-gradient(135deg, #3b82f6, #2563eb)';
            formTitle.innerText = "Agregar Nota Interna";
            formTitle.className = "fw-bold mb-3 small text-uppercase text-primary";
            modalBtnSaveProj.className = "btn btn-primary w-100 rounded-pill fw-bold py-2 mt-auto";
        } else {
            header.style.background = 'linear-gradient(135deg, #10b981, #059669)';
            formTitle.innerText = "Agregar Seguimiento Cliente";
            formTitle.className = "fw-bold mb-3 small text-uppercase text-success";
            modalBtnSaveProj.className = "btn btn-success w-100 rounded-pill fw-bold py-2 mt-auto";
        }

        renderFilteredNovedadesProj(tipo);
    }

    window.regresarAlDashboardModalProj = function() {
        document.getElementById('modal-dashboard-novedades-proj').classList.remove('d-none');
        document.getElementById('modal-content-novedades-proj').classList.add('d-none');
        document.getElementById('btn-back-dashboard-modal-proj').classList.add('d-none');
        document.getElementById('modal-header-novedades-proj').style.background = 'linear-gradient(135deg, #1e293b, #0f172a)';
    }

    function renderFilteredNovedadesProj(tipo) {
        const filtered = projectNotesData.filter(n => n.tipo === tipo);
        
        if (filtered.length === 0) {
            modalHistorialListProj.innerHTML = `
                <div class="text-center py-5 opacity-50">
                    <i class="bi bi-chat-left-dots fs-1 d-block mb-2"></i>
                    <p>No hay registros en esta categoría.</p>
                </div>`;
            return;
        }

        modalHistorialListProj.innerHTML = filtered.reverse().map(n => `
            <div class="glass-card-premium p-3 mb-3 border-0 animate__animated animate__fadeIn position-relative novelty-item-container" style="border-left: 4px solid ${tipo === 'interno' ? '#3b82f6' : '#10b981'} !important; overflow: visible !important;">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <span class="fw-bold small d-block ${tipo === 'interno' ? 'text-primary' : 'text-success'}">
                            <i class="bi ${tipo === 'interno' ? 'bi-shield-lock' : 'bi-person'} me-1"></i>
                            ${n.user_name}
                        </span>
                        <small class="text-muted" style="font-size: 0.65rem;">${n.created_at}</small>
                    </div>
                    
                    <div class="dropdown">
                        <button class="btn btn-sm btn-light rounded-circle shadow-sm p-0 border-0" type="button" data-bs-toggle="dropdown" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; background: var(--bg-surface);">
                            <i class="bi bi-three-dots-vertical fs-5 text-muted"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 p-1" style="min-width: 140px; border-radius: 12px; z-index: 1070;">
                            <li><a class="dropdown-item rounded-2 py-2" href="javascript:void(0)" onclick="editNovedadProjModal(${n.id}, \`${n.novedad.replace(/`/g, '\\`').replace(/\$\{/g, '\\${')}\`)"><i class="bi bi-pencil me-2 text-warning"></i> Editar</a></li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li><a class="dropdown-item rounded-2 py-2 text-danger" href="javascript:void(0)" onclick="deleteNovedadProjModal(${n.id}, this)"><i class="bi bi-trash me-2"></i> Eliminar</a></li>
                        </ul>
                    </div>
                </div>
                <p class="mb-2 small pe-3" style="white-space: pre-wrap; color: var(--text-main); line-height: 1.5;" id="novedad-proj-text-${n.id}">${n.novedad}</p>
                ${n.file_url ? `
                    <a href="${n.file_url}" class="btn btn-sm btn-outline-secondary py-1 px-3 rounded-pill" style="font-size: 0.7rem;">
                        <i class="bi bi-download me-1"></i> Descargar
                    </a>
                ` : ''}
            </div>
        `).join('');
    }

    if (modalFormProj) {
        modalFormProj.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const xhr = new XMLHttpRequest();
            const tipo = document.getElementById('modal-notes-tipo').value;

            modalBtnSaveProj.disabled = true;
            modalBtnSaveProj.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Publicando...';

            xhr.open('POST', '{{ route("proyectos.requerimientos.novedades.store") }}', true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

            xhr.upload.onprogress = function(e) {
                if (e.lengthComputable) {
                    const pct = Math.round((e.loaded / e.total) * 100);
                    const container = document.getElementById('modal-progress-container-proj');
                    container.classList.remove('d-none');
                    container.querySelector('.progress-bar').style.width = pct + '%';
                }
            };

            xhr.onload = function() {
                modalBtnSaveProj.disabled = false;
                modalBtnSaveProj.innerHTML = '<i class="bi bi-send-fill me-1"></i> Publicar Seguimiento';
                document.getElementById('modal-progress-container-proj').classList.add('d-none');

                if (xhr.status >= 200 && xhr.status < 300) {
                    const res = JSON.parse(xhr.responseText);
                    if (res.success) {
                        modalFormProj.reset();
                        // Actualizar data local y re-renderizar
                        projectNotesData.push({
                            id: res.novedad.id,
                            novedad: res.novedad.novedad,
                            user_name: res.user_name,
                            created_at: res.created_at,
                            file_url: res.file_url,
                            file_name: res.file_name,
                            tipo: res.tipo
                        });
                        renderFilteredNovedadesProj(tipo);
                    }
                }
            };
            xhr.send(formData);
        });
    }

    window.deleteNovedadProjModal = function(id, btn) {
        if (!confirm('¿Seguro que desea eliminar este seguimiento?')) return;
        
        fetch(`/proyectos/requerimientos/novedades/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const item = btn.closest('.novelty-item-container');
                item.classList.add('animate__animated', 'animate__fadeOutRight');
                setTimeout(() => {
                    item.remove();
                    // Actualizar data local
                    projectNotesData = projectNotesData.filter(n => n.id != id);
                }, 500);
            }
        });
    }

    window.editNovedadProjModal = function(id, currentText) {
        const newText = prompt('Editar seguimiento:', currentText);
        if (newText === null || newText.trim() === '' || newText === currentText) return;

        fetch(`/proyectos/requerimientos/novedades/${id}`, {
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
                document.getElementById(`novedad-proj-text-${id}`).innerText = newText;
                // Actualizar data local
                const idx = projectNotesData.findIndex(n => n.id == id);
                if (idx !== -1) projectNotesData[idx].novedad = newText;
            }
        });
    }
</script>
@endpush
@endsection
