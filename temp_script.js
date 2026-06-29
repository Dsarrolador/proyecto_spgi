
let productIdx = 0;
const calculationId = null;
const leadData = null;

document.addEventListener('DOMContentLoaded', () => {
    if (leadData) {
        document.getElementById('global_tasa').value = leadData.global_tasa || 63.23;
        document.getElementById('global_itbis_compra').value = leadData.global_itbis_compra || 18;
        document.getElementById('global_ganancia').value = leadData.global_ganancia || 25;
        document.getElementById('global_itbis_ventas').value = leadData.global_itbis_ventas || 18;
        const items = leadData.items || [];
        if (items.length > 0) items.forEach(item => addProduct(item));
        else addProduct();
    } else addProduct();
    updateAll();
});

function addProduct(data = {}) {
    productIdx++;
    const container = document.getElementById('products-container');
    const row = document.createElement('div');
    row.className = 'product-row product-item-col';
    row.id = `product_col_${productIdx}`;
    
    row.innerHTML = `
        <input type="hidden" class="item-is-honorario" value="${data.is_honorario ? '1' : '0'}">
        <input type="hidden" class="item-honorario-val" value="${data.honorario_val || 0}">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center gap-3 w-50">
                <span class="badge bg-dark rounded-pill">#${productIdx}</span>
                <input type="text" class="form-control border-0 bg-transparent fw-800 fs-5 p-0 item-nombre" value="${data.nombre_articulo || 'Nuevo Artículo...'}" oninput="updateAll()">
            </div>
            <button type="button" class="btn btn-sm btn-outline-danger border-0" onclick="removeProduct(${productIdx})"><i class="bi bi-trash3-fill"></i></button>
        </div>
        <div class="row-grid">
            <div class="item-group">
                <label class="item-label">Divisa</label>
                <select class="m-input-compact item-currency" onchange="updateAll()">
                    <option value="DOP" ${data.moneda === 'DOP' ? 'selected' : ''}>Pesos (DOP)</option>
                    <option value="USD" ${data.moneda === 'USD' ? 'selected' : ''}>Dólares (USD)</option>
                </select>
            </div>
            <div class="item-group">
                <label class="item-label">Costo (S/I)</label>
                <input type="number" class="m-input-compact m-input-editable item-costo" value="${data.costo || ''}" oninput="updateAll()">
            </div>
            <div class="item-group">
                <div class="d-flex justify-content-between align-items-center">
                    <label class="item-label text-warning mb-0">ITBIS %</label>
                    <input type="checkbox" class="item-has-itbis-c" ${data.has_itbis_c !== false ? 'checked' : ''} onchange="updateAll()">
                </div>
                <input type="number" class="m-input-compact m-input-editable border-warning item-itbis-compra" value="${data.itbis_perc ?? document.getElementById('global_itbis_compra').value}" oninput="updateAll()">
            </div>
            <div class="item-group">
                <label class="item-label text-success">Ganancia %</label>
                <input type="number" class="m-input-compact m-input-editable border-success item-ganancia" value="${data.margin_perc ?? document.getElementById('global_ganancia').value}" oninput="updateAll()">
            </div>
            <div class="item-group">
                <label class="item-label">Total Compra</label>
                <div class="fw-bold" id="res_subtotal_c_${productIdx}">$0.00</div>
            </div>
            <div class="item-group">
                <label class="item-label text-warning">Precio SI</label>
                <div class="fw-bold text-warning" id="res_p_si_${productIdx}">$0.00</div>
            </div>
            <div class="item-group">
                <label class="item-label text-muted">Precio Final</label>
                <div class="fw-bold text-muted" id="res_precio_f_${productIdx}">$0.00</div>
            </div>
            <div class="item-group">
                <label class="item-label text-primary">P. Ajustado</label>
                <input type="number" class="m-input-compact m-input-editable border-primary item-adj" value="${data.adj_price || ''}" oninput="updateAll()">
            </div>
            <div class="item-group text-end">
                <label class="item-label text-success">Ganancia Unit.</label>
                <div class="fw-bold text-success" id="res_ganancia_u_${productIdx}">$0.00</div>
            </div>
            <div class="item-group">
                <label class="item-label">Cant.</label>
                <input type="number" class="m-input-compact m-input-editable item-qty" value="${data.qty || 1}" oninput="updateAll()">
            </div>
            <div class="item-group text-end">
                <label class="item-label text-success">Ganancia T</label>
                <div class="h5 fw-900 text-success mb-0" id="res_ganancia_f_${productIdx}">$0.00</div>
            </div>
            <div class="item-group text-end">
                <label class="item-label text-primary">Valor Total</label>
                <div class="h5 fw-900 text-primary mb-0" id="res_valor_t_${productIdx}">$0.00</div>
            </div>
        </div>
    `;
    container.appendChild(row);
    updateAll();
}

