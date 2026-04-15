@extends('layouts.app')

@section('content')
<div class="container mt-4">

    <h3 class="mb-4">Agregar Requerimiento</h3>
    
    @if($errors->any())
      <div class="alert alert-danger">
        <ul class="mb-0">
          @foreach($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form action="{{ route('requerimientos.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- CLIENTE -->
        <div class="mb-3">
            <label class="form-label">Cliente</label>
            <select name="cliente_id" id="cliente_id" class="form-select" required>
                <option value="">Seleccione un cliente</option>
                @foreach($clientes as $cliente)
                    <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
                @endforeach
            </select>
            <!-- Alerta de Iguala -->
            <div id="iguala_alert" class="mt-2 d-none">
                <div class="alert alert-warning d-flex align-items-center mb-0 p-2" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <div id="iguala_alert_text" class="small fw-bold"></div>
                </div>
            </div>
        </div>

        <!-- CONTACTO -->
        <div class="mb-3">
            <label class="form-label">Contacto</label>
            <select name="contacto_id" id="contacto_id" class="form-select" required disabled>
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
                    <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                @endforeach
            </select>
        </div>

        <!-- COLABORATIVO -->
        <div class="mb-3">
            <div class="form-check form-switch p-3 bg-light border rounded d-flex flex-column justify-content-center">
                <div>
                    <input class="form-check-input ms-0 me-2 mt-1" style="float:left;" type="checkbox" name="es_colaborativo" id="es_colaborativo" value="1">
                    <label class="form-check-label fw-bold d-block" for="es_colaborativo">
                        <i class="bi bi-people-fill me-1 text-primary"></i> Requerimiento Colaborativo / Compartido
                    </label>
                </div>
                <small class="text-muted d-block ms-5 mt-1" style="margin-left: 2.5rem !important;">Permite que otros usuarios vean y colaboren en este requerimiento.</small>
                
                <div id="colaboradores_container" class="mt-3 d-none ms-5" style="margin-left: 2.5rem !important;">
                    <label class="form-label small fw-bold text-dark">Selecciona colaboradores adicionales:</label>
                    <div class="row g-2 border rounded p-3 bg-white shadow-sm" style="max-height: 200px; overflow-y: auto;">
                        @foreach($usuarios as $u)
                            @if($u->id != auth()->id())
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="colaboradores_ids[]" value="{{ $u->id }}" id="colab{{ $u->id }}">
                                        <label class="form-check-label small" for="colab{{ $u->id }}">
                                            {{ $u->name }}
                                        </label>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    <small class="text-muted d-block mt-2">A estos usuarios les aparecerá el requerimiento en su lista de tareas.</small>
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
                        <option value="{{ $u->id }}">
                            {{ $u->name }}
                        </option>
                    @endforeach
                @endisset
            </select>
            <small class="text-muted d-block mt-1">
                Opcional: si no asignas, quedará sin responsable.
            </small>
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
            ></textarea>

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
                Opcional: esta será la imagen principal del requerimiento.
            </small>
        </div>

        <!-- PREVIEW FOTO PRINCIPAL -->
        <div class="mb-3 d-none" id="previewContainer">
            <label class="form-label fw-semibold">Vista previa de la imagen principal</label><br>

            <img id="previewImagen"
                 src=""
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
                Puedes seleccionar varias imágenes adicionales.
            </small>
        </div>

        <!-- OPCIONES AVANZADAS -->
        <div class="card bg-light border-0 mb-3" style="border-radius: 12px;">
            <div class="card-body">

                <!-- RECURRENCIA -->
                <div class="form-check form-switch mt-3">
                    <input class="form-check-input" type="checkbox" name="es_recurrente" id="es_recurrente" value="1">
                    <label class="form-check-label fw-bold" for="es_recurrente">
                        <i class="bi bi-arrow-repeat me-1 text-success"></i> ¿Es un requerimiento recurrente?
                    </label>
                </div>
                
                <div id="freq_container" class="mt-3 d-none">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Frecuencia de repetición</label>
                        <select name="frecuencia" id="frecuencia" class="form-select">
                            <option value="Diario">Diario</option>
                            <option value="Semanal">Semanal</option>
                            <option value="Quincenal">Quincenal</option>
                            <option value="Mensual">Mensual</option>
                            <option value="Semestral">Semestral</option>
                            <option value="Al año">Al año</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Fecha de Inicio de Recurrencia</label>
                        <input type="datetime-local" name="fecha_inicio_recurrencia" class="form-control" value="{{ now()->format('Y-m-d\TH:i') }}">
                        <small class="text-muted d-block mt-1">El primer ciclo comenzará a partir de esta fecha.</small>
                    </div>

                    <small class="text-muted d-block mt-1">El sistema creará uno nuevo automáticamente al cumplirse el plazo.</small>
                </div>
            </div>
        </div>

        <!-- BOTONES -->
        <div class="d-flex gap-2">
            <a href="{{ route('requerimientos.index') }}" class="btn btn-secondary">
                Cancelar
            </a>
            <button type="submit" class="btn btn-primary">
                Guardar Requerimiento
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
                     src=""
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
    } else {
        img.src = '';
        imgGrande.src = '';
        container.classList.add('d-none');
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
                wrapper.className = 'border rounded p-1 bg-white';
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

    clienteSelect.addEventListener('change', async () => {
        const clienteId = clienteSelect.value;

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
                contactoSelect.appendChild(opt);
            });

            contactoSelect.disabled = false;
            helpText.textContent = 'Contactos cargados correctamente.';

            // ✅ NUEVO: Chequear balance de Iguala
            checkIgualaBalance(clienteId);

        } catch (e) {
            resetContactos('Error al cargar contactos');
            helpText.textContent = 'Hubo un error consultando los contactos. Revisa la ruta / controlador.';
            console.error(e);
        }
    });

    // ✅ Lógica de recurrencia
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

    async function checkIgualaBalance(clienteId) {
        const alertDiv = document.getElementById('iguala_alert');
        const alertText = document.getElementById('iguala_alert_text');
        
        try {
            const url = `{{ route('api.cliente-metrics', ':id') }}`.replace(':id', clienteId);
            const res = await fetch(url);
            if (!res.ok) return;

            const m = await res.json();
            if (!m) {
                alertDiv.classList.add('d-none');
                return;
            }

            let warnings = [];
            if (m.limite_remoto > 0 && m.disponible_remoto === 0) {
                warnings.push(`Soportes remotos AGOTADOS (Plan: ${m.plan_nombre})`);
            }
            if (m.limite_visita > 0 && m.disponible_visita === 0) {
                warnings.push(`Visitas presenciales AGOTADAS (Plan: ${m.plan_nombre})`);
            }

            if (warnings.length > 0) {
                alertText.innerHTML = warnings.join('<br>');
                alertDiv.classList.remove('d-none');
            } else {
                alertDiv.classList.add('d-none');
            }

        } catch (e) {
            console.error('Error al chequear balance de iguala:', e);
        }
    }

    const currentUserId = "{{ auth()->id() }}";
    const asignadoSelect = document.getElementById('asignado_user_id');
    const colaborativoCheck = document.getElementById('es_colaborativo');

    if (asignadoSelect && colaborativoCheck) {
        asignadoSelect.addEventListener('change', function() {
            if (this.value && this.value !== currentUserId) {
                // Si es un usuario diferente, se marca como colaborativo por defecto.
                colaborativoCheck.checked = true;
                // Disparar evento de cambio manualmente para que se muestre el contenedor
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