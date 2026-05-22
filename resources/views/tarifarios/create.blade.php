@extends('layouts.app')

@section('page_title', 'Nueva Tarifa')

@section('content')

<style>
  .spgi-bg{ padding: 24px 0; }

  .glass-form{
    background: var(--bg-surface-glass); border: 1px solid var(--border-main);
    border-radius: 24px; box-shadow: var(--shadow-main); backdrop-filter: blur(20px);
    padding: 32px; max-width: 800px; margin: 0 auto;
  }

  .form-label{ font-weight: 600; color: var(--text-main); font-size: 0.9rem; }
  .form-control{
    height: 48px; border-radius: 12px; border: 1px solid var(--border-main);
    background: var(--bg-surface); color: var(--text-main); transition: all 0.2s;
  }
  .form-control:focus{
    border-color: var(--spgi-primary); box-shadow: 0 0 0 4px var(--spgi-primary-glow);
  }
  .form-text { font-size: 0.8rem; color: var(--text-muted); }

  .btn-spgi{
    background: linear-gradient(135deg, #3b82f6, #2563eb); border: 0; color: #fff;
    min-height: 48px; border-radius: 14px; padding: 0 32px; font-weight: 700;
    box-shadow: 0 10px 25px rgba(59, 130, 246, 0.2); transition: all 0.3s;
  }
  .btn-spgi:hover{ transform: translateY(-2px); box-shadow: 0 15px 35px rgba(59, 130, 246, 0.3); color: #fff; }

  .section-title{
    font-size: 1.1rem; font-weight: 800; color: var(--text-main); margin-bottom: 20px;
    display: flex; align-items: center; gap: 10px;
  }
  .section-title i{ color: var(--spgi-primary); }
</style>

<div class="spgi-bg">
  <div class="container">

    <div class="d-flex align-items-center justify-content-between mb-4 max-w-800 mx-auto" style="max-width: 800px;">
        <div>
            <h1 class="h3 mb-1 fw-bold text-gradient">Crear Tarifa</h1>
            <p class="text-muted mb-0">Registra un nuevo tipo de soporte y sus precios.</p>
        </div>
        <a href="{{ route('tarifarios.index') }}" class="btn btn-outline-secondary rounded-pill">
            <i class="bi bi-arrow-left me-2"></i> Cancelar
        </a>
    </div>

    <div class="glass-form animate__animated animate__fadeInUp">
        <form action="{{ route('tarifarios.store') }}" method="POST">
            @csrf

            <div class="section-title">
                <i class="bi bi-info-circle-fill"></i> Información de la Tarifa
            </div>

            <div class="row g-4 mb-4">
                <div class="col-md-12">
                    <label class="form-label">Tipo de Tarifario</label>
                    <div class="input-group">
                        <select name="tipo_tarifario_id" id="tipo_tarifario_id" class="form-select" style="height: 48px; border-radius: 12px 0 0 12px;">
                            <option value="">-- Seleccione (Opcional) --</option>
                            @foreach($tipoSoportes as $tipo)
                                <option value="{{ $tipo->id }}" {{ old('tipo_tarifario_id') == $tipo->id ? 'selected' : '' }}>{{ $tipo->nombre }}</option>
                            @endforeach
                        </select>
                        <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#modalAddTipo" style="border-radius: 0 12px 12px 0;">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                    </div>
                    @error('tipo_tarifario_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="col-12">
                    <label class="form-label">Descripción <span class="text-danger">*</span></label>
                    <input type="text" name="descripcion" class="form-control" placeholder="Ej. Soporte Hardware, Entrenamiento..." required value="{{ old('descripcion') }}">
                    @error('descripcion') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Básico Int (RD$ o Texto)</label>
                    <input type="text" name="basico_int" class="form-control" placeholder="Ej. 600.00, VARIABLE, N/A" value="{{ old('basico_int') }}">
                    @error('basico_int') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Avanzado Int (RD$ o Texto)</label>
                    <input type="text" name="avanzado_int" class="form-control" placeholder="Ej. 1,000.00, VARIABLE, N/A" value="{{ old('avanzado_int') }}">
                    @error('avanzado_int') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Básico Ext (RD$ o Texto)</label>
                    <input type="text" name="basico_ext" class="form-control" placeholder="Ej. 1,500.00, VARIABLE, N/A" value="{{ old('basico_ext') }}">
                    @error('basico_ext') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Avanzado Ext (RD$ o Texto)</label>
                    <input type="text" name="avanzado_ext" class="form-control" placeholder="Ej. 2,500.00, VARIABLE, N/A" value="{{ old('avanzado_ext') }}">
                    @error('avanzado_ext') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-12">
                    <label class="form-label">Valor (Honorarios o precio único)</label>
                    <input type="text" name="valor" class="form-control" placeholder="Ej. 500.00, VARIABLE..." value="{{ old('valor') }}">
                    <div class="form-text">Si este tarifario es de honorario o precio único y no aplica lo de básico/avanzado.</div>
                    @error('valor') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>
            </div>

            <hr class="my-4 border-secondary opacity-25">

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-spgi">
                    <i class="bi bi-save me-2"></i> Guardar Tarifa
                </button>
            </div>
        </form>
    </div>

  </div>
</div>

<!-- Modal Añadir Tipo Soporte -->
<div class="modal fade" id="modalAddTipo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: var(--bg-surface); border-radius: 20px;">
            <div class="modal-header border-bottom border-secondary border-opacity-10">
                <h5 class="modal-title fw-bold">Nuevo Tipo de Tarifario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label">Nombre del Tipo <span class="text-danger">*</span></label>
                    <input type="text" id="nuevoTipoNombre" class="form-control" placeholder="Ej. Honorario Profesional">
                </div>
            </div>
            <div class="modal-footer border-top border-secondary border-opacity-10">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary rounded-pill px-4" onclick="guardarTipoSoporte()">Guardar</button>
            </div>
        </div>
    </div>
</div>

<script>
function guardarTipoSoporte() {
    const nombre = document.getElementById('nuevoTipoNombre').value;
    if(!nombre) {
        alert('Ingrese un nombre');
        return;
    }

    fetch('{{ route("tarifarios.tipo.ajax") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ nombre: nombre })
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            // Añadir al select
            const select = document.getElementById('tipo_tarifario_id');
            const option = document.createElement('option');
            option.value = data.data.id;
            option.text = data.data.nombre;
            option.selected = true;
            select.add(option);

            // Cerrar modal
            const modalEl = document.getElementById('modalAddTipo');
            const modal = bootstrap.Modal.getInstance(modalEl);
            modal.hide();
            
            // Limpiar
            document.getElementById('nuevoTipoNombre').value = '';
        } else {
            alert('Error al guardar el tipo.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error de conexión o el tipo ya existe.');
    });
}
</script>

@endsection
