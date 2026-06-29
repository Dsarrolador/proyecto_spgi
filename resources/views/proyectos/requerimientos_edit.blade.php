@extends('layouts.app')

@section('content')
@php
  $esAdministracion = false;
  if (auth()->check()) {
      $esAdministracion = auth()->user()->es_administrativo;
  }
  
  $facturadoActual = (int) old('facturado', $r->facturado ?? 0);
@endphp

<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h3 class="m-0">Editar Requerimiento de Proyecto</h3>
        <p class="text-muted m-0">Proyecto: <b>{{ $proyecto->nombre }}</b></p>
      </div>
      <a href="{{ route('proyectos.show', $proyecto->id) }}" class="btn btn-secondary rounded-pill px-4">
        <i class="bi bi-arrow-left"></i> Volver al proyecto
      </a>
    </div>

    @if($errors->any())
      <div class="alert alert-danger">
        <ul class="mb-0">
          @foreach($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form action="{{ route('requerimientos_proyecto.update', $r->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- CLIENTE -->
        <div class="mb-3">
            <label class="form-label">Cliente</label>
            <select name="cliente_id" id="cliente_id" class="form-select" required>
                <option value="">Seleccione un cliente</option>
                @foreach($clientes as $cliente)
                    <option value="{{ $cliente->id }}" {{ $cliente->id == old('cliente_id', $r->cliente_id) ? 'selected' : '' }}>
                        {{ $cliente->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- TAREA / REQUERIMIENTO GENERAL (OPCIONAL) -->
        <div class="mb-3">
            <label class="form-label">Tarea del Proyecto (Requerimiento Cliente)</label>
            <select name="requerimiento_cliente_id" id="requerimiento_cliente_id" class="form-select">
                <option value="">-- No vincular a una tarea (Requerimiento Cliente) --</option>
                @foreach($tareas as $t)
                    <option value="{{ $t->id }}" {{ $t->id == old('requerimiento_cliente_id', $r->requerimiento_cliente_id) ? 'selected' : '' }}>
                        #{{ $t->id }} - {{ \Illuminate\Support\Str::limit($t->texto_imagen, 80) }}
                    </option>
                @endforeach
            </select>
            <small class="text-muted d-block mt-1">
                Vincule esta interacción técnica a una tarea / requerimiento general del cliente.
            </small>
        </div>

        <!-- CONTACTO -->
        <div class="mb-3">
            <label class="form-label">Contacto</label>
            <select name="contacto_id" id="contacto_id" class="form-select" required>
                <option value="">Seleccione un contacto</option>
            </select>
            <small class="text-muted d-block mt-1" id="contacto_help">
                Seleccione un cliente para ver sus contactos.
            </small>
        </div>

        <!-- TIPO DE SOPORTE -->
        <div class="mb-3">
            <label class="form-label">Tipo de soporte</label>
            <select name="tipo_soporte_id" class="form-select" required>
                <option value="">Seleccione tipo de soporte</option>
                @foreach($tiposSoporte as $tipo)
                    <option value="{{ $tipo->id }}" {{ $tipo->id == old('tipo_soporte_id', $r->tipo_soporte_id) ? 'selected' : '' }}>
                        {{ $tipo->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- ESTADO INICIAL -->
        <div class="mb-3">
            <label class="form-label">Estado Inicial</label>
            <select name="estado_id" class="form-select">
                @foreach($estados as $estado)
                    <option value="{{ $estado->id }}" {{ $estado->id == old('estado_id', $r->estado_id) ? 'selected' : '' }}>
                        {{ $estado->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- PRIORIDAD -->
        <div class="mb-3">
            <label class="form-label">Prioridad</label>
            <select name="prioridad" class="form-select" required>
                <option value="5" class="text-danger fw-bold" {{ old('prioridad', $r->prioridad) == 5 ? 'selected' : '' }}>5 - Muy Urgente</option>
                <option value="4" class="text-warning fw-bold" {{ old('prioridad', $r->prioridad) == 4 ? 'selected' : '' }}>4 - Urgente</option>
                <option value="3" {{ old('prioridad', $r->prioridad) == 3 ? 'selected' : '' }}>3 - Media</option>
                <option value="2" {{ old('prioridad', $r->prioridad) == 2 ? 'selected' : '' }}>2 - Baja</option>
                <option value="1" {{ old('prioridad', $r->prioridad) == 1 ? 'selected' : '' }}>1 - Muy Baja</option>
            </select>
        </div>

        <!-- COLABORATIVO -->
        <div class="mb-3">
            <div class="form-check form-switch p-3 border rounded d-flex flex-column justify-content-center" style="background: rgba(var(--text-main), 0.03); border-color: var(--border-main) !important;">
                <div>
                    <input class="form-check-input ms-0 me-2 mt-1" style="float:left;" type="checkbox" name="es_colaborativo" id="es_colaborativo" value="1" {{ old('es_colaborativo', $r->es_colaborativo) ? 'checked' : '' }}>
                    <label class="form-check-label fw-bold d-block" for="es_colaborativo">
                        <i class="bi bi-people-fill me-1 text-primary"></i> Requerimiento Colaborativo / Compartido
                    </label>
                </div>
                <small class="text-muted d-block ms-5 mt-1" style="margin-left: 2.5rem !important;">Permite que otros usuarios vean y colaboren en este requerimiento.</small>
                
                <div id="colaboradores_container" class="mt-3 {{ old('es_colaborativo', $r->es_colaborativo) ? '' : 'd-none' }} ms-5" style="margin-left: 2.5rem !important;">
                    <label class="form-label small fw-bold" style="color: var(--text-main);">Selecciona colaboradores adicionales:</label>
                    <div class="row g-2 border rounded p-3 shadow-sm" style="max-height: 200px; overflow-y: auto; background: var(--bg-surface); border-color: var(--border-main) !important;">
                        @php
                            $colabsActivos = $r->colaboradores->pluck('id')->toArray();
                        @endphp
                        @foreach($usuarios as $u)
                            @if($u->id != auth()->id())
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="colaboradores_ids[]" value="{{ $u->id }}" id="colab{{ $u->id }}" {{ in_array($u->id, old('colaboradores_ids', $colabsActivos)) ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="colab{{ $u->id }}">
                                            {{ $u->name }}
                                        </label>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- ASIGNAR A USUARIO -->
        <div class="mb-3">
            <label class="form-label">Asignar a usuario</label>
            <select name="asignado_user_id" id="asignado_user_id" class="form-select">
                <option value="">Sin asignar</option>
                @isset($usuarios)
                    @foreach($usuarios as $u)
                        <option value="{{ $u->id }}" {{ $u->id == old('asignado_user_id', $r->asignado_user_id) ? 'selected' : '' }}>
                            {{ $u->name }}
                        </option>
                    @endforeach
                @endisset
            </select>
        </div>

        <!-- DESCRIPCIÓN -->
        <div class="mb-3">
            <label class="form-label">Descripción del problema</label>
            <textarea
                name="texto_imagen"
                id="texto_imagen"
                class="form-control"
                rows="4"
                required
                maxlength="2000"
                placeholder="Describe el problema (máximo 2000 caracteres)..."
            >{{ old('texto_imagen', $r->texto_imagen ?: $r->descripcion) }}</textarea>

            <div class="d-flex justify-content-between mt-1">
                <small class="text-muted">Máximo 2000 caracteres.</small>
                <small class="text-muted"><span id="contadorTexto">0</span>/2000</small>
            </div>
        </div>

        <!-- FOTO PRINCIPAL -->
        <div class="mb-3">
            <label class="form-label">Evidencia principal (foto)</label>
            <input type="file"
                   name="foto"
                   class="form-control"
                   accept="image/*"
                   onchange="previewFoto(event)">
            <small class="text-muted d-block mt-1">
                Opcional: reemplaza la imagen principal del requerimiento.
            </small>
        </div>

        <!-- PREVIEW FOTO PRINCIPAL -->
        <div class="mb-3 {{ !empty($r->foto) ? '' : 'd-none' }}" id="previewContainer">
            <label class="form-label fw-semibold">Vista previa de la imagen principal</label><br>

            <img id="previewImagen"
                 src="{{ !empty($r->foto) ? route('storage.proxy', ['path' => $r->foto]) : '' }}"
                 alt="Vista previa"
                 class="img-thumbnail"
                 style="max-width: 250px; cursor: pointer;"
                 data-bs-toggle="modal"
                 data-bs-target="#modalFotoPreview">
        </div>

        <!-- IMÁGENES ADICIONALES -->
        <div class="mb-3">
            <label class="form-label">Imágenes adicionales</label>
            <input type="file"
                   name="imagenes[]"
                   id="imagenes"
                   class="form-control"
                   accept="image/*"
                   multiple
                   onchange="previewImagenes(event)">
            <small class="text-muted d-block mt-1">
                Agrega más imágenes adicionales.
            </small>
        </div>

        <!-- IMÁGENES ADICIONALES EXISTENTES -->
        @if($r->imagenes->count() > 0)
            <div class="mb-3">
                <label class="form-label fw-bold">Imágenes adicionales actuales (Selecciona para eliminar):</label>
                <div class="row g-2">
                    @foreach($r->imagenes as $img)
                        <div class="col-6 col-md-3 text-center">
                            <div class="position-relative border rounded p-1" style="border-color: var(--border-main); background: var(--bg-surface);">
                                <img src="{{ route('storage.proxy', ['path' => $img->imagen]) }}" class="img-fluid rounded" style="height: 120px; object-fit: cover; width: 100%;">
                                <div class="mt-2">
                                    <input class="form-check-input" type="checkbox" name="eliminar_imagenes_ids[]" value="{{ $img->id }}" id="del_img_{{ $img->id }}">
                                    <label class="form-check-label small text-danger" for="del_img_{{ $img->id }}">
                                        Eliminar
                                    </label>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- PREVIEW IMÁGENES ADICIONALES NUEVAS -->
        <div class="mb-3 d-none" id="previewMultiplesContainer">
            <label class="form-label fw-semibold">Vista previa de nuevas imágenes adicionales</label>
            <div class="d-flex flex-wrap gap-2" id="previewMultiples">
                <!-- Injected by JS -->
            </div>
        </div>

        <!-- OPCIONES AVANZADAS -->
        <div class="card border-0 mb-3" style="border-radius: 12px; background: rgba(var(--text-main), 0.03);">
            <div class="card-body">

                <!-- RECURRENCIA -->
                <div class="form-check form-switch mt-3">
                    <input class="form-check-input" type="checkbox" name="es_recurrente" id="es_recurrente" value="1" {{ old('es_recurrente', $r->es_recurrente) ? 'checked' : '' }}>
                    <label class="form-check-label fw-bold" for="es_recurrente">
                        <i class="bi bi-arrow-repeat me-1 text-success"></i> ¿Es un requerimiento recurrente?
                    </label>
                </div>
                
                <div id="freq_container" class="mt-3 {{ old('es_recurrente', $r->es_recurrente) ? '' : 'd-none' }}">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Frecuencia de repetición</label>
                        <select name="frecuencia" id="frecuencia" class="form-select">
                            <option value="Diario" {{ $r->frecuencia == 'Diario' ? 'selected' : '' }}>Diario</option>
                            <option value="Semanal" {{ $r->frecuencia == 'Semanal' ? 'selected' : '' }}>Semanal</option>
                            <option value="Quincenal" {{ $r->frecuencia == 'Quincenal' ? 'selected' : '' }}>Quincenal</option>
                            <option value="Mensual" {{ $r->frecuencia == 'Mensual' ? 'selected' : '' }}>Mensual</option>
                            <option value="Semestral" {{ $r->frecuencia == 'Semestral' ? 'selected' : '' }}>Semestral</option>
                            <option value="Al año" {{ $r->frecuencia == 'Al año' ? 'selected' : '' }}>Al año</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Fecha de Inicio de Recurrencia</label>
                        <input type="datetime-local" name="fecha_inicio_recurrencia" class="form-control" value="{{ old('fecha_inicio_recurrencia', optional($r->fecha_inicio_recurrencia)->format('Y-m-d\TH:i') ?? now()->format('Y-m-d\TH:i')) }}">
                        <small class="text-muted d-block mt-1">El primer ciclo comenzará a partir de esta fecha.</small>
                    </div>
                </div>

                <!-- FACTURACIÓN (SOLO ADMINS) -->
                @if($esAdministracion)
                    <div class="border-top pt-3 mt-3">
                        <div class="mb-3">
                            <label class="form-label fw-bold"><i class="bi bi-wallet2 me-1 text-primary"></i> Estado de Facturación (Administración)</label>
                            <select name="facturado" class="form-select">
                                <option value="0" {{ $facturadoActual === 0 ? 'selected' : '' }}>No Facturado</option>
                                <option value="1" {{ $facturadoActual === 1 ? 'selected' : '' }}>Facturado</option>
                            </select>
                        </div>
                    </div>
                @else
                    <!-- Enviar el campo oculto para que no se pierda al guardar si es de administración -->
                    <input type="hidden" name="facturado" value="{{ $facturadoActual }}">
                @endif

            </div>
        </div>

        <!-- BOTONES -->
        <div class="d-flex gap-2">
            <a href="{{ route('proyectos.show', $proyecto->id) }}" class="btn btn-secondary">
                Cancelar
            </a>
            <button type="submit" class="btn btn-primary">
                Guardar Cambios
            </button>
        </div>

    </form>

</div>

<!-- MODAL FOTO PRINCIPAL GRANDE -->
<div class="modal fade" id="modalFotoPreview" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Vista de la evidencia principal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body text-center">
                <img id="previewImagenGrande"
                     src="{{ !empty($r->foto) ? route('storage.proxy', ['path' => $r->foto]) : '' }}"
                     class="img-fluid rounded"
                     alt="Evidencia">
            </div>

        </div>
    </div>
</div>

<!-- MODAL IMÁGENES ADICIONALES -->
<div class="modal fade" id="modalImagenAdicional" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Vista de imagen adicional</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body text-center">
                <img id="previewImagenAdicionalGrande"
                     src=""
                     class="img-fluid rounded"
                     alt="Imagen adicional">
            </div>

        </div>
    </div>
</div>

<script>
/** =========================
 *  Preview de foto principal
 *  ========================= */
function previewFoto(event) {
    const input = event.target;
    const container = document.getElementById('previewContainer');
    const img = document.getElementById('previewImagen');
    const imgGrande = document.getElementById('previewImagenGrande');

    if (input.files && input.files[0]) {
        const reader = new FileReader();

        reader.onload = function(e) {
            img.src = e.target.result;
            imgGrande.src = e.target.result;
            container.classList.remove('d-none');
        };

        reader.readAsDataURL(input.files[0]);
    }
}

/** =========================
 *  Preview de imágenes múltiples
 *  ========================= */
function previewImagenes(event) {
    const input = event.target;
    const container = document.getElementById('previewMultiplesContainer');
    const preview = document.getElementById('previewMultiples');
    const modalImg = document.getElementById('previewImagenAdicionalGrande');

    preview.innerHTML = '';

    if (input.files && input.files.length > 0) {
        container.classList.remove('d-none');

        Array.from(input.files).forEach((file, index) => {
            if (!file.type.startsWith('image/')) return;

            const reader = new FileReader();

            reader.onload = function(e) {
                const wrapper = document.createElement('div');
                wrapper.className = 'border rounded p-1';
                wrapper.style.borderColor = 'var(--border-main)';
                wrapper.style.background = 'var(--bg-surface)';
                wrapper.style.width = '120px';

                const img = document.createElement('img');
                img.src = e.target.result;
                img.alt = 'Imagen adicional ' + (index + 1);
                img.className = 'img-fluid rounded';
                img.style.width = '100%';
                img.style.height = '100px';
                img.style.objectFit = 'cover';
                img.style.cursor = 'pointer';
                img.setAttribute('data-bs-toggle', 'modal');
                img.setAttribute('data-bs-target', '#modalImagenAdicional');

                img.addEventListener('click', function() {
                    modalImg.src = e.target.result;
                });

                wrapper.appendChild(img);
                preview.appendChild(wrapper);
            };

            reader.readAsDataURL(file);
        });
    } else {
        container.classList.add('d-none');
    }
}

/** =========================
 *  Contador de caracteres
 *  ========================= */
function actualizarContadorTexto() {
    const ta = document.getElementById('texto_imagen');
    const cont = document.getElementById('contadorTexto');
    if (!ta || !cont) return;
    cont.textContent = (ta.value || '').length;
}

/** =========================
 *  Cargar contactos por cliente (AJAX)
 *  ========================= */
document.addEventListener('DOMContentLoaded', () => {
    const clienteSelect  = document.getElementById('cliente_id');
    const contactoSelect = document.getElementById('contacto_id');
    const helpText       = document.getElementById('contacto_help');

    actualizarContadorTexto();
    const ta = document.getElementById('texto_imagen');
    if (ta) ta.addEventListener('input', actualizarContadorTexto);

    const resetContactos = (msg = 'Seleccione un contacto') => {
        contactoSelect.innerHTML = `<option value="">${msg}</option>`;
        contactoSelect.disabled = true;
    };

    const setLoading = () => {
        contactoSelect.innerHTML = `<option value="">Cargando contactos...</option>`;
        contactoSelect.disabled = true;
    };

    const loadContactos = async (clienteId, selectedContactoId = null) => {
        if (!clienteId) {
            resetContactos('Seleccione un contacto');
            helpText.textContent = 'Seleccione un cliente para ver sus contactos.';
            return;
        }

        setLoading();
        helpText.textContent = 'Cargando contactos...';

        try {
            const url = `{{ route('clientes.contactos', ':id') }}`.replace(':id', clienteId);

            const res = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (!res.ok) throw new Error('No se pudieron cargar los contactos.');

            const contactos = await res.json();

            contactoSelect.innerHTML = `<option value="">Seleccione un contacto</option>`;

            if (!Array.isArray(contactos) || contactos.length === 0) {
                resetContactos('Este cliente no tiene contactos');
                helpText.textContent = 'Agrega un contacto a este cliente para poder seleccionarlo.';
                return;
            }

            contactos.forEach(c => {
                const opt = document.createElement('option');
                opt.value = c.id;
                opt.textContent = c.nombre ?? ('Contacto #' + c.id);
                if (selectedContactoId && c.id == selectedContactoId) {
                    opt.selected = true;
                }
                contactoSelect.appendChild(opt);
            });

            contactoSelect.disabled = false;
            helpText.textContent = 'Contactos cargados correctamente.';

        } catch (e) {
            resetContactos('Error al cargar contactos');
            helpText.textContent = 'Hubo un error consultando los contactos. Revisa la ruta / controlador.';
            console.error(e);
        }
    };

    clienteSelect.addEventListener('change', () => {
        loadContactos(clienteSelect.value);
    });

    if (clienteSelect.value) {
        loadContactos(clienteSelect.value, "{{ old('contacto_id', $r->contacto_id) }}");
    }

    // Lógica de recurrencia
    const switchRecurrente = document.getElementById('es_recurrente');
    const freqContainer   = document.getElementById('freq_container');

    if (switchRecurrente) {
        switchRecurrente.addEventListener('change', function() {
            if (this.checked) {
                freqContainer.classList.remove('d-none');
            } else {
                freqContainer.classList.add('d-none');
            }
        });
    }

    const currentUserId = "{{ auth()->id() }}";
    const asignadoSelect = document.getElementById('asignado_user_id');
    const colaborativoCheck = document.getElementById('es_colaborativo');

    if (asignadoSelect && colaborativoCheck) {
        asignadoSelect.addEventListener('change', function() {
            if (this.value && this.value !== currentUserId) {
                colaborativoCheck.checked = true;
                colaborativoCheck.dispatchEvent(new Event('change'));
            }
        });

        colaborativoCheck.addEventListener('change', function() {
            const container = document.getElementById('colaboradores_container');
            if (this.checked) {
                container.classList.remove('d-none');
            } else {
                container.classList.add('d-none');
            }
        });
    }
});
</script>
@endsection
