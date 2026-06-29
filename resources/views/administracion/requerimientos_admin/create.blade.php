@extends('layouts.app')

@section('page_title', 'Nuevo Requerimiento Administrativo')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4 bg-surface-glass" style="border: 1px solid var(--border-main) !important; backdrop-filter: blur(20px);">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-2">
                    <h3 class="fw-bold mb-0 text-gradient"><i class="bi bi-folder-plus me-2 text-primary"></i>Nuevo Requerimiento</h3>
                    <p class="text-muted small mb-0">Completa los datos para registrar la tarea administrativa.</p>
                </div>
                
                <div class="card-body p-4">
                    <form action="{{ route('requerimientos-administrativos.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="titulo" class="form-label fw-bold small text-muted">Título / Asunto</label>
                            <input type="text" name="titulo" id="titulo" class="form-control" placeholder="Ej: Pago de impuestos, Envío de cotización final" required>
                        </div>

                        <div class="mb-3">
                            <label for="descripcion" class="form-label fw-bold small text-muted">Descripción detallada</label>
                            <textarea name="descripcion" id="descripcion" rows="4" class="form-control" placeholder="Explica detalladamente la labor administrativa a realizar..."></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="prioridad" class="form-label fw-bold small text-muted">Prioridad</label>
                                <select name="prioridad" id="prioridad" class="form-select">
                                    <option value="Baja">Baja</option>
                                    <option value="Media" selected>Media</option>
                                    <option value="Alta">Alta</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="estado" class="form-label fw-bold small text-muted">Estado Inicial</label>
                                <select name="estado" id="estado" class="form-select">
                                    <option value="Pendiente" selected>Pendiente</option>
                                    <option value="En Proceso">En Proceso</option>
                                    <option value="Completado">Completado</option>
                                    <option value="Cancelado">Cancelado</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="asignado_user_id" class="form-label fw-bold small text-muted">Asignar a</label>
                                <select name="asignado_user_id" id="asignado_user_id" class="form-select">
                                    <option value="">-- Sin asignar --</option>
                                    @foreach($usuarios as $u)
                                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="fecha_limite" class="form-label fw-bold small text-muted">Fecha Límite</label>
                                <input type="date" name="fecha_limite" id="fecha_limite" class="form-control">
                            </div>
                        </div>

                        <!-- RECURRENCIA -->
                        <div class="card border-0 mb-3" style="border-radius: 12px; background: rgba(255,255,255,0.03); border: 1px solid var(--border-main) !important;">
                            <div class="card-body">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="es_recurrente" id="es_recurrente" value="1">
                                    <label class="form-check-label fw-bold text-white" for="es_recurrente">
                                        <i class="bi bi-arrow-repeat me-1 text-success"></i> ¿Es un requerimiento recurrente?
                                    </label>
                                </div>
                                
                                <div id="freq_container" class="mt-3 d-none">
                                    <div class="mb-3">
                                        <label for="frecuencia" class="form-label small fw-bold text-muted">Frecuencia de repetición</label>
                                        <select name="frecuencia" id="frecuencia" class="form-select">
                                            <option value="Diario">Diario</option>
                                            <option value="Semanal">Semanal</option>
                                            <option value="Quincenal">Quincenal</option>
                                            <option value="Mensual" selected>Mensual</option>
                                            <option value="Semestral">Semestral</option>
                                            <option value="Anual">Anual</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="fecha_inicio_recurrencia" class="form-label small fw-bold text-muted">Fecha de Inicio de Recurrencia</label>
                                        <input type="date" name="fecha_inicio_recurrencia" id="fecha_inicio_recurrencia" class="form-control" value="{{ now()->format('Y-m-d') }}">
                                        <small class="text-muted d-block mt-1">El primer ciclo comenzará a partir de esta fecha.</small>
                                    </div>

                                    <small class="text-muted d-block mt-1">El sistema generará una copia de esta tarea administrativa automáticamente al cumplirse el plazo.</small>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('requerimientos-administrativos.index') }}" class="btn btn-outline-secondary rounded-pill px-4">Cancelar</a>
                            <button type="submit" class="btn btn-primary rounded-pill px-4">Guardar Requerimiento</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const switchRecurrente = document.getElementById('es_recurrente');
    const freqContainer   = document.getElementById('freq_container');

    if (switchRecurrente) {
        switchRecurrente.addEventListener('change', function() {
            if (this.checked) {
                freqContainer.classList.remove('d-none');
            } else {
                freqContainer.classList.add('d-none');
            }
        });
    }
});
</script>
@endsection
