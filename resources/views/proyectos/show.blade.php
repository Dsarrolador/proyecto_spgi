@extends('layouts.app')

@section('page_title', 'Requerimientos del Proyecto')

@section('content')

<style>
  .spgi-bg{ background: transparent !important; padding: 24px 0; }

  /* Estilos para el indicador de pulso rojo */
  .pulse-dot {
    display: inline-block;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background-color: #dc3545;
    box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7);
    animation: pulse-animation 1.5s infinite;
    margin-left: 5px;
    vertical-align: middle;
  }

  .pulse-button {
    background-color: #dc3545 !important;
    color: white !important;
    border-color: #dc3545 !important;
    box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7);
    animation: pulse-animation-button 1.5s infinite;
  }

  @keyframes pulse-animation {
    0% {
      transform: scale(0.95);
      box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7);
    }
    70% {
      transform: scale(1);
      box-shadow: 0 0 0 6px rgba(220, 53, 69, 0);
    }
    100% {
      transform: scale(0.95);
      box-shadow: 0 0 0 0 rgba(220, 53, 69, 0);
    }
  }

  @keyframes pulse-animation-button {
    0% {
      box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.4);
    }
    70% {
      box-shadow: 0 0 0 6px rgba(220, 53, 69, 0);
    }
    100% {
      box-shadow: 0 0 0 0 rgba(220, 53, 69, 0);
    }
  }

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

  .toolbar-selects {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    background: var(--bg-surface-glass);
    padding: 16px;
    border-radius: 16px;
    border: 1px solid var(--border-main);
    box-shadow: var(--shadow-main);
    backdrop-filter: blur(12px);
    margin-top: 15px;
    width: 100%;
  }

  .toolbar-selects .form-select {
    flex: 1;
    min-width: 180px;
    background-color: var(--bg-surface);
    border: 1px solid rgba(255,255,255,0.1);
    color: var(--text-main);
    border-radius: 12px;
    height: 46px;
    font-size: 0.9rem;
    font-weight: 500;
  }
</style>

<div class="spgi-bg">
  <div class="container">

    {{-- TOPBAR --}}
    <div class="spgi-toolbar mb-3 d-flex flex-column gap-3">
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 w-100">
        <div>
          <h2 class="spgi-title">Requerimientos del Proyecto</h2>
          <div class="spgi-subtitle">
            Proyecto: <b>{{ $proyecto->nombre }}</b>
          </div>
        </div>

        <div class="toolbar-actions d-flex gap-2">
          <a href="{{ route('proyectos.index') }}" class="btn btn-soft d-flex align-items-center gap-2">
            <i class="bi bi-arrow-left"></i> Volver
          </a>

          <button type="button"
                  class="btn btn-soft d-flex align-items-center gap-2"
                  data-bs-toggle="modal"
                  data-bs-target="#modalFiltrosAvanzadosReqProyecto">
            <i class="bi bi-sliders"></i> Filtros avanzados
          </button>

          {{-- ✅ BOTÓN NUEVO: Agregar requerimiento --}}
          <a href="{{ route('proyectos.requerimientos.create', $proyecto->id) }}" class="btn btn-spgi d-flex align-items-center gap-2">
            <i class="bi bi-plus-lg"></i> Agregar requerimiento
          </a>
        </div>
      </div>

      {{-- FILTROS RÁPIDOS --}}
      <form action="{{ route('proyectos.show', $proyecto->id) }}" method="GET" class="toolbar-selects">
        
        <select name="estado" class="form-select" onchange="this.form.submit()">
          <option value="">Todos (sin completados)</option>
          @foreach($estados as $e)
            <option value="{{ $e->id }}" {{ (string)request('estado') === (string)$e->id ? 'selected' : '' }}>{{ $e->nombre }}</option>
          @endforeach
          <option value="Todos" {{ request('estado') == 'Todos' ? 'selected' : '' }}>Mostrar Todos</option>
          <option value="Eliminados" {{ request('estado') == 'Eliminados' ? 'selected' : '' }} style="color: #dc3545; font-weight: bold;">🗑️ Eliminados</option>
        </select>

        <select name="prioridad" class="form-select" onchange="this.form.submit()">
          <option value="">Cualquier Prioridad</option>
          <option value="5" {{ request('prioridad') == '5' ? 'selected' : '' }} style="color: #dc3545; font-weight: bold;">5 - Muy Urgente</option>
          <option value="4" {{ request('prioridad') == '4' ? 'selected' : '' }} style="color: #ffc107; font-weight: bold;">4 - Urgente</option>
          <option value="3" {{ request('prioridad') == '3' ? 'selected' : '' }}>3 - Media</option>
          <option value="2" {{ request('prioridad') == '2' ? 'selected' : '' }}>2 - Baja</option>
          <option value="1" {{ request('prioridad') == '1' ? 'selected' : '' }}>1 - Muy Baja</option>
        </select>

        <select name="asignado_id" class="form-select" onchange="this.form.submit()">
          <option value="mios" {{ request('asignado_id', 'mios') === 'mios' ? 'selected' : '' }}>Mis requerimientos</option>
          <option value="todos" {{ request('asignado_id') === 'todos' ? 'selected' : '' }}>Todos</option>
          @foreach(($usuarios ?? collect()) as $u)
            <option value="{{ $u->id }}" {{ (string)request('asignado_id') === (string)$u->id ? 'selected' : '' }}>
              {{ $u->name ?? $u->nombre ?? $u->email }}
            </option>
          @endforeach
        </select>

      </form>

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
                    @php
                      $_color = $r->estado_id == 6 ? 'bg-danger' : (optional($r->estadoRequerimiento)->color ?? 'bg-secondary');
                      $_nombre = $r->estado_id == 6 ? 'Eliminado' : (optional($r->estadoRequerimiento)->nombre ?? 'Pendiente');
                    @endphp
                    @if(\Illuminate\Support\Str::startsWith($_color, '#'))
                      <span class="badge spgi-badge" style="background-color: {{ $_color }}; color: #fff;">{{ $_nombre }}</span>
                    @else
                      <span class="badge {{ $_color }} spgi-badge">{{ $_nombre }}</span>
                    @endif
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
                              onclick="openNotesProyectoModal({{ $r->id }}, '{{ addslashes($proyecto->nombre) }}', {{ $r->cliente_id ?: ($proyecto->cliente_id ?: 'null') }})"
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

