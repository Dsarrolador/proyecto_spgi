@extends('layouts.app')

@section('page_title', 'Reporte de Ventas')

@section('content')
<style>
  .report-toolbar{
    background: var(--bg-surface-glass); border: 1px solid var(--border-main);
    border-radius: 20px; box-shadow: var(--shadow-main); backdrop-filter: blur(16px); padding: 20px;
    margin-bottom: 24px;
  }

  .kpi-card{
    background: var(--bg-surface-glass); border: 1px solid var(--border-main);
    border-radius: 20px; box-shadow: var(--shadow-main); padding: 24px;
    transition: all 0.3s ease; backdrop-filter: blur(16px);
    border-bottom: 4px solid var(--spgi-primary);
  }
  .kpi-card:hover{ transform: translateY(-4px); }
  .kpi-label{ font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1.5px; color: var(--text-muted); font-weight: 800; margin-bottom: 8px; }
  .kpi-value{ font-size: 2rem; font-weight: 900; color: var(--text-main); margin: 0; }

  .chart-box{
    background: var(--bg-surface-glass); border: 1px solid var(--border-main);
    border-radius: 24px; box-shadow: var(--shadow-main); padding: 24px; height: 100%;
    backdrop-filter: blur(16px);
  }
  .chart-title{
    font-weight: 800; font-size: 0.85rem; color: var(--text-muted);
    margin-bottom: 24px; text-align: center; text-transform: uppercase; letter-spacing: 2px;
  }
  .canvas-container { position: relative; height: 320px; }
</style>

