@extends('layouts.app')

@section('page_title', 'Control de Igualas Mensual')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between bg-white p-3 rounded-3 shadow-sm">
                <div>
                    <h4 class="fw-bold mb-1">Centro de Gestión de Igualas</h4>
                    <p class="text-muted mb-0">Seguimiento de consumos para el mes de <strong>{{ now()->translatedFormat('F Y') }}</strong></p>
                </div>
                <div class="text-end">
                    <span class="badge bg-primary px-3 py-2 rounded-pill">
                        <i class="bi bi-calendar3 me-1"></i> Ciclo Actual
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        @forelse($data as $item)
            @php $metrics = $item['metrics']; @endphp
            <div class="col-xl-4 col-md-6">
                <div class="card h-100 border-0 shadow-sm overflow-hidden" style="border-radius: 20px;">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="fw-bold text-dark mb-1">{{ $item['nombre'] }}</h5>
                                <span class="badge bg-light text-primary border border-primary-subtle rounded-pill small">
                                    {{ $metrics->plan_nombre }}
                                </span>
                            </div>
                            <a href="{{ route('requerimientos.index') }}?cliente_id={{ $item['id'] }}" class="btn btn-light btn-sm rounded-circle" title="Ver requerimientos">
                                <i class="bi bi-box-arrow-up-right"></i>
                            </a>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <!-- SOPORTE REMOTO (INTERNAL) -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-end mb-2">
                                <div>
                                    <label class="small fw-bold text-secondary mb-0">Soportes Remotos</label>
                                    <div class="h4 fw-bold mb-0 {{ ($metrics->limite_remoto != -1 && $metrics->disponible_remoto == 0) ? 'text-danger' : 'text-dark' }}">
                                        {{ $metrics->usados_remoto }} <small class="text-muted fw-normal">/ {{ $metrics->limite_remoto == -1 ? '∞' : $metrics->limite_remoto }}</small>
                                    </div>
                                </div>
                                <div class="text-end">
                                    @if($metrics->limite_remoto == -1)
                                        <span class="badge bg-info-subtle text-info rounded-pill small">
                                            Ilimitado
                                        </span>
                                    @elseif($metrics->disponible_remoto > 0)
                                        <span class="badge bg-success-subtle text-success rounded-pill small">
                                            {{ $metrics->disponible_remoto }} disponibles
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger rounded-pill small">
                                            Límite agotado
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="progress" style="height: 10px; border-radius: 5px; background-color: #f1f5f9;">
                                @php 
                                    if ($metrics->limite_remoto == -1) {
                                        $percRem = 0;
                                        $pbColorRem = 'bg-info';
                                    } else {
                                        $percRem = ($metrics->limite_remoto > 0) ? min(100, ($metrics->usados_remoto / $metrics->limite_remoto) * 100) : 100;
                                        $pbColorRem = ($metrics->disponible_remoto == 0) ? 'bg-danger' : 'bg-primary';
                                    }
                                @endphp
                                <div class="progress-bar {{ $pbColorRem }}" role="progressbar" style="width: {{ $percRem }}%" aria-valuenow="{{ $percRem }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>

                        <!-- VISITAS (EXTERNAL) -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-end mb-2">
                                <div>
                                    <label class="small fw-bold text-secondary mb-0">Visitas Presenciales</label>
                                    <div class="h4 fw-bold mb-0 {{ ($metrics->limite_visita != -1 && $metrics->disponible_visita == 0) ? 'text-danger' : 'text-dark' }}">
                                        {{ $metrics->usados_visita }} <small class="text-muted fw-normal">/ {{ $metrics->limite_visita == -1 ? '∞' : $metrics->limite_visita }}</small>
                                    </div>
                                </div>
                                <div class="text-end">
                                    @if($metrics->limite_visita == -1)
                                        <span class="badge bg-info-subtle text-info rounded-pill small">
                                            Ilimitado
                                        </span>
                                    @elseif($metrics->disponible_visita > 0)
                                        <span class="badge bg-success-subtle text-success rounded-pill small">
                                            {{ $metrics->disponible_visita }} disponibles
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger rounded-pill small">
                                            Límite agotado
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="progress" style="height: 10px; border-radius: 5px; background-color: #f1f5f9;">
                                @php 
                                    if ($metrics->limite_visita == -1) {
                                        $percVis = 0;
                                        $pbColorVis = 'bg-info';
                                    } else {
                                        $percVis = ($metrics->limite_visita > 0) ? min(100, ($metrics->usados_visita / $metrics->limite_visita) * 100) : 100;
                                        $pbColorVis = ($metrics->disponible_visita == 0) ? 'bg-danger' : 'bg-primary';
                                    }
                                @endphp
                                <div class="progress-bar {{ $pbColorVis }}" role="progressbar" style="width: {{ $percVis }}%" aria-valuenow="{{ $percVis }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>

                    @if($esAdmin || $esEncargado)
                        @if($metrics->excedidos_remoto > 0 || $metrics->excedidos_visita > 0)
                        <div class="card-footer bg-danger-subtle border-0 p-3">
                            <div class="d-flex align-items-center gap-2 text-danger fw-bold small">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                                SERVICIOS ADICIONALES (EXCEDIDOS):
                                @if($metrics->excedidos_remoto > 0)
                                    <span class="badge bg-danger rounded-pill">+{{ $metrics->excedidos_remoto }} Remotos</span>
                                @endif
                                @if($metrics->excedidos_visita > 0)
                                    <span class="badge bg-danger rounded-pill">+{{ $metrics->excedidos_visita }} Visitas</span>
                                @endif
                            </div>
                        </div>
                        @else
                        <div class="card-footer bg-light border-0 p-3">
                            <div class="d-flex align-items-center gap-2 text-success small fw-bold">
                                <i class="bi bi-check-circle-fill"></i>
                                Cuenta dentro del límite de la iguala.
                            </div>
                        </div>
                        @endif
                    @endif
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center p-5 bg-white rounded-4 shadow-sm">
                    <i class="bi bi-search fs-1 text-muted opacity-50"></i>
                    <h5 class="mt-3">No hay clientes con igualas asignadas para mostrar.</h5>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