function removeProduct(id) {
    if (document.querySelectorAll('.product-item-col').length > 1) {
        document.getElementById(`product_col_${id}`).remove();
        updateAll();
    }
}

function showHonorarioModal() {
    document.getElementById('honorario_servicio').value = "";
    document.getElementById('honorario_categoria').innerHTML = '<option value="">Seleccione categoría...</option>';
    new bootstrap.Modal(document.getElementById('modalHonorario')).show();
}

function updateHonorarioCategorias() {
    const select = document.getElementById('honorario_servicio');
    const catSelect = document.getElementById('honorario_categoria');
    catSelect.innerHTML = '<option value="">Seleccione categoría...</option>';
    
    if (!select.value) return;
    
    const opt = select.options[select.selectedIndex];
    
    const addCat = (val, text) => {
        if (val && val.trim() !== '' && val !== 'null') {
            catSelect.innerHTML += `<option value="${val}">${text} (${val})</option>`;
        }
    };
    
    addCat(opt.dataset.basicoInt, 'Básico Interno');
    addCat(opt.dataset.avanzadoInt, 'Avanzado Interno');
    addCat(opt.dataset.basicoExt, 'Básico Externo');
    addCat(opt.dataset.avanzadoExt, 'Avanzado Externo');
    addCat(opt.dataset.valor, 'Valor Único');
}

function parseCurrencyText(text) {
    if (!text) return 0;
    // Remove RD$, US$, spaces, and commas
    let val = text.replace(/RD\$|US\$|\s|,/gi, '');
    return parseFloat(val) || 0;
}

function addHonorarioToQuote() {
    const select = document.getElementById('honorario_servicio');
    const catSelect = document.getElementById('honorario_categoria');
    
    if (!select.value || !catSelect.value) {
        Swal.fire({ icon: 'warning', title: 'Atención', text: 'Debes seleccionar un servicio y una categoría.' });
        return;
    }
    
    const sName = select.options[select.selectedIndex].dataset.desc;
    const cName = catSelect.options[catSelect.selectedIndex].text.split(' (')[0];
    const stringVal = catSelect.value;
    const numericVal = parseCurrencyText(stringVal);
    
    addProduct({
        nombre_articulo: `Honorario: ${sName} - ${cName}`,
        moneda: 'DOP',
        costo: 0,
        adj_price: numericVal,
        has_itbis_c: false,
        itbis_perc: 0,
        margin_perc: 0,
        is_honorario: true,
        honorario_val: numericVal
    });
    
    bootstrap.Modal.getInstance(document.getElementById('modalHonorario')).hide();
}