<div class="modal fade" id="modalFiltrosAvanzadosReqProyecto" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form method="GET" action="{{ route('proyectos.show', $proyecto->id) }}">
        <div class="modal-header bg-dark text-white border-0" style="border-radius: 18px 18px 0 0;">
          <h5 class="modal-title"><i class="bi bi-sliders me-2"></i>Filtros avanzados</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body p-4 bg-light">
          <div class="row g-3">
            <div class="col-12 col-md-6">
              <label class="form-label fw-bold text-muted small">Estado</label>
              <select name="estado" class="form-select shadow-sm border-0">
                <option value="">Todos</option>
                @foreach($estados as $e)
                  <option value="{{ $e->id }}" {{ (string)request('estado') === (string)$e->id ? 'selected' : '' }}>{{ $e->nombre }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label fw-bold text-muted small">Prioridad</label>
              <select name="prioridad" class="form-select shadow-sm border-0">
                <option value="">Cualquier Prioridad</option>
                <option value="5" {{ request('prioridad') == '5' ? 'selected' : '' }} class="text-danger fw-bold">5 - Muy Urgente</option>
                <option value="4" {{ request('prioridad') == '4' ? 'selected' : '' }} class="text-warning fw-bold">4 - Urgente</option>
                <option value="3" {{ request('prioridad') == '3' ? 'selected' : '' }}>3 - Media</option>
                <option value="2" {{ request('prioridad') == '2' ? 'selected' : '' }}>2 - Baja</option>
                <option value="1" {{ request('prioridad') == '1' ? 'selected' : '' }}>1 - Muy Baja</option>
              </select>
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label fw-bold text-muted small">Asignado a</label>
              <select name="asignado_id" class="form-select shadow-sm border-0">
                <option value="">Cualquiera</option>
                <option value="mios" {{ request('asignado_id', 'mios') === 'mios' ? 'selected' : '' }}>Mis requerimientos</option>
                <option value="todos" {{ request('asignado_id') === 'todos' ? 'selected' : '' }}>Todos</option>
                @foreach(($usuarios ?? collect()) as $u)
                  <option value="{{ $u->id }}" {{ (string)request('asignado_id') === (string)$u->id ? 'selected' : '' }}>
                    {{ $u->name }}
                  </option>
                @endforeach
              </select>
            </div>

            @if(auth()->user()->es_administrativo)
            <div class="col-12 col-md-6">
              <label class="form-label fw-bold text-muted small">Facturación</label>
              <select name="facturado" class="form-select shadow-sm border-0">
                <option value="">Todos</option>
                <option value="1" {{ request('facturado') === '1' ? 'selected' : '' }}>Facturados</option>
                <option value="0" {{ request('facturado') === '0' ? 'selected' : '' }}>No facturados</option>
              </select>
            </div>
            @endif

            <div class="col-12 col-md-6">
              <label class="form-label fw-bold text-muted small">Desde</label>
              <input type="date" name="desde" class="form-control shadow-sm border-0" value="{{ request('desde') }}">
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label fw-bold text-muted small">Hasta</label>
              <input type="date" name="hasta" class="form-control shadow-sm border-0" value="{{ request('hasta') }}">
            </div>
          </div>
        </div>

        <div class="modal-footer bg-white border-0 flex-column flex-sm-row rounded-bottom-4">
          <a href="{{ route('proyectos.show', $proyecto->id) }}" class="btn btn-light w-100 w-sm-auto rounded-pill px-4">Limpiar</a>
          <button type="submit" class="btn btn-primary w-100 w-sm-auto rounded-pill px-4 shadow-sm">Aplicar Filtros</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- MODAL DE NOVEDADES DE PROYECTO DINÁMICO (CATEGORIZADO) -->
<div class="modal fade" id="modalNovedadesProyectoDinamico" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content glass-card-premium border-0 overflow-hidden">
      
      <!-- Cabecera Dinámica -->
      <div class="modal-header border-0 p-4 d-flex justify-content-between align-items-center" id="modal-header-proyecto-novedades" style="background: linear-gradient(135deg, var(--spgi-primary), #2563eb); transition: all 0.3s ease;">
        <div class="d-flex align-items-center gap-3">
            <button type="button" id="btn-back-dashboard-proyecto-modal" class="btn btn-link text-white p-0 d-none" onclick="regresarAlDashboardProyectoModal()">
                <i class="bi bi-arrow-left fs-4"></i>
            </button>
            <div>
                <h5 class="modal-title text-white fw-bold mb-0" id="modal-proyecto-dinamico-title">Novedades de Proyecto</h5>
                <small class="text-white text-opacity-75" id="modal-proyecto-dinamico-client">Proyecto</small>
            </div>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body p-0">
        
        <!-- DASHBOARD DE SELECCIÓN -->
        <div id="modal-dashboard-proyecto-novedades" class="p-5 animate__animated animate__fadeIn">
            <div class="text-center mb-4">
                <h4 class="fw-bold text-gradient">¿Qué desea consultar?</h4>
                <p class="text-muted">Seleccione el tipo de seguimiento para continuar</p>
            </div>
            <div class="row g-4 justify-content-center">
                <div class="col-md-5">
                    <div class="glass-card-premium p-4 text-center h-100 cursor-pointer hover-scale border-top border-4 border-primary" onclick="modalSwitchProyectoCategory('cliente')">
                        <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                            <i class="bi bi-shield-lock-fill fs-1 text-primary"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Notas Internas</h5>
                        <p class="small text-muted mb-0">Detalles técnicos y notas privadas para el equipo.</p>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="glass-card-premium p-4 text-center h-100 cursor-pointer hover-scale border-top border-4 border-success" onclick="modalSwitchProyectoCategory('interno')">
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
        <div id="modal-content-proyecto-novedades" class="d-none animate__animated animate__fadeIn">
            <div class="row g-0">
                <!-- Historial -->
                <div class="col-12 col-md-7 border-end p-4 overflow-auto" style="height: 500px; background: var(--bg-master); border-color: var(--border-main) !important; padding-bottom: 100px !important;" id="modal-proyecto-historial-list">
                    <!-- Los items se cargan aquí -->
                </div>

                <!-- Formulario -->
                <div class="col-12 col-md-5 p-4 d-flex flex-column" style="background: var(--bg-surface); border-color: var(--border-main) !important;">
                    <h6 class="fw-bold mb-3 small text-uppercase text-muted" id="modal-proyecto-form-title">Agregar Seguimiento</h6>
                    <form id="modal-form-novedad-proyecto-dinamico" enctype="multipart/form-data" data-no-loader="true">
                        @csrf
                        <input type="hidden" name="requerimiento_proyecto_id" id="modal-proyecto-req-id">
                        <input type="hidden" name="cliente_id" id="modal-proyecto-cliente-id">
                        <input type="hidden" name="tipo" id="modal-proyecto-tipo-input">

                        <div class="mb-3">
                            <textarea name="novedad" class="form-control border-0 shadow-sm" rows="6" placeholder="Escribe aquí..." required style="border-radius: 14px; resize: none; background: var(--bg-master); color: var(--text-main);"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small text-muted">Adjuntar archivo (opcional)</label>
                            <input type="file" name="adjunto" id="modal-proyecto-adjunto-input" class="form-control form-control-sm rounded-pill">
                        </div>

                        <div id="modal-proyecto-progress-container" class="mb-3 d-none">
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold py-2 mt-auto" id="modal-proyecto-btn-save">
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
    let currentProyectoNovedadesData = [];
    const modalProyectoDinamico = new bootstrap.Modal(document.getElementById('modalNovedadesProyectoDinamico'));
    const modalProyectoHistorialList = document.getElementById('modal-proyecto-historial-list');
    const modalProyectoBtnSave = document.getElementById('modal-proyecto-btn-save');
    const modalProyectoForm = document.getElementById('modal-form-novedad-proyecto-dinamico');
    
    window.openNotesProyectoModal = function(id, projectName, clientId) {
        document.getElementById('modal-proyecto-dinamico-title').innerText = "Novedades de:";
        document.getElementById('modal-proyecto-dinamico-client').innerText = projectName;
        document.getElementById('modal-proyecto-req-id').value = id;
        document.getElementById('modal-proyecto-cliente-id').value = clientId || '';
        
        regresarAlDashboardProyectoModal();
        modalProyectoHistorialList.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Cargando historial...</p></div>';
        
        modalProyectoDinamico.show();

        // Cargar datos vía AJAX
        fetch(`/proyectos-novedades/${id}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            currentProyectoNovedadesData = data;
            // Si hay un dot de notificaciones activo, podemos marcarlo como visto
            const dot = document.getElementById(`pulse-dot-${id}`);
            if (dot) dot.remove();
            const btn = document.getElementById(`btn-notes-${id}`);
            if (btn) {
                btn.className = 'btn btn-outline-info btn-sm';
            }
        })
        .catch(err => {
            modalProyectoHistorialList.innerHTML = '<p class="text-danger p-4">Error al cargar el historial.</p>';
        });
    }

    window.modalSwitchProyectoCategory = function(tipo) {
        document.getElementById('modal-dashboard-proyecto-novedades').classList.add('d-none');
        document.getElementById('modal-content-proyecto-novedades').classList.remove('d-none');
        document.getElementById('btn-back-dashboard-proyecto-modal').classList.remove('d-none');
        
        const header = document.getElementById('modal-header-proyecto-novedades');
        const formTitle = document.getElementById('modal-proyecto-form-title');
        const tipoInput = document.getElementById('modal-proyecto-tipo-input');
        
        tipoInput.value = tipo;
        
        if (tipo === 'cliente') {
            header.style.background = 'linear-gradient(135deg, #3b82f6, #2563eb)';
            formTitle.innerText = "Agregar Nota Interna";
            formTitle.className = "fw-bold mb-3 small text-uppercase text-primary";
            modalProyectoBtnSave.className = "btn btn-primary w-100 rounded-pill fw-bold py-2 mt-auto";
        } else {
            header.style.background = 'linear-gradient(135deg, #10b981, #059669)';
            formTitle.innerText = "Agregar Seguimiento Cliente";
            formTitle.className = "fw-bold mb-3 small text-uppercase text-success";
            modalProyectoBtnSave.className = "btn btn-success w-100 rounded-pill fw-bold py-2 mt-auto";
        }

        renderFilteredProyectoNovedades(tipo);
    }

    window.regresarAlDashboardProyectoModal = function() {
        document.getElementById('modal-dashboard-proyecto-novedades').classList.remove('d-none');
        document.getElementById('modal-content-proyecto-novedades').classList.add('d-none');
        document.getElementById('btn-back-dashboard-proyecto-modal').classList.add('d-none');
        document.getElementById('modal-header-proyecto-novedades').style.background = 'linear-gradient(135deg, #1e293b, #0f172a)';
    }

    function renderFilteredProyectoNovedades(tipo) {
        const filtered = currentProyectoNovedadesData.filter(n => n.tipo === tipo);
        
        if (filtered.length === 0) {
            modalProyectoHistorialList.innerHTML = `
                <div class="text-center py-5 opacity-50">
                    <i class="bi bi-chat-left-dots fs-1 d-block mb-2"></i>
                    <p>No hay registros en esta categoría.</p>
                </div>`;
            return;
        }

        modalProyectoHistorialList.innerHTML = filtered.reverse().map(n => `
            <div class="glass-card-premium p-3 mb-3 border-0 animate__animated animate__fadeIn position-relative novelty-item-container" style="border-left: 4px solid ${tipo === 'cliente' ? '#3b82f6' : '#10b981'} !important; overflow: visible !important;">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <span class="fw-bold small d-block ${tipo === 'cliente' ? 'text-primary' : 'text-success'}">
                            <i class="bi ${tipo === 'cliente' ? 'bi-shield-lock' : 'bi-person'} me-1"></i>
                            ${n.user_name}
                        </span>
                        <small class="text-muted" style="font-size: 0.65rem;">${n.created_at}</small>
                    </div>
                    
                    <div class="dropdown">
                        <button class="btn btn-sm btn-light rounded-circle shadow-sm p-0 border-0" type="button" data-bs-toggle="dropdown" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; background: var(--bg-surface);">
                            <i class="bi bi-three-dots-vertical fs-5 text-muted"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 p-1 animate__animated animate__fadeIn" style="min-width: 140px; border-radius: 12px; z-index: 1070;">
                            <li><a class="dropdown-item rounded-2 py-2" href="javascript:void(0)" onclick="editProyectoNovedadModal(${n.id}, \`${n.novedad.replace(/`/g, '\\`').replace(/\$\{/g, '\\${')}\`)"><i class="bi bi-pencil me-2 text-warning"></i> Editar</a></li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li><a class="dropdown-item rounded-2 py-2 text-danger" href="javascript:void(0)" onclick="deleteProyectoNovedadModal(${n.id}, this)"><i class="bi bi-trash me-2"></i> Eliminar</a></li>
                        </ul>
                    </div>
                </div>
                <p class="mb-2 small pe-3" style="white-space: pre-wrap; color: var(--text-main); line-height: 1.5;" id="proyecto-novedad-text-${n.id}">${n.novedad}</p>
                ${n.file_url ? `
                    <a href="${n.file_url}" class="btn btn-sm btn-outline-secondary py-1 px-3 rounded-pill" style="font-size: 0.7rem;">
                        <i class="bi bi-download me-1"></i> Descargar
                    </a>
                ` : ''}
            </div>
        `).join('');
    }

    if (modalProyectoForm) {
        modalProyectoForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const xhr = new XMLHttpRequest();
            const tipo = document.getElementById('modal-proyecto-tipo-input').value;

            modalProyectoBtnSave.disabled = true;
            modalProyectoBtnSave.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Publicando...';

            xhr.open('POST', '{{ route("proyectos-novedades.store") }}', true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

            xhr.upload.onprogress = function(e) {
                if (e.lengthComputable) {
                    const pct = Math.round((e.loaded / e.total) * 100);
                    const container = document.getElementById('modal-proyecto-progress-container');
                    container.classList.remove('d-none');
                    container.querySelector('.progress-bar').style.width = pct + '%';
                }
            };

            xhr.onload = function() {
                modalProyectoBtnSave.disabled = false;
                modalProyectoBtnSave.innerHTML = '<i class="bi bi-send-fill me-1"></i> Publicar Seguimiento';
                document.getElementById('modal-proyecto-progress-container').classList.add('d-none');

                if (xhr.status >= 200 && xhr.status < 300) {
                    const res = JSON.parse(xhr.responseText);
                    if (res.success) {
                        modalProyectoForm.reset();
                        currentProyectoNovedadesData.push({
                            id: res.novedad.id,
                            novedad: res.novedad.novedad,
                            user_name: res.user_name,
                            created_at: res.created_at,
                            file_url: res.file_url,
                            file_name: res.file_name,
                            tipo: res.tipo
                        });
                        renderFilteredProyectoNovedades(tipo);
                    }
                }
            };
            xhr.send(formData);
        });
    }

    window.deleteProyectoNovedadModal = function(id, btn) {
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
                const item = btn.closest('.novelty-item-container');
                item.classList.add('animate__animated', 'animate__fadeOutRight');
                setTimeout(() => {
                    item.remove();
                    currentProyectoNovedadesData = currentProyectoNovedadesData.filter(n => n.id != id);
                }, 500);
            }
        });
    }

    window.editProyectoNovedadModal = function(id, currentText) {
        const newText = prompt('Editar seguimiento:', currentText);
        if (newText === null || newText.trim() === '' || newText === currentText) return;

        fetch(`/proyectos-novedades/${id}`, {
            method: 'PATCH',
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
                document.getElementById(`proyecto-novedad-text-${id}`).innerText = newText;
                const idx = currentProyectoNovedadesData.findIndex(n => n.id == id);
                if (idx !== -1) currentProyectoNovedadesData[idx].novedad = newText;
            }
        });
    }
</script>
@endpush

@endsection