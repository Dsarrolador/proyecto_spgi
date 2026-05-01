@extends('layouts.app')

@section('page_title', 'Cotizador Matrix Pro: ' . $lead->nombre)

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    :root {
        --calc-primary: #3b82f6;
        --calc-success: #10b981;
        --calc-warning: #f59e0b;
        --calc-usd: #8b5cf6;
    }

    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background-color: var(--bg-master);
        color: var(--text-main);
    }

    .page-header-spgi {
        padding: 2rem 0;
        background: var(--bg-surface);
        border-bottom: 1px solid var(--border-main);
        margin-bottom: 2rem;
        box-shadow: var(--shadow-main);
    }

    .config-card {
        background: var(--bg-surface);
        border-radius: 20px;
        padding: 1.5rem;
        border: 1px solid var(--border-main);
        box-shadow: var(--shadow-main);
        margin-bottom: 2rem;
    }

    .product-row {
        background: var(--bg-surface);
        border-radius: 20px;
        border: 1px solid var(--border-main);
        box-shadow: var(--shadow-main);
        margin-bottom: 1.5rem;
        transition: all 0.3s ease;
        padding: 1.5rem;
    }

    .product-row:hover {
        border-color: var(--calc-primary);
        box-shadow: 0 10px 25px rgba(0,0,0,0.05);
    }

    .row-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        gap: 1.25rem;
        align-items: end;
    }

    .item-group { display: flex; flex-direction: column; gap: 4px; }
    .item-label {
        font-size: 0.65rem;
        font-weight: 800;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .m-input-compact {
        width: 100%;
        border: 1px solid var(--border-main);
        border-radius: 10px;
        padding: 8px 12px;
        font-weight: 700;
        background: var(--bg-master);
        color: var(--text-main);
        font-size: 0.9rem;
    }

    .m-input-editable { border-color: rgba(59, 130, 246, 0.3); background: rgba(59, 130, 246, 0.03); }
    
    .currency-badge {
        font-size: 0.65rem;
        font-weight: 800;
        padding: 2px 6px;
        border-radius: 4px;
        text-transform: uppercase;
    }
    .badge-usd { background: var(--calc-usd); color: #fff; }
    .badge-dop { background: var(--calc-primary); color: #fff; }

    /* Modal Table Horizontal */
    .table-horizontal th {
        background: var(--bg-surface);
        color: var(--text-muted);
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        border-bottom: 2px solid var(--border-main) !important;
    }

    .bottom-bar {
        position: fixed;
        bottom: 0; left: 0; right: 0;
        background: var(--bg-surface-glass);
        backdrop-filter: blur(20px);
        border-top: 1px solid var(--border-main);
        padding: 1.25rem 0;
        z-index: 1000;
        box-shadow: 0 -10px 30px rgba(0,0,0,0.1);
    }
</style>

<div class="page-header-spgi">
    <div class="container-fluid px-5">
        <div class="row align-items-center">
            <div class="col-md-5">
                <h1 class="h2 fw-900 mb-0">Cotizador <span class="text-primary">Matrix Pro v2</span></h1>
            </div>
            <div class="col-md-7 text-end d-flex gap-2 justify-content-end">
                <button type="button" class="btn btn-success btn-lg rounded-pill px-4 fw-bold" onclick="exportToExcel()">
                    <i class="bi bi-file-earmark-excel me-2"></i> Excel
                </button>
                <button type="button" class="btn btn-outline-primary btn-lg rounded-pill px-4 fw-bold" onclick="showMatrixModal()">
                    <i class="bi bi-list-ul me-2"></i> Enlistar
                </button>
                <button type="button" class="btn btn-primary btn-lg rounded-pill px-4 fw-bold shadow" onclick="saveCalculo()" id="btnSaveCalculo">
                    <i class="bi bi-cloud-arrow-up-fill me-2"></i> Guardar Todo
                </button>
                <a href="{{ route('leads.index') }}" class="btn btn-outline-secondary btn-lg rounded-pill px-3">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-5 pb-5 mb-5">
    <!-- Config Global -->
    <div class="config-card">
        <div class="row g-4 align-items-end">
            <div class="col-md-2">
                <label class="item-label text-primary">Tasa de Cambio (USD)</label>
                <input type="number" id="global_tasa" class="m-input-compact border-primary" value="63.23" oninput="updateAll()">
            </div>
            <div class="col-md-2">
                <label class="item-label">ITBIS Compra %</label>
                <input type="number" id="global_itbis_compra" class="m-input-compact" value="18" oninput="updateAll()">
            </div>
            <div class="col-md-2">
                <label class="item-label">Ganancia %</label>
                <input type="number" id="global_ganancia" class="m-input-compact" value="25" oninput="updateAll()">
            </div>
            <div class="col-md-2">
                <label class="item-label">ITBIS Venta %</label>
                <input type="number" id="global_itbis_ventas" class="m-input-compact" value="18" oninput="updateAll()">
            </div>
            <div class="col-md-4 text-end">
                <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold" onclick="addProduct()">
                    <i class="bi bi-plus-circle-fill me-2"></i> Añadir Artículo
                </button>
            </div>
        </div>
    </div>

    <!-- Items -->
    <div id="products-container"></div>
</div>

<div class="bottom-bar">
    <div class="container-fluid px-5">
        <div class="row align-items-center">
            <div class="col-md-6 d-flex gap-5">
                <div>
                    <div class="item-label">Artículos</div>
                    <div class="h4 fw-900 mb-0" id="total-items-count">0</div>
                </div>
                <div>
                    <div class="item-label text-success">Ganancia Neta (DOP)</div>
                    <div class="h4 fw-900 mb-0 text-success" id="grand_total_margin">$0.00</div>
                </div>
            </div>
            <div class="col-md-6 text-end">
                <span class="item-label me-3">Total Cotizado (DOP)</span>
                <span class="display-6 fw-900 text-primary" id="grand_total_value">$0.00</span>
            </div>
        </div>
    </div>
</div>

<!-- Modal Enlistar Horizontal -->
<div class="modal fade" id="modalMatrix" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fw-900">Listado Detallado de Cotización</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive h-100">
                    <table class="table table-hover align-middle mb-0 text-nowrap table-horizontal">
                        <thead class="sticky-top">
                            <tr>
                                <th class="ps-4">Artículo</th>
                                <th class="text-center">Divisa</th>
                                <th class="text-end">Costo Orig.</th>
                                <th class="text-end">Costo (S/I)</th>
                                <th class="text-end">ITBIS Compra</th>
                                <th class="text-end">Costo Total (C/I)</th>
                                <th class="text-end text-secondary">P. Sin ITBIS</th>
                                <th class="text-end text-warning">P. Final (C/I)</th>
                                <th class="text-end text-primary">P. Ajustado</th>
                                <th class="text-center">Cant.</th>
                                <th class="text-end text-success">Ganancia Total</th>
                                <th class="text-end pe-4 text-primary">Subtotal Item</th>
                            </tr>
                        </thead>
                        <tbody id="matrix-horizontal-body"></tbody>
                        <tfoot class="table-light fw-900" id="matrix-horizontal-foot">
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold" onclick="saveCalculo()">Guardar Cotización</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>
<script>
let productIdx = 0;
const leadData = @json($lead->calculo_data);

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

function updateAll() {
    const tasa = parseFloat(document.getElementById('global_tasa').value) || 1;
    const itbis_global = parseFloat(document.getElementById('global_itbis_compra').value) || 0;
    const ganancia_global = parseFloat(document.getElementById('global_ganancia').value) || 0;
    const itbis_v_perc = parseFloat(document.getElementById('global_itbis_ventas').value) || 0;

    let totalV = 0; let totalM = 0; let count = 0;

    document.querySelectorAll('.product-item-col').forEach(col => {
        count++;
        const id = col.id.split('_')[2];
        const moneda = col.querySelector('.item-currency').value;
        const costoOrig = parseFloat(col.querySelector('.item-costo').value) || 0;
        
        const itbisInput = col.querySelector('.item-itbis-compra');
        const hasItbisC = col.querySelector('.item-has-itbis-c').checked;
        const itbis_c_perc = (itbisInput && hasItbisC) ? (parseFloat(itbisInput.value) || 0) : 0;
        
        const gananciaInput = col.querySelector('.item-ganancia');
        const ganancia_perc = gananciaInput ? (parseFloat(gananciaInput.value) || 0) : ganancia_global;

        const adj = parseFloat(col.querySelector('.item-adj').value) || 0;
        const qty = parseFloat(col.querySelector('.item-qty').value) || 1;

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

        const fmt = (v) => '$' + v.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        
        if(document.getElementById(`res_subtotal_c_${id}`)) document.getElementById(`res_subtotal_c_${id}`).innerText = fmt(costo_total_compra);
        if(document.getElementById(`res_p_si_${id}`)) document.getElementById(`res_p_si_${id}`).innerText = fmt(p_si);
        if(document.getElementById(`res_precio_f_${id}`)) document.getElementById(`res_precio_f_${id}`).innerText = fmt(p_f);
        if(document.getElementById(`res_ganancia_u_${id}`)) document.getElementById(`res_ganancia_u_${id}`).innerText = fmt(ganancia_u);
        if(document.getElementById(`res_ganancia_f_${id}`)) document.getElementById(`res_ganancia_f_${id}`).innerText = fmt(gan_f);
        if(document.getElementById(`res_valor_t_${id}`)) document.getElementById(`res_valor_t_${id}`).innerText = fmt(val_t);

        totalV += val_t; totalM += gan_f;
    });

    document.getElementById('total-items-count').innerText = count;
    document.getElementById('grand_total_margin').innerText = '$' + totalM.toLocaleString('en-US', {minimumFractionDigits: 2});
    
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
        const cOrig = parseFloat(col.querySelector('.item-costo').value) || 0;
        const hasItbisC = col.querySelector('.item-has-itbis-c').checked;
        const itbis_c_perc = hasItbisC ? (parseFloat(col.querySelector('.item-itbis-compra').value) || 0) : 0;
        const ganancia_perc = parseFloat(col.querySelector('.item-ganancia').value) || 0;
        const adj = parseFloat(col.querySelector('.item-adj').value) || 0;
        const qty = parseFloat(col.querySelector('.item-qty').value) || 0;

        const cDOP = moneda === 'USD' ? cOrig * tasa : cOrig;
        const iC = cDOP * (itbis_c_perc / 100);
        const sub = (cDOP + iC) * qty;
        
        const gan_u = cDOP * (ganancia_perc / 100);
        const pSi = cDOP + gan_u;
        const pF = pSi * (1 + (itbis_v_perc/100));
        
        const fP = adj > 0 ? adj : pF;
        const vT = fP * qty;
        const gF = gan_u * qty;

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
        const cO = parseFloat(col.querySelector('.item-costo').value) || 0;
        const itbis_c_p = parseFloat(col.querySelector('.item-itbis-compra').value) || 0;
        const hasItbisC = col.querySelector('.item-has-itbis-c').checked;
        const gan_p = parseFloat(col.querySelector('.item-ganancia').value) || 0;
        const adj = parseFloat(col.querySelector('.item-adj').value) || 0;
        const q = parseFloat(col.querySelector('.item-qty').value) || 0;
        
        const cD = mon === 'USD' ? cO * tasa : cO;
        const iC = hasItbisC ? (cD * (itbis_c_p / 100)) : 0; 
        const sub = (cD + iC) * q;
        
        const gan_u = cD * (gan_p / 100);
        const pSi = cD + gan_u;
        const pF = pSi * (1 + (itbis_v_p/100));
        
        const fP = adj > 0 ? adj : pF;
        const vT = fP * q; 
        const gF = gan_u * q;
        
        data.push([n, mon, cO, cD, iC, sub, pSi, pF, adj, q, gF, vT]);
    });

    const ws = XLSX.utils.aoa_to_sheet(data);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, "Cotización");
    XLSX.writeFile(wb, `Cotizacion_${@json($lead->nombre)}.xlsx`);
}

async function saveCalculo() {
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
        const resp = await fetch(`/leads/${@json($lead->id)}/save-calculo`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ total_estimado, calculo_data })
        });
        const res = await resp.json();
        if (res.success) {
            Swal.fire({ 
                icon: 'success', 
                title: 'Cotización Guardada', 
                text: 'Los cambios se han sincronizado correctamente.',
                showConfirmButton: false, 
                timer: 2000 
            });
        }
    } catch (e) { alert('Error de conexión'); } finally { btn.disabled = false; btn.innerHTML = orig; }
}
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection
