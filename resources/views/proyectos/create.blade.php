@extends('layouts.app')

@section('page_title', 'Nuevo Proyecto')

@section('content')

@php
    // ✅ Blindaje: si el controller no envía estas variables, no rompe la vista
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

    .spgi-wrap{
        max-width: 1100px;
        margin: 28px auto 60px;
    }

    .spgi-topbar{
        background: rgba(255,255,255,.92);
        border-radius: 20px;
        box-shadow: var(--shadowSoft);
        padding: 16px 20px;
        backdrop-filter: blur(10px);
    }

    .spgi-title{
        font-weight: 800;
        font-size: 1.05rem;
        color: var(--ink);
        margin:0;
    }

    .spgi-subtitle{
        color: var(--muted);
        font-size: .85rem;
    }

    .btn-pill{
        border-radius: 14px;
        padding: .55rem 1rem;
        font-weight: 700;
        transition:.15s ease;
    }

    .btn-primary-pill{
        background:#1d4ed8;
        color:#fff;
    }

    .btn-primary-pill:hover{
        background:#1e40af;
        transform:translateY(-1px);
    }

    .btn-secondary-pill{
        background:#eef2ff;
        color:#1e40af;
    }

    .btn-secondary-pill:hover{
        background:#e0e7ff;
        transform:translateY(-1px);
    }

    .spgi-card{
        margin-top:18px;
        background: rgba(255,255,255,.92);
        border-radius:22px;
        box-shadow: var(--shadow);
        backdrop-filter: blur(10px);
    }

    .spgi-card-header{
        padding:16px 20px;
        border-bottom:1px solid var(--line);
    }

    .spgi-card-body{
        padding:20px;
    }

    .spgi-label{
        font-weight:700;
        font-size:.9rem;
        color:var(--ink);
        margin-bottom:.35rem;
    }

    .spgi-control,
    .spgi-select{
        border-radius:14px !important;
        border:1px solid rgba(15,23,42,.10) !important;
        padding:.7rem .9rem !important;
        box-shadow:0 8px 18px rgba(2,6,23,.04);
        transition:.15s ease;
    }

    .spgi-control:focus,
    .spgi-select:focus{
        border-color:rgba(37,99,235,.35) !important;
        box-shadow:0 0 0 .22rem rgba(37,99,235,.12) !important;
    }

    .spgi-help{
        color: var(--muted);
        font-size: .82rem;
        margin-top:.35rem;
    }

    @media (max-width: 768px){
        .btn-pill{ width:100%; }
    }
</style>

