@extends('layouts.app')

@section('page_title', 'Tipos de Equipo')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0">Tipos de Equipo</h3>
            <p class="text-muted small mb-0">Gestión de las categorías de equipos (ej. Servidores, Laptops, UPS).</p>
        </div>
        <button class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalNuevo">
            <i class="bi bi-plus-circle me-1"></i> Nuevo Tipo
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-dark text-white">
                    <tr>
                        <th class="ps-4">Nombre</th>
                        <th>Descripción</th>
                        <th class="text-center">Estado</th>
                        <th class="text-center pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @forelse($tipos as $t)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-light rounded-circle p-2 text-primary" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                        <i class="bi bi-tags"></i>
                                    </div>
                                    <div class="fw-bold text-dark">{{ $t->nombre }}</div>
                                </div>
                            </td>
                            <td>
                                <span class="text-muted small">{{ $t->descripcion ?: '-' }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge rounded-pill {{ $t->activo ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} border">
                                    {{ $t->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="text-center pe-4">
                                <div class="d-flex justify-content-center gap-2">
                                    <button class="btn btn-outline-warning btn-sm rounded-circle" data-bs-toggle="modal" data-bs-target="#modalEditar{{ $t->id }}" style="width: 32px; height: 32px;">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('mantenimiento.tipos-equipo.destroy', $t->id) }}" method="POST" onsubmit="return confirm('¿Eliminar este tipo de equipo?')">
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
                        <div class="modal fade" id="modalEditar{{ $t->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                                    <form action="{{ route('mantenimiento.tipos-equipo.update', $t->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header bg-warning text-dark border-0">
                                            <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i> Editar Tipo</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold small">Nombre</label>
                                                <input type="text" name="nombre" class="form-control rounded-3" value="{{ $t->nombre }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold small">Descripción</label>
                                                <textarea name="descripcion" class="form-control rounded-3" rows="3">{{ $t->descripcion }}</textarea>
                                            </div>
                                            <div class="form-check form-switch mt-3">
                                                <input class="form-check-input" type="checkbox" name="activo" value="1" {{ $t->activo ? 'checked' : '' }}>
                                                <label class="form-check-label small fw-bold">Activo</label>
                                            </div>
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
                            <td colspan="4" class="py-5 text-center text-muted">
                                <i class="bi bi-tags fs-1 d-block mb-3 opacity-25"></i>
                                No hay tipos de equipo registrados.
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
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <form action="{{ route('mantenimiento.tipos-equipo.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i> Nuevo Tipo de Equipo</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Nombre</label>
                        <input type="text" name="nombre" class="form-control rounded-3" placeholder="Ej: Laptop, Servidor, UPS" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Descripción</label>
                        <textarea name="descripcion" class="form-control rounded-3" rows="3" placeholder="Opcional..."></textarea>
                    </div>
                    <div class="form-check form-switch mt-3">
                        <input class="form-check-input" type="checkbox" name="activo" value="1" checked>
                        <label class="form-check-label small fw-bold">Activo</label>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Crear Tipo</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
