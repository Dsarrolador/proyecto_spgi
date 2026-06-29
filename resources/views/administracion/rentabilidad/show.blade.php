@extends('layouts.app')

@section('page_title', 'Análisis Rentabilidad: ' . $proyecto->nombre)

@section('content')
<style>
  .rent-card {
    background: var(--bg-surface-glass); border: 1px solid var(--border-main);
    border-radius: 24px; box-shadow: var(--shadow-main); backdrop-filter: blur(20px); overflow: hidden;
  }
  .rent-header { background: #0b1220; color: #fff; padding: 25px 30px; }
  .rent-body { padding: 35px; }

  .nav-tabs-excel { border-bottom: 2px solid var(--border-main); margin-bottom: 25px; }
  .nav-tabs-excel .nav-link {
    border: 0; background: transparent; color: var(--text-muted); font-weight: 800;
    padding: 14px 28px; border-bottom: 2px solid transparent; transition: all 0.3s ease;
    text-transform: uppercase; letter-spacing: 0.5px; font-size: 0.85rem;
  }
  .nav-tabs-excel .nav-link:hover { color: var(--text-main); }
  .nav-tabs-excel .nav-link.active {
    color: var(--spgi-primary); border-bottom: 2px solid var(--spgi-primary); background: transparent;
  }

  .excel-table { width: 100%; margin-bottom: 0; font-size: 0.88rem; }
  .excel-table thead th {
    background: #0f172a; color: #f8fafc; font-weight: 700; text-transform: uppercase;
    font-size: 0.75rem; letter-spacing: 0.5px; border: 1px solid rgba(255,255,255,0.08) !important;
    padding: 12px; text-align: center; vertical-align: middle;
  }
  .excel-table tbody td {
    border: 1px solid var(--border-main) !important; color: var(--text-main);
    padding: 10px 12px; vertical-align: middle; background: rgba(255,255,255,0.01);
  }
  .excel-table tfoot th, .excel-table tfoot td {
    background: rgba(var(--spgi-primary), 0.05); color: var(--text-main);
    border: 1px solid var(--border-main) !important; padding: 12px; font-weight: 800;
  }

  .excel-summary-box {
    background: rgba(255,255,255,0.02); border: 1px solid var(--border-main);
    border-radius: 16px; padding: 20px;
  }
  
  .text-number { text-align: right; font-family: 'Courier New', Courier, monospace; font-weight: bold; }
  .text-negative { color: #ef4444 !important; }
  .text-positive { color: #10b981 !important; }

  .excel-card-header {
    background: #1e293b; color: #fff; font-weight: 800; padding: 10px 15px;
    border-radius: 8px 8px 0 0; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;
  }
</style>

<div class="container py-4">
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <a href="{{ route('administracion.rentabilidad.index') }}" class="btn btn-light rounded-pill border">
            <i class="bi bi-arrow-left me-1"></i> Volver al listado
        </a>
        <div class="d-flex gap-2">
            <a href="{{ route('proyectos.show', $proyecto->id) }}" class="btn btn-outline-primary rounded-pill px-4">
                <i class="bi bi-kanban me-1"></i> Ver Proyecto (Tareas/Requisitos)
            </a>
        </div>
    </div>

    @php
        // 1. Cálculos de Proyección
        $totalAbono = $proyecciones->sum('abono');
        $totalEquipos = $proyecciones->sum('equipos_materiales');
        $totalHonorariosProyeccion = $proyecciones->sum('honorarios');
        $totalItbis = $proyecciones->sum('itbis');
        $totalFacturado = $proyecciones->sum('total_facturado');
        $totalAdeudado = $proyecciones->sum('total_adeudado');

        // Extraer los costos de equipos del lead asociado si existe
        $costoEquiposLead = 0;
        if ($proyecto->lead) {
            foreach ($proyecto->lead->calculations as $calc) {
                $c = $calc->calculo_data;
                if (is_string($c)) {
                    $c = json_decode($c, true);
                }
                if ($c) {
                    $items = $c['items'] ?? [];
                    if (empty($items) && isset($c['costo'])) {
                        $items = [$c];
                    }
                    $tasa = floatval($c['global_tasa'] ?? 63.23);
                    foreach ($items as $item) {
                        $is_honorario = !empty($item['is_honorario']);
                        if (!$is_honorario) {
                            $costo = floatval($item['costo'] ?? 0);
                            $moneda = $item['moneda'] ?? 'DOP';
                            $qty = floatval($item['qty'] ?? 1);
                            $costoDOP = $moneda === 'USD' ? $costo * $tasa : $costo;
                            $costoEquiposLead += $costoDOP * $qty;
                        }
                    }
                }
            }
        }

        // 2. Cálculos de Gastos
        $gastoHonorariosTerceros = $gastos->where('clasificacion', 'Honorario a Terceros')->sum('monto');
        $gastoUsoInterno = $gastos->where('clasificacion', 'Uso Interno')->sum('monto');
        $gastoViaticos = $gastos->where('clasificacion', 'Viáticos')->sum('monto');
        $gastoTransporte = $gastos->where('clasificacion', 'Transporte')->sum('monto');
        $gastoEquipoManual = $gastos->where('clasificacion', 'Equipo')->sum('monto');
        $gastoEquipo = $gastoEquipoManual + $costoEquiposLead;
        
        $subtotalGastos = $gastoHonorariosTerceros + $gastoUsoInterno + $gastoViaticos + $gastoTransporte + $gastoEquipo;
        $totalHorasExtras = $horasExtras->sum('total_pagar');
        $totalGastos = $subtotalGastos + $totalHorasExtras;

        // 3. Cálculos de Rentabilidad
        $valorProyectoSubtotal = $totalEquipos + $totalHonorariosProyeccion;
        $retencionIsr = $gastoHonorariosTerceros * 0.02;
        
        $totalAntesComision = $valorProyectoSubtotal 
                             - $gastoEquipo 
                             - $gastoTransporte 
                             - $gastoUsoInterno 
                             - $gastoViaticos 
                             - $totalHorasExtras 
                             - $gastoHonorariosTerceros 
                             - $retencionIsr;
                             
        $comisionMonto = $valorProyectoSubtotal * ($rentabilidad->comision_porcentaje / 100);
        $totalNeto = $totalAntesComision - $comisionMonto;
    @endphp

    <div class="rent-card">
        <div class="rent-header">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h2 class="fw-bold mb-1"><i class="bi bi-graph-up-arrow me-2 text-primary"></i>Análisis de Rentabilidad</h2>
                    <p class="mb-0 text-white-50"><i class="bi bi-kanban me-1"></i>Proyecto: {{ $proyecto->nombre }} | Cliente: {{ $proyecto->cliente->nombre ?? 'Sin cliente' }}</p>
                </div>
                <div class="text-end">
                    <span class="text-muted small d-block">Margen Neto Estimado</span>
                    <span class="fs-4 fw-900 text-gradient {{ $totalNeto >= 0 ? 'text-success' : 'text-danger' }}">
                        ${{ number_format($totalNeto, 2) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="rent-body">
            <ul class="nav nav-tabs nav-tabs-excel" id="excelTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="proyeccion-tab" data-bs-toggle="tab" data-bs-target="#proyeccion-tab-pane" type="button" role="tab">PROYECCIÓN</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="gastos-tab" data-bs-toggle="tab" data-bs-target="#gastos-tab-pane" type="button" role="tab">GASTOS</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="he-tab" data-bs-toggle="tab" data-bs-target="#he-tab-pane" type="button" role="tab">HORAS EXTRAS</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="rentabilidad-tab" data-bs-toggle="tab" data-bs-target="#rentabilidad-tab-pane" type="button" role="tab">RENTABILIDAD</button>
                </li>
            </ul>

            <div class="tab-content" id="excelTabsContent">
                <!-- 1. PESTAÑA PROYECCIÓN -->
                <div class="tab-pane fade show active" id="proyeccion-tab-pane" role="tabpanel" tabindex="0">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <h4 class="fw-bold mb-0 text-white">Análisis de Cotizaciones</h4>
                        <button class="btn btn-sm btn-primary rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#modalAddCotizacion">
                            <i class="bi bi-plus-circle me-1"></i> Agregar Cotización
                        </button>
                    </div>

                    <div class="table-responsive mb-5">
                        <table class="table table-bordered excel-table align-middle">
                            <thead>
                                <tr>
                                    <th>Cotización No.</th>
                                    <th>Referencia</th>
                                    <th>Abono</th>
                                    <th>Equipos y Materiales</th>
                                    <th>Honorarios</th>
                                    <th>ITBIS (18%)</th>
                                    <th>Total Facturado</th>
                                    <th>Total Adeudado</th>
                                    <th>Fecha de Pago</th>
                                    <th style="width: 60px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($proyecciones as $proy)
                                    <tr>
                                        <td class="text-center">{{ $proy->cotizacion_no ?? '---' }}</td>
                                        <td class="fw-bold text-white">{{ $proy->referencia }}</td>
                                        <td class="text-number text-positive">${{ number_format($proy->abono, 2) }}</td>
                                        <td class="text-number">${{ number_format($proy->equipos_materiales, 2) }}</td>
                                        <td class="text-number">${{ number_format($proy->honorarios, 2) }}</td>
                                        <td class="text-number text-muted">${{ number_format($proy->itbis, 2) }}</td>
                                        <td class="text-number text-primary fw-bold">${{ number_format($proy->total_facturado, 2) }}</td>
                                        <td class="text-number text-warning fw-bold">${{ number_format($proy->total_adeudado, 2) }}</td>
                                        <td class="text-center">{{ $proy->fecha_pago ? $proy->fecha_pago->format('d/m/Y') : '---' }}</td>
                                        <td class="text-center">
                                            <form action="{{ route('administracion.rentabilidad.proyecciones.destroy', $proy->id) }}" method="POST" onsubmit="return confirm('¿Eliminar esta cotización del análisis?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-link text-danger p-0" title="Eliminar"><i class="bi bi-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center text-muted py-4">No hay cotizaciones registradas.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if($proyecciones->count() > 0)
                            <tfoot>
                                <tr>
                                    <th colspan="2" class="text-end">TOTALES:</th>
                                    <td class="text-number text-positive">${{ number_format($totalAbono, 2) }}</td>
                                    <td class="text-number">${{ number_format($totalEquipos, 2) }}</td>
                                    <td class="text-number">${{ number_format($totalHonorariosProyeccion, 2) }}</td>
                                    <td class="text-number text-muted">${{ number_format($totalItbis, 2) }}</td>
                                    <td class="text-number text-primary fw-bold">${{ number_format($totalFacturado, 2) }}</td>
                                    <td class="text-number text-warning fw-bold">${{ number_format($totalAdeudado, 2) }}</td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>

                    <!-- Proyecto Facturación Card -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="excel-summary-box">
                                <div class="excel-card-header mb-3">Análisis del Proyecto para Generar Factura</div>
                                <div class="d-flex flex-column gap-2">
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted fw-bold">VALOR DEL PROYECTO EN PROPUESTA:</span>
                                        <span class="fw-bold text-white">${{ number_format($totalFacturado, 2) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted fw-bold">ABONO REALIZADO:</span>
                                        <span class="fw-bold text-success">${{ number_format($totalAbono, 2) }}</span>
                                    </div>
                                    <hr class="my-2 border-secondary">
                                    <div class="d-flex justify-content-between fs-5">
                                        <span class="text-muted fw-bold">SALDO PENDIENTE:</span>
                                        <span class="fw-bold text-warning">${{ number_format($totalAdeudado, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. PESTAÑA GASTOS -->
                <div class="tab-pane fade" id="gastos-tab-pane" role="tabpanel" tabindex="0">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <h4 class="fw-bold mb-0 text-white">Listado de Egresos / Gastos del Proyecto</h4>
                        <button class="btn btn-sm btn-primary rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#modalAddGasto">
                            <i class="bi bi-plus-circle me-1"></i> Agregar Gasto
                        </button>
                    </div>

                    <div class="table-responsive mb-5">
                        <table class="table table-bordered excel-table align-middle">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Factura</th>
                                    <th>Cuenta</th>
                                    <th>Proveedor</th>
                                    <th>Concepto</th>
                                    <th>Monto</th>
                                    <th>Clasificación</th>
                                    <th style="width: 60px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($gastos as $gasto)
                                    <tr>
                                        <td class="text-center">{{ $gasto->fecha->format('d/m/Y') }}</td>
                                        <td class="text-center">{{ $gasto->factura ?? '---' }}</td>
                                        <td class="text-center">{{ $gasto->cuenta ?? '---' }}</td>
                                        <td class="text-white fw-bold">{{ $gasto->proveedor }}</td>
                                        <td>{{ $gasto->concepto }}</td>
                                        <td class="text-number text-negative">-${{ number_format($gasto->monto, 2) }}</td>
                                        <td class="text-center"><span class="badge bg-secondary">{{ $gasto->clasificacion }}</span></td>
                                        <td class="text-center">
                                            <form action="{{ route('administracion.rentabilidad.gastos.destroy', $gasto->id) }}" method="POST" onsubmit="return confirm('¿Eliminar este gasto del análisis?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-link text-danger p-0" title="Eliminar"><i class="bi bi-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">No hay gastos registrados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if($gastos->count() > 0)
                            <tfoot>
                                <tr>
                                    <th colspan="5" class="text-end">SUBTOTAL GASTOS REGISTRADOS:</th>
                                    <td class="text-number text-negative">-${{ number_format($subtotalGastos, 2) }}</td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>

                    <!-- Resumen de Gastos por Clasificacion Card -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="excel-summary-box">
                                <div class="excel-card-header mb-3">Resumen de Egresos por Clasificación</div>
                                <div class="d-flex flex-column gap-2">
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted fw-bold">HONORARIO A TERCEROS:</span>
                                        <span class="fw-bold text-white">${{ number_format($gastoHonorariosTerceros, 2) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted fw-bold">USO INTERNO:</span>
                                        <span class="fw-bold text-white">${{ number_format($gastoUsoInterno, 2) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted fw-bold">VIÁTICOS:</span>
                                        <span class="fw-bold text-white">${{ number_format($gastoViaticos, 2) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted fw-bold">TRANSPORTE:</span>
                                        <span class="fw-bold text-white">${{ number_format($gastoTransporte, 2) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-start">
                                        <span class="text-muted fw-bold">COMPRA EQUIPOS:</span>
                                        <div class="text-end">
                                            <span class="fw-bold text-white">${{ number_format($gastoEquipo, 2) }}</span>
                                            @if($costoEquiposLead > 0)
                                                <div class="small text-muted" style="font-size: 0.75rem;">
                                                    Manual: ${{ number_format($gastoEquipoManual, 2) }} | Lead: ${{ number_format($costoEquiposLead, 2) }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between text-info">
                                        <span class="text-muted fw-bold">HORAS EXTRAS PAGADAS (Pestaña HE):</span>
                                        <span class="fw-bold">${{ number_format($totalHorasExtras, 2) }}</span>
                                    </div>
                                    <hr class="my-2 border-secondary">
                                    <div class="d-flex justify-content-between fs-5 text-danger">
                                        <span class="text-muted fw-bold">TOTAL GASTOS CONSOLIDADO:</span>
                                        <span class="fw-bold">-${{ number_format($totalGastos, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. PESTAÑA HORAS EXTRAS -->
                <div class="tab-pane fade" id="he-tab-pane" role="tabpanel" tabindex="0">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <h4 class="fw-bold mb-0 text-white">Spreadsheet de Horas Extras de Colaboradores</h4>
                        <button class="btn btn-sm btn-primary rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#modalAddHoraExtra">
                            <i class="bi bi-plus-circle me-1"></i> Agregar Registro Salarial
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered excel-table align-middle text-center">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Colaborador</th>
                                    <th>Salario Mensual</th>
                                    <th>Salario Diario (Mensual/23.83)</th>
                                    <th>Salario por Hora (Diario/8)</th>
                                    <th>Recargo / Al 100%</th>
                                    <th>Valor Hora Recargada</th>
                                    <th>Cantidad de Horas</th>
                                    <th>Total a Pagar</th>
                                    <th style="width: 60px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($horasExtras as $he)
                                    <tr>
                                        <td>{{ $he->fecha->format('d/m/Y') }}</td>
                                        <td class="fw-bold text-white text-start ps-3">{{ $he->colaborador }}</td>
                                        <td class="text-number">${{ number_format($he->salario_mensual, 2) }}</td>
                                        <td class="text-number text-muted">${{ number_format($he->salario_diario, 2) }}</td>
                                        <td class="text-number text-muted">${{ number_format($he->salario_por_hora, 2) }}</td>
                                        <td>{{ number_format($he->al_100, 0) }}%</td>
                                        <td class="text-number text-infofw-bold">${{ number_format($he->total, 2) }}</td>
                                        <td class="fw-bold">{{ number_format($he->cantidad_horas, 1) }}</td>
                                        <td class="text-number text-primary fw-bold">${{ number_format($he->total_pagar, 2) }}</td>
                                        <td>
                                            <form action="{{ route('administracion.rentabilidad.horas-extras.destroy', $he->id) }}" method="POST" onsubmit="return confirm('¿Eliminar este registro de horas extras?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-link text-danger p-0" title="Eliminar"><i class="bi bi-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center text-muted py-4">No hay registros de horas extras cargados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if($horasExtras->count() > 0)
                            <tfoot>
                                <tr>
                                    <th colspan="8" class="text-end text-white">TOTAL EXTRA A PAGAR GENERAL:</th>
                                    <td class="text-number text-primary fw-bold">${{ number_format($totalHorasExtras, 2) }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>

                <!-- 4. PESTAÑA RENTABILIDAD -->
                <div class="tab-pane fade" id="rentabilidad-tab-pane" role="tabpanel" tabindex="0">
                    <div class="row">
                        <!-- Rentabilidad Calculadora Sheet -->
                        <div class="col-md-7">
                            <div class="excel-summary-box">
                                <div class="excel-card-header mb-4">Análisis de Rentabilidad del Proyecto</div>
                                
                                <div class="table-responsive">
                                    <table class="table table-borderless text-white" style="font-size: 0.95rem;">
                                        <tbody>
                                            <tr>
                                                <td class="fw-bold py-2">VALOR DEL PROYECTO SUB TOTAL</td>
                                                <td class="text-number text-white py-2">${{ number_format($valorProyectoSubtotal, 2) }}</td>
                                            </tr>
                                            <tr class="text-negative">
                                                <td class="py-2">
                                                    <i class="bi bi-dash me-2"></i>COMPRAS DE EQUIPO REALIZADA
                                                    @if($costoEquiposLead > 0)
                                                        <div class="small text-muted ms-4" style="font-size: 0.78rem;">
                                                            • Manual: ${{ number_format($gastoEquipoManual, 2) }} <br>
                                                            • Desde Lead: ${{ number_format($costoEquiposLead, 2) }}
                                                        </div>
                                                    @endif
                                                </td>
                                                <td class="text-number py-2">-${{ number_format($gastoEquipo, 2) }}</td>
                                            </tr>
                                            <tr class="text-negative">
                                                <td class="py-2"><i class="bi bi-dash me-2"></i>TRANSPORTE</td>
                                                <td class="text-number py-2">-${{ number_format($gastoTransporte, 2) }}</td>
                                            </tr>
                                            <tr class="text-negative">
                                                <td class="py-2"><i class="bi bi-dash me-2"></i>USO INTERNO</td>
                                                <td class="text-number py-2">-${{ number_format($gastoUsoInterno, 2) }}</td>
                                            </tr>
                                            <tr class="text-negative">
                                                <td class="py-2"><i class="bi bi-dash me-2"></i>VIATICOS</td>
                                                <td class="text-number py-2">-${{ number_format($gastoViaticos, 2) }}</td>
                                            </tr>
                                            <tr class="text-negative text-info">
                                                <td class="py-2"><i class="bi bi-dash me-2"></i>HORAS EXTRAS PAGADAS</td>
                                                <td class="text-number py-2">-${{ number_format($totalHorasExtras, 2) }}</td>
                                            </tr>
                                            <tr class="text-negative">
                                                <td class="py-2"><i class="bi bi-dash me-2"></i>HONORARIOS PAGADOS A TERCEROS</td>
                                                <td class="text-number py-2">-${{ number_format($gastoHonorariosTerceros, 2) }}</td>
                                            </tr>
                                            <tr class="text-negative text-warning">
                                                <td class="py-2"><i class="bi bi-dash me-2"></i>ISR Retención 2% (sobre Honorarios a 3eros)</td>
                                                <td class="text-number py-2">-${{ number_format($retencionIsr, 2) }}</td>
                                            </tr>
                                            <tr class="border-top border-secondary">
                                                <td class="fw-bold py-3 fs-5">TOTAL ANTES DE COMISIÓN</td>
                                                <td class="text-number fw-bold py-3 fs-5 text-white">${{ number_format($totalAntesComision, 2) }}</td>
                                            </tr>
                                            <tr class="text-negative">
                                                <td class="py-2">
                                                    <i class="bi bi-dash me-2"></i>COMISIÓN DEL {{ number_format($rentabilidad->comision_porcentaje, 0) }}% (sobre Subtotal Facturado)
                                                    <div class="small text-muted ms-4" style="font-size: 0.78rem;">
                                                        • Base: ${{ number_format($valorProyectoSubtotal, 2) }} <br>
                                                        • Comercial: {{ $rentabilidad->comisionUser->name ?? 'Sin comercial' }}
                                                    </div>
                                                </td>
                                                <td class="text-number py-2">-${{ number_format($comisionMonto, 2) }}</td>
                                            </tr>
                                            <tr class="border-top border-primary border-2">
                                                <td class="fw-900 py-3 fs-4 text-gradient">TOTAL NETO (BENEFICIO)</td>
                                                <td class="text-number fw-900 py-3 fs-4 text-gradient {{ $totalNeto >= 0 ? 'text-success' : 'text-danger' }}">
                                                    ${{ number_format($totalNeto, 2) }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Configurar comisiones Sidebar -->
                        <div class="col-md-5">
                            <div class="card border-0 rounded-4 p-4 shadow-sm h-100" style="background: rgba(255,255,255,0.03); border: 1px solid var(--border-main) !important;">
                                <h5 class="fw-bold mb-3 text-white"><i class="bi bi-percent me-2 text-primary"></i>Configurar Comisión de Ventas</h5>
                                
                                <form action="{{ route('administracion.rentabilidad.update-comision', $proyecto->id) }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="comision_user_id" class="form-label fw-bold small text-muted">Comercial del Lead/Proyecto</label>
                                        <select name="comision_user_id" class="form-select">
                                            <option value="">-- Seleccionar comercial --</option>
                                            @foreach($comerciales as $com)
                                                <option value="{{ $com->id }}" {{ $rentabilidad->comision_user_id == $com->id ? 'selected' : '' }}>
                                                    {{ $com->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="comision_porcentaje" class="form-label fw-bold small text-muted">Porcentaje de Comisión (%)</label>
                                        <input type="number" name="comision_porcentaje" step="0.01" min="0" max="100" class="form-control" value="{{ $rentabilidad->comision_porcentaje }}" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100 rounded-pill mt-2">
                                        <i class="bi bi-save me-1"></i> Actualizar Comisión
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL ADD COTIZACION -->
<div class="modal fade" id="modalAddCotizacion" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="background: var(--bg-surface); border-color: var(--border-main); color: var(--text-main);">
      <form action="{{ route('administracion.rentabilidad.proyecciones.store', $proyecto->id) }}" method="POST">
        @csrf
        <div class="modal-header border-bottom-0">
          <h5 class="modal-title fw-bold">Agregar Cotización</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter: invert(1);"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label small fw-bold text-muted">Cotización No.</label>
            <input type="text" name="cotizacion_no" class="form-control" placeholder="Ej: 1240">
          </div>
          <div class="mb-3">
            <label class="form-label small fw-bold text-muted">Referencia</label>
            <input type="text" name="referencia" class="form-control" placeholder="Ej: Venta de materiales" required>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-bold text-muted">Abono Inicial Recibido</label>
            <input type="number" step="0.01" name="abono" class="form-control" value="0.00" required>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-bold text-muted">Importe Equipos y Materiales</label>
            <input type="number" step="0.01" name="equipos_materiales" class="form-control" value="0.00" required>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-bold text-muted">Importe Honorarios / Servicios</label>
            <input type="number" step="0.01" name="honorarios" class="form-control" value="0.00" required>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-bold text-muted">Fecha Estimada de Pago</label>
            <input type="date" name="fecha_pago" class="form-control">
          </div>
        </div>
        <div class="modal-footer border-top-0">
          <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary rounded-pill px-4">Agregar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- MODAL ADD GASTO -->
<div class="modal fade" id="modalAddGasto" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="background: var(--bg-surface); border-color: var(--border-main); color: var(--text-main);">
      <form action="{{ route('administracion.rentabilidad.gastos.store', $proyecto->id) }}" method="POST">
        @csrf
        <div class="modal-header border-bottom-0">
          <h5 class="modal-title fw-bold">Registrar Gasto del Proyecto</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter: invert(1);"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label small fw-bold text-muted">Fecha del Gasto</label>
            <input type="date" name="fecha" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-bold text-muted">Factura (Opcional)</label>
            <input type="text" name="factura" class="form-control" placeholder="Ej: E31-261">
          </div>
          <div class="mb-3">
            <label class="form-label small fw-bold text-muted">Cuenta Contable (Opcional)</label>
            <input type="text" name="cuenta" class="form-control" placeholder="Ej: 6589">
          </div>
          <div class="mb-3">
            <label class="form-label small fw-bold text-muted">Proveedor</label>
            <select name="proveedor" class="form-select" required>
                <option value="">-- Seleccionar proveedor --</option>
                @foreach($proveedores as $prov)
                    <option value="{{ $prov->nombre }}">{{ $prov->nombre }}</option>
                @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-bold text-muted">Concepto</label>
            <input type="text" name="concepto" class="form-control" placeholder="Ej: Almuerzo, Transporte, Switch" required>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-bold text-muted">Monto total pagado</label>
            <input type="number" step="0.01" name="monto" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-bold text-muted">Clasificación</label>
            <select name="clasificacion" class="form-select" required>
                <option value="Honorario a Terceros">Honorario a Terceros</option>
                <option value="Uso Interno">Uso Interno</option>
                <option value="Viáticos">Viáticos</option>
                <option value="Transporte">Transporte</option>
                <option value="Equipo">Equipos y Materiales</option>
            </select>
          </div>
        </div>
        <div class="modal-footer border-top-0">
          <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary rounded-pill px-4">Guardar Gasto</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- MODAL ADD HORA EXTRA -->
<div class="modal fade" id="modalAddHoraExtra" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="background: var(--bg-surface); border-color: var(--border-main); color: var(--text-main);">
      <form action="{{ route('administracion.rentabilidad.horas-extras.store', $proyecto->id) }}" method="POST">
        @csrf
        <div class="modal-header border-bottom-0">
          <h5 class="modal-title fw-bold">Agregar Hora Extra</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter: invert(1);"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label small fw-bold text-muted">Fecha</label>
            <input type="date" name="fecha" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-bold text-muted">Colaborador</label>
            <input type="text" name="colaborador" class="form-control" placeholder="Ej: Yeandri Morillo" required>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-bold text-muted">Salario Mensual</label>
            <input type="number" step="0.01" name="salario_mensual" class="form-control" placeholder="Ej: 19000" required>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-bold text-muted">Recargo / Al 100% (%)</label>
            <input type="number" name="al_100" class="form-control" value="100" required>
            <div class="form-text">Por defecto 100% para duplicar la tarifa por hora normal.</div>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-bold text-muted">Cantidad de Horas Extras</label>
            <input type="number" step="0.1" name="cantidad_horas" class="form-control" placeholder="Ej: 10.5" required>
          </div>
        </div>
        <div class="modal-footer border-top-0">
          <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary rounded-pill px-4">Calcular y Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