function updateAll() {
    const tasa = parseFloat(document.getElementById('global_tasa').value) || 1;
    const itbis_global = parseFloat(document.getElementById('global_itbis_compra').value) || 0;
    const ganancia_global = parseFloat(document.getElementById('global_ganancia').value) || 0;
    const itbis_v_perc = parseFloat(document.getElementById('global_itbis_ventas').value) || 0;
    let totalV = 0; let totalM = 0; let totalI = 0; let count = 0;

    document.querySelectorAll('.product-item-col').forEach(col => {
        count++;
        const id = col.id.split('_')[2];
        const moneda = col.querySelector('.item-currency').value;
        const adj = parseFloat(col.querySelector('.item-adj').value) || 0;
        const qty = parseFloat(col.querySelector('.item-qty').value) || 1;
        const isHonorario = col.querySelector('.item-is-honorario') && col.querySelector('.item-is-honorario').value === '1';

        const fmt = (v) => '$' + v.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});

        if (isHonorario) {
            const hVal = parseFloat(col.querySelector('.item-honorario-val').value) || 0;
            const val_t = hVal * qty;
            
            if(document.getElementById(`res_subtotal_c_${id}`)) document.getElementById(`res_subtotal_c_${id}`).innerText = fmt(0);
            if(document.getElementById(`res_p_si_${id}`)) document.getElementById(`res_p_si_${id}`).innerText = fmt(0);
            if(document.getElementById(`res_precio_f_${id}`)) document.getElementById(`res_precio_f_${id}`).innerText = fmt(hVal);
            if(document.getElementById(`res_ganancia_u_${id}`)) document.getElementById(`res_ganancia_u_${id}`).innerText = fmt(0);
            if(document.getElementById(`res_ganancia_f_${id}`)) document.getElementById(`res_ganancia_f_${id}`).innerText = fmt(0);
            if(document.getElementById(`res_valor_t_${id}`)) document.getElementById(`res_valor_t_${id}`).innerText = fmt(val_t);

            totalV += val_t;
            
            col.querySelector('.item-costo').value = 0; col.querySelector('.item-costo').disabled = true;
            col.querySelector('.item-adj').value = hVal; col.querySelector('.item-adj').disabled = true;
            if(col.querySelector('.item-ganancia')) { col.querySelector('.item-ganancia').value = 0; col.querySelector('.item-ganancia').disabled = true; }
            if(col.querySelector('.item-itbis-compra')) { col.querySelector('.item-itbis-compra').value = 0; col.querySelector('.item-itbis-compra').disabled = true; }
            if(col.querySelector('.item-has-itbis-c')) { col.querySelector('.item-has-itbis-c').checked = false; col.querySelector('.item-has-itbis-c').disabled = true; }
            
            return;
        }

        const costoOrig = parseFloat(col.querySelector('.item-costo').value) || 0;
        
        const itbisInput = col.querySelector('.item-itbis-compra');
        const hasItbisC = col.querySelector('.item-has-itbis-c').checked;
        const itbis_c_perc = (itbisInput && hasItbisC) ? (parseFloat(itbisInput.value) || 0) : 0;
        
        const gananciaInput = col.querySelector('.item-ganancia');
        const ganancia_perc = gananciaInput ? (parseFloat(gananciaInput.value) || 0) : ganancia_global;

        // Fórmulas EXACTAS de Excel:
        const costoDOP = moneda === 'USD' ? costoOrig * tasa : costoOrig;
        const itbis_compra = costoDOP * (itbis_c_perc / 100);
        const costo_total_compra = (costoDOP + itbis_compra) * qty; // Total Compra (de todas las unidades)
        
        const ganancia_u = costoDOP * (ganancia_perc / 100);
        const p_si = costoDOP + ganancia_u;
        const itbis_v = p_si * (itbis_v_perc / 100);
        const p_f = p_si + itbis_v;
        
        const fP = adj > 0 ? adj : p_f;
        const val_t = fP * qty; // Valor Total
        
        // Ganancia T (Según Excel siempre se basa en el markup sugerido)
        const gan_f = ganancia_u * qty;
        
        if(document.getElementById(`res_subtotal_c_${id}`)) document.getElementById(`res_subtotal_c_${id}`).innerText = fmt(costo_total_compra);
        if(document.getElementById(`res_p_si_${id}`)) document.getElementById(`res_p_si_${id}`).innerText = fmt(p_si);
        if(document.getElementById(`res_precio_f_${id}`)) document.getElementById(`res_precio_f_${id}`).innerText = fmt(p_f);
        if(document.getElementById(`res_ganancia_u_${id}`)) document.getElementById(`res_ganancia_u_${id}`).innerText = fmt(ganancia_u);
        if(document.getElementById(`res_ganancia_f_${id}`)) document.getElementById(`res_ganancia_f_${id}`).innerText = fmt(gan_f);
        if(document.getElementById(`res_valor_t_${id}`)) document.getElementById(`res_valor_t_${id}`).innerText = fmt(val_t);

        totalV += val_t; totalM += gan_f; totalI += (itbis_compra * qty);
    });

    document.getElementById('total-items-count').innerText = count;
    document.getElementById('grand_total_margin').innerText = '$' + totalM.toLocaleString('en-US', {minimumFractionDigits: 2});
    document.getElementById('grand_total_itbis').innerText = '$' + totalI.toLocaleString('en-US', {minimumFractionDigits: 2});
    
    const totalUSD = totalV / tasa;
    document.getElementById('grand_total_value').innerHTML = `
        <div class="h3 fw-900 mb-0">$${totalV.toLocaleString('en-US', {minimumFractionDigits: 2})}</div>
        <div class="fs-5 text-muted">US$${totalUSD.toLocaleString('en-US', {minimumFractionDigits: 2})}</div>
    `;
}