<div class="container-fluid">
    
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold">Reportes de Ventas</h1>
            <p class="text-muted mb-0">Análisis detallado de captación y cierre de leads.</p>
        </div>
        <a href="{{ route('leads.bienvenido') }}" class="btn btn-outline-secondary rounded-pill">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    </div>

    <!-- FILTROS -->
    <div class="report-toolbar">
        <form action="{{ route('leads.reportes') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-bold">Desde</label>
                <input type="date" name="desde" class="form-control rounded-pill" value="{{ request('desde') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold">Hasta</label>
                <input type="date" name="hasta" class="form-control rounded-pill" value="{{ request('hasta') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold">
                    <i class="bi bi-filter me-1"></i> Filtrar
                </button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('leads.reportes') }}" class="btn btn-light w-100 rounded-pill border fw-bold text-muted">
                    Limpiar
                </a>
            </div>
        </form>
    </div>

    <!-- KPIs -->
    <div class="row g-4 mb-4 text-center">
        <div class="col-md-4">
            <div class="kpi-card">
                <div class="kpi-label">Total Leads</div>
                <div class="kpi-value text-primary">{{ $totals['count'] }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="kpi-card" style="border-bottom-color: #10b981;">
                <div class="kpi-label">Valor Estimado Total</div>
                <div class="kpi-value text-success">${{ number_format($totals['value'], 2) }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="kpi-card" style="border-bottom-color: #f59e0b;">
                <div class="kpi-label">Tasa de Cierre</div>
                <div class="kpi-value text-warning">
                    {{ $totals['count'] > 0 ? round(($totals['won'] / $totals['count']) * 100, 1) : 0 }}%
                </div>
            </div>
        </div>
    </div>

    <!-- CHARTS -->
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="chart-box">
                <h6 class="chart-title">Distribución por Estado</h6>
                <div class="canvas-container">
                    <canvas id="chartStatus"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="chart-box">
                <h6 class="chart-title">Valor Estimado por Estado ($)</h6>
                <div class="canvas-container">
                    <canvas id="chartValue"></canvas>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="chart-box">
                <h6 class="chart-title">Captación Mensual (Últimos 12 meses)</h6>
                <div class="canvas-container" style="height: 300px;">
                    <canvas id="chartHistorical"></canvas>
                </div>
            </div>
        </div>
    <div class="row g-4 mb-5">
        <div class="col-12">
            <div class="chart-box">
                <h6 class="chart-title text-start mb-4">Detalle de Leads (Filtrados)</h6>
                <div class="table-responsive">
                    <table class="table table-hover align-middle" style="color: var(--text-main);">
                        <thead>
                            <tr style="border-bottom: 2px solid var(--border-main);">
                                <th class="text-muted small fw-bold">FECHA</th>
                                <th class="text-muted small fw-bold">NOMBRE</th>
                                <th class="text-muted small fw-bold">CLIENTE / CONTACTO</th>
                                <th class="text-muted small fw-bold">TOTAL ESTIMADO</th>
                                <th class="text-muted small fw-bold text-center">STATUS</th>
                                <th class="text-muted small fw-bold text-end">ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($leads as $l)
                            <tr style="border-bottom: 1px solid rgba(var(--text-main), 0.05);">
                                <td class="small text-muted">{{ $l->created_at->format('d/m/Y') }}</td>
                                <td><span class="fw-bold">{{ $l->nombre }}</span></td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="small">{{ $l->contacto }}</span>
                                        <span class="small text-muted" style="font-size: 0.75rem;">{{ $l->correo }}</span>
                                    </div>
                                </td>
                                <td class="fw-bold text-success">${{ number_format($l->total_estimado, 2) }}</td>
                                <td class="text-center">
                                    @php
                                        $colors = [
                                            'Pendiente' => ['bg' => 'rgba(245, 158, 11, 0.1)', 'color' => '#f59e0b'],
                                            'Seguimiento' => ['bg' => 'rgba(59, 130, 246, 0.1)', 'color' => '#3b82f6'],
                                            'Ganado' => ['bg' => 'rgba(16, 185, 129, 0.1)', 'color' => '#10b981'],
                                            'Perdido' => ['bg' => 'rgba(239, 68, 68, 0.1)', 'color' => '#ef4444'],
                                        ];
                                        $c = $colors[$l->status] ?? ['bg' => 'rgba(156, 163, 175, 0.1)', 'color' => '#9ca3af'];
                                    @endphp
                                    <span class="badge rounded-pill px-3 py-2" style="background: {{ $c['bg'] }}; color: {{ $c['color'] }}; font-weight: 800; font-size: 0.7rem;">
                                        {{ strtoupper($l->status) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('leads.show', $l->id) }}" class="btn btn-sm btn-light border rounded-circle" title="Ver detalle">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">No hay leads que coincidan con los filtros aplicados.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const colors = {
            'Pendiente': '#f59e0b',
            'Seguimiento': '#3b82f6',
            'Ganado': '#10b981',
            'Perdido': '#ef4444'
        };

        // 1. Chart Status (Pie)
        const statusData = @json($statusDistribution);
        new Chart(document.getElementById('chartStatus'), {
            type: 'pie',
            data: {
                labels: statusData.map(d => d.status),
                datasets: [{
                    data: statusData.map(d => d.total),
                    backgroundColor: statusData.map(d => colors[d.status] || '#cbd5e1'),
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });

        // 2. Chart Value (Bar)
        const valueData = @json($valuePerStatus);
        new Chart(document.getElementById('chartValue'), {
            type: 'bar',
            data: {
                labels: valueData.map(d => d.status),
                datasets: [{
                    label: 'Valor Total $',
                    data: valueData.map(d => d.total_valor),
                    backgroundColor: valueData.map(d => colors[d.status] || '#cbd5e1'),
                    borderRadius: 12
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { y: { beginAtZero: true } },
                plugins: { legend: { display: false } }
            }
        });

        // 3. Chart Historical (Line)
        const histData = @json($historical);
        new Chart(document.getElementById('chartHistorical'), {
            type: 'line',
            data: {
                labels: histData.map(d => d.mes),
                datasets: [{
                    label: 'Leads Captados',
                    data: histData.map(d => d.total),
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 6,
                    pointBackgroundColor: '#10b981'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });
    });
</script>
@endpush
@endsection
