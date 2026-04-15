@extends('layouts.app')

@section('page_title', 'Centro de Notificaciones')

@section('content')
<div class="container-fluid pb-5">
    <!-- Header Decorativo -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-primary text-white overflow-hidden" style="border-radius: 20px;">
                <div class="card-body p-4 position-relative">
                    <div class="d-flex align-items-center gap-4 position-relative" style="z-index: 2;">
                        <div class="bg-white bg-opacity-25 rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="bi bi-bell-fill fs-1"></i>
                        </div>
                        <div>
                            <h2 class="fw-bold mb-1">Tu Historial de Avisos</h2>
                            <p class="mb-0 text-white-50">Gestiona y revisa todas tus notificaciones del sistema.</p>
                        </div>
                    </div>
                    <!-- Decoración fondo -->
                    <i class="bi bi-megaphone position-absolute top-50 end-0 translate-middle-y me-4 opacity-25" style="font-size: 10rem;"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Acciones Globales -->
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center bg-white p-3 shadow-sm rounded-4 mx-3" style="width: auto;">
            <div class="d-flex align-items-center gap-3">
                <span class="badge bg-light text-dark border p-2 px-3 rounded-pill fw-normal">
                    <i class="bi bi-info-circle me-1"></i> Total: {{ $notificaciones->total() > 99 ? '99+' : $notificaciones->total() }}
                </span>

            </div>
            <div class="d-flex gap-2">
                <button type="button" onclick="confirmDeleteAll()" class="btn btn-outline-danger rounded-pill px-4">
                    <i class="bi bi-trash3 me-1"></i> Borrar Todo
                </button>
            </div>
        </div>
    </div>

    <!-- Lista de Notificaciones -->
    <div class="row">
        <div class="col-12">
            @if($notificaciones->isEmpty())
                <div class="card border-0 shadow-sm text-center py-5" style="border-radius: 20px;">
                    <div class="card-body">
                        <i class="bi bi-inbox text-muted" style="font-size: 4rem;"></i>
                        <h4 class="mt-3 text-muted">No tienes notificaciones</h4>
                        <p class="text-muted">Cuando recibas un aviso, aparecerá en este centro.</p>
                    </div>
                </div>
            @else
                <div class="list-group shadow-sm border-0" style="border-radius: 20px; overflow: hidden;">
                    @foreach($notificaciones as $n)
                        <div id="notif-row-{{ $n->id }}" class="list-group-item list-group-item-action border-0 border-bottom p-4 notification-row {{ $n->leido_at ? 'bg-light bg-opacity-50 opacity-75' : 'bg-white' }}">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-start gap-4 flex-grow-1" style="cursor: pointer;" onclick="handleHistoryClick('{{ $n->url }}', '{{ $n->id }}')">
                                    <div class="rounded-circle p-3 d-flex align-items-center justify-content-center {{ $n->leido_at ? 'bg-secondary bg-opacity-10 text-secondary' : 'bg-primary bg-opacity-10 text-primary' }}" style="width: 50px; height: 50px; flex-shrink: 0;">
                                        <i class="bi {{ $n->leido_at ? 'bi-envelope-open' : 'bi-envelope-fill' }} fs-4"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center gap-3 mb-2">
                                            <h6 class="fw-bold mb-0">{{ $n->sender ? $n->sender->name : 'Sistema de Gestión' }}</h6>
                                            <span class="text-muted small fw-normal">
                                                <i class="bi bi-clock me-1"></i>{{ $n->created_at->diffForHumans() }}
                                            </span>
                                            @if(!$n->leido_at)
                                                <span class="badge bg-success rounded-pill fw-normal px-2">Nuevo</span>
                                            @endif
                                        </div>
                                        <p id="msg-{{ $n->id }}" class="mb-0 text-dark overflow-hidden" style="max-height: 2.6em; line-height: 1.3; transition: max-height 0.4s ease;">
                                            {{ $n->mensaje }}
                                        </p>
                                    </div>
                                </div>
                                <div class="d-flex gap-2 ms-4">
                                    @if(!$n->leido_at)
                                        <button onclick="localMarkAsRead({{ $n->id }}, event)" class="btn btn-light rounded-circle shadow-sm" style="width: 40px; height: 40px;" title="Marcar como leída">
                                            <i class="bi bi-check-all text-primary"></i>
                                        </button>
                                    @endif
                                    <button onclick="localDelete({{ $n->id }}, event)" class="btn btn-light rounded-circle shadow-sm" style="width: 40px; height: 40px;" title="Eliminar">
                                        <i class="bi bi-trash text-danger"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="mt-4 px-3">
                    {{ $notificaciones->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .notification-row {
        transition: all 0.3s ease;
    }
    .notification-row:hover {
        background-color: #f8fafc !important;
        transform: scale(1.005);
    }
    .notification-row.fade-out {
        animation: rowFadeOut 0.4s forwards;
    }
    @keyframes rowFadeOut {
        to { opacity: 0; transform: translateX(30px); }
    }
</style>

@push('scripts')
<script>
    function handleHistoryClick(url, id) {
        if (url && url !== '' && url !== '#') {
            localMarkAsRead(id, null, url);
        } else {
            toggleExpand(id);
        }
    }

    function toggleExpand(id) {
        const p = document.getElementById('msg-' + id);
        if (p.style.maxHeight === 'none' || p.style.maxHeight === '') {
            p.style.maxHeight = '2000px'; 
        } else {
            p.style.maxHeight = '2.6em';
        }
    }

    function localMarkAsRead(id, event = null, redirectUrl = null) {
        if (event) event.stopPropagation();

        fetch(`{{ url('api/notificaciones') }}/${id}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        }).then(() => {
            if (redirectUrl) {
                window.location.href = redirectUrl;
            } else {
                location.reload(); 
            }
        });
    }

    function localDelete(id, event = null) {
        if (event) event.stopPropagation();
        const row = document.getElementById('notif-row-' + id);
        row.classList.add('fade-out');

        setTimeout(() => {
            fetch(`{{ url('api/notificaciones') }}/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            }).then(() => {
                location.reload();
            });
        }, 400);
    }
</script>
@endpush
@endsection
