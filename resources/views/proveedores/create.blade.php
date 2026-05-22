@extends('layouts.app')

@section('page_title', 'Nuevo Proveedor')

@section('content')

<style>
    .form-container {
        max-width: 900px; margin: 0 auto;
    }
    .card-form {
        border-radius: 24px; border: 1px solid var(--border-main);
        background: var(--bg-surface-glass); backdrop-filter: blur(16px);
        box-shadow: var(--shadow-main); padding: 40px;
    }
    .input-group-custom {
        background: var(--bg-master); border-radius: 16px; padding: 20px;
        margin-bottom: 24px; border: 1px solid var(--border-main);
    }
    .input-label {
        font-weight: 700; font-size: 0.85rem; text-transform: uppercase;
        letter-spacing: 0.5px; color: var(--text-muted); margin-bottom: 12px;
        display: flex; align-items: center; gap: 8px;
    }
    .input-label i { color: var(--spgi-primary); }
    
    .btn-save {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        border: 0; color: #fff !important; min-height:54px; border-radius:16px; padding:0 40px;
        box-shadow: 0 10px 25px rgba(59, 130, 246, 0.3); font-weight:700;
        transition: all 0.3s;
    }
    .btn-save:hover { transform: translateY(-2px); box-shadow: 0 15px 30px rgba(59, 130, 246, 0.4); }
</style>

<div class="container py-5">
    <div class="form-container">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 fw-bold mb-0"><i class="bi bi-truck me-2 text-primary"></i> Registrar Proveedor</h1>
            <a href="{{ route('proveedores.index') }}" class="btn btn-light rounded-pill border px-4">
                <i class="bi bi-arrow-left me-2"></i> Cancelar
            </a>
        </div>

        @if($errors->any())
            <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('proveedores.store') }}" method="POST" class="card-form animate__animated animate__fadeIn">
            @csrf
            
            <div class="row">
                <div class="col-md-7">
                    <div class="input-group-custom">
                        <label class="input-label"><i class="bi bi-person-badge"></i> Información Principal</label>
                        <div class="mb-3">
                            <label class="small text-muted mb-1">Nombre de la Empresa / Proveedor *</label>
                            <input type="text" name="nombre" class="form-control" required placeholder="Ej: Importadora Global S.R.L." value="{{ old('nombre') }}">
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="small text-muted mb-1">RNC / Cédula</label>
                                <input type="text" name="rnc" class="form-control" placeholder="001-00000-0" value="{{ old('rnc') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="small text-muted mb-1">Categoría</label>
                                <select name="categoria" class="form-select">
                                    <option value="Servicios">Servicios</option>
                                    <option value="Suministros">Suministros</option>
                                    <option value="Equipos">Equipos</option>
                                    <option value="Mantenimiento">Mantenimiento</option>
                                    <option value="Otros">Otros</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="input-group-custom">
                        <label class="input-label"><i class="bi bi-person-lines-fill"></i> Contacto</label>
                        <div class="mb-3">
                            <label class="small text-muted mb-1">Persona de Contacto</label>
                            <input type="text" name="persona_contacto" class="form-control" placeholder="Nombre de quien atiende" value="{{ old('persona_contacto') }}">
                        </div>
                        <div class="mb-3">
                            <label class="small text-muted mb-1">Teléfono</label>
                            <input type="text" name="telefono" class="form-control" placeholder="809-000-0000" value="{{ old('telefono') }}">
                        </div>
                        <div class="mb-0">
                            <label class="small text-muted mb-1">Correo Electrónico</label>
                            <input type="email" name="correo" class="form-control" placeholder="contacto@empresa.com" value="{{ old('correo') }}">
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="input-group-custom">
                        <label class="input-label"><i class="bi bi-geo-alt"></i> Ubicación y Notas</label>
                        <div class="mb-3">
                            <label class="small text-muted mb-1">Dirección Física</label>
                            <input type="text" name="direccion" class="form-control" placeholder="Calle, Número, Ciudad..." value="{{ old('direccion') }}">
                        </div>
                        <div class="mb-0">
                            <label class="small text-muted mb-1">Observaciones / Detalles Adicionales</label>
                            <textarea name="observaciones" class="form-control" rows="3" placeholder="Escribe aquí cualquier detalle relevante sobre este proveedor...">{{ old('observaciones') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-end mt-4">
                <button type="submit" class="btn btn-save">
                    <i class="bi bi-check-circle me-2"></i> Guardar Proveedor
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
