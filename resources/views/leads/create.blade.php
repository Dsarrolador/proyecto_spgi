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
                                <label class="form-label fw-bold">Persona de Contacto</label>
                                <input type="text" name="persona_contacto" class="form-control" value="{{ old('persona_contacto') }}" placeholder="Nombre de la persona contactada">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Contacto (Teléfono/WhatsApp)</label>
                                <input type="text" name="contacto" class="form-control" value="{{ old('contacto') }}" placeholder="Ej: +1 809-555-5555">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold">Correo Electrónico</label>
                                <input type="email" name="correo" class="form-control" value="{{ old('correo') }}" placeholder="ejemplo@correo.com">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Total Estimado ($)</label>
                                <input type="text" id="total_estimado_input" class="form-control" placeholder="0.00">
                                <input type="hidden" name="total_estimado" id="total_estimado_hidden" value="{{ old('total_estimado') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Subir Cotización (PDF / Excel)</label>
                                <input type="file" name="cotizacion_pdf" class="form-control" accept=".pdf,.xlsx,.xls">
                                <small class="text-muted">Formatos permitidos: PDF, XLSX, XLS. Máx: 10MB</small>
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
    // --- Calculation Logic ---
    const calcCosto = document.getElementById('calc_costo');
    const calcItbisPerc = document.getElementById('calc_itbis_perc');
    const calcMarginPerc = document.getElementById('calc_margin_perc');
    const calcItbisSalesPerc = document.getElementById('calc_itbis_sales_perc');
    const calcQty = document.getElementById('calc_qty');
    const calcAdjPrice = document.getElementById('calc_adj_price');
    const calculoDataHidden = document.getElementById('calculo_data_hidden');

    function updateCalculations() {
        const costo = parseFloat(calcCosto.value) || 0;
        const itbis_perc = parseFloat(calcItbisPerc.value) || 0;
        const margin_perc = parseFloat(calcMarginPerc.value) || 0;
        const itbis_sales_perc = parseFloat(calcItbisSalesPerc.value) || 0;
        const qty = parseFloat(calcQty.value) || 1;
        const adj_price = parseFloat(calcAdjPrice.value) || 0;

        const itbis_amt = costo * (itbis_perc / 100);
        const subtotal = costo + itbis_amt;
        const price_si = margin_perc < 100 ? (subtotal / (1 - (margin_perc / 100))) : 0;
        const margin_amt = price_si - subtotal;
        const itbis_sales_amt = price_si * (itbis_sales_perc / 100);
        const final_price = price_si + itbis_sales_amt;
        
        const final_margin_unit = adj_price > 0 ? (adj_price - subtotal) : 0;
        const diff = adj_price > 0 ? (final_margin_unit - margin_amt) : 0;
        const total_margin = final_margin_unit * qty;
        const total_value = adj_price * qty;

        // Update UI
        document.getElementById('res_itbis').innerText = itbis_amt.toFixed(2);
        document.getElementById('res_subtotal').innerText = subtotal.toFixed(2);
        document.getElementById('res_price_si').innerText = price_si.toFixed(2);
        document.getElementById('res_margin_amt').innerText = margin_amt.toFixed(2);
        document.getElementById('res_itbis_sales').innerText = itbis_sales_amt.toFixed(2);
        document.getElementById('res_final_price').innerText = final_price.toFixed(2);
        document.getElementById('res_diff').innerText = diff.toFixed(2);
        document.getElementById('res_total_margin').innerText = total_margin.toFixed(2);
        document.getElementById('res_total_value').innerText = total_value.toFixed(2);
        document.getElementById('res_final_margin_unit').innerText = final_margin_unit.toFixed(2);

        // Sync with Total Estimado
        let mainTotal = 0;
        if (total_value > 0) {
            mainTotal = total_value;
        } else if (final_price > 0) {
            mainTotal = final_price;
        }

        if (mainTotal > 0) {
            hiddenInput.value = mainTotal.toFixed(2);
            displayInput.value = formatNumber(mainTotal.toFixed(2));
        }

        // Save JSON data
        calculoDataHidden.value = JSON.stringify({
            costo, itbis_perc, margin_perc, itbis_sales_perc, adj_price, qty
        });
    }

    [calcCosto, calcItbisPerc, calcMarginPerc, calcItbisSalesPerc, calcQty, calcAdjPrice].forEach(input => {
        input.addEventListener('input', updateCalculations);
    });

    // Handle old value on load
    if (hiddenInput.value) {
        displayInput.value = formatNumber(hiddenInput.value);
    }
});
</script>
@endsection