function showMatrixModal() {
    const modal = new bootstrap.Modal(document.getElementById('modalMatrix'));
    const body = document.getElementById('matrix-horizontal-body');
    const foot = document.getElementById('matrix-horizontal-foot');
    const tasa = parseFloat(document.getElementById('global_tasa').value) || 1;
    const itbis_c_perc = parseFloat(document.getElementById('global_itbis_compra').value) || 0;
    const ganancia_perc = parseFloat(document.getElementById('global_ganancia').value) || 0;
    const itbis_v_perc = parseFloat(document.getElementById('global_itbis_ventas').value) || 0;

    body.innerHTML = '';
    let gTotals = { cost: 0, itbis: 0, sub: 0, sug: 0, adj: 0, qty: 0, gan: 0, val: 0 };

    document.querySelectorAll('.product-item-col').forEach(col => {
        const moneda = col.querySelector('.item-currency').value;
        const adj = parseFloat(col.querySelector('.item-adj').value) || 0;
        const qty = parseFloat(col.querySelector('.item-qty').value) || 0;
        const isHonorario = col.querySelector('.item-is-honorario') && col.querySelector('.item-is-honorario').value === '1';

        let cOrig, cDOP, iC, sub, pSi, pF, fP, vT, gF;

        if (isHonorario) {
            const hVal = parseFloat(col.querySelector('.item-honorario-val').value) || 0;
            cOrig = 0; cDOP = 0; iC = 0; sub = 0;
            pSi = 0; pF = hVal; fP = hVal;
            vT = hVal * qty; gF = 0;
        } else {
            cOrig = parseFloat(col.querySelector('.item-costo').value) || 0;
            const hasItbisC = col.querySelector('.item-has-itbis-c').checked;
            const itbis_c_perc = hasItbisC ? (parseFloat(col.querySelector('.item-itbis-compra').value) || 0) : 0;
            const ganancia_perc = parseFloat(col.querySelector('.item-ganancia').value) || 0;
            
            cDOP = moneda === 'USD' ? cOrig * tasa : cOrig;
            iC = cDOP * (itbis_c_perc / 100);
            sub = (cDOP + iC) * qty;
            
            const gan_u = cDOP * (ganancia_perc / 100);
            pSi = cDOP + gan_u;
            pF = pSi * (1 + (itbis_v_perc/100));
            
            fP = adj > 0 ? adj : pF;
            vT = fP * qty;
            gF = gan_u * qty;
        }

        const fmt = (v) => '$' + v.toLocaleString('en-US', {minimumFractionDigits: 2});
        
        body.innerHTML += `
            <tr>
                <td class="ps-4 fw-bold">${col.querySelector('.item-nombre').value}</td>
                <td class="text-center"><span class="currency-badge ${moneda === 'USD' ? 'badge-usd' : 'badge-dop'}">${moneda}</span></td>
                <td class="text-end">${fmt(cOrig)}</td>
                <td class="text-end">${fmt(cDOP)}</td>
                <td class="text-end">${fmt(iC)}</td>
                <td class="text-end fw-bold">${fmt(sub)}</td>
                <td class="text-end text-warning fw-bold">${fmt(pSi)}</td>
                <td class="text-end text-muted">${fmt(pF)}</td>
                <td class="text-end text-primary fw-bold">${fmt(adj)}</td>
                <td class="text-center">${qty}</td>
                <td class="text-end text-success fw-bold">${fmt(gF)}</td>
                <td class="text-end pe-4 fw-900">${fmt(vT)}</td>
            </tr>
        `;
        gTotals.cost += cDOP; gTotals.itbis += iC; gTotals.sub += sub; gTotals.qty += qty; gTotals.gan += gF; gTotals.val += vT;
    });

    foot.innerHTML = `
        <tr class="table-light">
            <td colspan="2" class="ps-4 fw-900">TOTALES GENERALES (DOP)</td>
            <td class="text-end">$${gTotals.cost.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
            <td class="text-end">$${gTotals.itbis.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
            <td class="text-end">$${gTotals.sub.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
            <td colspan="4"></td>
            <td class="text-center">${gTotals.qty}</td>
            <td class="text-end text-success">$${gTotals.gan.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
            <td class="text-end pe-4 text-primary">$${gTotals.val.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
        </tr>
        <tr class="table-light border-top">
            <td colspan="2" class="ps-4 fw-900 text-muted">TOTALES GENERALES (USD)</td>
            <td class="text-end text-muted">US$${(gTotals.cost / tasa).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
            <td class="text-end text-muted">US$${(gTotals.itbis / tasa).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
            <td class="text-end text-muted">US$${(gTotals.sub / tasa).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
            <td colspan="4"></td>
            <td class="text-center text-muted">${gTotals.qty}</td>
            <td class="text-end text-muted">US$${(gTotals.gan / tasa).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
            <td class="text-end pe-4 text-muted">US$${(gTotals.val / tasa).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
        </tr>
    `;
    modal.show();
}

