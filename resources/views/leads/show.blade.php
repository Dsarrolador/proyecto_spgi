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

    select.status-select {
        border: 1px solid transparent !important;
        cursor: pointer;
        text-align: center;
        text-align-last: center;
        padding: 8px 32px 8px 16px;
        border-radius: 999px;
        font-size: 0.85rem;
        font-weight: 800;
        text-transform: uppercase;
        width: auto;
        display: inline-block;
    }
    select.status-select:focus {
        box-shadow: 0 0 0 0.25rem rgba(16, 185, 129, 0.25) !important;
    }
    select.status-select.status-pendiente { background-color: rgba(245, 158, 11, 0.1) !important; color: #f59e0b !important; }
    select.status-select.status-seguimiento { background-color: rgba(59, 130, 246, 0.1) !important; color: #3b82f6 !important; }
    select.status-select.status-ganado { background-color: rgba(16, 185, 129, 0.1) !important; color: #10b981 !important; }
    select.status-select.status-perdido { background-color: rgba(239, 68, 68, 0.1) !important; color: #ef4444 !important; }

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
@php
    $subtotal_general = floatval($lead->total_estimado ?? 0);
    $itbis_general = $subtotal_general * 0.18;
    $total_general = $subtotal_general + $itbis_general;

    $costo_equipos = 0;
    $total_honorarios = 0;

    foreach ($lead->calculations as $calc) {
        $c = $calc->calculo_data;
        if ($c) {
            $items = $c['items'] ?? [];
            if (empty($items) && isset($c['costo'])) {
                $items = [$c];
            }
            
            $tasa = floatval($c['global_tasa'] ?? 63.23);
            
            foreach ($items as $item) {
                $is_honorario = !empty($item['is_honorario']);
                $qty = floatval($item['qty'] ?? 1);
                $adj_price = floatval($item['adj_price'] ?? 0);
                
                if ($is_honorario) {
                    $hVal = floatval($item['honorario_val'] ?? 0);
                    $sell_price = $adj_price > 0 ? $adj_price : $hVal;
                    $total_honorarios += $sell_price * $qty;
                } else {
                    $costo = floatval($item['costo'] ?? 0);
                    $moneda = $item['moneda'] ?? 'DOP';
                    $costoDOP = $moneda === 'USD' ? $costo * $tasa : $costo;
                    $costo_equipos += $costoDOP * $qty;
                }
            }
        }
    }
    $ganancia_equipos = $subtotal_general - $total_honorarios - $costo_equipos;
@endphp
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <a href="{{ route('leads.index') }}" class="btn btn-light rounded-pill border">
            <i class="bi bi-arrow-left me-1"></i> Volver al listado
        </a>
        <div class="d-flex gap-2">
            @if($lead->status !== 'Ganado' && $lead->status !== 'Perdido')
            <button type="button" class="btn btn-success rounded-pill px-4 btn-ganar" data-lead-id="{{ $lead->id }}" data-lead-nombre="{{ $lead->nombre }}">
                <i class="bi bi-check-circle-fill me-1"></i> Marcar Ganado
            </button>
            <button type="button" class="btn btn-danger rounded-pill px-4 btn-perder" data-lead-id="{{ $lead->id }}" data-lead-nombre="{{ $lead->nombre }}">
                <i class="bi bi-x-circle-fill me-1"></i> Marcar Perdido
            </button>
            @endif
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
                <select class="form-select form-select-sm status-select {{ $statusClass }}" data-lead-id="{{ $lead->id }}">
                    <option value="Pendiente" {{ $lead->status == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                    <option value="Seguimiento" {{ $lead->status == 'Seguimiento' ? 'selected' : '' }}>Seguimiento</option>
                    <option value="Ganado" {{ $lead->status == 'Ganado' ? 'selected' : '' }}>Ganado</option>
                    <option value="Perdido" {{ $lead->status == 'Perdido' ? 'selected' : '' }}>Perdido</option>
                </select>
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
                    <div class="info-value">
                        <div class="d-flex flex-column gap-1 p-3 rounded-4 border" style="background: rgba(255, 255, 255, 0.02); border-color: rgba(255, 255, 255, 0.08) !important;">
                            <div style="font-size: 0.85rem;" class="d-flex justify-content-between">
                                <span class="text-muted">Subtotal:</span>
                                <span class="fw-bold text-main">${{ number_format($subtotal_general, 2) }}</span>
                            </div>
                            <div style="font-size: 0.85rem;" class="d-flex justify-content-between">
                                <span class="text-muted">ITBIS:</span>
                                <span class="fw-bold text-warning">${{ number_format($itbis_general, 2) }}</span>
                            </div>
                            <hr class="my-1" style="border-top: 1px solid rgba(255, 255, 255, 0.1);">
                            <div style="font-size: 1.15rem;" class="d-flex justify-content-between text-success fw-900 align-items-center">
                                <span class="text-muted fs-6" style="font-size: 0.85rem !important;">Total:</span>
                                <span><i class="bi bi-currency-dollar me-1"></i>{{ number_format($total_general, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-value">
                        <div class="d-flex flex-column gap-1 p-3 rounded-4 border" style="background: rgba(255, 255, 255, 0.02); border-color: rgba(255, 255, 255, 0.08) !important;">
                            <div style="font-size: 0.85rem;" class="d-flex justify-content-between">
                                <span class="text-muted">Costo Equipos:</span>
                                <span class="fw-bold text-main">${{ number_format($costo_equipos, 2) }}</span>
                            </div>
                            <hr class="my-1" style="border-top: 1px solid rgba(255, 255, 255, 0.1);">
                            <div style="font-size: 1.15rem;" class="d-flex justify-content-between text-success fw-900 align-items-center">
                                <span class="text-muted fs-6" style="font-size: 0.85rem !important;">Ganancia Proyecto:</span>
                                <span><i class="bi bi-currency-dollar me-1"></i>{{ number_format($ganancia_equipos, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-value">
                        <div class="d-flex flex-column gap-1 p-3 rounded-4 border" style="background: rgba(255, 255, 255, 0.02); border-color: rgba(255, 255, 255, 0.08) !important;">
                            <div style="font-size: 0.85rem;" class="d-flex justify-content-between">
                                <span class="text-muted">Honorarios y Servicios:</span>
                                <span class="fw-bold text-main">${{ number_format($total_honorarios, 2) }}</span>
                            </div>
                            <hr class="my-1" style="border-top: 1px solid rgba(255, 255, 255, 0.1);">
                            <div style="font-size: 1.15rem;" class="d-flex justify-content-between text-info fw-900 align-items-center">
                                <span class="text-muted fs-6" style="font-size: 0.85rem !important;">Total Honorarios:</span>
                                <span><i class="bi bi-person-workspace me-1"></i>{{ number_format($total_honorarios, 2) }}</span>
                            </div>
                        </div>
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
                                                           @php
                                        $c = $lead->calculo_data;
                                        $items = $c['items'] ?? [];
                                        if (empty($items) && isset($c['costo'])) {
                                            $items = [$c];
                                        }
                                        $grandTotalValue = 0;
                                        $totalHonorariosLatest = 0;
                                        $totalCostoEquiposLatest = 0;
                                        $tasa = floatval($c['global_tasa'] ?? 63.23);
                                    @endphp
                                    
                                    @foreach($items as $item)
                                        @php
                                            $costo = floatval($item['costo'] ?? 0);
                                            $moneda = $item['moneda'] ?? 'DOP';
                                            $costoDOP = $moneda === 'USD' ? $costo * $tasa : $costo;
                                            
                                            $margin_perc = floatval($item['margin_perc'] ?? 0);
                                            $qty = floatval($item['qty'] ?? 1);
                                            $adj_price = floatval($item['adj_price'] ?? 0);
                                            $is_honorario = !empty($item['is_honorario']);
                                            
                                            if ($is_honorario) {
                                                $hVal = floatval($item['honorario_val'] ?? 0);
                                                $price_si = $adj_price > 0 ? $adj_price : $hVal;
                                                $row_total = $price_si * $qty;
                                                $totalHonorariosLatest += $row_total;
                                            } else {
                                                $gan_u = $costoDOP * ($margin_perc / 100);
                                                $p_si = $costoDOP + $gan_u;
                                                $price_si = $adj_price > 0 ? $adj_price : $p_si;
                                                $row_total = $price_si * $qty;
                                                $totalCostoEquiposLatest += $costoDOP * $qty;
                                            }
                                            $grandTotalValue += $row_total;
                                        @endphp
                                        <tr>
                                            <td class="px-4 py-3 fw-bold text-main">{{ $item['nombre_articulo'] ?: 'Sin nombre' }}</td>
                                            <td class="px-3 py-3 text-end text-muted small">${{ number_format($costo, 2) }}</td>
                                            <td class="px-3 py-3 text-end small">{{ $margin_perc }}%</td>
                                            <td class="px-3 py-3 text-end fw-bold">{{ $qty }}</td>
                                            <td class="px-3 py-3 text-end fw-bold text-primary">${{ number_format($price_si, 2) }}</td>
                                            <td class="px-4 py-3 text-end fw-bold text-main">${{ number_format($row_total, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot style="background: rgba(16, 185, 129, 0.05);">
                                    @if($totalHonorariosLatest > 0)
                                    <tr style="background: rgba(13, 202, 240, 0.05);">
                                        <th colspan="5" class="text-end px-4 py-3 border-0 text-info">TOTAL HONORARIOS</th>
                                        <th class="text-end px-4 py-3 border-0 text-info fw-900">${{ number_format($totalHonorariosLatest, 2) }}</th>
                                    </tr>
                                    @endif
                                    <tr>
                                        <th colspan="5" class="text-end px-4 py-3 border-0">SUBTOTAL</th>
                                        <th class="text-end px-4 py-3 border-0 text-main fw-900">${{ number_format($grandTotalValue, 2) }}</th>
                                    </tr>
                                    <tr style="background: rgba(245, 158, 11, 0.05);">
                                        <th colspan="5" class="text-end px-4 py-3 border-0">ITBIS (18%)</th>
                                        <th class="text-end px-4 py-3 border-0 text-warning fw-900">${{ number_format($grandTotalValue * 0.18, 2) }}</th>
                                    </tr>
                                    <tr style="background: rgba(16, 185, 129, 0.1);">
                                        <th colspan="5" class="text-end px-4 py-3 border-0 fs-5 text-primary">VALOR TOTAL COTIZADO</th>
                                        <th class="text-end px-4 py-3 border-0 fs-5 text-primary fw-900">${{ number_format($grandTotalValue * 1.18, 2) }}</th>
                                    </tr>
                                    <tr style="background: rgba(16, 185, 129, 0.05);">
                                        <th colspan="5" class="text-end px-4 py-3 border-0">GANANCIA NETA ESTIMADA</th>
                                        <th class="text-end px-4 py-3 border-0 text-success fw-900">${{ number_format($grandTotalValue - $totalHonorariosLatest - $totalCostoEquiposLatest, 2) }}</th>
                                    </tr>
                                </tfoot>         </tfoot>
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

                 <!-- SECCIÓN CUESTIONARIOS TÉCNICOS (CHECKLISTS) -->
                 <div class="col-12 mt-5 pt-4 border-top">
                     <div class="d-flex justify-content-between align-items-center mb-3">
                         <div class="info-label mb-0"><i class="bi bi-clipboard2-check me-2"></i>Evaluaciones Técnicas (Checklists)</div>
                         <div class="d-flex gap-2">
                             <a href="{{ route('checklists.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold">
                                 <i class="bi bi-gear me-1"></i> Gestionar Plantillas
                             </a>
                             <a href="{{ route('leads.checklists.create', $lead->id) }}" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold">
                                 <i class="bi bi-plus-circle me-1"></i> Nueva Evaluación
                             </a>
                         </div>
                     </div>

                     @php
                         $leadChecklists = \App\Models\LeadChecklist::where('lead_id', $lead->id)->with('template', 'user')->orderBy('created_at', 'desc')->get();
                     @endphp

                     @if($leadChecklists->count() > 0)
                         <div class="row g-3">
                             @foreach($leadChecklists as $lCheck)
                                 <div class="col-md-6">
                                     <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: rgba(var(--text-main), 0.02); border: 1px solid var(--border-main) !important;">
                                         <div class="d-flex justify-content-between align-items-start mb-2">
                                             <div>
                                                 <div class="fw-bold text-white fs-6">{{ $lCheck->template->nombre }}</div>
                                                 <div class="text-muted small"><i class="bi bi-calendar-event me-1"></i>{{ $lCheck->created_at->format('d/m/Y h:i A') }}</div>
                                             </div>
                                             @php
                                                 $lblClass = 'bg-success';
                                                 if ($lCheck->estado_cliente == 'Crítico') {
                                                     $lblClass = 'bg-danger';
                                                 } elseif ($lCheck->estado_cliente == 'Regular') {
                                                     $lblClass = 'bg-warning text-dark';
                                                 }
                                             @endphp
                                             <span class="badge {{ $lblClass }} px-2 py-1 rounded-pill">{{ $lCheck->estado_cliente ?? 'Pendiente' }}</span>
                                         </div>

                                         <div class="d-flex align-items-center justify-content-between my-2 p-2 rounded" style="background: rgba(255,255,255,0.03);">
                                             <span class="text-muted small">Puntuación Total:</span>
                                             <span class="fw-bold text-success fs-5">{{ $lCheck->total_puntos ?? 0 }} pts</span>
                                         </div>

                                         @if($lCheck->accion_sugerida)
                                             <div class="mb-3 text-muted small">
                                                 <strong>Siguiente Paso:</strong> {{ $lCheck->accion_sugerida }}
                                             </div>
                                         @endif

                                         <div class="d-flex align-items-center justify-content-between mt-auto pt-2 border-top border-secondary border-opacity-10">
                                             <span class="text-muted small"><i class="bi bi-person me-1"></i>{{ $lCheck->user->name ?? 'Usuario' }}</span>
                                             <a href="{{ route('leads.checklists.edit', [$lead->id, $lCheck->id]) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold">
                                                 <i class="bi bi-pencil-square me-1"></i> Editar Respuestas
                                             </a>
                                         </div>
                                     </div>
                                 </div>
                             @endforeach
                         </div>
                     @else
                         <div class="text-muted small p-4 border rounded-3 bg-light text-center">
                             <i class="bi bi-clipboard-x fs-2 d-block mb-2"></i> No hay evaluaciones técnicas registradas para este lead.
                         </div>
                     @endif
                 </div>

                 <!-- SECCIÓN NOVEDADES -->
                 <div class="col-12 mt-5 pt-4 border-top">
                     <div class="d-flex justify-content-between align-items-center mb-3">
                         <div class="info-label mb-0"><i class="bi bi-journal-text me-2"></i>Novedades (Bitácora)</div>
                         <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#novedadModal">
                             <i class="bi bi-plus-circle me-1"></i> Agregar Novedad
                         </button>
                     </div>

                     @php
                         $novedades = \App\Models\NovedadLead::where('lead_id', $lead->id)->orderBy('created_at', 'desc')->get();
                     @endphp

                     @if($novedades->count() > 0)
                         <div class="timeline ps-3" style="border-left: 2px solid var(--border-main);">
                             @foreach($novedades as $novedad)
                                 <div class="position-relative mb-4 pb-2">
                                     <div class="position-absolute bg-primary rounded-circle" style="width: 12px; height: 12px; left: -23px; top: 5px;"></div>
                                     <div class="d-flex align-items-center mb-1 gap-2">
                                         <div class="fw-bold text-main" style="font-size: 0.9rem;">
                                             <i class="bi bi-person-circle me-1 text-muted"></i> {{ $novedad->user->name ?? 'Usuario' }}
                                         </div>
                                         <span class="badge bg-secondary" style="font-size: 0.65rem;">{{ ucfirst($novedad->tipo) }}</span>
                                         <span class="text-muted small ms-auto"><i class="bi bi-clock me-1"></i>{{ $novedad->created_at->format('d/m/Y h:i A') }}</span>
                                     </div>
                                     <div class="p-3 rounded-3" style="background: rgba(var(--text-main), 0.02); border: 1px solid var(--border-main); font-size: 0.9rem; white-space: pre-wrap;">{{ $novedad->mensaje }}</div>
                                     
                                     @if($novedad->adjunto)
                                         <div class="mt-2">
                                             <a href="{{ route('leads.novedades.download', $novedad->id) }}" class="btn btn-sm btn-outline-secondary rounded-pill" style="font-size: 0.75rem;">
                                                 <i class="bi bi-paperclip me-1"></i> {{ $novedad->nombre_original ?? 'Descargar Adjunto' }}
                                             </a>
                                         </div>
                                     @endif
                                 </div>
                             @endforeach
                         </div>
                     @else
                         <div class="text-muted small p-4 border rounded-3 bg-light text-center">
                             <i class="bi bi-chat-square-text fs-2 d-block mb-2"></i> No hay novedades registradas.
                         </div>
                     @endif
                 </div>

             </div>
         </div>
     </div>
 </div>

 <!-- Modal para Nueva Novedad -->
 <div class="modal fade" id="novedadModal" tabindex="-1" aria-hidden="true">
     <div class="modal-dialog">
         <form action="{{ route('leads.novedades.store', $lead->id) }}" method="POST" enctype="multipart/form-data">
             @csrf
             <div class="modal-content" style="background: var(--bg-surface); border-color: var(--border-main); color: var(--text-main);">
                 <div class="modal-header border-bottom-0">
                     <h5 class="modal-title fw-bold">Agregar Novedad</h5>
                     <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: var(--bs-theme) == 'dark' ? 'invert(1)' : 'none';"></button>
                 </div>
                 <div class="modal-body">
                     <div class="mb-3">
                         <label class="form-label text-muted small fw-bold">Mensaje / Detalle</label>
                         <textarea name="mensaje" rows="4" class="form-control bg-transparent" required style="color: var(--text-main); border-color: var(--border-main);" placeholder="Describe la novedad, acuerdo o seguimiento..."></textarea>
                     </div>
                     <div class="mb-3">
                         <label class="form-label text-muted small fw-bold">Archivo Adjunto (Opcional)</label>
                         <input type="file" name="adjunto" class="form-control bg-transparent" style="color: var(--text-main); border-color: var(--border-main);">
                     </div>
                 </div>
                 <div class="modal-footer border-top-0">
                     <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                     <button type="submit" class="btn btn-primary rounded-pill px-4">Guardar Novedad</button>
                 </div>
             </div>
         </form>
     </div>
 </div>
 </div>
 @endsection

 @section('scripts')
 <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
 <script>
 document.addEventListener('DOMContentLoaded', function() {
     if(window.location.hash === '#novedadModal') {
         var myModal = new bootstrap.Modal(document.getElementById('novedadModal'));
         myModal.show();
     }
 });

 async function showCalculationDetails(id) {
     try {
         const url = "{{ route('leads.getCalculationDetails', ['lead' => $lead->id, 'calc_id' => ':id']) }}".replace(':id', id);
         const resp = await fetch(url);
         const data = await resp.json();
         
         let calcData = data.calculo_data;
         if (typeof calcData === 'string') {
             try {
                 calcData = JSON.parse(calcData);
             } catch(e) {
                 console.error("Error parsing calculo_data:", e);
                 calcData = {};
             }
         }
         calcData = calcData || {};
         
         const container = document.getElementById('calculation-details-container');
         const defaultSection = document.getElementById('default-calculation-section');
         if (defaultSection) defaultSection.style.display = 'none';
         container.style.display = 'block';
         
         let items = calcData.items || [];
         let rows = '';
         let totalVal = 0, totalHonorarios = 0, totalCostoEquipos = 0;
         const tasa = parseFloat(calcData.global_tasa) || 63.23;
         
         items.forEach(item => {
             const costo = parseFloat(item.costo) || 0;
             const moneda = item.moneda || 'DOP';
             const costoDOP = moneda === 'USD' ? costo * tasa : costo;
             
             const margin_perc = parseFloat(item.margin_perc) || 0;
             const qty = parseFloat(item.qty) || 1;
             const adj_price = parseFloat(item.adj_price) || 0;
             const is_honorario = item.is_honorario || false;
             
             let price_si, row_total;
             if (is_honorario) {
                 const hVal = parseFloat(item.honorario_val) || 0;
                 price_si = adj_price > 0 ? adj_price : hVal;
                 row_total = price_si * qty;
                 totalHonorarios += row_total;
             } else {
                 const gan_u = costoDOP * (margin_perc / 100);
                 const p_si = costoDOP + gan_u;
                 price_si = adj_price > 0 ? adj_price : p_si;
                 row_total = price_si * qty;
                 totalCostoEquipos += costoDOP * qty;
             }
             
             totalVal += row_total;
             
             rows += `
                 <tr>
                     <td class="px-4 py-3 fw-bold text-main">${item.nombre_articulo || 'Sin nombre'}</td>
                     <td class="px-3 py-3 text-end text-muted small">$${costo.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                     <td class="px-3 py-3 text-end small">${margin_perc}%</td>
                     <td class="px-3 py-3 text-end fw-bold">${qty}</td>
                     <td class="px-3 py-3 text-end fw-bold text-primary">$${price_si.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
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
                                 ${totalHonorarios > 0 ? `
                                 <tr style="background: rgba(13, 202, 240, 0.05);">
                                     <th colspan="5" class="text-end px-4 py-3 border-0 text-info">TOTAL HONORARIOS</th>
                                     <th class="text-end px-4 py-3 border-0 text-info fw-900">$${totalHonorarios.toLocaleString('en-US', {minimumFractionDigits: 2})}</th>
                                 </tr>
                                 ` : ''}
                                 <tr>
                                     <th colspan="5" class="text-end px-4 py-3 border-0">SUBTOTAL</th>
                                     <th class="text-end px-4 py-3 border-0 text-main fw-900">$${totalVal.toLocaleString('en-US', {minimumFractionDigits: 2})}</th>
                                 </tr>
                                 <tr style="background: rgba(245, 158, 11, 0.05);">
                                     <th colspan="5" class="text-end px-4 py-3 border-0">ITBIS (18%)</th>
                                     <th class="text-end px-4 py-3 border-0 text-warning fw-900">$${(totalVal * 0.18).toLocaleString('en-US', {minimumFractionDigits: 2})}</th>
                                 </tr>
                                 <tr style="background: rgba(16, 185, 129, 0.1);">
                                     <th colspan="5" class="text-end px-4 py-3 border-0 fs-5 text-primary">VALOR TOTAL COTIZADO</th>
                                     <th class="text-end px-4 py-3 border-0 fs-5 text-primary fw-900">$${(totalVal * 1.18).toLocaleString('en-US', {minimumFractionDigits: 2})}</th>
                                 </tr>
                                 <tr style="background: rgba(16, 185, 129, 0.05);">
                                     <th colspan="5" class="text-end px-4 py-3 border-0">GANANCIA NETA ESTIMADA</th>
                                     <th class="text-end px-4 py-3 border-0 text-success fw-900">$${(totalVal - totalHonorarios - totalCostoEquipos).toLocaleString('en-US', {minimumFractionDigits: 2})}</th>
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

  document.addEventListener('DOMContentLoaded', () => {
      document.querySelectorAll('.status-select').forEach(select => {
          select.addEventListener('change', async function() {
              const leadId = this.dataset.leadId;
              const newStatus = this.value;
              const selectEl = this;
              
              const originalClass = selectEl.className;
              
              // Cambiar la clase dinámicamente de inmediato para dar feedback visual rápido
              selectEl.className = `form-select form-select-sm status-select status-${newStatus.toLowerCase().replace(' ', '-')}`;
              
              try {
                  const response = await fetch(`/leads/${leadId}/update-status`, {
                      method: 'POST',
                      headers: {
                          'Content-Type': 'application/json',
                          'X-CSRF-TOKEN': '{{ csrf_token() }}'
                      },
                      body: JSON.stringify({ status: newStatus })
                  });
                  
                  const data = await response.json();
                  if (data.success) {
                      Swal.fire({
                          icon: 'success',
                          title: 'Estado Actualizado',
                          text: `El lead ahora está en estado: ${newStatus}`,
                          toast: true,
                          position: 'top-end',
                          showConfirmButton: false,
                          timer: 3000,
                          timerProgressBar: true
                      });
                  } else {
                      throw new Error(data.error || 'Error al actualizar');
                  }
              } catch (error) {
                  console.error(error);
                  // Revertir clase y valor en caso de error
                  selectEl.className = originalClass;
                  const match = originalClass.match(/status-(\w+)/);
                  if (match) {
                      const oldStatus = match[1];
                      selectEl.value = oldStatus.charAt(0).toUpperCase() + oldStatus.slice(1);
                  }
                  Swal.fire({
                      icon: 'error',
                      title: 'Error',
                      text: 'No se pudo actualizar el estado del lead.'
                  });
              }
          });
      });
  });

  document.querySelectorAll('.btn-ganar').forEach(btn => {
      btn.addEventListener('click', async function() {
          const leadId = this.dataset.leadId;
          const leadNombre = this.dataset.leadNombre;
          
          const result = await Swal.fire({
              title: '¿Confirmar Lead como GANADO?',
              text: `El lead "${leadNombre}" será marcado como Ganado. Se creará automáticamente el cliente y proyecto respectivo y pasarás al módulo de rentabilidad.`,
              icon: 'question',
              showCancelButton: true,
              confirmButtonText: 'Sí, ganar y cotizar',
              cancelButtonText: 'Cancelar',
              confirmButtonColor: '#10b981'
          });
          
          if (result.isConfirmed) {
              Swal.fire({
                  title: 'Procesando...',
                  text: 'Creando cliente y proyecto en administración...',
                  allowOutsideClick: false,
                  didOpen: () => {
                      Swal.showLoading();
                  }
              });
              
              try {
                  const response = await fetch(`/leads/${leadId}/convertir-ganado`, {
                      method: 'POST',
                      headers: {
                          'Content-Type': 'application/json',
                          'X-CSRF-TOKEN': '{{ csrf_token() }}'
                      }
                  });
                  
                  const data = await response.json();
                  if (data.success) {
                      Swal.fire({
                          icon: 'success',
                          title: '¡Conversión Exitosa!',
                          text: 'El cliente y el proyecto han sido creados.',
                          timer: 2000,
                          showConfirmButton: false
                      }).then(() => {
                          window.location.href = data.redirect_url;
                      });
                  } else {
                      throw new Error(data.error || 'Error al convertir');
                  }
              } catch (error) {
                  console.error(error);
                  Swal.fire('Error', error.message || 'Ocurrió un error al convertir el lead a proyecto.', 'error');
              }
          }
      });
  });

  document.querySelectorAll('.btn-perder').forEach(btn => {
      btn.addEventListener('click', async function() {
          const leadId = this.dataset.leadId;
          const leadNombre = this.dataset.leadNombre;
          
          const result = await Swal.fire({
              title: '¿Marcar Lead como PERDIDO?',
              text: `El lead "${leadNombre}" pasará al estado de Perdido.`,
              icon: 'warning',
              showCancelButton: true,
              confirmButtonText: 'Sí, marcar perdido',
              cancelButtonText: 'Cancelar',
              confirmButtonColor: '#ef4444'
          });
          
          if (result.isConfirmed) {
              try {
                  const response = await fetch(`/leads/${leadId}/marcar-perdido`, {
                      method: 'POST',
                      headers: {
                          'Content-Type': 'application/json',
                          'X-CSRF-TOKEN': '{{ csrf_token() }}'
                      }
                  });
                  
                  const data = await response.json();
                  if (data.success) {
                      Swal.fire({
                          icon: 'success',
                          title: 'Lead Perdido',
                          text: 'El estado del lead ha sido actualizado.',
                          timer: 1500,
                          showConfirmButton: false
                      }).then(() => {
                          window.location.reload();
                      });
                  } else {
                      throw new Error(data.error || 'Error al actualizar');
                  }
              } catch (error) {
                  console.error(error);
                  Swal.fire('Error', 'No se pudo actualizar el estado del lead.', 'error');
              }
          }
      });
  });
});
</script>
  @endsection
