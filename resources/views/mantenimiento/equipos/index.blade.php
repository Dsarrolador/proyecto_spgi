@extends('layouts.app')

@section('page_title', 'Mantenimiento: Catálogo de Equipos')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h4 class="fw-bold mb-1">Catálogo de Equipos</h4>
            <p class="text-muted small mb-0">Definición de hardware, modelos y drivers para asignación global.</p>
        </div>
        <button class="btn btn-spgi" data-bs-toggle="modal" data-bs-target="#modalNuevo">
            <i class="bi bi-plus-lg me-2"></i> Nuevo Equipo
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4" style="background: rgba(34, 197, 94, 0.1); color: #22c55e;">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        </div>
    @endif

    <div class="spgi-card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-spgi align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4" style="width: 25%;">Tipo / Categorización</th>
                            <th style="width: 25%;">Identificación</th>
                            <th style="width: 20%;">Recursos / Drivers</th>
                            <th class="text-center" style="width: 15%;">Estado</th>
                            <th class="text-center pe-4" style="width: 15%;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($equipos as $e)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; background: rgba(var(--text-main), 0.05); border: 1px solid var(--border-main);">
                                            @if(stripos($e->tipo, 'printer') !== false || stripos($e->tipoEquipment->nombre ?? '', 'printer') !== false) 
                                                <i class="bi bi-printer text-primary fs-5"></i>
                                            @elseif(stripos($e->tipo, 'monitor') !== false || stripos($e->tipoEquipment->nombre ?? '', 'monitor') !== false) 
                                                <i class="bi bi-display text-primary fs-5"></i>
                                            @elseif(stripos($e->tipo, 'laptop') !== false || stripos($e->tipoEquipment->nombre ?? '', 'laptop') !== false) 
                                                <i class="bi bi-laptop text-primary fs-5"></i>
                                            @else 
                                                <i class="bi bi-cpu text-primary fs-5"></i> 
                                            @endif
                                        </div>
                                        <div>
                                            <div class="fw-bold fs-6">{{ $e->nombre }}</div>
                                            <span class="text-muted small">
                                                {{ $e->tipoEquipo->nombre ?? $e->tipo ?? 'Genérico' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold small">{{ $e->marca ?: 'Marca N/A' }}</div>
                                    <div class="text-muted x-small text-uppercase letter-spacing-1">{{ $e->modelo ?: 'Modelo N/A' }}</div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        @if($e->driverDoc)
                                            <a href="{{ route('wiki.download', $e->driverDoc->id) }}" class="btn btn-link btn-sm p-0 text-spgi-primary text-decoration-none fw-bold text-start">
                                                <i class="bi bi-file-earmark-zip me-1"></i> Driver Catálogo
                                            </a>
                                        @endif
                                        
                                        @if($e->driver_url)
                                            <a href="{{ $e->driver_url }}" target="_blank" class="btn btn-link btn-sm p-0 text-muted text-decoration-none small text-start">
                                                <i class="bi bi-link-45deg me-1"></i> URL Externa
                                            </a>
                                        @endif

                                        @if(!$e->driverDoc && !$e->driver_url)
                                            <span class="text-muted small italic opacity-50">Sin recursos adjuntos</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center">
                                    @if($e->activo)
                                        <span class="badge rounded-pill" style="background: rgba(34, 197, 94, 0.15); color: #22c55e; border: 1px solid rgba(34, 197, 94, 0.2);">Activo</span>
                                    @else
                                        <span class="badge rounded-pill" style="background: rgba(148, 163, 184, 0.15); color: #94a3b8; border: 1px solid rgba(148, 163, 184, 0.2);">Inactivo</span>
                                    @endif
                                </td>
                                <td class="text-center pe-4">
                                    <div class="d-flex justify-content-center gap-2">
                                        <button class="btn btn-sm btn-outline-warning border-0 rounded-circle" data-bs-toggle="modal" data-bs-target="#modalEditar{{ $e->id }}" title="Editar Especificaciones">
                                            <i class="bi bi-pencil-square fs-5"></i>
                                        </button>
                                        <form action="{{ route('mantenimiento.equipos.destroy', $e->id) }}" method="POST" onsubmit="return confirm('¿Eliminar este equipo del catálogo?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger border-0 rounded-circle" title="Eliminar del Sistema">
                                                <i class="bi bi-trash3 fs-5"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>


                        @empty
                            <tr>
                                <td colspan="5" class="py-5 text-center">
                                    <div class="text-muted opacity-50">
                                        <i class="bi bi-box-seam fs-1 d-block mb-3"></i>
                                        El catálogo está vacío actualmente.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nuevo -->
<div class="modal fade" id="modalNuevo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('mantenimiento.equipos.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-plus-lg me-2 text-primary"></i>Nuevo Item de Catálogo
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    @include('mantenimiento.equipos._form', ['equipo' => null])
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-spgi">Registrar Equipo</button>
                </div>
            </form>
        </div>
    </div>
</div>

@foreach($equipos as $e)
<!-- Modal Editar -->
<div class="modal fade" id="modalEditar{{ $e->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('mantenimiento.equipos.update', $e->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-pencil-fill me-2 text-warning"></i>Editar Ficha Técnica
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    @include('mantenimiento.equipos._form', ['equipo' => $e])
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-spgi">Actualizar Equipo</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@endsection
