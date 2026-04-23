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
                                <label class="form-label fw-bold">Persona de Contacto</label>
                                <input type="text" name="persona_contacto" class="form-control" value="{{ old('persona_contacto', $lead->persona_contacto) }}" placeholder="Nombre de la persona contactada">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Contacto (Teléfono/WhatsApp)</label>
                                <input type="text" name="contacto" class="form-control" value="{{ old('contacto', $lead->contacto) }}" placeholder="Ej: +1 809-555-5555">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold">Correo Electrónico</label>
                                <input type="email" name="correo" class="form-control" value="{{ old('correo', $lead->correo) }}" placeholder="ejemplo@correo.com">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Total Estimado ($)</label>
                                <input type="text" id="total_estimado_input" class="form-control" placeholder="0.00">
                                <input type="hidden" name="total_estimado" id="total_estimado_hidden" value="{{ old('total_estimado', $lead->total_estimado) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Actualizar Cotización (PDF / Excel)</label>
                                <input type="file" name="cotizacion_pdf" class="form-control" accept=".pdf,.xlsx,.xls">
                                @if($lead->cotizacion_pdf)
                                    @php
                                        $ext = pathinfo($lead->cotizacion_pdf, PATHINFO_EXTENSION);
                                        $isExcel = in_array($ext, ['xlsx', 'xls']);
                                    @endphp
                                    <div class="mt-2">
                                        <a href="{{ asset('storage/' . $lead->cotizacion_pdf) }}" target="_blank" class="btn btn-sm {{ $isExcel ? 'btn-outline-success' : 'btn-outline-danger' }}">
                                            <i class="bi {{ $isExcel ? 'bi-file-earmark-excel' : 'bi-file-earmark-pdf' }} me-1"></i> Ver {{ strtoupper($ext) }} actual
                                        </a>
                                    </div>
                                @endif
                                <small class="text-muted d-block mt-1">PDF, XLSX, XLS. Máx: 10MB</small>
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

        // Sync with Total Estimado (optional on edit, but good for UX)
        if (total_value > 0 || final_price > 0) {
            let mainTotal = total_value > 0 ? total_value : final_price;
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

    // Load existing data
    const existingData = {!! json_encode($lead->calculo_data ?? []) !!};
    if (existingData && typeof existingData === 'object' && Object.keys(existingData).length > 0) {
        calcCosto.value = existingData.costo || '';
        calcItbisPerc.value = existingData.itbis_perc || 18;
        calcMarginPerc.value = existingData.margin_perc || 25;
        calcItbisSalesPerc.value = existingData.itbis_sales_perc || 18;
        calcQty.value = existingData.qty || 1;
        calcAdjPrice.value = existingData.adj_price || '';
        updateCalculations();
    }

    // Handle initial total value
    if (hiddenInput.value) {
        displayInput.value = formatNumber(hiddenInput.value);
    }
});
</script>
@endsection