function exportToExcel() {
    const data = [["Artículo", "Divisa", "Costo Orig.", "Costo (S/I)", "ITBIS Compra", "Costo Total (C/I)", "P. Sin ITBIS", "P. Final (C/I)", "P. Ajustado", "Cant.", "Ganancia Total", "Subtotal Item"]];
    const tasa = parseFloat(document.getElementById('global_tasa').value) || 1;
    const itbis_v_p = parseFloat(document.getElementById('global_itbis_ventas').value) || 0;

    document.querySelectorAll('.product-item-col').forEach(col => {
        const n = col.querySelector('.item-nombre').value;
        const mon = col.querySelector('.item-currency').value;
        const adj = parseFloat(col.querySelector('.item-adj').value) || 0;
        const q = parseFloat(col.querySelector('.item-qty').value) || 0;
        const isHonorario = col.querySelector('.item-is-honorario') && col.querySelector('.item-is-honorario').value === '1';

        let cO, cD, iC, sub, pSi, pF, fP, vT, gF;

        if (isHonorario) {
            const hVal = parseFloat(col.querySelector('.item-honorario-val').value) || 0;
            cO = 0; cD = 0; iC = 0; sub = 0;
            pSi = 0; pF = hVal; fP = hVal;
            vT = hVal * q; gF = 0;
        } else {
            cO = parseFloat(col.querySelector('.item-costo').value) || 0;
            const hasItbisC = col.querySelector('.item-has-itbis-c').checked;
            const itbis_c_p = parseFloat(col.querySelector('.item-itbis-compra').value) || 0;
            const gan_p = parseFloat(col.querySelector('.item-ganancia').value) || 0;
            
            cD = mon === 'USD' ? cO * tasa : cO;
            iC = hasItbisC ? (cD * (itbis_c_p / 100)) : 0; 
            sub = (cD + iC) * q;
            
            const gan_u = cD * (gan_p / 100);
            pSi = cD + gan_u;
            pF = pSi * (1 + (itbis_v_p/100));
            
            fP = adj > 0 ? adj : pF;
            vT = fP * q; 
            gF = gan_u * q;
        }
        
        data.push([n, mon, cO, cD, iC, sub, pSi, pF, adj, q, gF, vT]);
    });

    const ws = XLSX.utils.aoa_to_sheet(data);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, "Cotización");
    XLSX.writeFile(wb, `Cotizacion_${null}.xlsx`);
}

