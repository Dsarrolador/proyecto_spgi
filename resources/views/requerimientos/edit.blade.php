@extends('layouts.app')

@section('content')
@php
  use Illuminate\Support\Str;

  $esAdministracion = false;

  if (isset($esAdmin)) {
      $esAdministracion = (bool) $esAdmin;
  } else {
      $u = auth()->user();
      $roleName = null;

      if ($u) {
          if (method_exists($u, 'rol') && optional($u->rol)->nombre) {
              $roleName = $u->rol->nombre;
          } elseif (method_exists($u, 'role') && optional($u->role)->nombre) {
              $roleName = $u->role->nombre;
          } elseif (isset($u->rol)) {
              $roleName = $u->rol;
          } elseif (isset($u->perfil)) {
              $roleName = $u->perfil;
          } elseif (isset($u->role_name)) {
              $roleName = $u->role_name;
          }
      }

      if ($roleName) {
          $norm = Str::of($roleName)->ascii()->lower()->trim()->toString();
          $esAdministracion = in_array($norm, ['administracion','administrador','admin','administration'], true);
      }
  }

  $facturadoActual = (int) old('facturado', $requerimiento->facturado ?? 0);

  $fotoPrincipalUrl = !empty($requerimiento->foto)
      ? url('storage/' . ltrim($requerimiento->foto, '/'))
      : null;
@endphp

