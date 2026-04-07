@extends('layouts.app')

@section('page_title', 'Editar Proyecto')

@section('content')

@php
    $clientes = $clientes ?? collect();
    $encargados = $encargados ?? collect();
@endphp

<style>
    :root{
        --bg1: rgba(59,130,246,.18);
        --bg2: rgba(236,72,153,.14);
        --bg3: rgba(34,197,94,.12);
        --ink:#0f172a;
        --muted:#64748b;
        --line: rgba(15,23,42,.10);
        --shadow:0 18px 60px rgba(0,0,0,.10);
        --shadowSoft:0 10px 30px rgba(0,0,0,.06);
    }
    body{
        background:
            radial-gradient(900px 600px at 15% 18%, var(--bg1), transparent 55%),
            radial-gradient(800px 600px at 90% 20%, var(--bg2), transparent 55%),
            radial-gradient(700px 500px at 50% 90%, var(--bg3), transparent 60%),
            linear-gradient(180deg, #f7f8fb 0%, #eef2f7 45%, #f7f8fb 100%);
        background-attachment: fixed;
    }
    .spgi-wrap{ max-width: 1100px; margin: 28px auto 60px; }
    .spgi-topbar{
        background: rgba(255,255,255,.92);
        border-radius: 20px;
        box-shadow: var(--shadowSoft);
        padding: 16px 20px;
        backdrop-filter: blur(10px);
    }
    .spgi-title{ font-weight: 800; font-size: 1.05rem; color: var(--ink); margin:0; }
    .spgi-subtitle{ color: var(--muted); font-size: .85rem; }
    .btn-pill{ border-radius: 14px; padding: .55rem 1rem; font-weight: 700; transition:.15s ease; }
    .btn-primary-pill{ background:#1d4ed8; color:#fff; }
    .btn-primary-pill:hover{ background:#1e40af; transform:translateY(-1px); }
    .btn-secondary-pill{ background:#eef2ff; color:#1e40af; }
    .btn-secondary-pill:hover{ background:#e0e7ff; transform:translateY(-1px); }
    .spgi-card{
        margin-top:18px;
        background: rgba(255,255,255,.92);
        border-radius:22px;
        box-shadow: var(--shadow);
        backdrop-filter: blur(10px);
    }
    .spgi-card-header{ padding:16px 20px; border-bottom:1px solid var(--line); }
    .spgi-card-body{ padding:20px; }
    .spgi-label{ font-weight:700; font-size:.9rem; color:var(--ink); margin-bottom:.35rem; }
    .spgi-control,.spgi-select{
        border-radius:14px !important;
        border:1px solid rgba(15,23,42,.10) !important;
        padding:.7rem .9rem !important;
        box-shadow:0 8px 18px rgba(2,6,23,.04);
        transition:.15s ease;
    }
    .spgi-control:focus,.spgi-select:focus{
        border-color:rgba(37,99,235,.35) !important;
        box-shadow:0 0 0 .22rem rgba(37,99,235,.12) !important;
    }
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