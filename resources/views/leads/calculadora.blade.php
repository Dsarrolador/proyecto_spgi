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
                <h1 class="h2 fw-900 mb-0">Cotizador: <span class="text-primary">{{ $lead->nombre }}</span></h1>
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
            <div class="col-md-4 text-end d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-info text-white rounded-pill px-4 fw-bold shadow-sm" onclick="showHonorarioModal()">
                    <i class="bi bi-person-workspace me-2"></i> Añadir Honorario
                </button>
                <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold" onclick="addProduct()">
                    <i class="bi bi-plus-circle-fill me-2"></i> Añadir Artículo
                </button>
            </div>
        </div>
    </div>

    <!-- Honorarios Agrupados -->
    <div id="honorarios-section" class="mb-4" style="display: none;">
        <h5 class="fw-bold text-info mb-3"><i class="bi bi-person-workspace me-2"></i>Honorarios y Servicios</h5>
        <div class="card border-info shadow-sm" style="border-radius: 16px; overflow: hidden;">
            <div class="card-body p-0">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-info">
                        <tr>
                            <th class="ps-4">Descripción</th>
                            <th class="text-center" style="width: 120px;">Cant.</th>
                            <th class="text-center" style="width: 160px;">ITBIS %</th>
                            <th class="text-end" style="width: 150px;">Valor Fijo</th>
                            <th class="text-end" style="width: 150px;">Total</th>
                            <th class="text-center pe-3" style="width: 80px;"></th>
                        </tr>
                    </thead>
                    <tbody id="honorarios-container">
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="4" class="text-end fw-bold text-uppercase">Total Honorarios:</td>
                            <td class="text-end fw-900 text-info fs-5" id="honorarios_total_ui">$0.00</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
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
                <div>
                    <div class="item-label text-warning">ITBIS (DOP)</div>
                    <div class="h4 fw-900 mb-0 text-warning" id="grand_total_itbis">$0.00</div>
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

