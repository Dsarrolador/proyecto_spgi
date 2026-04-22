@extends('layouts.app')

@section('page_title', 'Centro de Notificaciones')

@section('content')
<style>
    .spgi-bg{ padding: 12px 0 24px 0; }
    .spgi-title{ font-weight: 900; font-size: 1.8rem; color: var(--text-main); letter-spacing: -1px; margin:0; }
    .spgi-subtitle{ color: var(--text-muted); font-size: 1rem; margin-top: 4px; }

    .spgi-header-card{
        background: var(--bg-surface-glass); border: 1px solid var(--border-main);
        border-radius: 24px; box-shadow: var(--shadow-main); backdrop-filter: blur(24px);
        padding: 40px; margin-bottom: 24px; position: relative; overflow: hidden;
    }
    .header-icon-box{
        width: 80px; height: 80px; background: rgba(var(--spgi-primary), 0.1);
        border-radius: 22px; display: flex; align-items: center; justify-content: center;
        border: 1px solid var(--border-main); color: var(--spgi-primary);
    }

    .spgi-toolbar{
        background: var(--bg-surface-glass); border: 1px solid var(--border-main);
        border-radius: 20px; padding: 16px 24px; margin-bottom: 24px;
        display: flex; justify-content: space-between; align-items: center; gap: 16px;
    }

    .spgi-notif-list{
        background: var(--bg-surface-glass); border: 1px solid var(--border-main);
        border-radius: 24px; overflow: hidden; backdrop-filter: blur(16px);
    }
    .notif-item{
        padding: 24px 32px; border-bottom: 1px solid var(--border-main);
        transition: all 0.3s ease; display: flex; align-items: center; justify-content: space-between;
    }
    .notif-item:last-child{ border-bottom: 0; }
    .notif-item:hover{ background: rgba(var(--spgi-primary), 0.05); transform: scale(1.002); }
    .notif-item.read{ opacity: 0.7; filter: grayscale(0.5); }
    
    .notif-icon-box{
        width: 52px; height: 52px; border-radius: 14px; display: flex;
        align-items: center; justify-content: center; flex-shrink: 0;
        background: rgba(var(--text-main), 0.05); border: 1px solid var(--border-main);
        color: var(--text-main); font-size: 1.35rem; transition: all 0.3s ease;
    }
    .unread .notif-icon-box{ background: rgba(var(--spgi-primary), 0.1); border-color: var(--spgi-primary); color: var(--spgi-primary); }

    .notif-actions .btn{
        width: 44px; height: 44px; border-radius: 12px; display: inline-flex;
        align-items: center; justify-content: center; background: var(--bg-surface);
        border: 1px solid var(--border-main); color: var(--text-main); transition: all 0.2s ease;
    }
    .notif-actions .btn:hover{ background: rgba(var(--spgi-primary), 0.1); border-color: var(--spgi-primary); transform: translateY(-2px); }
    .notif-actions .btn-delete:hover{ background: rgba(239, 68, 68, 0.1); border-color: #ef4444; color: #ef4444; }

    @media (max-width: 768px){ .spgi-toolbar{ flex-direction: column; align-items: stretch; } }
</style>

<div class="spgi-bg">
    <div class="container">
        
        <div class="spgi-header-card">
            <div class="d-flex align-items-center gap-4 position-relative" style="z-index: 2;">
                <div class="header-icon-box">
                    <i class="bi bi-bell-fill fs-1"></i>
                </div>
                <div>
                    <h2 class="spgi-title">Centro de Notificaciones</h2>
                    <p class="spgi-subtitle">Gestiona y revisa todas tus alertas del sistema.</p>
                </div>
            </div>
            <i class="bi bi-megaphone position-absolute top-50 end-0 translate-middle-y me-4 opacity-10" style="font-size: 10rem; color: var(--text-main);"></i>
        </div>

        <div class="spgi-toolbar">
            <span class="badge" style="background: rgba(var(--spgi-primary), 0.1); color: var(--spgi-primary); border: 1px solid var(--border-main); padding: 10px 18px; border-radius: 12px; font-weight: 800;">
                <i class="bi bi-info-circle me-1"></i> TOTAL: {{ $notificaciones->total() }}
            </span>
            <button type="button" onclick="confirmDeleteAll()" class="btn btn-outline-danger" style="border-radius: 12px; font-weight: 800; padding: 10px 24px;">
                <i class="bi bi-trash3 me-1"></i> BORRAR TODO
            </button>
        </div>

        <div class="row">
            <div class="col-12">
                @if($notificaciones->isEmpty())
                    <div class="spgi-card text-center py-5">
                        <div class="card-body">
                            <i class="bi bi-inbox text-muted" style="font-size: 4rem;"></i>
                            <h4 class="mt-3 spgi-title opacity-50">No tienes notificaciones</h4>
                            <p class="spgi-subtitle">Cuando recibas un aviso, aparecerá aquí.</p>
                        </div>
                    </div>
                @else
                    <div class="spgi-notif-list">
                        @foreach($notificaciones as $n)
                            <div id="notif-row-{{ $n->id }}" class="notif-item {{ $n->leido_at ? 'read' : 'unread' }}">
                                <div class="d-flex align-items-start gap-4 flex-grow-1" style="cursor: pointer;" onclick="handleHistoryClick('{{ $n->url }}', '{{ $n->id }}')">
                                    <div class="notif-icon-box">
                                        <i class="bi {{ $n->leido_at ? 'bi-envelope-open' : 'bi-envelope-fill' }}"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center gap-3 mb-2">
                                            <h6 class="fw-bold mb-0" style="color: var(--text-main);">{{ $n->sender ? $n->sender->name : 'Sistema de Gestión' }}</h6>
                                            <span class="text-muted small">
                                                <i class="bi bi-clock me-1"></i>{{ $n->created_at->diffForHumans() }}
                                            </span>
                                            @if(!$n->leido_at)
                                                <span class="badge bg-success rounded-pill px-2">NUEVO</span>
                                            @endif
                                        </div>
                                        <p id="msg-{{ $n->id }}" class="mb-0" style="max-height: 2.6em; line-height: 1.4; overflow: hidden; transition: max-height 0.4s ease; color: var(--text-muted);">
                                            {{ $n->mensaje }}
                                        </p>
                                    </div>
                                </div>
                                <div class="notif-actions d-flex gap-2 ms-4">
                                    @if(!$n->leido_at)
                                        <button onclick="localMarkAsRead({{ $n->id }}, event)" class="btn" title="Marcar como leída">
                                            <i class="bi bi-check-all text-primary"></i>
                                        </button>
                                    @endif
                                    <button onclick="localDelete({{ $n->id }}, event)" class="btn btn-delete" title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="mt-4">
                        {{ $notificaciones->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

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
