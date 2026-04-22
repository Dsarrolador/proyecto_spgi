@extends('layouts.app')

@section('page_title', 'Editar Lead: ' . $lead->nombre)

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0" style="border-radius: 20px; overflow: hidden;">
                <div class="card-header bg-dark text-white p-4 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-pencil-square me-2"></i> Editar Lead</h5>
                    <span class="badge bg-secondary rounded-pill">ID: #{{ $lead->id }}</span>
                </div>
                <div class="card-body p-4">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('leads.update', $lead->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-9">
                                <label class="form-label fw-bold">Nombre del Lead / Empresa <span class="text-danger">*</span></label>
                                <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $lead->nombre) }}" required>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-bold">Estado</label>
                                <select name="status" class="form-select fw-bold">
                                    <option value="Pendiente" {{ old('status', $lead->status) == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                                    <option value="Seguimiento" {{ old('status', $lead->status) == 'Seguimiento' ? 'selected' : '' }}>En Seguimiento</option>
                                    <option value="Ganado" {{ old('status', $lead->status) == 'Ganado' ? 'selected' : '' }}>Ganado</option>
                                    <option value="Perdido" {{ old('status', $lead->status) == 'Perdido' ? 'selected' : '' }}>Perdido</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold">Dirección</label>
                                <textarea name="direccion" class="form-control" rows="2">{{ old('direccion', $lead->direccion) }}</textarea>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Contacto (Teléfono/WhatsApp)</label>
                                <input type="text" name="contacto" class="form-control" value="{{ old('contacto', $lead->contacto) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Correo Electrónico</label>
                                <input type="email" name="correo" class="form-control" value="{{ old('correo', $lead->correo) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Total Estimado ($)</label>
                                <input type="text" id="total_estimado_input" class="form-control" placeholder="0.00">
                                <input type="hidden" name="total_estimado" id="total_estimado_hidden" value="{{ old('total_estimado', $lead->total_estimado) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Actualizar Cotización (PDF)</label>
                                <input type="file" name="cotizacion_pdf" class="form-control" accept="application/pdf">
                                @if($lead->cotizacion_pdf)
                                    <div class="mt-2">
                                        <a href="{{ asset('storage/' . $lead->cotizacion_pdf) }}" target="_blank" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-file-earmark-pdf me-1"></i> Ver PDF actual
                                        </a>
                                    </div>
                                @endif
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold">Observaciones</label>
                                <textarea name="observaciones" class="form-control" rows="4">{{ old('observaciones', $lead->observaciones) }}</textarea>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill fw-bold">
                                <i class="bi bi-save me-1"></i> Actualizar Lead
                            </button>
                            <a href="{{ route('leads.index') }}" class="btn btn-light px-4 py-2 rounded-pill fw-bold border">
                                Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const displayInput = document.getElementById('total_estimado_input');
    const hiddenInput = document.getElementById('total_estimado_hidden');

    function formatNumber(val) {
        if (!val) return '';
        let strVal = val.toString();
        let parts = strVal.split(".");
        parts[0] = parts[0].replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        return parts.join(".");
    }

    displayInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/,/g, '');
        if (isNaN(value) && value !== '.') {
            e.target.value = e.target.value.slice(0, -1);
            return;
        }
        hiddenInput.value = value;
        e.target.value = formatNumber(value);
    });

    // Handle old value or current value on load
    if (hiddenInput.value) {
        displayInput.value = formatNumber(hiddenInput.value);
    }
});
</script>
@endsection
