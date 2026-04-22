@extends('layouts.app')

@section('page_title', 'Crear Requerimiento Comercial')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0" style="border-radius: 20px; overflow: hidden;">
                <div class="card-header bg-dark text-white p-4">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-journal-plus me-2"></i> Nuevo Requerimiento Comercial</h5>
                </div>
                <div class="card-body p-4">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('lead-requirements.store') }}" method="POST">
                        @csrf

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-bold">Lead / Prospecto <span class="text-danger">*</span></label>
                                <select name="lead_id" class="form-select @error('lead_id') is-invalid @enderror" required>
                                    <option value="">Selecciona un lead...</option>
                                    @foreach($leads as $l)
                                        <option value="{{ $l->id }}" {{ (old('lead_id') ?? $selected_lead) == $l->id ? 'selected' : '' }}>
                                            {{ $l->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('lead_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold">Descripción del Requerimiento <span class="text-danger">*</span></label>
                                <textarea name="descripcion" class="form-control" rows="4" placeholder="Ej: Llamar para confirmar demo, enviar cotización revisada..." required>{{ old('descripcion') }}</textarea>
                                @error('descripcion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Estado Inicial</label>
                                <select name="estado" class="form-select">
                                    <option value="Pendiente" {{ old('estado') == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                                    <option value="Realizado" {{ old('estado') == 'Realizado' ? 'selected' : '' }}>Realizado</option>
                                    <option value="Cancelado" {{ old('estado') == 'Cancelado' ? 'selected' : '' }}>Cancelado</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Asignado a (Opcional)</label>
                                <select name="asignado_id" class="form-select">
                                    <option value="">Sin asignar</option>
                                    @foreach($users as $u)
                                        <option value="{{ $u->id }}" {{ old('asignado_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top d-flex gap-2">
                            <button type="submit" class="btn btn-success px-4 py-2 rounded-pill fw-bold">
                                <i class="bi bi-save me-1"></i> Guardar Requerimiento
                            </button>
                            <a href="{{ route('lead-requirements.index') }}" class="btn btn-light px-4 py-2 rounded-pill fw-bold border">
                                Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
