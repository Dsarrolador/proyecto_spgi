@extends('layouts.app')

@section('page_title', 'Mantenimiento: Tipos de Soporte')

@section('content')
<div class="container-fluid py-4">

  <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h4 class="fw-bold mb-1">Tipos de Soporte</h4>
        <p class="text-muted small mb-0">Gestión de las modalidades de atención técnica del sistema.</p>
    </div>

    <a href="{{ route('mantenimiento.tipo-soporte.create') }}" class="btn btn-spgi">
      <i class="bi bi-plus-lg me-2"></i> Nuevo Tipo
    </a>
  </div>

  @if(session('success'))
    <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4" style="background: rgba(34, 197, 94, 0.1); color: #22c55e;">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
    </div>
  @endif

  <div class="spgi-card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-spgi mb-0 align-middle">
          <thead>
            <tr>
              <th style="width:80px;">ID</th>
              <th>Nombre del Servicio</th>
              <th>Descripción Detallada</th>
              <th style="width:120px;" class="text-center">Estado</th>
              <th style="width:180px;" class="text-center">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @forelse($tipos as $t)
              <tr>
                <td class="text-muted fw-mono">#{{ $t->id }}</td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center" style="width:32px; height:32px;">
                            <i class="bi bi-headset text-primary small"></i>
                        </div>
                        <span class="fw-bold">{{ $t->nombre }}</span>
                    </div>
                </td>
                <td class="text-muted small">{{ Str::limit($t->descripcion ?? 'Sin descripción', 80) }}</td>
                <td class="text-center">
                  @if($t->activo)
                    <span class="badge rounded-pill" style="background: rgba(34, 197, 94, 0.15); color: #22c55e; border: 1px solid rgba(34, 197, 94, 0.2);">Activo</span>
                  @else
                    <span class="badge rounded-pill" style="background: rgba(148, 163, 184, 0.15); color: #94a3b8; border: 1px solid rgba(148, 163, 184, 0.2);">Inactivo</span>
                  @endif
                </td>
                <td class="text-center">
                  <div class="d-flex gap-2 justify-content-center">
                    <a class="btn btn-sm btn-outline-warning border-0 rounded-circle"
                       href="{{ route('mantenimiento.tipo-soporte.edit', $t->id) }}" title="Editar">
                      <i class="bi bi-pencil-square fs-5"></i>
                    </a>

                    <form action="{{ route('mantenimiento.tipo-soporte.destroy', $t->id) }}"
                          method="POST"
                          onsubmit="return confirm('¿Eliminar este tipo de soporte?');">
                      @csrf
                      @method('DELETE')
                      <button class="btn btn-sm btn-outline-danger border-0 rounded-circle" type="submit" title="Eliminar">
                        <i class="bi bi-trash3 fs-5"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center py-5">
                  <div class="text-muted">
                    <i class="bi bi-inbox fs-1 d-block mb-3 opacity-25"></i>
                    No hay tipos de soporte registrados en el catálogo.
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
@endsection
