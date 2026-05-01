@extends('layouts.cliente')

@section('content')
<div class="row g-4 mb-5">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="text-muted small fw-bold mb-1">TOTAL REQUERIMIENTOS</div>
            <div class="h2 fw-bold mb-0 text-primary">{{ $stats['total'] }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card border-start border-warning border-5">
            <div class="text-muted small fw-bold mb-1">PENDIENTES</div>
            <div class="h2 fw-bold mb-0 text-warning">{{ $stats['pendientes'] }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card border-start border-info border-5">
            <div class="text-muted small fw-bold mb-1">EN PROGRESO</div>
            <div class="h2 fw-bold mb-0 text-info">{{ $stats['en_progreso'] }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card border-start border-success border-5">
            <div class="text-muted small fw-bold mb-1">COMPLETADOS</div>
            <div class="h2 fw-bold mb-0 text-success">{{ $stats['completados'] }}</div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0">Requerimientos de {{ $nombreMes }}</h5>
                <form action="{{ route('cliente.dashboard') }}" method="GET" class="d-flex gap-2">
                    <select name="estado" class="form-select form-select-sm rounded-pill px-3" onchange="this.form.submit()">
                        <option value="">Pendientes/En Progreso</option>
                        @foreach($estados as $est)
                            <option value="{{ $est->id }}" {{ request('estado') == $est->id ? 'selected' : '' }}>{{ $est->nombre }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th>Encargado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($requirements as $req)
                        @php
                            $statusColor = $req->estadoRequerimiento->color ?? '#6c757d';
                            $statusName = $req->estadoRequerimiento->nombre ?? 'N/A';
                        @endphp
                        <tr>
                            <td class="small">{{ $req->created_at->timezone('America/Santo_Domingo')->format('d/m/Y H:i') }}</td>
                            <td>
                                <span class="badge" style="background-color: {{ $statusColor }}; color: #fff; border: 1px solid rgba(0,0,0,0.1);">
                                    {{ $statusName }}
                                </span>
                            </td>
                            <td class="small">{{ $req->asignado->name ?? 'Pendiente' }}</td>
                            <td class="text-end">
                                <a href="{{ route('cliente.requerimientos.show', $req->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    <i class="bi bi-journal-text me-1"></i> Novedades
                                </a>
                            </td>
                        </tr>
                        @endforeach
                        @if($requirements->isEmpty())
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">No se encontraron requerimientos.</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-primary text-white mb-4">
            <h5 class="fw-bold mb-3">Información de Iguala</h5>
            <div class="mb-3">
                <div class="small opacity-75">Plan Actual</div>
                <div class="h4 fw-bold">{{ $cliente->igualaPlan->nombre ?? 'Sin plan' }}</div>
            </div>
            
            @if($metrics)
            <div class="mb-3">
                <div class="small opacity-75">Horas Disponibles</div>
                <div class="h2 fw-bold">{{ $metrics['horas_iguala'] }} hrs</div>
            </div>
            <div class="mb-1 d-flex justify-content-between small">
                <span>Consumo del Mes</span>
                <span>{{ $metrics['horas_consumidas_mes'] }} / {{ $metrics['horas_iguala'] }}</span>
            </div>
            <div class="progress" style="height: 10px; background: rgba(255,255,255,0.2);">
                <div class="progress-bar bg-white" style="width: {{ min(($metrics['horas_consumidas_mes'] / max($metrics['horas_iguala'], 1)) * 100, 100) }}%"></div>
            </div>
            @endif
        </div>

        <div class="card border-0 shadow-sm rounded-4 p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-info-circle me-2"></i> Soporte Técnico</h6>
            <p class="small text-muted mb-0">Si necesitas ayuda con un requerimiento, por favor contacta a tu encargado asignado.</p>
        </div>
    </div>
</div>
@endsection