<div class="container py-4">

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0">Editar Requerimiento</h4>

    <div class="d-flex gap-2">
      <a href="{{ route('requerimientos.show', $requerimiento->id) }}" class="btn btn-secondary">
        <i class="bi bi-eye"></i> Ver detalle
      </a>

      <a href="{{ route('requerimientos.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Volver
      </a>
    </div>
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
    <div class="card-body">

      <form method="POST" action="{{ route('requerimientos.update', $requerimiento->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-4">

          <div class="col-md-6">
            <div class="text-muted small">Cliente</div>
            <select name="cliente_id" class="form-select">
              <option value="">Seleccione</option>
              @foreach(($clientes ?? collect()) as $c)
                <option value="{{ $c->id }}" {{ old('cliente_id', $requerimiento->cliente_id) == $c->id ? 'selected' : '' }}>
                  {{ $c->nombre }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="col-md-6 text-md-end">
            <div class="text-muted small">Estado</div>

            @php $estado_id = old('estado_id', $requerimiento->estado_id ?? 1); @endphp
            <select name="estado_id" id="estado" class="form-select d-inline-block" style="max-width: 220px;">
              @foreach(($estados ?? collect()) as $e)
                <option value="{{ $e->id }}" {{ $estado_id == $e->id ? 'selected' : '' }}>
                  {{ $e->nombre }}
                </option>
              @endforeach
            </select>

            <div class="text-muted small mt-2">
              <i class="bi bi-person-circle me-1"></i>
              Creado por:
              <span class="fw-semibold text-dark">
                {{ $requerimiento->user->name ?? 'Sistema' }}
              </span>
            </div>
          </div>

          <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
              <div class="text-muted small mb-1">Tipo de soporte</div>

              <a href="{{ route('mantenimiento.tipo-soporte.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-gear"></i> Administrar
              </a>
            </div>

            <select name="tipo_soporte_id" class="form-select @error('tipo_soporte_id') is-invalid @enderror">
              <option value="">Seleccione tipo de soporte</option>
              @foreach(($tiposSoporte ?? collect()) as $t)
                <option value="{{ $t->id }}" {{ old('tipo_soporte_id', $requerimiento->tipo_soporte_id) == $t->id ? 'selected' : '' }}>
                  {{ $t->nombre }}
                </option>
              @endforeach
            </select>

            @error('tipo_soporte_id')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="col-12">
            <div class="text-muted small mb-1">Asignado a</div>

            <select name="asignado_user_id" id="asignado_user_id" class="form-select">
              <option value="">Sin asignar</option>
              @foreach(($usuarios ?? collect()) as $u)
                <option value="{{ $u->id }}"
                  {{ (string)old('asignado_user_id', $requerimiento->asignado_user_id) === (string)$u->id ? 'selected' : '' }}>
                  {{ $u->name }}{{ !empty($u->email) ? ' - '.$u->email : '' }}
                </option>
              @endforeach
            </select>

            <div class="small text-muted mt-1">
              Puedes reasignar el responsable desde aquí.
            </div>
          </div>

          <div class="col-12">
            <div class="text-muted small mb-1">Contacto</div>

            <select name="contacto_id" class="form-select">
              <option value="">Sin contacto</option>
              @foreach(($contactos ?? collect()) as $con)
                <option value="{{ $con->id }}" {{ old('contacto_id', $requerimiento->contacto_id) == $con->id ? 'selected' : '' }}>
                  {{ $con->nombre }}
                  @if(!empty($con->telefono)) - {{ $con->telefono }} @endif
                  @if(!empty($con->correo)) - {{ $con->correo }} @endif
                </option>
              @endforeach
            </select>

            <div class="small text-muted mt-1">
              Selecciona el contacto asignado al requerimiento (opcional).
            </div>
          </div>

          <div class="col-12">
            <div class="text-muted small mb-1">Requerimiento</div>

            <textarea
              name="texto_imagen"
              id="texto_imagen"
              class="form-control"
              rows="4"
              required
              maxlength="2000"
              placeholder="Describe el problema (máximo 2000 caracteres)..."
            >{{ old('texto_imagen', $requerimiento->texto_imagen) }}</textarea>

            <div class="d-flex justify-content-between mt-1">
              <small class="text-muted">Máximo 2000 caracteres.</small>
              <small class="text-muted"><span id="contadorTexto">0</span>/2000</small>
            </div>
          </div>

          <div class="col-12">
            <div class="text-muted small">Foto principal</div>

            @if(!empty($requerimiento->foto))
              <div class="mt-2">
                <img
                  src="{{ $fotoPrincipalUrl }}"
                  class="img-fluid rounded border"
                  style="max-height:420px; cursor:pointer;"
                  alt="Foto principal del requerimiento"
                  onclick="abrirModalImagen('{{ $fotoPrincipalUrl }}')"
                  onerror="this.style.display='none'; document.getElementById('error-foto-principal').classList.remove('d-none');"
                >
                <div id="error-foto-principal" class="alert alert-warning mt-2 mb-0 d-none">
                  No se pudo cargar la foto principal.
                </div>
              </div>
            @else
              <div class="text-muted mt-2">No hay foto principal adjunta.</div>
            @endif

            <div class="mt-3">
              <label class="form-label">Cambiar foto principal (opcional)</label>
              <input type="file" name="foto" class="form-control" accept="image/*">
            </div>
          </div>

          <div class="col-12">
            <div class="text-muted small mb-2">Imágenes adicionales guardadas</div>

            @if(isset($requerimiento->imagenes) && $requerimiento->imagenes->count())
              <div class="d-flex flex-wrap gap-3">
                @foreach($requerimiento->imagenes as $index => $img)
                  @php
                    $fotoAdicionalUrl = !empty($img->imagen)
                        ? url('storage/' . ltrim($img->imagen, '/'))
                        : null;
                  @endphp

                  <div class="border rounded p-2 bg-light" style="width: 170px;">
                    <img
                      src="{{ $fotoAdicionalUrl }}"
                      class="img-fluid rounded"
                      style="width: 100%; height: 140px; object-fit: cover; cursor:pointer;"
                      alt="Imagen adicional"
                      onclick="abrirModalImagen('{{ $fotoAdicionalUrl }}')"
                      onerror="this.style.display='none'; document.getElementById('error-foto-adicional-{{ $index }}').classList.remove('d-none');"
                    >
                    <div id="error-foto-adicional-{{ $index }}" class="alert alert-warning mt-2 mb-0 d-none py-2">
                      No se pudo cargar esta imagen adicional.
                    </div>
                  </div>
                @endforeach
              </div>
            @else
              <div class="text-muted">No hay imágenes adicionales guardadas.</div>
            @endif
          </div>

          <div class="col-12">
            <label class="form-label">Agregar más imágenes adicionales</label>
            <input
              type="file"
              name="imagenes[]"
              id="imagenes"
              class="form-control"
              accept="image/*"
              multiple
            >
            <div class="small text-muted mt-1">
              Puedes seleccionar varias imágenes para agregarlas al requerimiento.
            </div>
          </div>

          <div class="col-12 d-none" id="previewMultiplesContainer">
            <div class="text-muted small mb-2">Vista previa de nuevas imágenes</div>
            <div id="previewMultiples" class="d-flex flex-wrap gap-3"></div>
          </div>

          <div class="col-md-6">
            <div class="d-flex justify-content-between align-items-center">
              <div class="text-muted small">Creado (fecha/hora)</div>

              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="editarFecha">
                <label class="form-check-label small" for="editarFecha">Editar</label>
              </div>
            </div>

            @php
              $createdValue = optional($requerimiento->created_at)->format('Y-m-d\TH:i');
            @endphp

            <input
              type="datetime-local"
              name="created_at"
              id="created_at"
              class="form-control"
              value="{{ old('created_at', $createdValue) }}"
              disabled
            >

            <div class="small text-muted mt-1">
              Por defecto no se cambia. Activa "Editar" si deseas modificarla.
            </div>
          </div>

          <div class="col-md-6">
            <div class="text-muted small">Finalizado (fecha/hora)</div>

            @php
              $finalizadoValue = $requerimiento->fecha_finalizado
                ? \Carbon\Carbon::parse($requerimiento->fecha_finalizado)->format('Y-m-d\TH:i')
                : '';
            @endphp

            <input
              type="datetime-local"
              name="fecha_finalizado"
              id="fecha_finalizado"
              class="form-control"
              value="{{ old('fecha_finalizado', $finalizadoValue) }}"
            >

            <div class="small text-muted mt-1">
              Opcional. Colócala cuando el requerimiento esté completado.
            </div>
          </div>

          <div class="col-md-3">
            <div class="text-muted small">Tiempo invertido</div>
            <input
              type="text"
              name="tiempo_invertido"
              class="form-control"
              placeholder="Ej: 1h 30m"
              value="{{ old('tiempo_invertido', $requerimiento->tiempo_invertido) }}"
            >
            <div class="small text-muted mt-1">
              Ej: 45m, 1h, 2h 15m
            </div>
          </div>

          <div class="col-md-3">
            <div class="d-flex justify-content-between align-items-center">
              <div class="text-muted small">Facturación</div>

              @if($esAdministracion)
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="editarFacturado">
                  <label class="form-check-label small" for="editarFacturado">Editar</label>
                </div>
              @endif
            </div>

            @if(!$esAdministracion)
              <input type="hidden" name="facturado" value="{{ $facturadoActual }}">
            @endif

            <select name="facturado" id="facturado" class="form-select" disabled>
              <option value="0" {{ $facturadoActual === 0 ? 'selected' : '' }}>No facturado</option>
              <option value="1" {{ $facturadoActual === 1 ? 'selected' : '' }}>Facturado</option>
            </select>

            @if(!$esAdministracion)
              <div class="small text-muted mt-1">
                Solo el rol <b>Administración</b> puede editar este campo.
              </div>
            @else
              <div class="small text-muted mt-1">
                Por defecto no se cambia. Activa "Editar" para modificarlo.
              </div>
            @endif
          </div>

          <div class="col-md-6"></div>

          <div class="col-12 d-flex gap-2">
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-save me-1"></i> Guardar cambios
            </button>

            <a href="{{ route('requerimientos.show', $requerimiento->id) }}" class="btn btn-outline-secondary">
              Cancelar
            </a>
          </div>

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
  (function () {
    function actualizarContadorTexto() {
      const ta = document.getElementById('texto_imagen');
      const cont = document.getElementById('contadorTexto');
      if (!ta || !cont) return;
      cont.textContent = (ta.value || '').length;
    }

    function abrirModalImagen(src) {
      const img = document.getElementById('imagenModalPreview');
      img.src = src;

      const modal = new bootstrap.Modal(document.getElementById('modalImagenGeneral'));
      modal.show();
    }

    function previewImagenesMultiples(input) {
      const container = document.getElementById('previewMultiplesContainer');
      const preview = document.getElementById('previewMultiples');

      if (!container || !preview) return;

      preview.innerHTML = '';

      if (!input.files || !input.files.length) {
        container.classList.add('d-none');
        return;
      }

      container.classList.remove('d-none');

      Array.from(input.files).forEach((file) => {
        if (!file.type.startsWith('image/')) return;

        const reader = new FileReader();

        reader.onload = function(e) {
          const box = document.createElement('div');
          box.className = 'border rounded p-2 bg-light';
          box.style.width = '170px';

          const img = document.createElement('img');
          img.src = e.target.result;
          img.className = 'img-fluid rounded';
          img.style.width = '100%';
          img.style.height = '140px';
          img.style.objectFit = 'cover';
          img.style.cursor = 'pointer';

          img.addEventListener('click', function() {
            abrirModalImagen(e.target.result);
          });

          box.appendChild(img);
          preview.appendChild(box);
        };

        reader.readAsDataURL(file);
      });
    }

    window.abrirModalImagen = abrirModalImagen;

    document.addEventListener('DOMContentLoaded', () => {
      actualizarContadorTexto();

      const ta = document.getElementById('texto_imagen');
      if (ta) ta.addEventListener('input', actualizarContadorTexto);

      const inputImagenes = document.getElementById('imagenes');
      if (inputImagenes) {
        inputImagenes.addEventListener('change', function() {
          previewImagenesMultiples(this);
        });
      }
    });

    const toggle = document.getElementById('editarFecha');
    const input = document.getElementById('created_at');
    if (toggle && input) {
      toggle.addEventListener('change', function () {
        input.disabled = !this.checked;
        if (!this.checked) {
          input.value = "{{ optional($requerimiento->created_at)->format('Y-m-d\\TH:i') }}";
        }
      });
    }

    const toggleFact = document.getElementById('editarFacturado');
    const selectFact = document.getElementById('facturado');
    const factOriginal = "{{ (int)($requerimiento->facturado ?? 0) }}";

    if (toggleFact && selectFact) {
      toggleFact.addEventListener('change', function () {
        selectFact.disabled = !this.checked;
        if (!this.checked) {
          selectFact.value = factOriginal;
        }
      });
    }

    const estado = document.getElementById('estado');
    const finalizado = document.getElementById('fecha_finalizado');

    function pad(n){ return String(n).padStart(2,'0'); }
    function nowLocal(){
      const d = new Date();
      return d.getFullYear() + '-' + pad(d.getMonth()+1) + '-' + pad(d.getDate()) + 'T' + pad(d.getHours()) + ':' + pad(d.getMinutes());
    }

    if (estado && finalizado) {
      estado.addEventListener('change', function () {
        if (this.options[this.selectedIndex].text.trim().toLowerCase() === 'completado' && !finalizado.value) {
          finalizado.value = nowLocal();
        }
      });
    }
  })();
</script>
@endsection