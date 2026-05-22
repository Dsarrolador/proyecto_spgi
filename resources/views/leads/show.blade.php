@extends('layouts.app')

@section('page_title', 'Detalle de Lead: ' . $lead->nombre)

@section('content')
<style>
    .lead-detail-card {
        background: var(--bg-surface-glass);
        border: 1px solid var(--border-main);
        border-radius: 24px;
        box-shadow: var(--shadow-main);
        backdrop-filter: blur(20px);
        overflow: hidden;
    }
    .lead-header {
        background: #0b1220;
        color: #fff;
        padding: 30px;
    }
    .lead-body {
        padding: 40px;
    }
    .info-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: var(--text-muted);
        font-weight: 800;
        margin-bottom: 5px;
    }
    .info-value {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--text-main);
        margin-bottom: 25px;
    }
    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 20px;
        border-radius: 999px;
        font-weight: 800;
        font-size: 0.85rem;
    }
    .status-pendiente { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
    .status-seguimiento { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
    .status-ganado { background: rgba(16, 185, 129, 0.1); color: #10b981; }
    .status-perdido { background: rgba(239, 68, 68, 0.1); color: #ef4444; }

    .obs-box {
        background: rgba(var(--text-main), 0.03);
        border: 1px solid var(--border-main);
        border-radius: 16px;
        padding: 20px;
        white-space: pre-wrap;
        color: var(--text-main);
        font-style: italic;
    }
</style>

<div class="container py-5">
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <a href="{{ route('leads.index') }}" class="btn btn-light rounded-pill border">
            <i class="bi bi-arrow-left me-1"></i> Volver al listado
        </a>
        <div class="d-flex gap-2">
            <a href="{{ route('leads.edit', $lead->id) }}" class="btn btn-primary rounded-pill px-4">
                <i class="bi bi-pencil me-1"></i> Editar
            </a>
            <form action="{{ route('leads.destroy', $lead->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger rounded-pill px-4" onclick="return confirm('¿Eliminar este lead?')">
                    <i class="bi bi-trash me-1"></i> Eliminar
                </button>
            </form>
        </div>
    </div>

    <div class="lead-detail-card">
        <div class="lead-header">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h1 class="display-6 fw-bold mb-1">{{ $lead->nombre }}</h1>
                    <p class="mb-0 text-white-50"><i class="bi bi-calendar3 me-2"></i>Registrado el {{ $lead->created_at->format('d/m/Y') }}</p>
                </div>
                @php
                    $statusClass = 'status-' . strtolower(str_replace(' ', '-', $lead->status));
                @endphp
                <div class="status-pill {{ $statusClass }}">
                    <i class="bi bi-info-circle"></i> {{ $lead->status }}
                </div>
            </div>
        </div>

        <div class="lead-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="info-label">Persona de Contacto</div>
                    <div class="info-value">
                        <i class="bi bi-person text-primary me-2"></i>{{ $lead->persona_contacto ?? 'No especificada' }}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-label">Contacto (Tel/WA)</div>
                    <div class="info-value">
                        <i class="bi bi-telephone text-primary me-2"></i>{{ $lead->contacto ?? 'No especificado' }}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-label">Correo Electrónico</div>
                    <div class="info-value">
                        <i class="bi bi-envelope text-primary me-2"></i>{{ $lead->correo ?? 'No especificado' }}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-label">Total Estimado</div>
                    <div class="info-value text-success fw-bold">
                        <i class="bi bi-currency-dollar me-1"></i>{{ $lead->total_estimado ? number_format($lead->total_estimado, 2) : '0.00' }}
                    </div>
                </div>
                
                <div class="col-12 mt-3">
                    <div class="info-label">Dirección</div>
                    <div class="info-value">
                        <i class="bi bi-geo-alt text-danger me-2"></i>{{ $lead->direccion ?? 'No especificada' }}
                    </div>
                </div>

                <div class="col-12 mt-4">
                    <div class="info-label">Cotización Adjunta</div>
                    @if($lead->cotizacion_pdf)
                        @php
                            $ext = pathinfo($lead->cotizacion_pdf, PATHINFO_EXTENSION);
                            $isExcel = in_array($ext, ['xlsx', 'xls']);
                        @endphp
                        @if(!$isExcel)
                            @php
                                $downloadUrl = route('leads.serveFile', ['path' => $lead->cotizacion_pdf]);
                            @endphp
                            <div class="pdf-preview-container" style="height: 500px; border: 1px solid var(--border-main); border-radius: 16px; overflow: hidden; background: #525659;">
                                <iframe src="{{ $downloadUrl }}" width="100%" height="100%" style="border: none;"></iframe>
                            </div>
                            <div class="mt-3">
                                <a href="{{ $downloadUrl }}" target="_blank" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                    <i class="bi bi-box-arrow-up-right me-1"></i> Abrir PDF en pestaña nueva
                                </a>
                            </div>
                        @else
                            <div class="text-center p-5 border rounded-4" style="background: rgba(16, 185, 129, 0.05); border-color: rgba(16, 185, 129, 0.2) !important;">
                                @php
                                    $downloadUrl = route('leads.serveFile', ['path' => $lead->cotizacion_pdf]);
                                @endphp
                                <i class="bi bi-file-earmark-excel text-success display-1 mb-3 d-block"></i>
                                <h5 class="fw-bold">Documento Excel Adjunto</h5>
                                <p class="text-muted small">Haz clic debajo para descargar y ver el archivo.</p>
                                <a href="{{ $downloadUrl }}" class="btn btn-success rounded-pill px-4 fw-bold">
                                    <i class="bi bi-download me-1"></i> Descargar {{ strtoupper($ext) }}
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="text-muted small p-4 border rounded-3 bg-light text-center">
                            <i class="bi bi-file-earmark-x fs-2 d-block mb-2"></i> No hay cotización adjunta.
                        </div>
                    @endif
                </div>

                @if($lead->calculations->count() > 0)
                <div class="col-12 mt-5">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="info-label mb-0"><i class="bi bi-clock-history me-2"></i>Historial de Cálculos (Versiones)</div>
                        <a href="{{ route('leads.calculadora', $lead->id) }}" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold">
                            <i class="bi bi-plus-circle me-1"></i> Nuevo Cálculo
                        </a>
                    </div>
                    <div class="row g-3">
                        @foreach($lead->calculations->sortByDesc('created_at') as $calc)
                            <div class="col-md-4">
                                <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: rgba(var(--text-main), 0.02); border: 1px solid var(--border-main) !important;">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <div class="fw-bold text-main text-truncate" title="{{ $calc->nombre }}">{{ $calc->nombre }}</div>
                                        <div class="dropdown">
                                            <button class="btn btn-link btn-sm text-muted p-0" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                                                <li><a class="dropdown-item small fw-bold" href="{{ route('leads.calculadora', [$lead->id, 'calculation_id' => $calc->id]) }}"><i class="bi bi-pencil me-2"></i>Editar Versión</a></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li><button class="dropdown-item small text-danger fw-bold" onclick="deleteCalculation({{ $calc->id }})"><i class="bi bi-trash me-2"></i>Eliminar</button></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="text-muted small mb-2"><i class="bi bi-calendar-event me-1"></i>{{ $calc->created_at->format('d/m/y h:i A') }}</div>
                                    <div class="h5 fw-900 text-primary mb-2">${{ number_format($calc->total_estimado, 2) }}</div>
                                    
                                    @if($calc->files->count() > 0)
                                        <div class="mb-3">
                                            @foreach($calc->files as $file)
                                                <div class="d-flex align-items-center bg-light rounded-pill px-2 py-1 mb-1 border" style="font-size: 0.75rem;">
                                                    <a href="{{ route('leads.downloadFile', $file->id) }}" target="_blank" class="text-decoration-none text-dark text-truncate d-inline-block" style="max-width: 150px;" title="{{ $file->filename }}">
                                                        <i class="bi bi-file-earmark-text text-primary me-1"></i>{{ $file->filename }}
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                    
                                    <button class="btn btn-sm btn-outline-primary rounded-pill w-100 fw-bold" onclick="showCalculationDetails({{ $calc->id }})">
                                        <i class="bi bi-eye me-1"></i> Ver Detalles
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @else
                <div class="col-12 mt-5 text-center p-5 border rounded-4 bg-light">
                    <i class="bi bi-calculator display-4 text-muted mb-3 d-block"></i>
                    <h5 class="text-muted">No hay cálculos registrados aún</h5>
                    <a href="{{ route('leads.calculadora', $lead->id) }}" class="btn btn-primary rounded-pill px-4 mt-2 fw-bold">
                        <i class="bi bi-plus-circle me-1"></i> Crear Primer Cálculo
                    </a>
                </div>
                @endif

                <div id="calculation-details-container" style="display: none;">
                    <!-- Se llenará vía JS al hacer clic en "Ver Detalles" -->
                </div>

                @if($lead->calculo_data)
                <div class="col-12 mt-5" id="default-calculation-section">
                    <div class="info-label mb-3 d-flex align-items-center">
                        <i class="bi bi-list-check me-2 text-primary"></i> Último Cálculo Guardado
                    </div>
                    <div class="card border-0 bg-surface rounded-4 shadow-sm overflow-hidden" style="border: 1px solid var(--border-main) !important;">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0" style="font-size: 0.9rem;">
                                <thead style="background: var(--text-main); color: var(--bg-surface);">
                                    <tr>
                                        <th class="px-4 py-3">Artículo</th>
                                        <th class="px-3 py-3 text-end">Costo Base</th>
                                        <th class="px-3 py-3 text-end">Margen %</th>
                                        <th class="px-3 py-3 text-end">Cant.</th>
                                        <th class="px-3 py-3 text-end">Precio Unit.</th>
                                        <th class="px-4 py-3 text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $c = $lead->calculo_data;
                                        $items = $c['items'] ?? [];
                                        if (empty($items) && isset($c['costo'])) {
                                            $items = [$c];
                                        }
                                        $grandTotalValue = 0;
                                        $grandTotalMargin = 0;
                                        $grandTotalItbis = 0;
                                    @endphp
                                    
                                    @foreach($items as $item)
                                        @php
                                            $costo = $item['costo'] ?? 0;
                                            $has_itbis_c = $item['has_itbis_c'] ?? true;
                                            $itbis_perc = $has_itbis_c ? ($item['itbis_perc'] ?? 18) : 0;
                                            $margin_perc = $item['margin_perc'] ?? 0;
                                            $qty = $item['qty'] ?? 1;
                                            $adj_price = $item['adj_price'] ?? 0;
                                            
                                            $itbis_amt = $costo * ($itbis_perc / 100);
                                            $subtotal_compra = $costo + $itbis_amt;
                                            $row_total = $adj_price * $qty;
                                            $row_margin = ($adj_price - $subtotal_compra) * $qty;
                                            
                                            $grandTotalValue += $row_total;
                                            $grandTotalMargin += $row_margin;
                                            $grandTotalItbis += $itbis_amt * $qty;
                                        @endphp
                                        <tr>
                                            <td class="px-4 py-3 fw-bold text-main">{{ $item['nombre_articulo'] ?: 'Sin nombre' }}</td>
                                            <td class="px-3 py-3 text-end text-muted small">${{ number_format($costo, 2) }}</td>
                                            <td class="px-3 py-3 text-end small">{{ $margin_perc }}%</td>
                                            <td class="px-3 py-3 text-end fw-bold">{{ $qty }}</td>
                                            <td class="px-3 py-3 text-end fw-bold text-primary">${{ number_format($adj_price, 2) }}</td>
                                            <td class="px-4 py-3 text-end fw-bold text-main">${{ number_format($row_total, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot style="background: rgba(16, 185, 129, 0.05);">
                                    <tr>
                                        <th colspan="5" class="text-end px-4 py-3 border-0">VALOR TOTAL COTIZADO</th>
                                        <th class="text-end px-4 py-3 border-0 fs-5 text-primary fw-900">${{ number_format($grandTotalValue, 2) }}</th>
                                    </tr>
                                    <tr style="background: rgba(245, 158, 11, 0.05);">
                                        <th colspan="5" class="text-end px-4 py-3 border-0">ITBIS TOTAL</th>
                                        <th class="text-end px-4 py-3 border-0 text-warning fw-900">${{ number_format($grandTotalItbis, 2) }}</th>
                                    </tr>
                                    <tr style="background: rgba(16, 185, 129, 0.1);">
                                        <th colspan="5" class="text-end px-4 py-3 border-0">GANANCIA NETA ESTIMADA</th>
                                        <th class="text-end px-4 py-3 border-0 text-success fw-900">${{ number_format($grandTotalMargin, 2) }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                @endif

                <div class="col-md-6 mt-3">
                    <div class="info-label">Asignado por (Creador)</div>
                    <div class="info-value">
                        <div class="d-flex align-items-center gap-2">
                            <div class="bg-primary text-white rounded-circle d-grid place-items-center" style="width: 32px; height: 32px; font-size: 0.8rem; font-weight: 800;">
                                {{ strtoupper(substr($lead->user->name ?? 'U', 0, 1)) }}
                            </div>
                            {{ $lead->user->name ?? 'Usuario' }}
                        </div>
                    </div>
                </div>

                <div class="col-12 mt-4 pt-4 border-top">
                    <div class="info-label">Observaciones Adicionales</div>
                    <div class="obs-box">
                        {{ $lead->observaciones ?? 'Sin observaciones registradas.' }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
async function showCalculationDetails(id) {
    try {
        const url = "{{ route('leads.getCalculationDetails', ['lead' => $lead->id, 'calc_id' => ':id']) }}".replace(':id', id);
        const resp = await fetch(url);
        const data = await resp.json();
        
        const container = document.getElementById('calculation-details-container');
        const defaultSection = document.getElementById('default-calculation-section');
        if (defaultSection) defaultSection.style.display = 'none';
        container.style.display = 'block';
        
        let items = data.calculo_data.items || [];
        let rows = '';
        let totalVal = 0, totalMargin = 0, totalItbis = 0;
        
        items.forEach(item => {
            const costo = parseFloat(item.costo) || 0;
            const has_itbis_c = item.has_itbis_c !== false;
            const itbis_perc = has_itbis_c ? (parseFloat(item.itbis_perc) || 18) : 0;
            const margin_perc = parseFloat(item.margin_perc) || 0;
            const qty = parseFloat(item.qty) || 1;
            const adj_price = parseFloat(item.adj_price) || 0;
            
            const itbis_amt = costo * (itbis_perc / 100);
            const subtotal_compra = costo + itbis_amt;
            const row_total = adj_price * qty;
            const row_margin = (adj_price - subtotal_compra) * qty;
            
            totalVal += row_total;
            totalMargin += row_margin;
            totalItbis += itbis_amt * qty;
            
            rows += `
                <tr>
                    <td class="px-4 py-3 fw-bold text-main">${item.nombre_articulo || 'Sin nombre'}</td>
                    <td class="px-3 py-3 text-end text-muted small">$${costo.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                    <td class="px-3 py-3 text-end small">${margin_perc}%</td>
                    <td class="px-3 py-3 text-end fw-bold">${qty}</td>
                    <td class="px-3 py-3 text-end fw-bold text-primary">$${adj_price.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                    <td class="px-4 py-3 text-end fw-bold text-main">$${row_total.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                </tr>
            `;
        });
        
        container.innerHTML = `
            <div class="col-12 mt-4">
                <div class="info-label mb-3 d-flex align-items-center justify-content-between">
                    <span><i class="bi bi-file-earmark-spreadsheet me-2 text-primary"></i> Detalle de Versión: <strong>${data.nombre}</strong></span>
                    <button class="btn btn-sm btn-outline-secondary rounded-pill fw-bold" onclick="hideDetails()">
                        <i class="bi bi-x-lg me-1"></i> Cerrar Vista
                    </button>
                </div>
                <div class="card border-0 bg-surface rounded-4 shadow-sm overflow-hidden" style="border: 1px solid var(--border-main) !important;">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0" style="font-size: 0.9rem;">
                            <thead style="background: var(--text-main); color: var(--bg-surface);">
                                <tr>
                                    <th class="px-4 py-3">Artículo</th>
                                    <th class="px-3 py-3 text-end">Costo Base</th>
                                    <th class="px-3 py-3 text-end">Margen %</th>
                                    <th class="px-3 py-3 text-end">Cant.</th>
                                    <th class="px-3 py-3 text-end">Precio Unit.</th>
                                    <th class="px-4 py-3 text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>${rows}</tbody>
                            <tfoot style="background: rgba(16, 185, 129, 0.05);">
                                <tr>
                                    <th colspan="5" class="text-end px-4 py-3 border-0">VALOR TOTAL COTIZADO</th>
                                    <th class="text-end px-4 py-3 border-0 fs-5 text-primary fw-900">$${totalVal.toLocaleString('en-US', {minimumFractionDigits: 2})}</th>
                                </tr>
                                <tr style="background: rgba(245, 158, 11, 0.05);">
                                    <th colspan="5" class="text-end px-4 py-3 border-0">ITBIS TOTAL</th>
                                    <th class="text-end px-4 py-3 border-0 text-warning fw-900">$${totalItbis.toLocaleString('en-US', {minimumFractionDigits: 2})}</th>
                                </tr>
                                <tr style="background: rgba(16, 185, 129, 0.1);">
                                    <th colspan="5" class="text-end px-4 py-3 border-0">GANANCIA NETA ESTIMADA</th>
                                    <th class="text-end px-4 py-3 border-0 text-success fw-900">$${totalMargin.toLocaleString('en-US', {minimumFractionDigits: 2})}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        `;
        container.scrollIntoView({ behavior: 'smooth' });
    } catch (e) {
        console.error("Error al cargar detalles:", e);
        Swal.fire('Error', 'No se pudieron cargar los detalles de este cálculo. Por favor, revisa la consola para más información.', 'error');
    }
}

function hideDetails() {
    document.getElementById('calculation-details-container').style.display = 'none';
    const defaultSection = document.getElementById('default-calculation-section');
    if (defaultSection) defaultSection.style.display = 'block';
}

async function deleteCalculation(id) {
    const res = await Swal.fire({
        title: '¿Eliminar este cálculo?',
        text: 'Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    });
    
    if (res.isConfirmed) {
        try {
            const resp = await fetch(`/leads/calculations/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });
            const data = await resp.json();
            if (data.success) {
                location.reload();
            }
        } catch (e) {
            Swal.fire('Error', 'No se pudo eliminar.', 'error');
        }
    }
}
</script>
@endsection
