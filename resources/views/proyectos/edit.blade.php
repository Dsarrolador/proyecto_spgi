@extends('layouts.app')

@section('page_title', 'Editar Proyecto')

@section('content')

@php
    $clientes = $clientes ?? collect();
    $encargados = $encargados ?? collect();
@endphp

<style>
  .spgi-wrap{ max-width: 1100px; margin: 28px auto 60px; }

  .spgi-topbar{
    background: var(--bg-surface-glass); border: 1px solid var(--border-main);
    border-radius: 20px; box-shadow: var(--shadow-main); backdrop-filter: blur(16px); padding: 20px;
  }

  .spgi-title{ font-weight: 800; font-size: 1.4rem; color: var(--text-main); letter-spacing: -.5px; margin:0; }
  .spgi-subtitle{ color: var(--text-muted); font-size: .95rem; margin-top: 4px; }

  .btn-pill{ border-radius: 14px; padding: .65rem 1.5rem; font-weight: 700; transition:all 0.3s ease; }
  .btn-primary-pill{ background: var(--spgi-primary); color:#fff; border: 0; box-shadow: 0 10px 25px var(--spgi-primary-glow); }
  .btn-primary-pill:hover{ filter: brightness(1.1); transform:translateY(-2px); color: #fff; }

  .btn-secondary-pill{ background: var(--bg-surface); color: var(--text-main); border: 1px solid var(--border-main); }
  .btn-secondary-pill:hover{ background: rgba(var(--spgi-primary), 0.05); transform:translateY(-2px); }

  .spgi-card{
    margin-top:24px; background: var(--bg-surface-glass); border-radius:24px;
    border: 1px solid var(--border-main); box-shadow: var(--shadow-main); backdrop-filter: blur(16px);
    overflow: hidden;
  }
  .spgi-card-header{ padding:20px 24px; border-bottom:1px solid var(--border-main); font-weight: 800; color: var(--text-main); }
  .spgi-card-body{ padding:32px; }

  .spgi-label{ font-weight: 800; font-size:.75rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; }

  .spgi-control, .spgi-select{
    background: var(--bg-surface) !important; color: var(--text-main) !important;
    border-radius:12px !important; border:1px solid var(--border-main) !important;
    padding:.75rem 1rem !important; box-shadow: none !important; transition: all 0.2s ease;
  }
  .spgi-control:focus, .spgi-select:focus{ border-color: var(--spgi-primary) !important; box-shadow: 0 0 0 4px rgba(var(--spgi-primary), 0.1) !important; }
</style>

<div class="spgi-wrap">

    <div class="spgi-topbar d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h3 class="spgi-title">Editar Proyecto</h3>
            <div class="spgi-subtitle">Actualiza la información del proyecto</div>
        </div>

        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('proyectos.index') }}" class="btn btn-secondary-pill btn-pill">
                <i class="bi bi-arrow-left"></i> Volver
            </a>

            <button form="form-proyecto" type="submit" class="btn btn-primary-pill btn-pill">
                <i class="bi bi-save"></i> Guardar cambios
            </button>
        </div>
    </div>

    <div class="spgi-card">
        <div class="spgi-card-header">
            <strong>Datos del Proyecto</strong>
            <div class="small text-muted">Nombre, tipo, cliente, encargado y estado.</div>
        </div>

        <div class="spgi-card-body">
            @if ($errors->any())
                <div class="alert alert-danger rounded-4">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="form-proyecto" method="POST"
                  action="{{ route('proyectos.update', $proyecto->id) }}"
                  enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="spgi-label">Nombre del Proyecto</label>
                        <input type="text" name="nombre" class="form-control spgi-control"
                               value="{{ old('nombre', $proyecto->nombre) }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="spgi-label">Tipo de Proyecto</label>
                        <select name="tipo_proyecto" class="form-select spgi-select" required>
                            @php $tp = old('tipo_proyecto', $proyecto->tipo_proyecto); @endphp
                            <option value="">Seleccionar tipo</option>
                            <option value="Implementación" {{ $tp=='Implementación' ? 'selected' : '' }}>Implementación</option>
                            <option value="Migración" {{ $tp=='Migración' ? 'selected' : '' }}>Migración</option>
                            <option value="Soporte" {{ $tp=='Soporte' ? 'selected' : '' }}>Soporte</option>
                            <option value="Mejora" {{ $tp=='Mejora' ? 'selected' : '' }}>Mejora</option>
                            <option value="Otro" {{ $tp=='Otro' ? 'selected' : '' }}>Otro</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="spgi-label">Cliente</label>
                        <select name="cliente_id" class="form-select spgi-select" required>
                            <option value="">Seleccionar cliente</option>
                            @foreach($clientes as $c)
                                <option value="{{ $c->id }}"
                                    {{ (string)old('cliente_id', $proyecto->cliente_id) === (string)$c->id ? 'selected' : '' }}>
                                    {{ $c->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="spgi-label">Encargado (opcional)</label>
                        <select name="contacto_id" class="form-select spgi-select">
                            <option value="">Seleccionar encargado</option>
                            @foreach($encargados as $u)
                                <option value="{{ $u->id }}"
                                    {{ (string)old('contacto_id', $proyecto->contacto_id) === (string)$u->id ? 'selected' : '' }}>
                                    {{ $u->name ?? $u->nombre ?? $u->email }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="spgi-label">Estado</label>
                        @php $est = old('estado', $proyecto->estado); @endphp
                        <select name="estado" class="form-select spgi-select">
                            <option value="Activo" {{ $est=='Activo' ? 'selected' : '' }}>Activo</option>
                            <option value="Pausado" {{ $est=='Pausado' ? 'selected' : '' }}>Pausado</option>
                            <option value="Cerrado" {{ $est=='Cerrado' ? 'selected' : '' }}>Cerrado</option>
                            <option value="En progreso" {{ $est=='En progreso' ? 'selected' : '' }}>En progreso</option>
                            <option value="Completado" {{ $est=='Completado' ? 'selected' : '' }}>Completado</option>
                            <option value="Cancelado" {{ $est=='Cancelado' ? 'selected' : '' }}>Cancelado</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="spgi-label">Prioridad</label>
                        @php $prio = old('prioridad', $proyecto->prioridad ?? 'Media'); @endphp
                        <select name="prioridad" class="form-select spgi-select">
                            <option value="Baja" {{ $prio=='Baja' ? 'selected' : '' }}>Baja</option>
                            <option value="Media" {{ $prio=='Media' ? 'selected' : '' }}>Media</option>
                            <option value="Alta" {{ $prio=='Alta' ? 'selected' : '' }}>Alta</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="spgi-label">Fecha inicio</label>
                        <input type="date" name="fecha_inicio" class="form-control spgi-control"
                               value="{{ old('fecha_inicio', optional($proyecto->fecha_inicio)->format('Y-m-d')) }}">
                    </div>

                    <div class="col-md-6">
                        <label class="spgi-label">Fecha fin</label>
                        <input type="date" name="fecha_fin" class="form-control spgi-control"
                               value="{{ old('fecha_fin', optional($proyecto->fecha_fin)->format('Y-m-d')) }}">
                    </div>

                    <div class="col-12">
                        <label class="spgi-label">Alcance / Descripción</label>
                        <textarea name="alcance" rows="3" class="form-control spgi-control">{{ old('alcance', $proyecto->descripcion) }}</textarea>
                    </div>

                    <div class="col-12">
                        <label class="spgi-label">Adjunto (opcional)</label>
                        <input type="file" name="adjunto" class="form-control spgi-control">
                        @if(!empty($proyecto->adjunto))
                            <div class="mt-2 small">
                                Adjunto actual:
                                <a href="{{ asset('storage/'.$proyecto->adjunto) }}" target="_blank">Ver</a>
                            </div>
                        @endif
                    </div>

                </div>
            </form>
        </div>
    </div>

</div>

@endsection