<!-- Modal Honorario -->
<div class="modal fade" id="modalHonorario" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-info text-white border-0">
                <h5 class="modal-title fw-900"><i class="bi bi-person-workspace me-2"></i>Añadir Honorario / Servicio</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="item-label mb-2">Servicio</label>
                    <select id="honorario_servicio" class="form-select m-input-compact" onchange="updateHonorarioCategorias()">
                        <option value="">Seleccione un servicio...</option>
                        @foreach($tarifarios as $t)
                            <option value="{{ $t->id }}" 
                                data-basico-int="{{ $t->basico_int }}"
                                data-avanzado-int="{{ $t->avanzado_int }}"
                                data-basico-ext="{{ $t->basico_ext }}"
                                data-avanzado-ext="{{ $t->avanzado_ext }}"
                                data-valor="{{ $t->valor }}"
                                data-desc="{{ $t->descripcion }}">
                                {{ $t->descripcion }} {{ $t->tipoTarifario ? '('.$t->tipoTarifario->nombre.')' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="item-label mb-2">Categoría</label>
                    <select id="honorario_categoria" class="form-select m-input-compact">
                        <option value="">Seleccione categoría...</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer border-0 bg-light">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-info text-white rounded-pill px-4 fw-bold shadow-sm" onclick="addHonorarioToQuote()">Agregar a Cotización</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>
<script>
let productIdx = 0;
let globalSubtotalVal = 0;
const calculationId = @json($calculation ? $calculation->id : null);
const leadData = @json($calculation ? $calculation->calculo_data : $lead->calculo_data);

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
    const row = document.createElement('div');
    row.className = 'product-row product-item-col';
    row.id = `product_col_${productIdx}`;
    
    if (data.is_honorario) {
        document.getElementById('honorarios-section').style.display = 'block';
        const hContainer = document.getElementById('honorarios-container');
        const tr = document.createElement('tr');
        tr.className = 'product-item-col honorario-row';
        tr.id = `product_col_${productIdx}`;
        tr.innerHTML = `
            <input type="hidden" class="item-is-honorario" value="1">
            <input type="hidden" class="item-honorario-val" value="${data.honorario_val || 0}">
            <input type="hidden" class="item-currency" value="DOP">
            <input type="hidden" class="item-adj" value="${data.adj_price || 0}">
            <input type="hidden" class="item-costo" value="0">
            <input type="hidden" class="item-ganancia" value="0">
            
            <td class="ps-4">
                <input type="text" class="form-control border-0 bg-transparent fw-bold p-0 item-nombre" value="${data.nombre_articulo || 'Honorario'}" oninput="updateAll()" readonly>
            </td>
            <td class="text-center">
                <input type="number" class="form-control text-center item-qty" value="${data.qty || 1}" oninput="updateAll()">
            </td>
            <td class="text-center">
                <div class="d-inline-flex align-items-center gap-1 justify-content-center">
                    <input type="checkbox" class="form-check-input item-has-itbis-c" ${data.has_itbis_c ? 'checked' : ''} onchange="toggleItbisEditable(${productIdx})">
                    <input type="number" class="form-control text-center item-itbis-compra p-1" style="width: 65px; font-weight: 700; border-radius: 6px;" value="${data.has_itbis_c ? (data.itbis_perc !== undefined ? data.itbis_perc : 18) : 18}" ${data.has_itbis_c ? '' : 'disabled'} oninput="updateAll()">
                    <span class="small text-muted fw-bold">%</span>
                </div>
            </td>
            <td class="text-end fw-bold text-primary" id="res_precio_f_${productIdx}">
                $0.00
            </td>
            <td class="text-end fw-900 text-info" id="res_valor_t_${productIdx}">
                $0.00
            </td>
            <td class="text-center pe-3">
                <button type="button" class="btn btn-sm btn-outline-danger border-0" onclick="removeProduct(${productIdx})"><i class="bi bi-trash3-fill"></i></button>
            </td>
        `;
        hContainer.appendChild(tr);
        updateAll();
        return;
    }

    const container = document.getElementById('products-container');
    let innerContent = `
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

    row.innerHTML = `
        <input type="hidden" class="item-is-honorario" value="0">
        <input type="hidden" class="item-honorario-val" value="0">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center gap-3 w-50">
                <span class="badge bg-dark rounded-pill">#${productIdx}</span>
                <input type="text" class="form-control border-0 bg-transparent fw-800 fs-5 p-0 item-nombre" value="${data.nombre_articulo || 'Nuevo Artículo...'}" oninput="updateAll()">
            </div>
            <button type="button" class="btn btn-sm btn-outline-danger border-0" onclick="removeProduct(${productIdx})"><i class="bi bi-trash3-fill"></i></button>
        </div>
        ${innerContent}
    `;
    container.appendChild(row);
    updateAll();
}

function removeProduct(id) {
    if (document.querySelectorAll('.product-item-col').length > 1) {
        document.getElementById(`product_col_${id}`).remove();
        
        if (document.querySelectorAll('.honorario-row').length === 0) {
            document.getElementById('honorarios-section').style.display = 'none';
        }
        
        updateAll();
    }
}

function toggleItbisEditable(id) {
    const col = document.getElementById(`product_col_${id}`);
    if (col) {
        const checkbox = col.querySelector('.item-has-itbis-c');
        const itbisInput = col.querySelector('.item-itbis-compra');
        if (checkbox && itbisInput) {
            itbisInput.disabled = !checkbox.checked;
            if (!checkbox.checked) {
                itbisInput.value = 18;
            }
        }
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
        itbis_perc: 18,
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
    let totalV = 0; let totalM = 0; let totalI = 0; let count = 0; let totalHonorarios = 0;
    let totalCostoEquipos = 0;

    document.querySelectorAll('.product-item-col').forEach(col => {
        count++;
        const id = col.id.split('_')[2];
        const moneda = col.querySelector('.item-currency').value;
        const adj = col.querySelector('.item-adj') ? parseFloat(col.querySelector('.item-adj').value) || 0 : 0;
        const qty = parseFloat(col.querySelector('.item-qty').value) || 1;
        const isHonorario = col.querySelector('.item-is-honorario') && col.querySelector('.item-is-honorario').value === '1';

        const fmt = (v) => '$' + v.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});

        if (isHonorario) {
            const hVal = parseFloat(col.querySelector('.item-honorario-val').value) || 0;
            const itbisInput = col.querySelector('.item-itbis-compra');
            const itbis_perc = itbisInput ? (parseFloat(itbisInput.value) || 0) : 18;
            
            const p_si = hVal;
            const p_f = hVal * (1 + itbis_perc / 100);
            const sell_price_si = adj > 0 ? adj : p_si;
            
            const val_t = sell_price_si * qty;
            const item_itbis = sell_price_si * (itbis_perc / 100) * qty;
            
            if(document.getElementById(`res_precio_f_${id}`)) document.getElementById(`res_precio_f_${id}`).innerText = fmt(p_f);
            if(document.getElementById(`res_valor_t_${id}`)) document.getElementById(`res_valor_t_${id}`).innerText = fmt(val_t);
            if(document.getElementById(`res_ganancia_u_${id}`)) document.getElementById(`res_ganancia_u_${id}`).innerText = fmt(0);
            if(document.getElementById(`res_ganancia_f_${id}`)) document.getElementById(`res_ganancia_f_${id}`).innerText = fmt(0);

            totalV += val_t;
            totalHonorarios += val_t;
            totalI += item_itbis;
            return;
        }

        const costoOrig = parseFloat(col.querySelector('.item-costo').value) || 0;
        
        const itbisInput = col.querySelector('.item-itbis-compra');
        const hasItbisC = col.querySelector('.item-has-itbis-c').checked;
        const itbis_c_perc = (itbisInput && hasItbisC) ? (parseFloat(itbisInput.value) || 0) : 0;
        
        const gananciaInput = col.querySelector('.item-ganancia');
        const ganancia_perc = gananciaInput ? (parseFloat(gananciaInput.value) || 0) : ganancia_global;

        const costoDOP = moneda === 'USD' ? costoOrig * tasa : costoOrig;
        const itbis_compra = costoDOP * (itbis_c_perc / 100);
        const costo_total_compra = (costoDOP + itbis_compra) * qty;
        
        const ganancia_u = costoDOP * (ganancia_perc / 100);
        const p_si = costoDOP + ganancia_u;
        const itbis_v = p_si * (itbis_v_perc / 100);
        const p_f = p_si + itbis_v;
        
        const sell_price_si = adj > 0 ? adj : p_si;
        const val_t = sell_price_si * qty;
        const item_itbis = sell_price_si * (itbis_v_perc / 100) * qty;
        
        const real_gan_u = sell_price_si - costoDOP;
        const real_gan_t = real_gan_u * qty;
        
        if(document.getElementById(`res_subtotal_c_${id}`)) document.getElementById(`res_subtotal_c_${id}`).innerText = fmt(costo_total_compra);
        if(document.getElementById(`res_p_si_${id}`)) document.getElementById(`res_p_si_${id}`).innerText = fmt(p_si);
        if(document.getElementById(`res_precio_f_${id}`)) document.getElementById(`res_precio_f_${id}`).innerText = fmt(p_f);
        if(document.getElementById(`res_ganancia_u_${id}`)) document.getElementById(`res_ganancia_u_${id}`).innerText = fmt(real_gan_u);
        if(document.getElementById(`res_ganancia_f_${id}`)) document.getElementById(`res_ganancia_f_${id}`).innerText = fmt(real_gan_t);
        if(document.getElementById(`res_valor_t_${id}`)) document.getElementById(`res_valor_t_${id}`).innerText = fmt(val_t);

        totalV += val_t;
        totalCostoEquipos += costoDOP * qty;
        totalI += item_itbis;
    });

    totalM = totalV - totalHonorarios - totalCostoEquipos;
    globalSubtotalVal = totalV;

    document.getElementById('honorarios_total_ui').innerText = '$' + totalHonorarios.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    
    document.getElementById('total-items-count').innerText = count;
    document.getElementById('grand_total_margin').innerText = '$' + totalM.toLocaleString('en-US', {minimumFractionDigits: 2});
    document.getElementById('grand_total_itbis').innerText = '$' + totalI.toLocaleString('en-US', {minimumFractionDigits: 2});
    
    const totalConITBIS = totalV + totalI;
    const totalUSD = totalConITBIS / tasa;
    document.getElementById('grand_total_value').innerHTML = `
        <div class="h3 fw-900 mb-0">$${totalConITBIS.toLocaleString('en-US', {minimumFractionDigits: 2})}</div>
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
    let totalHonorarios = 0;

    document.querySelectorAll('.product-item-col').forEach(col => {
        const moneda = col.querySelector('.item-currency').value;
        const adj = parseFloat(col.querySelector('.item-adj').value) || 0;
        const qty = parseFloat(col.querySelector('.item-qty').value) || 0;
        const isHonorario = col.querySelector('.item-is-honorario') && col.querySelector('.item-is-honorario').value === '1';

        let cOrig, cDOP, iC, sub, pSi, pF, sell_price_si, vT, gF;

        if (isHonorario) {
            const hVal = parseFloat(col.querySelector('.item-honorario-val').value) || 0;
            const itbisInput = col.querySelector('.item-itbis-compra');
            const itbis_perc = itbisInput ? (parseFloat(itbisInput.value) || 0) : 18;
            const p_f = hVal * (1 + itbis_perc / 100);
            
            cOrig = 0; cDOP = 0; iC = 0; sub = 0;
            pSi = hVal; pF = p_f; sell_price_si = adj > 0 ? adj : pSi;
            vT = sell_price_si * (1 + itbis_perc / 100) * qty; gF = 0;
            totalHonorarios += sell_price_si * qty;
        } else {
            cOrig = parseFloat(col.querySelector('.item-costo').value) || 0;
            const hasItbisC = col.querySelector('.item-has-itbis-c').checked;
            const itbis_c_perc_val = hasItbisC ? (parseFloat(col.querySelector('.item-itbis-compra').value) || 0) : 0;
            const ganancia_perc_val = parseFloat(col.querySelector('.item-ganancia').value) || 0;
            
            cDOP = moneda === 'USD' ? cOrig * tasa : cOrig;
            iC = cDOP * (itbis_c_perc_val / 100);
            sub = (cDOP + iC) * qty;
            
            const gan_u = cDOP * (ganancia_perc_val / 100);
            pSi = cDOP + gan_u;
            pF = pSi * (1 + (itbis_v_perc/100));
            
            sell_price_si = adj > 0 ? adj : pSi;
            vT = sell_price_si * (1 + (itbis_v_perc/100)) * qty;
            gF = (sell_price_si - cDOP) * qty;
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
        gTotals.cost += cDOP * qty; gTotals.itbis += iC * qty; gTotals.sub += sub; gTotals.qty += qty; gTotals.gan += gF; gTotals.val += vT;
    });

    foot.innerHTML = `
        ${totalHonorarios > 0 ? `
        <tr class="table-light border-bottom">
            <td colspan="10" class="ps-4 fw-900 text-info text-end">TOTAL HONORARIOS (DOP)</td>
            <td colspan="2" class="text-end pe-4 text-info fw-900">$${totalHonorarios.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
        </tr>
        <tr class="table-light border-bottom">
            <td colspan="10" class="ps-4 fw-900 text-info text-end opacity-75">TOTAL HONORARIOS (USD)</td>
            <td colspan="2" class="text-end pe-4 text-info fw-900 opacity-75">US$${(totalHonorarios / tasa).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
        </tr>
        ` : ''}
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
    const data = [["Artículo", "Divisa", "Costo Orig.", "Costo (S/I)", "ITBIS Compra", "Costo Total (C/I)", "P. Sin ITBIS", "P. Final (C/I)", "P. Ajustado", "Cant.", "Ganancia Total", "Subtotal Venta"]];
    const tasa = parseFloat(document.getElementById('global_tasa').value) || 1;
    const itbis_v_p = parseFloat(document.getElementById('global_itbis_ventas').value) || 0;

    document.querySelectorAll('.product-item-col').forEach(col => {
        const n = col.querySelector('.item-nombre').value;
        const mon = col.querySelector('.item-currency').value;
        const adj = parseFloat(col.querySelector('.item-adj').value) || 0;
        const q = parseFloat(col.querySelector('.item-qty').value) || 0;
        const isHonorario = col.querySelector('.item-is-honorario') && col.querySelector('.item-is-honorario').value === '1';

        let cO, cD, iC, sub, pSi, pF, sell_price_si, vT, gF;

        if (isHonorario) {
            const hVal = parseFloat(col.querySelector('.item-honorario-val').value) || 0;
            const itbisInput = col.querySelector('.item-itbis-compra');
            const itbis_perc = itbisInput ? (parseFloat(itbisInput.value) || 0) : 18;
            const p_f = hVal * (1 + itbis_perc / 100);
            
            cO = 0; cD = 0; iC = 0; sub = 0;
            pSi = hVal; pF = p_f; sell_price_si = adj > 0 ? adj : pSi;
            vT = sell_price_si * (1 + itbis_perc / 100) * q; gF = 0;
        } else {
            cO = parseFloat(col.querySelector('.item-costo').value) || 0;
            const hasItbisC = col.querySelector('.item-has-itbis-c').checked;
            const itbis_c_p_val = parseFloat(col.querySelector('.item-itbis-compra').value) || 0;
            const gan_p_val = parseFloat(col.querySelector('.item-ganancia').value) || 0;
            
            cD = mon === 'USD' ? cO * tasa : cO;
            iC = hasItbisC ? (cD * (itbis_c_p_val / 100)) : 0; 
            sub = (cD + iC) * q;
            
            const gan_u = cD * (gan_p_val / 100);
            pSi = cD + gan_u;
            pF = pSi * (1 + (itbis_v_p/100));
            
            sell_price_si = adj > 0 ? adj : pSi;
            vT = sell_price_si * (1 + (itbis_v_p/100)) * q; 
            gF = (sell_price_si - cD) * q;
        }
        
        data.push([n, mon, cO, cD, iC, sub, pSi, pF, adj, q, gF, vT]);
    });

    const ws = XLSX.utils.aoa_to_sheet(data);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, "Cotización");
    XLSX.writeFile(wb, `Cotizacion_${@json($lead->nombre)}.xlsx`);
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
    const total_estimado = globalSubtotalVal;
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
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection
