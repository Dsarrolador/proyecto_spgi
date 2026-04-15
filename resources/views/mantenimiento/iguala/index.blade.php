@extends('layouts.app')

@section('content')
<div class="container mt-4">

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Iguala</h3>

    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevo">
      <i class="bi bi-plus-circle"></i> Nuevo
    </button>
  </div>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  @if($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="card shadow-sm">
    <div class="card-body p-0">
      <table class="table table-bordered table-hover mb-0 align-middle">
        <thead class="table-dark">
          <tr>
            <th style="width:80px;">ID</th>
            <th>Nombre</th>
            <th>Soporte/Visitas</th>
            <th>Servicios Especiales</th>
            <th style="width:90px;">Activo</th>
            <th style="width:210px;">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse($igualas as $i)
            <tr>
              <td>{{ $i->id }}</td>
              <td>
                <div><strong>{{ $i->nombre }}</strong></div>
                <small class="text-muted">{{ $i->descripcion }}</small>
              </td>
              <td class="text-center">
                <span class="badge bg-info text-dark" title="Soporte Remoto">
                  <i class="bi bi-headset"></i> {{ $i->cantidad_soporte_remoto == -1 ? '∞' : $i->cantidad_soporte_remoto }}
                </span>
                <span class="badge bg-primary" title="Visitas Presenciales">
                  <i class="bi bi-geo-alt"></i> {{ $i->cantidad_visitas == -1 ? '∞' : $i->cantidad_visitas }}
                </span>
              </td>
              <td>
                <div class="d-flex flex-wrap gap-1">
                  @if($i->mantenimiento_sw_hw)
                    <span class="badge rounded-pill bg-light text-dark border" title="Mant. SW/HW">
                      <i class="bi bi-cpu text-primary"></i> SW/HW
                    </span>
                  @endif
                  @if($i->equipo_prestamo)
                    <span class="badge rounded-pill bg-light text-dark border" title="Equipo Préstamo">
                      <i class="bi bi-laptop text-primary"></i> Préstamo
                    </span>
                  @endif
                  @if($i->asistencia_vip)
                    <span class="badge rounded-pill bg-warning text-dark border" title="Asistencia VIP">
                      <i class="bi bi-star-fill"></i> VIP
                    </span>
                  @endif
                  @if(!$i->mantenimiento_sw_hw && !$i->equipo_prestamo && !$i->asistencia_vip)
                    <span class="text-muted small">Ninguno</span>
                  @endif
                </div>
              </td>
              <td class="text-center">
                @if($i->activo)
                  <span class="badge bg-success">Sí</span>
                @else
                  <span class="badge bg-secondary">No</span>
                @endif
              </td>
              <td class="text-center">
                <button class="btn btn-warning btn-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#modalEditar{{ $i->id }}">
                  <i class="bi bi-pencil-square"></i> Editar
                </button>

                <form action="{{ route('mantenimiento.iguala.destroy', $i->id) }}"
                      method="POST"
                      class="d-inline"
                      onsubmit="return confirm('¿Eliminar esta iguala?')">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-danger btn-sm">
                    <i class="bi bi-trash"></i> Eliminar
                  </button>
                </form>
              </td>
            </tr>

            <!-- Modal editar -->
            <div class="modal fade" id="modalEditar{{ $i->id }}" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <form method="POST" action="{{ route('mantenimiento.iguala.update', $i->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="modal-header bg-warning">
                      <h5 class="modal-title">Editar Iguala</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                      <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="nombre" class="form-control" value="{{ $i->nombre }}" required>
                      </div>

                      <div class="row">
                        <div class="col-md-6 mb-3">
                          <label class="form-label text-primary fw-bold small">Soportes Remotos</label>
                          <div class="input-group">
                            <input type="hidden" name="cantidad_soporte_remoto" id="remoto_hidden{{ $i->id }}" value="{{ $i->cantidad_soporte_remoto }}">
                            <input type="{{ $i->cantidad_soporte_remoto == -1 ? 'text' : 'number' }}" 
                                   id="remoto_display{{ $i->id }}" 
                                   class="form-control" 
                                   value="{{ $i->cantidad_soporte_remoto == -1 ? 'Ilimitada' : $i->cantidad_soporte_remoto }}" 
                                   {{ $i->cantidad_soporte_remoto == -1 ? 'readonly' : '' }}
                                   oninput="document.getElementById('remoto_hidden{{ $i->id }}').value = this.value">
                            <div class="input-group-text">
                              <input class="form-check-input mt-0" type="checkbox" title="Marcar como Ilimitado" onchange="handleIlimitadoToggle('remoto_display{{ $i->id }}', 'remoto_hidden{{ $i->id }}', this)" {{ $i->cantidad_soporte_remoto == -1 ? 'checked' : '' }}>
                              <small class="ms-1">∞</small>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6 mb-3">
                          <label class="form-label text-primary fw-bold small">Visitas</label>
                          <div class="input-group">
                            <input type="hidden" name="cantidad_visitas" id="visita_hidden{{ $i->id }}" value="{{ $i->cantidad_visitas }}">
                            <input type="{{ $i->cantidad_visitas == -1 ? 'text' : 'number' }}" 
                                   id="visita_display{{ $i->id }}" 
                                   class="form-control" 
                                   value="{{ $i->cantidad_visitas == -1 ? 'Ilimitada' : $i->cantidad_visitas }}" 
                                   {{ $i->cantidad_visitas == -1 ? 'readonly' : '' }}
                                   oninput="document.getElementById('visita_hidden{{ $i->id }}').value = this.value">
                            <div class="input-group-text">
                              <input class="form-check-input mt-0" type="checkbox" title="Marcar como Ilimitado" onchange="handleIlimitadoToggle('visita_display{{ $i->id }}', 'visita_hidden{{ $i->id }}', this)" {{ $i->cantidad_visitas == -1 ? 'checked' : '' }}>
                              <small class="ms-1">∞</small>
                            </div>
                          </div>
                        </div>
                      </div>

                      <div class="card p-3 bg-light border-0 mb-3">
                        <h6 class="mb-3 fw-bold text-secondary">Servicios Incluidos</h6>
                        <div class="form-check form-switch mb-2">
                          <input class="form-check-input" type="checkbox" name="mantenimiento_sw_hw" id="swhw{{ $i->id }}" {{ $i->mantenimiento_sw_hw ? 'checked' : '' }}>
                          <label class="form-check-label" for="swhw{{ $i->id }}">Mant. Software y Hardware</label>
                        </div>
                        <div class="form-check form-switch mb-2">
                          <input class="form-check-input" type="checkbox" name="equipo_prestamo" id="pres{{ $i->id }}" {{ $i->equipo_prestamo ? 'checked' : '' }}>
                          <label class="form-check-label" for="pres{{ $i->id }}">Equipo de Préstamo</label>
                        </div>
                        <div class="form-check form-switch mb-2">
                          <input class="form-check-input" type="checkbox" name="asistencia_vip" id="vip{{ $i->id }}" {{ $i->asistencia_vip ? 'checked' : '' }}>
                          <label class="form-check-label" for="vip{{ $i->id }}">Asistencia VIP</label>
                        </div>
                      </div>

                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="activo" id="act{{ $i->id }}" {{ $i->activo ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold" for="act{{ $i->id }}">Iguala Activa</label>
                      </div>
                    </div>

                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                      <button type="submit" class="btn btn-success">Guardar</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>

          @empty
            <tr>
              <td colspan="5" class="text-center text-muted p-4">No hay igualas registradas.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal nuevo -->
<div class="modal fade" id="modalNuevo" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="{{ route('mantenimiento.iguala.store') }}">
        @csrf

        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title">Nueva Iguala</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input type="text" name="nombre" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Descripción (Opcional)</label>
            <textarea name="descripcion" class="form-control" rows="2" placeholder="Notas adicionales..."></textarea>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label text-primary fw-bold small">Soportes Remotos</label>
              <div class="input-group">
                <input type="hidden" name="cantidad_soporte_remoto" id="remoto_hiddenNew" value="0">
                <input type="number" id="remoto_displayNew" class="form-control" value="0" oninput="document.getElementById('remoto_hiddenNew').value = this.value">
                <div class="input-group-text">
                  <input class="form-check-input mt-0" type="checkbox" title="Marcar como Ilimitado" onchange="handleIlimitadoToggle('remoto_displayNew', 'remoto_hiddenNew', this)">
                  <small class="ms-1">∞</small>
                </div>
              </div>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label text-primary fw-bold small">Visitas</label>
              <div class="input-group">
                <input type="hidden" name="cantidad_visitas" id="visita_hiddenNew" value="0">
                <input type="number" id="visita_displayNew" class="form-control" value="0" oninput="document.getElementById('visita_hiddenNew').value = this.value">
                <div class="input-group-text">
                  <input class="form-check-input mt-0" type="checkbox" title="Marcar como Ilimitado" onchange="handleIlimitadoToggle('visita_displayNew', 'visita_hiddenNew', this)">
                  <small class="ms-1">∞</small>
                </div>
              </div>
            </div>
          </div>

          <div class="card p-3 bg-light border-0 mb-3">
            <h6 class="mb-3 fw-bold text-secondary">Servicios Incluidos</h6>
            <div class="form-check form-switch mb-2">
              <input class="form-check-input" type="checkbox" name="mantenimiento_sw_hw" id="swhwNew">
              <label class="form-check-label" for="swhwNew">Mant. Software y Hardware</label>
            </div>
            <div class="form-check form-switch mb-2">
              <input class="form-check-input" type="checkbox" name="equipo_prestamo" id="presNew">
              <label class="form-check-label" for="presNew">Equipo de Préstamo</label>
            </div>
            <div class="form-check form-switch mb-2">
              <input class="form-check-input" type="checkbox" name="asistencia_vip" id="vipNew">
              <label class="form-check-label" for="vipNew">Asistencia VIP</label>
            </div>
          </div>

          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="activo" id="activoNuevo" checked>
            <label class="form-check-label fw-bold" for="activoNuevo">Iguala Activa</label>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Crear</button>
        </div>

      </form>
    </div>
  </div>
</div>
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
