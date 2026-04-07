@extends('layouts.app')

@section('content')
<div class="container py-4">

  <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h4 class="fw-bold mb-0">Tipo de soporte</h4>

    <a href="{{ route('mantenimiento.tipo-soporte.create') }}" class="btn btn-primary">
      <i class="bi bi-plus-circle me-1"></i> Nuevo
    </a>
  </div>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <div class="card shadow-sm">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-striped table-bordered mb-0 align-middle">
          <thead class="table-light">
            <tr>
              <th style="width:70px;">ID</th>
              <th>Nombre</th>
              <th>Descripción</th>
              <th style="width:110px;">Activo</th>
              <th style="width:200px;">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @forelse($tipos as $t)
              <tr>
                <td>{{ $t->id }}</td>
                <td class="fw-semibold">{{ $t->nombre }}</td>
                <td class="text-muted">{{ $t->descripcion ?? '-' }}</td>
                <td>
                  @if($t->activo)
                    <span class="badge bg-success">Sí</span>
                  @else
                    <span class="badge bg-secondary">No</span>
                  @endif
                </td>
                <td>
                  <div class="d-flex gap-2">
                    <a class="btn btn-sm btn-warning"
                       href="{{ route('mantenimiento.tipo-soporte.edit', $t->id) }}">
                      <i class="bi bi-pencil"></i> Editar
                    </a>

                    <form action="{{ route('mantenimiento.tipo-soporte.destroy', $t->id) }}"
                          method="POST"
                          onsubmit="return confirm('¿Eliminar este tipo de soporte?');">
                      @csrf
                      @method('DELETE')
                      <button class="btn btn-sm btn-danger" type="submit">
                        <i class="bi bi-trash"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center text-muted py-4">
                  No hay tipos de soporte creados.
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
