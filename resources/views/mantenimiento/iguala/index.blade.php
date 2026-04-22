@extends('layouts.app')

@section('page_title', 'Mantenimiento: Planes de Iguala')

@section('content')
<div class="container-fluid py-4">

  <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h4 class="fw-bold mb-1">Catálogo de Igualas</h4>
        <p class="text-muted small mb-0">Configuración de planes de soporte y mantenimiento para clientes.</p>
    </div>

    <button class="btn btn-spgi" data-bs-toggle="modal" data-bs-target="#modalNuevo">
      <i class="bi bi-plus-lg me-2"></i> Nuevo Plan
    </button>
  </div>

  @if(session('success'))
    <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4" style="background: rgba(34, 197, 94, 0.1); color: #22c55e;">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
    </div>
  @endif

  @if($errors->any())
    <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4">
      <ul class="mb-0 small fw-bold">
        @foreach($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="spgi-card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-spgi mb-0 align-middle">
          <thead>
            <tr>
              <th style="width:80px;">ID</th>
              <th>Información del Plan</th>
              <th class="text-center">Soporte/Visitas</th>
              <th>Servicios Incluidos</th>
              <th style="width:100px;" class="text-center">Estado</th>
              <th style="width:200px;" class="text-center">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @forelse($igualas as $i)
              <tr>
                <td class="text-muted fw-mono">#{{ $i->id }}</td>
                <td>
                  <div class="fw-bold fs-6">{{ $i->nombre }}</div>
                  <div class="text-muted small d-block" style="max-width:300px;">{{ Str::limit($i->descripcion, 60) }}</div>
                </td>
                <td class="text-center">
                    <div class="d-flex flex-column gap-1 align-items-center">
                        <span class="badge rounded-pill px-3 py-2" style="background: rgba(59, 130, 246, 0.1); color: var(--spgi-primary); border: 1px solid rgba(59, 130, 246, 0.2);">
                            <i class="bi bi-headset me-1"></i> {{ $i->cantidad_soporte_remoto == -1 ? '∞ Remoto' : $i->cantidad_soporte_remoto . ' Remotos' }}
                        </span>
                        <span class="badge rounded-pill px-3 py-2" style="background: rgba(var(--spgi-primary-rgb, 59, 130, 246), 0.05); color: var(--text-muted); border: 1px solid var(--border-main);">
                            <i class="bi bi-geo-alt me-1"></i> {{ $i->cantidad_visitas == -1 ? '∞ Visitas' : $i->cantidad_visitas . ' Visitas' }}
                        </span>
                    </div>
                </td>
                <td>
                  <div class="d-flex flex-wrap gap-2">
                    @if($i->mantenimiento_sw_hw)
                        <span class="badge rounded-pill px-2 py-1" style="background: rgba(255,255,255,0.05); border: 1px solid var(--border-main); color: var(--text-main);">
                            <i class="bi bi-cpu text-primary me-1"></i> Mant.
                        </span>
                    @endif
                    @if($i->equipo_prestamo)
                        <span class="badge rounded-pill px-2 py-1" style="background: rgba(255,255,255,0.05); border: 1px solid var(--border-main); color: var(--text-main);">
                            <i class="bi bi-laptop text-primary me-1"></i> Préstamo
                        </span>
                    @endif
                    @if($i->asistencia_vip)
                        <span class="badge rounded-pill px-2 py-1" style="background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); color: #f59e0b;">
                            <i class="bi bi-star-fill me-1"></i> VIP
                        </span>
                    @endif
                    @if(!$i->mantenimiento_sw_hw && !$i->equipo_prestamo && !$i->asistencia_vip)
                        <span class="text-muted small italic">Base únicamente</span>
                    @endif
                  </div>
                </td>
                <td class="text-center">
                  @if($i->activo)
                    <span class="badge rounded-pill" style="background: rgba(34, 197, 94, 0.15); color: #22c55e; border: 1px solid rgba(34, 197, 94, 0.2);">Activo</span>
                  @else
                    <span class="badge rounded-pill" style="background: rgba(148, 163, 184, 0.15); color: #94a3b8; border: 1px solid rgba(148, 163, 184, 0.2);">Inactivo</span>
                  @endif
                </td>
                <td class="text-center">
                    <div class="d-flex gap-2 justify-content-center">
                        <button class="btn btn-sm btn-outline-warning border-0 rounded-circle"
                                data-bs-toggle="modal"
                                data-bs-target="#modalEditar{{ $i->id }}" title="Editar Plan">
                            <i class="bi bi-pencil-square fs-5"></i>
                        </button>

                        <form action="{{ route('mantenimiento.iguala.destroy', $i->id) }}"
                              method="POST"
                              class="d-inline"
                              onsubmit="return confirm('¿Eliminar esta iguala?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger border-0 rounded-circle" title="Eliminar">
                                <i class="bi bi-trash3 fs-5"></i>
                            </button>
                        </form>
                    </div>
                </td>
              </tr>


            @empty
              <tr>
                <td colspan="6" class="text-center py-5">
                    <div class="text-muted">
                        <i class="bi bi-award fs-1 d-block mb-3 opacity-25"></i>
                        No hay planes de iguala definidos actualmente.
                    </div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Modal nuevo -->
<div class="modal fade" id="modalNuevo" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form method="POST" action="{{ route('mantenimiento.iguala.store') }}">
        @csrf

        <div class="modal-header">
          <h5 class="modal-title fw-bold">Nueva Categoría de Iguala</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body p-4">
          <div class="mb-3">
            <label class="form-label">Nombre del Plan</label>
            <input type="text" name="nombre" class="form-control" required placeholder="Ej: Platinum Elite">
          </div>

          <div class="mb-4">
            <label class="form-label">Descripción Informativa</label>
            <textarea name="descripcion" class="form-control" rows="2" placeholder="Describa brevemente qué cubre este plan..."></textarea>
          </div>

          <div class="row g-3 mb-4">
            <div class="col-md-6">
              <label class="form-label text-primary small fw-bold">Soportes Remotos</label>
              <div class="input-group">
                <input type="hidden" name="cantidad_soporte_remoto" id="remoto_hiddenNew" value="0">
                <input type="number" id="remoto_displayNew" class="form-control" value="0" oninput="document.getElementById('remoto_hiddenNew').value = this.value">
                <div class="input-group-text border-start-0" style="background: rgba(255,255,255,0.05); border-color: var(--border-main);">
                  <input class="form-check-input mt-0" type="checkbox" title="Ilimitado" onchange="handleIlimitadoToggle('remoto_displayNew', 'remoto_hiddenNew', this)">
                  <small class="ms-1 fw-bold">∞</small>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label text-primary small fw-bold">Visitas Físicas</label>
              <div class="input-group">
                <input type="hidden" name="cantidad_visitas" id="visita_hiddenNew" value="0">
                <input type="number" id="visita_displayNew" class="form-control" value="0" oninput="document.getElementById('visita_hiddenNew').value = this.value">
                <div class="input-group-text border-start-0" style="background: rgba(255,255,255,0.05); border-color: var(--border-main);">
                  <input class="form-check-input mt-0" type="checkbox" title="Ilimitado" onchange="handleIlimitadoToggle('visita_displayNew', 'visita_hiddenNew', this)">
                  <small class="ms-1 fw-bold">∞</small>
                </div>
              </div>
            </div>
          </div>

          <div class="rounded-4 p-3 mb-4" style="background: rgba(var(--text-main), 0.03); border: 1px solid var(--border-main);">
            <h6 class="mb-3 fw-bold small text-muted text-uppercase letter-spacing-1">Servicios Incluidos</h6>
            <div class="form-check form-switch mb-2">
              <input class="form-check-input" type="checkbox" name="mantenimiento_sw_hw" id="swhwNew">
              <label class="form-check-label" for="swhwNew">Mantenimiento SW/HW</label>
            </div>
            <div class="form-check form-switch mb-2">
              <input class="form-check-input" type="checkbox" name="equipo_prestamo" id="presNew">
              <label class="form-check-label" for="presNew">Disponibilidad de Préstamo</label>
            </div>
            <div class="form-check form-switch mb-0">
              <input class="form-check-input" type="checkbox" name="asistencia_vip" id="vipNew">
              <label class="form-check-label" for="vipNew">Soporte Prioritario VIP</label>
            </div>
          </div>

          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="activo" id="activoNuevo" checked>
            <label class="form-check-label fw-bold" for="activoNuevo">Publicar Plan como Activo</label>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-spgi">Crear Plan</button>
        </div>

      </form>
    </div>
  </div>
</div>
@foreach($igualas as $i)
<!-- Modal editar -->
<div class="modal fade" id="modalEditar{{ $i->id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form method="POST" action="{{ route('mantenimiento.iguala.update', $i->id) }}">
        @csrf
        @method('PUT')

        <div class="modal-header">
          <h5 class="modal-title fw-bold">
              <i class="bi bi-pencil-fill me-2 text-warning"></i>Editar Iguala
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body p-4">
          <div class="mb-4">
            <label class="form-label">Nombre del Plan</label>
            <input type="text" name="nombre" class="form-control" value="{{ $i->nombre }}" required>
          </div>

          <div class="row g-3 mb-4">
            <div class="col-md-6">
              <label class="form-label text-primary small fw-bold">Soportes Remotos</label>
              <div class="input-group">
                <input type="hidden" name="cantidad_soporte_remoto" id="remoto_hidden{{ $i->id }}" value="{{ $i->cantidad_soporte_remoto }}">
                <input type="{{ $i->cantidad_soporte_remoto == -1 ? 'text' : 'number' }}" 
                       id="remoto_display{{ $i->id }}" 
                       class="form-control" 
                       value="{{ $i->cantidad_soporte_remoto == -1 ? 'Ilimitada' : $i->cantidad_soporte_remoto }}" 
                       {{ $i->cantidad_soporte_remoto == -1 ? 'readonly' : '' }}
                       oninput="document.getElementById('remoto_hidden{{ $i->id }}').value = this.value">
                <div class="input-group-text border-start-0" style="background: rgba(255,255,255,0.05); border-color: var(--border-main);">
                  <input class="form-check-input mt-0" type="checkbox" title="Ilimitado" onchange="handleIlimitadoToggle('remoto_display{{ $i->id }}', 'remoto_hidden{{ $i->id }}', this)" {{ $i->cantidad_soporte_remoto == -1 ? 'checked' : '' }}>
                  <small class="ms-1 fw-bold">∞</small>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label text-primary small fw-bold">Visitas Físicas</label>
              <div class="input-group">
                <input type="hidden" name="cantidad_visitas" id="visita_hidden{{ $i->id }}" value="{{ $i->cantidad_visitas }}">
                <input type="{{ $i->cantidad_visitas == -1 ? 'text' : 'number' }}" 
                       id="visita_display{{ $i->id }}" 
                       class="form-control" 
                       value="{{ $i->cantidad_visitas == -1 ? 'Ilimitada' : $i->cantidad_visitas }}" 
                       {{ $i->cantidad_visitas == -1 ? 'readonly' : '' }}
                       oninput="document.getElementById('visita_hidden{{ $i->id }}').value = this.value">
                <div class="input-group-text border-start-0" style="background: rgba(255,255,255,0.05); border-color: var(--border-main);">
                  <input class="form-check-input mt-0" type="checkbox" title="Ilimitado" onchange="handleIlimitadoToggle('visita_display{{ $i->id }}', 'visita_hidden{{ $i->id }}', this)" {{ $i->cantidad_visitas == -1 ? 'checked' : '' }}>
                  <small class="ms-1 fw-bold">∞</small>
                </div>
              </div>
            </div>
          </div>

          <div class="rounded-4 p-3 mb-4" style="background: rgba(var(--text-main), 0.03); border: 1px solid var(--border-main);">
            <h6 class="mb-3 fw-bold small text-muted text-uppercase letter-spacing-1">Servicios Premium</h6>
            <div class="form-check form-switch mb-2">
              <input class="form-check-input" type="checkbox" name="mantenimiento_sw_hw" id="swhw{{ $i->id }}" {{ $i->mantenimiento_sw_hw ? 'checked' : '' }}>
              <label class="form-check-label" for="swhw{{ $i->id }}">Mant. Software y Hardware</label>
            </div>
            <div class="form-check form-switch mb-2">
              <input class="form-check-input" type="checkbox" name="equipo_prestamo" id="pres{{ $i->id }}" {{ $i->equipo_prestamo ? 'checked' : '' }}>
              <label class="form-check-label" for="pres{{ $i->id }}">Equipo de Préstamo</label>
            </div>
            <div class="form-check form-switch mb-0">
              <input class="form-check-input" type="checkbox" name="asistencia_vip" id="vip{{ $i->id }}" {{ $i->asistencia_vip ? 'checked' : '' }}>
              <label class="form-check-label" for="vip{{ $i->id }}">Prioridad de Asistencia VIP</label>
            </div>
          </div>

          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="activo" id="act{{ $i->id }}" {{ $i->activo ? 'checked' : '' }}>
            <label class="form-check-label fw-bold" for="act{{ $i->id }}">Plan Registrado y Activo</label>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-spgi">Guardar Cambios</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endforeach

@endsection

@push('scripts')
<script>
function handleIlimitadoToggle(displayId, hiddenId, checkbox) {
    const display = document.getElementById(displayId);
    const hidden = document.getElementById(hiddenId);
    
    if (checkbox.checked) {
        display.type = "text";
        display.value = "Ilimitada";
        display.readOnly = true;
        hidden.value = -1;
    } else {
        display.type = "number";
        display.value = 0;
        display.readOnly = false;
        hidden.value = 0;
    }
}
</script>
@endpush
