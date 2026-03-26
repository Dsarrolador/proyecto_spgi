@extends('layouts.app')

@section('content')
<div class="container mt-4">

    <h3 class="mb-4">Agregar Requerimiento</h3>

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

        <!-- PREVIEW IMÁGENES ADICIONALES -->
        <div class="mb-3 d-none" id="previewMultiplesContainer">
            <label class="form-label fw-semibold">Vista previa de imágenes adicionales</label>
            <div id="previewMultiples" class="d-flex flex-wrap gap-2"></div>
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

        } catch (e) {
            resetContactos('Error al cargar contactos');
            helpText.textContent = 'Hubo un error consultando los contactos. Revisa la ruta / controlador.';
            console.error(e);
        }
    });
});
</script>
@endsection