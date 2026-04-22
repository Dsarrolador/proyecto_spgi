@extends('layouts.app')

@section('page_title', 'Crear Nuevo Lead')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0" style="border-radius: 20px; overflow: hidden;">
                <div class="card-header bg-dark text-white p-4">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-briefcase me-2"></i> Nuevo Lead de Cliente</h5>
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

                    <form action="{{ route('leads.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-bold">Nombre del Lead / Empresa <span class="text-danger">*</span></label>
                                <input type="text" name="nombre" class="form-control" value="{{ old('nombre') }}" required placeholder="Ej: Juan Perez o Empresa S.A.">
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold">Dirección</label>
                                <textarea name="direccion" class="form-control" rows="2" placeholder="Ubicación física">{{ old('direccion') }}</textarea>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Contacto (Teléfono/WhatsApp)</label>
                                <input type="text" name="contacto" class="form-control" value="{{ old('contacto') }}" placeholder="Ej: +1 809-555-5555">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Correo Electrónico</label>
                                <input type="email" name="correo" class="form-control" value="{{ old('correo') }}" placeholder="ejemplo@correo.com">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Total Estimado ($)</label>
                                <input type="text" id="total_estimado_input" class="form-control" placeholder="0.00">
                                <input type="hidden" name="total_estimado" id="total_estimado_hidden" value="{{ old('total_estimado') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Subir Cotización (PDF)</label>
                                <input type="file" name="cotizacion_pdf" class="form-control" accept="application/pdf">
                                <small class="text-muted">Tamaño máximo: 10MB</small>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold">Observaciones</label>
                                <textarea name="observaciones" class="form-control" rows="4" placeholder="Detalles adicionales sobre el prospecto...">{{ old('observaciones') }}</textarea>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top d-flex gap-2">
                            <button type="submit" class="btn btn-success px-4 py-2 rounded-pill fw-bold">
                                <i class="bi bi-save me-1"></i> Guardar Lead
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
        // Remove non-numeric characters except for the decimal point
        let parts = val.toString().split(".");
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

    // Handle old value on load
    if (hiddenInput.value) {
        displayInput.value = formatNumber(hiddenInput.value);
    }
});
</script>
@endsection
