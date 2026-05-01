@extends('layouts.cliente')

@section('content')
<div class="mb-4">
    <a href="{{ route('cliente.dashboard') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
        <i class="bi bi-arrow-left me-1"></i> Volver al listado
    </a>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
            <h5 class="fw-bold mb-3">Detalle del Requerimiento #{{ $requerimiento->id }}</h5>
            <hr>
            <div class="mb-3">
                <label class="small text-muted fw-bold text-uppercase">Descripción del Requerimiento</label>
                <div class="p-3 bg-light rounded-3 mt-1" style="font-size: 0.9rem; border-left: 4px solid #0d6efd;">
                    {{ $requerimiento->texto_imagen }}
                </div>
            </div>
            @if($requerimiento->foto)
            <div class="mb-3">
                <label class="small text-muted fw-bold text-uppercase">Evidencia Adjunta</label>
                <div class="mt-2 text-center">
                    <img src="{{ asset('storage/' . $requerimiento->foto) }}" class="img-fluid rounded-3 shadow-sm border" style="max-height: 200px;">
                </div>
            </div>
            @endif
            <div class="mb-3">
                <label class="small text-muted fw-bold text-uppercase">Tipo de Soporte</label>
                <div class="fw-bold">{{ $requerimiento->tipoSoporte->nombre ?? 'General' }}</div>
            </div>
            <div class="mb-3">
                <label class="small text-muted fw-bold text-uppercase">Estado Actual</label>
                <div>
                    <span class="badge" style="background-color: {{ $requerimiento->estadoRequerimiento->color ?? '#6c757d' }}; color: #fff; font-size: 0.9rem; padding: 0.5rem 1rem; border: 1px solid rgba(0,0,0,0.1);">
                        {{ $requerimiento->estadoRequerimiento->nombre ?? 'Pendiente' }}
                    </span>
                </div>
            </div>
            <div class="mb-3">
                <label class="small text-muted fw-bold text-uppercase">Fecha de Creación</label>
                <div class="fw-bold">{{ $requerimiento->created_at->timezone('America/Santo_Domingo')->format('d/m/Y h:i A') }}</div>
            </div>
            <div class="mb-3">
                <label class="small text-muted fw-bold text-uppercase">Encargado</label>
                <div class="fw-bold text-primary">{{ $requerimiento->asignado->name ?? 'Por asignar' }}</div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
            <h5 class="fw-bold mb-4"><i class="bi bi-chat-dots me-2"></i> Enviar Actualización</h5>
            
            @if(session('success'))
                <div class="alert alert-success small py-2">{{ session('success') }}</div>
            @endif

            <form action="{{ route('cliente.requerimientos.novedad.store', $requerimiento->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <textarea name="novedad" class="form-control border-0 bg-light" rows="3" placeholder="Escribe tu mensaje o duda aquí..." style="border-radius: 12px; resize: none;" required></textarea>
                </div>
                <div class="mb-3">
                    <label class="small text-muted fw-bold mb-1">ADJUNTAR ARCHIVO (OPCIONAL)</label>
                    <input type="file" name="adjunto" class="form-control form-control-sm rounded-pill">
                </div>
                <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold">
                    <i class="bi bi-send me-2"></i> Enviar Mensaje
                </button>
            </form>
        </div>

        <div class="card border-0 shadow-sm rounded-4 p-4">
            <h5 class="fw-bold mb-4"><i class="bi bi-journal-text me-2"></i> Historial de Seguimiento</h5>
            
            <div class="timeline">
                @foreach($requerimiento->novedades as $nov)
                <div class="mb-4 position-relative ps-4" style="border-left: 2px solid #e9ecef;">
                    <div class="position-absolute start-0 top-0 translate-middle-x bg-primary rounded-circle" style="width: 12px; height: 12px; margin-left: -1px; margin-top: 5px;"></div>
                    
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <span class="small text-muted">{{ $nov->created_at->timezone('America/Santo_Domingo')->format('d M, Y h:i A') }}</span>
                        @if($nov->adjunto)
                        <a href="{{ asset('storage/' . $nov->adjunto) }}" download class="badge bg-light text-primary border text-decoration-none px-3 py-2 rounded-pill hover-shadow">
                            <i class="bi bi-download me-1"></i> Descargar Adjunto
                        </a>
                        @endif
                    </div>
                    
                    <div class="p-3 bg-light rounded-3 shadow-sm border-start border-primary border-4" style="font-size: 0.9rem; white-space: pre-wrap;">{{ $nov->novedad }}</div>
                    
                    <div class="mt-2 small text-muted">
                        <i class="bi bi-person-circle me-1"></i> {{ $nov->user->name ?? 'Tú (Cliente)' }}
                    </div>
                </div>
                @endforeach

                @if($requerimiento->novedades->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-chat-dots fs-1 d-block mb-2 opacity-25"></i>
                    Aún no hay novedades oficiales registradas para este requerimiento.
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
