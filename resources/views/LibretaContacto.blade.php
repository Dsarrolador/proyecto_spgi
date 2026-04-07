@extends('layouts.app')

@section('title', 'Libreta de Contactos')

@section('content')
<div class="container py-4">

    {{-- ALERTAS --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Listo:</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Ojo:</strong> Hay errores en el formulario
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h4 class="fw-bold mb-0">Libreta de Contactos</h4>

        <div class="d-flex gap-2">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCrearContacto">
                <i class="bi bi-person-plus"></i> Nuevo Contacto
            </button>
        </div>
    </div>

    {{-- TABLA --}}
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-bordered mb-0 align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 70px;">ID</th>
                            <th>Nombre</th>
                            <th style="width: 160px;">Teléfono</th>
                            <th>Correo</th>
                            <th style="width: 160px;">Rol</th>
                            <th>Nota</th>
                            <th style="width: 170px;" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contactos as $c)
                            <tr>
                                <td class="text-center">{{ $c->id }}</td>
                                <td class="fw-semibold">{{ $c->nombre }}</td>
                                <td>{{ $c->telefono ?? '-' }}</td>
                                <td>{{ $c->correo ?? '-' }}</td>
                                <td>{{ $c->rol->nombre ?? 'Sin rol' }}</td>
                                <td>{{ $c->nota ?? '-' }}</td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2 flex-wrap">

                                        {{-- EDITAR (modal) --}}
                                        <button
                                            type="button"
                                            class="btn btn-warning btn-sm text-dark"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalEditarContacto"
                                            data-id="{{ $c->id }}"
                                            data-codigo_cliente_maestro="{{ $c->codigo_cliente_maestro }}"
                                            data-nombre="{{ $c->nombre }}"
                                            data-telefono="{{ $c->telefono }}"
                                            data-correo="{{ $c->correo }}"
                                            data-nota="{{ $c->nota }}"
                                            data-codigo_rol="{{ $c->codigo_rol }}"
                                        >
                                            <i class="bi bi-pencil-square"></i> Editar
                                        </button>

                                        {{-- ELIMINAR --}}
                                        <form action="{{ route('libreta_contacto.destroy', $c->id) }}" method="POST"
                                              onsubmit="return confirm('¿Seguro que deseas eliminar este contacto?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="bi bi-trash"></i> Borrar
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    No hay contactos registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    {{-- =========================
        MODAL CREAR CONTACTO
    ========================= --}}
    <div class="modal fade" id="modalCrearContacto" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-person-plus"></i> Agregar Contacto
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <form action="{{ route('libreta_contacto.store') }}" method="POST">
                    @csrf

                    <div class="modal-body">
                        <div class="row g-3">

                            <div class="col-md-4">
                                <label class="form-label">Código Cliente Maestro</label>
                                <input type="number" name="codigo_cliente_maestro" class="form-control"
                                       value="{{ old('codigo_cliente_maestro') }}" required>
                                <div class="form-text">Debe ser el ID del cliente (cliente_maestro).</div>
                            </div>

                            <div class="col-md-8">
                                <label class="form-label">Nombre</label>
                                <input type="text" name="nombre" class="form-control"
                                       value="{{ old('nombre') }}" maxlength="100" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Teléfono</label>
                                <input type="text" name="telefono" class="form-control"
                                       value="{{ old('telefono') }}" maxlength="20">
                            </div>

                            <div class="col-md-5">
                                <label class="form-label">Correo</label>
                                <input type="email" name="correo" class="form-control"
                                       value="{{ old('correo') }}" maxlength="100">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Rol</label>
                                <select name="codigo_rol" class="form-select">
                                    <option value="">-- Seleccionar --</option>
                                    @foreach($roles as $r)
                                        <option value="{{ $r->id }}" {{ old('codigo_rol') == $r->id ? 'selected' : '' }}>
                                            {{ $r->nombre ?? ('Rol #' . $r->id) }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text">Si no eliges, se asigna rol por defecto (ID=1).</div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Nota</label>
                                <textarea name="nota" class="form-control" rows="3" maxlength="255">{{ old('nota') }}</textarea>
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Guardar
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>


    {{-- =========================
        MODAL EDITAR CONTACTO
    ========================= --}}
    <div class="modal fade" id="modalEditarContacto" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header bg-warning">
                    <h5 class="modal-title">
                        <i class="bi bi-pencil-square"></i> Editar Contacto
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <form id="formEditarContacto" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="modal-body">
                        <div class="row g-3">

                            <div class="col-md-4">
                                <label class="form-label">Código Cliente Maestro</label>
                                <input type="number" id="edit_codigo_cliente_maestro" class="form-control" disabled>
                                <div class="form-text">Este campo no se edita aquí.</div>
                            </div>

                            <div class="col-md-8">
                                <label class="form-label">Nombre</label>
                                <input type="text" name="nombre" id="edit_nombre" class="form-control" maxlength="100" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Teléfono</label>
                                <input type="text" name="telefono" id="edit_telefono" class="form-control" maxlength="20">
                            </div>

                            <div class="col-md-5">
                                <label class="form-label">Correo</label>
                                <input type="email" name="correo" id="edit_correo" class="form-control" maxlength="100">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Rol</label>
                                <select name="codigo_rol" id="edit_codigo_rol" class="form-select" required>
                                    @foreach($roles as $r)
                                        <option value="{{ $r->id }}">
                                            {{ $r->nombre ?? ('Rol #' . $r->id) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Nota</label>
                                <textarea name="nota" id="edit_nota" class="form-control" rows="3" maxlength="255"></textarea>
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-check-circle"></i> Actualizar
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

</div>
@endsection


@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const modalEditar = document.getElementById('modalEditarContacto');
    const formEditar  = document.getElementById('formEditarContacto');

    modalEditar.addEventListener('show.bs.modal', function (event) {
        const btn = event.relatedTarget;
        const id = btn.getAttribute('data-id');

        // Ruta update: /libreta-contacto/{id}
        formEditar.action = `{{ url('/libreta-contacto') }}/${id}`;

        // Inputs
        document.getElementById('edit_codigo_cliente_maestro').value = btn.getAttribute('data-codigo_cliente_maestro') || '';
        document.getElementById('edit_nombre').value   = btn.getAttribute('data-nombre') || '';
        document.getElementById('edit_telefono').value = btn.getAttribute('data-telefono') || '';
        document.getElementById('edit_correo').value   = btn.getAttribute('data-correo') || '';
        document.getElementById('edit_nota').value     = btn.getAttribute('data-nota') || '';

        // Rol
        const codigoRol = btn.getAttribute('data-codigo_rol') || '';
        const selectRol = document.getElementById('edit_codigo_rol');
        if (codigoRol !== '') {
            selectRol.value = codigoRol;
        }
    });
});
</script>
@endsection
