@extends('layouts.app')

@section('page_title', 'Requerimientos del Proyecto')

@section('content')

<style>
  .spgi-bg{ background: transparent !important; padding: 24px 0; }

  .btn-spgi{
    background: linear-gradient(135deg, var(--spgi-primary), #2563eb);
    border: 0; color: #fff !important; min-height:46px; border-radius:14px; padding:0 20px;
    box-shadow: 0 10px 25px var(--spgi-primary-glow); font-weight:700;
  }
  .btn-spgi:hover{ filter: brightness(1.1); transform: translateY(-1px); }

  .btn-soft{
    background: var(--bg-surface); color: var(--text-main);
    border: 1px solid var(--border-main); border-radius: 12px;
  }
  .btn-soft:hover{ background: rgba(var(--spgi-primary), 0.05); transform: translateY(-1px); }

  .spgi-toolbar{
    background: var(--bg-surface-glass); border: 1px solid var(--border-main);
    border-radius: 20px; box-shadow: var(--shadow-main); backdrop-filter: blur(16px); padding: 20px;
  }

  .spgi-title{ font-weight: 800; font-size: 1.4rem; color: var(--text-main); letter-spacing: -.5px; margin:0; }
  .spgi-subtitle{ color: var(--text-muted); font-size: .95rem; margin-top: 4px; }

  .spgi-card{
    background: var(--bg-surface-glass); border: 1px solid var(--border-main);
    border-radius: 20px; box-shadow: var(--shadow-main); backdrop-filter: blur(16px); overflow: hidden;
  }
  .spgi-card-header{
    padding: 16px 20px; border-bottom: 1px solid var(--border-main);
    display:flex; justify-content:space-between; align-items:center; gap:12px;
  }

  .spgi-table{ width:100%; border-collapse: separate; border-spacing: 0; }
  .spgi-table thead th{
    background:#0b1220; color:#fff; font-weight:700; font-size: 0.75rem;
    text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid rgba(255,255,255,.08);
    padding: 12px 14px;
  }
  .spgi-table tbody td{
    padding: 14px; border-bottom: 1px solid var(--border-main); color: var(--text-main);
    vertical-align: middle;
  }

  .badge-soft{
    background: rgba(var(--text-main), 0.05); color: var(--text-main);
    border: 1px solid var(--border-main); font-weight: 700; border-radius: 8px;
    padding: 6px 12px; font-size: .8rem;
  }

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

  /* Pulse dot next to description */
  .pulse-dot {
    display: inline-block;
    width: 10px;
    height: 10px;
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

    {{-- TOPBAR --}}
    <div class="spgi-toolbar mb-3 d-flex justify-content-between align-items-center flex-wrap gap-3">
      <div>
        <h2 class="spgi-title">Requerimientos del Proyecto</h2>
        <div class="spgi-subtitle">
          Proyecto: <b>{{ $proyecto->nombre }}</b>
        </div>
      </div>

      <div class="toolbar-actions">
        <a href="{{ route('proyectos.index') }}" class="btn btn-soft">
          <i class="bi bi-arrow-left"></i> Volver
        </a>

        {{-- ✅ BOTÓN NUEVO: Agregar requerimiento --}}
        <a href="{{ route('proyectos.requerimientos.create', $proyecto->id) }}" class="btn btn-spgi">
          <i class="bi bi-plus-lg"></i> Agregar requerimiento
        </a>
      </div>
    </div>

    {{-- CARD LISTADO --}}
    <div class="spgi-card">
      <div class="spgi-card-header">
        <div class="fw-bold" style="color:var(--spgi-ink)">Listado</div>
        <div class="small text-muted">
          {{ method_exists($requerimientos, 'total') ? $requerimientos->total() : 0 }} registro(s)
        </div>
      </div>

      <div class="spgi-card-body">

        @if(session('success'))
          <div class="alert alert-success rounded-4">{{ session('success') }}</div>
        @endif

        @if($requerimientos->count() === 0)
          <div class="empty">
            Este proyecto no tiene requerimientos registrados.
          </div>
        @else

          <table class="spgi-table">
            <thead>
              <tr>
                <th>Descripción</th>
                <th class="col-fecha text-center">Fecha</th>
                <th class="col-estado text-center">Estado</th>
                <th class="col-acciones text-center">Acciones</th>
              </tr>
            </thead>

            <tbody>
              @foreach($requerimientos as $r)
                <tr>
                  <td class="td-ellipsis" style="{{ $r->parent_id ? 'padding-left: 36px !important;' : '' }}">
                    @php
                      $mostrarAlertaRoja = ($r->user_id === auth()->id() && $r->notas_last_user_id && $r->notas_last_user_id !== auth()->id() && !$r->notas_seen);
                    @endphp
                    @if($mostrarAlertaRoja)
                      <span class="pulse-dot" title="Modificado por otro usuario" id="pulse-dot-{{ $r->id }}"></span>
                    @endif
                    @if($r->parent_id)
                      <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle me-1" style="font-size: 0.65rem;">
                        <i class="bi bi-arrow-return-right me-1"></i> Sub-tarea (#{{ $r->parent_id }})
                      </span>
                    @endif
                    {{ $r->texto_imagen ?? $r->descripcion ?? '—' }}
                  </td>

                  <td class="text-center">
                    {{ optional($r->created_at)->format('d/m/Y') }}
                  </td>

                  <td class="text-center">
                    <span class="badge-soft">{{ $r->estado ?? 'Pendiente' }}</span>
                  </td>

                  <td class="text-center">
                    <div class="d-inline-flex gap-2 acciones">
                      {{-- Ver --}}
                      <a href="{{ route('requerimientos_proyecto.show', $r->id) }}"
                         class="btn btn-primary btn-sm" title="Ver">
                        <i class="bi bi-eye"></i>
                      </a>

                      {{-- Novedades / Notas --}}
                      @php
                        $mostrarAlertaRoja = ($r->user_id === auth()->id() && $r->notas_last_user_id && $r->notas_last_user_id !== auth()->id() && !$r->notas_seen);
                      @endphp
                      <button type="button"
                              class="btn {{ $mostrarAlertaRoja ? 'pulse-button' : 'btn-outline-info' }} btn-sm"
                              onclick="openNotesProyectoModal({{ $r->id }}, '{{ addslashes($proyecto->nombre) }}')"
                              title="Notas / Novedades"
                              id="btn-notes-{{ $r->id }}">
                        <i class="bi bi-journal-text"></i>
                      </button>

                      {{-- Editar --}}
                      <a href="{{ route('requerimientos_proyecto.edit', $r->id) }}"
                         class="btn btn-warning btn-sm" title="Editar">
                        <i class="bi bi-pencil-square"></i>
                      </a>

                      {{-- Eliminar --}}
                      <form method="POST"
                            action="{{ route('requerimientos_proyecto.destroy', $r->id) }}"
                            onsubmit="return confirm('¿Eliminar este requerimiento?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm" title="Eliminar">
                          <i class="bi bi-trash"></i>
                        </button>
                      </form>
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>

          <div class="mt-3">
            {{ $requerimientos->links() }}
          </div>

        @endif
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