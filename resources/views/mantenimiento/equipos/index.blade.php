@extends('layouts.app')

@section('page_title', 'Mantenimiento de Equipos')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0">Catálogo de Equipos</h3>
            <p class="text-muted small mb-0">Define los modelos de equipos y sus drivers para luego asignarlos a los clientes.</p>
        </div>
        <button class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalNuevo">
            <i class="bi bi-plus-circle me-1"></i> Nuevo Equipo
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        </div>
    @endif

    <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-dark text-white">
                    <tr>
                        <th class="ps-4">Tipo / Nombre</th>
                        <th>Marca / Modelo</th>
                        <th>Drivers / Config</th>
                        <th class="text-center">Estado</th>
                        <th class="text-center pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @forelse($equipos as $e)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-light rounded-circle p-2 text-primary" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                        @if(stripos($e->tipo, 'printer') !== false) <i class="bi bi-printer"></i>
                                        @elseif(stripos($e->tipo, 'monitor') !== false) <i class="bi bi-display"></i>
                                        @else <i class="bi bi-cpu"></i> @endif
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $e->nombre }}</div>
                                        <span class="badge bg-light text-dark fw-normal border" style="font-size: 0.7rem;">
                                            {{ $e->tipoEquipo->nombre ?? $e->tipo ?? 'Sin tipo' }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="small fw-semibold">{{ $e->marca ?: '-' }}</div>
                                <div class="text-muted small">{{ $e->modelo ?: '-' }}</div>
                            </td>
                            <td>
                                @if($e->driver_url)
                                    <a href="{{ $e->driver_url }}" target="_blank" class="btn btn-link btn-sm p-0 text-decoration-none">
                                        <i class="bi bi-download me-1"></i> Drivers
                                    </a>
                                @else
                                    <span class="text-muted small italic">Sin drivers</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge rounded-pill {{ $e->activo ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} border">
                                    {{ $e->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="text-center pe-4">
                                <div class="d-flex justify-content-center gap-2">
                                    <button class="btn btn-outline-warning btn-sm rounded-circle" data-bs-toggle="modal" data-bs-target="#modalEditar{{ $e->id }}" style="width: 32px; height: 32px;">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('mantenimiento.equipos.destroy', $e->id) }}" method="POST" onsubmit="return confirm('¿Eliminar este equipo?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-outline-danger btn-sm rounded-circle" style="width: 32px; height: 32px;">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <!-- Modal Editar -->
                        <div class="modal fade" id="modalEditar{{ $e->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                                    <form action="{{ route('mantenimiento.equipos.update', $e->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header bg-warning text-dark border-0">
                                            <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i> Editar Equipo</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            @include('mantenimiento.equipos._form', ['equipo' => $e])
                                        </div>
                                        <div class="modal-footer border-0 p-4">
                                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                                            <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold">Guardar Cambios</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    @empty
                        <tr>
                            <td colspan="5" class="py-5 text-center text-muted">
                                <i class="bi bi-box-seam fs-1 d-block mb-3 opacity-25"></i>
                                No hay equipos registrados en el catálogo.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Nuevo -->
<div class="modal fade" id="modalNuevo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <form action="{{ route('mantenimiento.equipos.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i> Nuevo Equipo en Catálogo</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    @include('mantenimiento.equipos._form', ['equipo' => null])
                </div>
                <div class="modal-footer border-0 p-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Crear Equipo</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
