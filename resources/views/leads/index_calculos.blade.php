@extends('layouts.app')

@section('page_title', 'Dashboard de Cálculos')

@section('content')
<style>
    .btn-action {
        width: 42px; height: 42px; border-radius: 12px; display: inline-flex; 
        align-items: center; justify-content: center; transition: all 0.2s;
        border: 1px solid transparent; text-decoration: none !important;
    }
    .btn-action:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.2); }
    
    .btn-matrix { background: #0d6efd; color: #fff !important; }
    .btn-matrix:hover { background: #0b5ed7; }
    
    .btn-validar { background: #10b981; color: #fff !important; }
    .btn-validar:hover { background: #059669; }

    .btn-view { 
        background: var(--bg-surface); 
        color: var(--text-main) !important; 
        border-color: var(--border-main); 
    }
    .btn-view:hover { background: var(--border-main); }
    
    .btn-edit { background: #212529; color: #fff !important; }
    .btn-edit:hover { background: #000; }

    .table-premium { background: var(--bg-surface); }
    .table-premium thead th {
        background: #0f172a; color: #fff; font-size: 0.75rem; 
        text-transform: uppercase; letter-spacing: 1px; padding: 15px;
    }
    .table-premium tbody td { padding: 15px; border-bottom: 1px solid var(--border-main); color: var(--text-main); }
    
    .matrix-header { background: #1e293b; color: #fff; padding: 20px; }
    .card-dashboard { background: var(--bg-surface-glass); border: 1px solid var(--border-main); backdrop-filter: blur(10px); }

    /* Estilos Responsivos Adicionales */
    @media (max-width: 768px) {
        .btn-action { width: 36px; height: 36px; border-radius: 8px; }
        .table-premium th, .table-premium td { white-space: nowrap; }
        .h3 { font-size: 1.5rem !important; }
        .matrix-header h5 { font-size: 1.25rem !important; }
    }
    
    /* Prevenir salto de línea feo en la matriz para móviles */
    #modalMatrix .table th, #modalMatrix .table td {
        white-space: nowrap;
    }
</style>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-900 text-main mb-0">Dashboard de <span class="text-primary">Cálculos</span></h1>
            <p class="text-muted">Control de cotizaciones y estados de validación.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('leads.index') }}" class="btn btn-outline-secondary rounded-pill px-4 fw-bold">
                <i class="bi bi-arrow-left me-1"></i> Volver a Leads
            </a>
        </div>
    </div>

    <!-- Filtros por Estado Simplificados -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden card-dashboard">
        <div class="card-body p-3">
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <span class="text-muted small fw-bold text-uppercase me-2">Filtrar por:</span>
                <a href="{{ route('leads.indexCalculos') }}" class="btn btn-sm {{ !request('status') ? 'btn-primary' : 'btn-outline-primary' }} rounded-pill px-3 fw-bold">
                    Todos
                </a>
                @foreach(['Pendiente', 'En proceso', 'Realizado'] as $st)
                    <a href="{{ route('leads.indexCalculos', ['status' => $st]) }}" 
                       class="btn btn-sm {{ request('status') == $st ? 'btn-primary' : 'btn-outline-primary' }} rounded-pill px-3 fw-bold">
                        {{ $st }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden card-dashboard">
        <div class="table-responsive">
            <table class="table align-middle mb-0 table-premium">
                <thead>
                    <tr>
                        <th class="ps-4">Lead / Cliente</th>
                        <th class="text-center">Estado</th>
                        <th class="text-end">Total Cotizado</th>
                        <th class="text-center">Cotización (File)</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($calculations as $calc)
                    @php $l = $calc->lead; @endphp
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold text-main fs-6">{{ $calc->nombre }}</div>
                            <div class="text-muted small"><i class="bi bi-person me-1"></i>{{ $l->nombre }} ({{ $l->persona_contacto }})</div>
                        </td>
                        <td class="text-center">
                            @php
                                $statusClass = match($calc->status) {
                                    'Realizado' => 'bg-success',
                                    'En proceso' => 'bg-info',
                                    default => 'bg-warning text-dark'
                                };
                            @endphp
                            <span class="badge {{ $statusClass }} rounded-pill px-3 shadow-sm">{{ $calc->status }}</span>
                        </td>
                        <td class="text-end fw-900 text-primary fs-5">
                            ${{ number_format($calc->total_estimado, 2) }}
                        </td>
                        <td class="text-center">
                            @if($calc->files->count() > 0)
                                <div class="d-flex flex-column align-items-center gap-1 mb-2">
                                    @foreach($calc->files as $file)
                                        <div class="d-flex align-items-center bg-light rounded-pill px-2 py-1 shadow-sm border" style="font-size: 0.75rem;">
                                            <a href="{{ route('leads.downloadFile', $file->id) }}" target="_blank" class="text-decoration-none text-dark text-truncate d-inline-block" style="max-width: 100px;" title="{{ $file->filename }}">
                                                <i class="bi bi-file-earmark-text text-primary me-1"></i>{{ $file->filename }}
                                            </a>
                                            <form action="{{ route('leads.deleteFile', $file->id) }}" method="POST" class="ms-1 m-0 p-0 d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-link text-danger p-0 m-0 border-0" onclick="return confirm('¿Eliminar archivo?')" title="Eliminar">
                                                    <i class="bi bi-x-circle-fill"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @endforeach
                                </div>
                                <button class="btn btn-link btn-sm text-muted p-0 text-decoration-none" onclick="document.getElementById('file_input_{{ $calc->id }}').click()">
                                    <small><i class="bi bi-plus-circle me-1"></i>Agregar más</small>
                                </button>
                            @else
                                <button class="btn btn-sm btn-outline-success rounded-pill px-3 fw-bold shadow-sm" onclick="document.getElementById('file_input_{{ $calc->id }}').click()">
                                    <i class="bi bi-upload me-1"></i> Adjuntar
                                </button>
                            @endif
                            <form action="{{ route('leads.uploadFiles', $l->id) }}" method="POST" enctype="multipart/form-data" class="d-none">
                                @csrf
                                <input type="hidden" name="calculation_id" value="{{ $calc->id }}">
                                <input type="file" id="file_input_{{ $calc->id }}" name="cotizacion_files[]" multiple onchange="this.form.submit()" accept=".pdf,.xlsx,.xls,.doc,.docx">
                            </form>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('leads.show', $l->id) }}" class="btn-action btn-view" title="Ver Lead">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <button type="button" class="btn-action btn-matrix" title="Ver Matriz Horizontal" 
                                        onclick='viewMatrix(@json($calc->nombre), @json($calc->calculo_data))'>
                                    <i class="bi bi-table"></i>
                                </button>
                                
                                @if($calc->status !== 'Realizado')
                                    <button type="button" class="btn-action btn-validar" title="Validar Cotización"
                                            onclick="validarCalculation({{ $calc->id }}, '{{ $calc->nombre }}')">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                @endif

                                <a href="{{ route('leads.calculadora', [$l->id, 'calculation_id' => $calc->id]) }}" class="btn-action btn-edit" title="Editar Cálculo">
                                    <i class="bi bi-calculator"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-calculator fs-1 d-block mb-2"></i>
                            No hay registros con cálculos realizados.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center mt-3 p-3 border-top">
            {{ $calculations->links() }}
        </div>
    </div>
</div>

<!-- Modal Matrix -->
<div class="modal fade" id="modalMatrix" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content border-0">
            <div class="matrix-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0 fw-900 fs-4 text-white" id="matrixTitle">Vista de Cotización</h5>
                    <p class="mb-0 text-light opacity-75 small">Desglose horizontal detallado</p>
                </div>
                <div class="d-flex gap-3">
                    <button type="button" class="btn btn-success btn-lg rounded-pill px-4 fw-900 shadow-sm" id="btnExportExcel">
                        <i class="bi bi-file-earmark-excel me-2"></i> Exportar Excel
                    </button>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive h-100">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-dark text-white sticky-top">
                            <tr class="small fw-bold">
                                <th class="ps-4">ARTÍCULO</th>
                                <th class="text-center">DIVISA</th>
                                <th class="text-end">COSTO ORIG.</th>
                                <th class="text-end">COSTO DOP</th>
                                <th class="text-end text-muted small">ITBIS C.</th>
                                <th class="text-end">COSTO TOTAL</th>
                                <th class="text-end text-secondary">P. SUG. S/I</th>
                                <th class="text-end">P. SUGERIDO</th>
                                <th class="text-end">P. AJUSTADO</th>
                                <th class="text-center">CANT.</th>
                                <th class="text-end">GANANCIA R.</th>
                                <th class="text-end pe-4">SUBTOTAL V.</th>
                            </tr>
                        </thead>
                        <tbody id="matrixBody"></tbody>
                        <tfoot id="matrixFoot" class="fw-900"></tfoot>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-dark rounded-pill px-5 fw-bold" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
async function validarCalculation(id, name) {
    const result = await Swal.fire({
        title: '¿Validar Cotización?',
        text: `Se marcará la versión "${name}" como Realizada.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        confirmButtonText: 'Sí, validar',
        cancelButtonText: 'Cancelar'
    });
    if (result.isConfirmed) {
        try {
            const resp = await fetch(`/leads/calculations/${id}/validar`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            });
            const res = await resp.json();
            if (res.success) {
                Swal.fire({ icon: 'success', title: 'Validado', timer: 1500, showConfirmButton: false }).then(() => location.reload());
            } else {
                Swal.fire('Error', 'Servidor: ' + (res.error || 'Desconocido'), 'error');
            }
        } catch (e) { Swal.fire('Error', 'No se pudo procesar la solicitud (CORS o Red).', 'error'); }
    }
}

function viewMatrix(name, data) {
    if (!data) { Swal.fire('Aviso', 'No hay datos guardados.', 'warning'); return; }
    document.getElementById('matrixTitle').innerText = `Matriz: ${name}`;
    const body = document.getElementById('matrixBody');
    const foot = document.getElementById('matrixFoot');
    body.innerHTML = ''; foot.innerHTML = '';
    
    const tasa = parseFloat(data.global_tasa) || 63.23;
    const itbis_c_p = parseFloat(data.global_itbis_compra) || 18;
    const gan_p = parseFloat(data.global_ganancia) || 25;
    const itbis_v_p = parseFloat(data.global_itbis_ventas) || 18;

    let gT = { cost: 0, itbis: 0, sub: 0, qty: 0, gan: 0, val: 0 };
    const items = data.items || [];
    let totalHonorarios = 0;

    items.forEach(item => {
        const mon = item.moneda || 'DOP';
        const adj = parseFloat(item.adj_price) || 0;
        const qty = parseFloat(item.qty) || 0;
        const isHonorario = item.is_honorario || false;

        let cO, cD, iC, sub, pSi, pF, sell_price_si, vT, gF;

        if (isHonorario) {
            cO = 0; cD = 0; iC = 0; sub = 0;
            const hVal = parseFloat(item.honorario_val) || adj || 0;
            pSi = hVal; pF = hVal * (1 + (parseFloat(item.itbis_perc) || 18) / 100);
            sell_price_si = adj > 0 ? adj : pSi;
            vT = sell_price_si * (1 + (parseFloat(item.itbis_perc) || 18) / 100) * qty;
            gF = 0;
            totalHonorarios += sell_price_si * qty;
        } else {
            cO = parseFloat(item.costo) || 0;
            cD = mon === 'USD' ? cO * tasa : cO;
            
            const has_itbis_c = item.has_itbis_c !== false;
            const itbis_perc = has_itbis_c ? (parseFloat(item.itbis_perc) || itbis_c_p) : 0;
            iC = cD * (itbis_perc / 100);
            sub = (cD + iC) * qty;
            
            const item_gan_p = parseFloat(item.margin_perc) !== undefined ? parseFloat(item.margin_perc) : gan_p;
            const gan_u = cD * (item_gan_p / 100);
            pSi = cD + gan_u;
            pF = pSi * (1 + (itbis_v_p/100));
            
            sell_price_si = adj > 0 ? adj : pSi;
            vT = sell_price_si * (1 + (itbis_v_p/100)) * qty;
            gF = (sell_price_si - cD) * qty;
        }

        const fmt = (v) => '$' + v.toLocaleString('en-US', {minimumFractionDigits: 2});
        
        body.innerHTML += `
            <tr>
                <td class="ps-4 fw-bold text-main">${item.nombre_articulo}</td>
                <td class="text-center"><span class="badge ${mon === 'USD' ? 'bg-info' : 'bg-primary'} rounded-pill">${mon}</span></td>
                <td class="text-end text-main">${fmt(cO)}</td>
                <td class="text-end text-main">${fmt(cD)}</td>
                <td class="text-end text-muted small">${fmt(iC)}</td>
                <td class="text-end fw-bold text-main">${fmt(sub)}</td>
                <td class="text-end text-secondary">${fmt(pSi)}</td>
                <td class="text-end text-warning">${fmt(pF)}</td>
                <td class="text-end text-primary fw-bold">${fmt(adj)}</td>
                <td class="text-center text-main">${qty}</td>
                <td class="text-end text-success fw-bold">${fmt(gF)}</td>
                <td class="text-end pe-4 text-primary fw-900 fs-6">${fmt(vT)}</td>
            </tr>
        `;
        gT.cost += cD * qty; gT.itbis += iC * qty; gT.sub += sub; gT.qty += qty; gT.gan += gF; gT.val += vT;
    });

    foot.innerHTML = `
        ${totalHonorarios > 0 ? `
        <tr class="bg-surface border-bottom">
            <td colspan="10" class="ps-4 fw-900 text-info text-end">TOTAL HONORARIOS (DOP)</td>
            <td colspan="2" class="text-end pe-4 text-info fw-900">$${totalHonorarios.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
        </tr>
        ` : ''}
        <tr class="bg-surface">
            <td colspan="2" class="ps-4 fw-900 text-main">TOTALES GENERALES (DOP)</td>
            <td class="text-end text-main">-</td>
            <td class="text-end text-main">$${gT.cost.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
            <td class="text-end text-main">$${gT.itbis.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
            <td class="text-end text-main">$${gT.sub.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
            <td colspan="3"></td>
            <td class="text-center text-main">${gT.qty}</td>
            <td class="text-end text-success">$${gT.gan.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
            <td class="text-end pe-4 text-primary">$${gT.val.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
        </tr>
    `;
    new bootstrap.Modal(document.getElementById('modalMatrix')).show();
    document.getElementById('btnExportExcel').onclick = () => exportMatrixToExcel(name, data);
}

function exportMatrixToExcel(name, data) {
    const tableData = [["Artículo", "Divisa", "Costo Orig.", "Costo DOP", "ITBIS Compra", "Costo Total", "Precio Sug. (S/I)", "Precio Sug.", "Precio Ajustado", "Cant.", "Ganancia Real", "Subtotal Venta"]];
    const tasa = parseFloat(data.global_tasa) || 63.23;
    const itbis_c_p = parseFloat(data.global_itbis_compra) || 18;
    const gan_p = parseFloat(data.global_ganancia) || 25;
    const itbis_v_p = parseFloat(data.global_itbis_ventas) || 18;
    (data.items || []).forEach(item => {
        const mon = item.moneda || 'DOP';
        const adj = parseFloat(item.adj_price) || 0;
        const q = parseFloat(item.qty) || 0;
        const isHonorario = item.is_honorario || false;

        let cO, cD, iC, sub, pSi, pF, sell_price_si, vT, gF;

        if (isHonorario) {
            cO = 0; cD = 0; iC = 0; sub = 0;
            const hVal = parseFloat(item.honorario_val) || adj || 0;
            pSi = hVal; pF = hVal * (1 + (parseFloat(item.itbis_perc) || 18) / 100);
            sell_price_si = adj > 0 ? adj : pSi;
            vT = sell_price_si * (1 + (parseFloat(item.itbis_perc) || 18) / 100) * q; gF = 0;
        } else {
            cO = parseFloat(item.costo) || 0;
            cD = mon === 'USD' ? cO * tasa : cO;
            
            const has_itbis_c = item.has_itbis_c !== false;
            const itbis_perc = has_itbis_c ? (parseFloat(item.itbis_perc) || itbis_c_p) : 0;
            iC = cD * (itbis_perc / 100);
            sub = (cD + iC) * q;
            
            const item_gan_p = parseFloat(item.margin_perc) !== undefined ? parseFloat(item.margin_perc) : gan_p;
            const gan_u = cD * (item_gan_p / 100);
            pSi = cD + gan_u;
            pF = pSi * (1 + (itbis_v_p/100));
            
            sell_price_si = adj > 0 ? adj : pSi;
            vT = sell_price_si * (1 + (itbis_v_p/100)) * q; 
            gF = (sell_price_si - cD) * q;
        }
        tableData.push([item.nombre_articulo, mon, cO, cD, iC, sub, pSi, pF, adj, q, gF, vT]);
    });
    const ws = XLSX.utils.aoa_to_sheet(tableData);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, "Cotización");
    XLSX.writeFile(wb, `Cotizacion_${name}.xlsx`);
}
</script>
@endsection
