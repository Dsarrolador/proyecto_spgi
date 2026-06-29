@extends('layouts.app')

@section('page_title', 'Plantilla de Comisiones de Equipos (Leads)')

@section('content')
<style>
  .comm-card {
    background: var(--bg-surface-glass); border: 1px solid var(--border-main);
    border-radius: 24px; box-shadow: var(--shadow-main); backdrop-filter: blur(20px); overflow: hidden;
  }
  .comm-header { background: #0b1220; color: #fff; padding: 25px 30px; }
  .comm-body { padding: 35px; }

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
  
  .text-number { text-align: right; font-family: 'Courier New', Courier, monospace; font-weight: bold; }
  .text-positive { color: #10b981 !important; }

  .month-group-header {
    background: rgba(var(--spgi-primary), 0.08);
    color: var(--spgi-primary);
    font-weight: 800;
    padding: 12px 20px;
    border-radius: 12px;
    font-size: 1rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .hover-primary:hover {
    color: var(--spgi-primary) !important;
  }
</style>

<div class="container py-4">
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <h3 class="fw-bold mb-0 text-white"><i class="bi bi-percent me-2 text-primary"></i>Comisiones por Equipos (Leads)</h3>
        <span class="badge bg-primary px-3 py-2 rounded-pill fw-bold">Tasa Fija 5.0%</span>
    </div>

    <div class="comm-card mb-5">
        <div class="comm-header">
            <h5 class="fw-bold mb-1"><i class="bi bi-journal-check me-2 text-primary"></i>Listado Organizado por Mes</h5>
            <p class="mb-0 text-white-50">Visualiza el desglose del 5% de comisión del monto total de equipos facturados en Leads con estado "Ganado".</p>
        </div>

        <div class="comm-body">
            @forelse($comisionesPorMes as $mesKey => $datosMes)
                <div class="mb-5 animate__animated animate__fadeIn">
                    <div class="month-group-header mb-3">
                        <span><i class="bi bi-calendar3 me-2"></i>{{ $datosMes['mes_nombre'] }}</span>
                        <div class="d-flex gap-4">
                            <span class="fs-6 text-white-50">Total Equipos: <strong class="text-white">${{ number_format($datosMes['total_equipos'], 2) }}</strong></span>
                            <span class="fs-6 text-white-50">Total Comisión: <strong class="text-success">${{ number_format($datosMes['total_comision'], 2) }}</strong></span>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered excel-table align-middle">
                            <thead>
                                <tr>
                                    <th style="width: 120px;">Fecha Ganado</th>
                                    <th>Lead / Cliente</th>
                                    <th>Comercial Asignado</th>
                                    <th class="text-end">Monto Equipos (Subtotal)</th>
                                    <th class="text-end" style="width: 180px;">Comisión (5.0%)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($datosMes['leads'] as $item)
                                    <tr>
                                        <td class="text-center">{{ $item['fecha_ganado'] }}</td>
                                        <td>
                                            <a href="{{ route('leads.show', $item['lead']->id) }}" class="fw-bold text-decoration-none text-white hover-primary">
                                                {{ $item['lead']->nombre }}
                                            </a>
                                        </td>
                                        <td class="text-muted">{{ $item['comercial'] }}</td>
                                        <td class="text-number">${{ number_format($item['monto_equipos'], 2) }}</td>
                                        <td class="text-number text-positive">${{ number_format($item['comision'], 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-end">TOTAL MES:</th>
                                    <td class="text-number">${{ number_format($datosMes['total_equipos'], 2) }}</td>
                                    <td class="text-number text-positive">${{ number_format($datosMes['total_comision'], 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="bi bi-info-circle fs-1 text-muted d-block mb-3"></i>
                    <h5 class="text-muted">No se encontraron comisiones de equipos para leads ganados.</h5>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
