@extends('layouts.cliente')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <h5 class="fw-bold mb-4">Novedades y Seguimientos</h5>
            
            <div class="timeline">
                @foreach($novedades as $nov)
                <div class="mb-5 position-relative ps-4" style="border-left: 2px solid #e9ecef;">
                    <div class="position-absolute start-0 top-0 translate-middle-x bg-primary rounded-circle" style="width: 12px; height: 12px; margin-left: -1px; margin-top: 5px;"></div>
                    
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <span class="badge bg-light text-dark border mb-2">{{ $nov->created_at->timezone('America/Santo_Domingo')->format('d M, Y h:i A') }}</span>
                            <h6 class="fw-bold mb-1">Requerimiento #{{ $nov->requerimiento_id }}</h6>
                        </div>
                        @if($nov->adjunto)
                        <a href="{{ asset('storage/' . $nov->adjunto) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                            <i class="bi bi-paperclip me-1"></i> Ver Adjunto
                        </a>
                        @endif
                    </div>
                    
                    <div class="p-3 bg-light rounded-3 shadow-sm" style="font-size: 0.95rem; white-space: pre-wrap;">{{ $nov->novedad }}</div>
                    
                    <div class="mt-2 small text-muted">
                        <i class="bi bi-person me-1"></i> Actualizado por: {{ $nov->user->name ?? 'Sistema' }}
                    </div>
                </div>
                @endforeach

                @if($novedades->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-journal-x fs-1 text-muted d-block mb-3"></i>
                    <p class="text-muted">No hay novedades registradas para tus requerimientos aún.</p>
                </div>
                @endif
            </div>

            <div class="mt-4">
                {{ $novedades->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
