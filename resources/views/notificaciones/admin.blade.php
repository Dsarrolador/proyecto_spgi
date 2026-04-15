@extends('layouts.app')

@section('page_title', 'Centro de Notificaciones')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0" style="border-radius: 20px;">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h4 class="fw-bold mb-0">Enviar Aviso del Sistema</h4>
                    <p class="text-muted small">Envía mensajes individuales o globales a los colaboradores.</p>
                </div>

                <div class="card-body p-4">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('notificaciones.send') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase">Destinatarios</label>
                            
                            <div class="card border p-3" style="max-height: 300px; overflow-y: auto; border-radius: 12px;">
                                <div class="form-check mb-3 pb-2 border-bottom">
                                    <input class="form-check-input" type="checkbox" id="checkAll" value="all" name="destinatario_global">
                                    <label class="form-check-label fw-bold text-primary" for="checkAll">
                                        📢 SELECCIONAR PROXIMOS TODOS (GLOBAL)
                                    </label>
                                </div>

                                <div class="row g-2" id="userCheckboxes">
                                    @foreach($usuarios as $u)
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input user-checkbox" type="checkbox" name="destinatarios[]" value="{{ $u->id }}" id="user_{{ $u->id }}">
                                                <label class="form-check-label small" for="user_{{ $u->id }}">
                                                    {{ $u->name }} <span class="text-muted" style="font-size: 0.75rem;">({{ optional($u->role)->nombre ?? 'Sin Rol' }})</span>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="form-text mt-2">Selecciona uno, varios o marca la opción global para todos.</div>
                        </div>

                        <script>
                            document.getElementById('checkAll').addEventListener('change', function() {
                                const checkboxes = document.querySelectorAll('.user-checkbox');
                                checkboxes.forEach(cb => {
                                    cb.checked = this.checked;
                                    cb.disabled = this.checked; // Si es global, desactivamos los individuales para evitar confusión
                                });
                            });
                        </script>

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase">Mensaje</label>
                            <textarea name="mensaje" class="form-control" rows="4" placeholder="Escribe el mensaje aquí..." required maxlength="500"></textarea>
                            <div class="form-text">Máximo 500 caracteres.</div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary py-2 fw-bold" style="border-radius: 12px;">
                                <i class="bi bi-send me-2"></i> Enviar Notificación
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