async function saveCalculo() {
    const { value: nombreCalculo } = await Swal.fire({
        title: 'Nombre del Cálculo',
        input: 'text',
        inputLabel: 'Asigna un nombre para identificar este cálculo',
        inputValue: 'Cotización ' + new Date().toLocaleDateString(),
        showCancelButton: true,
        confirmButtonText: 'Guardar',
        cancelButtonText: 'Cancelar',
        inputValidator: (value) => {
            if (!value) return '¡Debes ingresar un nombre para guardar!'
        }
    });

    if (!nombreCalculo) return;

    const btn = document.getElementById('btnSaveCalculo');
    const orig = btn.innerHTML; btn.disabled = true; btn.innerHTML = 'Procesando...';
    const items = [];
    document.querySelectorAll('.product-item-col').forEach(col => {
        items.push({
            nombre_articulo: col.querySelector('.item-nombre').value,
            moneda: col.querySelector('.item-currency').value,
            costo: parseFloat(col.querySelector('.item-costo').value) || 0,
            adj_price: parseFloat(col.querySelector('.item-adj').value) || 0,
            itbis_perc: parseFloat(col.querySelector('.item-itbis-compra').value) || 0,
            has_itbis_c: col.querySelector('.item-has-itbis-c').checked,
            margin_perc: parseFloat(col.querySelector('.item-ganancia').value) || 0,
            qty: parseFloat(col.querySelector('.item-qty').value) || 0,
            itbis_sales_perc: parseFloat(document.getElementById('global_itbis_ventas').value) || 18,
            is_honorario: col.querySelector('.item-is-honorario') && col.querySelector('.item-is-honorario').value === '1',
            honorario_val: col.querySelector('.item-honorario-val') ? parseFloat(col.querySelector('.item-honorario-val').value) : 0,
        });
    });
    const total_estimado = parseFloat(document.getElementById('grand_total_value').innerText.replace(/[$,]/g, '')) || 0;
    const calculo_data = {
        global_tasa: document.getElementById('global_tasa').value,
        global_itbis_compra: document.getElementById('global_itbis_compra').value,
        global_ganancia: document.getElementById('global_ganancia').value,
        global_itbis_ventas: document.getElementById('global_itbis_ventas').value,
        items
    };
    try {
        const resp = await fetch(`/leads/${null}/save-calculo`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ total_estimado, calculo_data, nombre_calculo: nombreCalculo, calculation_id: calculationId })
        });
        const res = await resp.json();
        if (res.success) {
            Swal.fire({ 
                icon: 'success', 
                title: 'Cotización Guardada', 
                text: 'El cálculo "' + nombreCalculo + '" se ha guardado correctamente.',
                showConfirmButton: true,
                confirmButtonText: 'Ir al Listado',
                showCancelButton: true,
                cancelButtonText: 'Seguir Editando'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('leads.show', $lead->id) }}";
                }
            });
        }
    } catch (e) { alert('Error de conexión'); } finally { btn.disabled = false; btn.innerHTML = orig; }
}