<div class="spgi-wrap">

    {{-- TOPBAR --}}
    <div class="spgi-topbar d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h3 class="spgi-title">Nuevo Proyecto</h3>
            <div class="spgi-subtitle">Completa la información del proyecto</div>
        </div>

        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('proyectos.index') }}" class="btn btn-secondary-pill btn-pill">
                <i class="bi bi-arrow-left"></i> Volver
            </a>

            <button form="form-proyecto" type="submit" class="btn btn-primary-pill btn-pill">
                <i class="bi bi-save"></i> Guardar
            </button>
        </div>
    </div>

    {{-- CARD --}}
    <div class="spgi-card">
        <div class="spgi-card-header">
            <strong>Datos del Proyecto</strong>
            <div class="small text-muted">Nombre, tipo, cliente, encargado y estado.</div>
        </div>

        <div class="spgi-card-body">

            {{-- ERRORES --}}
            @if ($errors->any())
                <div class="alert alert-danger rounded-4">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- ✅ OJO: enctype para adjunto --}}
            <form id="form-proyecto" method="POST" action="{{ route('proyectos.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="spgi-label">Nombre del Proyecto</label>
                        <input type="text"
                               name="nombre"
                               class="form-control spgi-control"
                               value="{{ old('nombre') }}"
                               required>
                    </div>

                    {{-- ✅ REQUIRED por validación --}}
                    <div class="col-md-6">
                        <label class="spgi-label">Tipo de Proyecto</label>
                        <select name="tipo_proyecto" class="form-select spgi-select" required>
                            <option value="">Seleccionar tipo</option>
                            <option value="Implementación" {{ old('tipo_proyecto')=='Implementación' ? 'selected' : '' }}>Implementación</option>
                            <option value="Migración" {{ old('tipo_proyecto')=='Migración' ? 'selected' : '' }}>Migración</option>
                            <option value="Soporte" {{ old('tipo_proyecto')=='Soporte' ? 'selected' : '' }}>Soporte</option>
                            <option value="Mejora" {{ old('tipo_proyecto')=='Mejora' ? 'selected' : '' }}>Mejora</option>
                            <option value="Otro" {{ old('tipo_proyecto')=='Otro' ? 'selected' : '' }}>Otro</option>
                        </select>
                    </div>

                    {{-- CLIENTE SELECT --}}
                    <div class="col-md-6">
                        <label class="spgi-label">Cliente (opcional)</label>
                        <select name="cliente_id" class="form-select spgi-select">
                            <option value="">Seleccionar cliente</option>
                            @foreach($clientes as $cliente)
                                <option value="{{ $cliente->id }}"
                                    {{ (string)old('cliente_id') === (string)$cliente->id ? 'selected' : '' }}>
                                    {{ $cliente->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @if($clientes->isEmpty())
                            <div class="spgi-help">No hay clientes cargados (o no se están enviando desde el controller).</div>
                        @endif
                    </div>

                    {{-- ENCARGADO SELECT (map a contacto_id) --}}
                    <div class="col-md-6">
                        <label class="spgi-label">Encargado (opcional)</label>
                        {{-- ✅ Tu controller guarda contacto_id, así que el select debe llamarse contacto_id --}}
                        <select name="contacto_id" class="form-select spgi-select">
                            <option value="">Seleccionar encargado</option>
                            @foreach($encargados as $encargado)
                                <option value="{{ $encargado->id }}"
                                    {{ (string)old('contacto_id') === (string)$encargado->id ? 'selected' : '' }}>
                                    {{ $encargado->name ?? $encargado->nombre ?? ('Usuario #' . $encargado->id) }}
                                </option>
                            @endforeach
                        </select>
                        @if($encargados->isEmpty())
                            <div class="spgi-help">No hay encargados cargados (o no se están enviando desde el controller).</div>
                        @endif
                    </div>

                    <div class="col-md-6">
                        <label class="spgi-label">Estado</label>
                        <select name="estado" class="form-select spgi-select">
                            <option value="Activo" {{ old('estado','Activo')=='Activo' ? 'selected' : '' }}>Activo</option>
                            <option value="Pausado" {{ old('estado')=='Pausado' ? 'selected' : '' }}>Pausado</option>
                            <option value="Cerrado" {{ old('estado')=='Cerrado' ? 'selected' : '' }}>Cerrado</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="spgi-label">Prioridad</label>
                        <select name="prioridad" class="form-select spgi-select">
                            <option value="Baja" {{ old('prioridad')=='Baja' ? 'selected' : '' }}>Baja</option>
                            <option value="Media" {{ old('prioridad','Media')=='Media' ? 'selected' : '' }}>Media</option>
                            <option value="Alta" {{ old('prioridad')=='Alta' ? 'selected' : '' }}>Alta</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="spgi-label">Fecha inicio (opcional)</label>
                        <input type="date" name="fecha_inicio" class="form-control spgi-control" value="{{ old('fecha_inicio') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="spgi-label">Fecha fin (opcional)</label>
                        <input type="date" name="fecha_fin" class="form-control spgi-control" value="{{ old('fecha_fin') }}">
                    </div>

                    {{-- ✅ Tu controller espera "alcance" y lo mapea a descripcion --}}
                    <div class="col-12">
                        <label class="spgi-label">Alcance / Descripción (opcional)</label>
                        <textarea name="alcance"
                                  rows="3"
                                  class="form-control spgi-control">{{ old('alcance') }}</textarea>
                    </div>

                    <div class="col-12">
                        <label class="spgi-label">Adjunto (opcional)</label>
                        <input type="file" name="adjunto" class="form-control spgi-control">
                        <div class="spgi-help">Formatos: jpg, png, pdf, doc, xls. Máx: 8MB.</div>
                    </div>

                </div>
            </form>

        </div>
    </div>

</div>

@endsection