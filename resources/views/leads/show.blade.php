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
                    <div class="info-label">Contacto Principal</div>
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
                    <div class="info-label">Vista Previa de Cotización</div>
                    @if($lead->cotizacion_pdf)
                        <div class="pdf-preview-container" style="height: 600px; border: 1px solid var(--border-main); border-radius: 16px; overflow: hidden; background: #525659;">
                            <iframe src="{{ asset('storage/' . $lead->cotizacion_pdf) }}" width="100%" height="100%" style="border: none;"></iframe>
                        </div>
                        <div class="mt-3">
                            <a href="{{ asset('storage/' . $lead->cotizacion_pdf) }}" target="_blank" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                <i class="bi bi-box-arrow-up-right me-1"></i> Abrir en pestaña nueva / Descargar
                            </a>
                        </div>
                    @else
                        <div class="text-muted small p-4 border rounded-3 bg-light text-center">
                            <i class="bi bi-file-earmark-x fs-2 d-block mb-2"></i>
                            No hay cotización adjunta.
                        </div>
                    @endif
                </div>

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
@endsection
