@extends('layouts.app')

@section('page_title', 'Bitácora: ' . $cliente->nombre)

@section('content')
<style>
  .client-detail-card {
    background: var(--bg-surface-glass); border: 1px solid var(--border-main);
    border-radius: 24px; box-shadow: var(--shadow-main); backdrop-filter: blur(20px); overflow: hidden;
  }
  .client-header { background: #0b1220; color: #fff; padding: 30px; }
  .client-body { padding: 40px; }
  
  .nav-tabs-premium { border-bottom: 2px solid var(--border-main); margin-bottom: 30px; }
  .nav-tabs-premium .nav-link {
    border: 0; background: transparent; color: var(--text-muted); font-weight: 700;
    padding: 12px 24px; border-bottom: 2px solid transparent; transition: all 0.3s ease;
  }
  .nav-tabs-premium .nav-link:hover { color: var(--text-main); }
  .nav-tabs-premium .nav-link.active {
    color: var(--spgi-primary); border-bottom: 2px solid var(--spgi-primary); background: transparent;
  }

  .timeline-admin { border-left: 2px solid var(--border-main); }
  .timeline-item-admin { position: relative; margin-bottom: 30px; }
  .timeline-marker {
    position: absolute; left: -25px; top: 4px; width: 12px; height: 12px;
    border-radius: 50%; background-color: var(--spgi-primary); border: 2px solid var(--bg-master);
  }

  .media-badge {
    padding: 4px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: 800; text-transform: uppercase;
  }
  .media-llamada { background: rgba(59, 130, 246, 0.15); color: #3b82f6; }
  .media-correo { background: rgba(16, 185, 129, 0.15); color: #10b981; }
  .media-reunion { background: rgba(245, 158, 11, 0.15); color: #f59e0b; }
  .media-whatsapp { background: rgba(34, 197, 94, 0.15); color: #22c55e; }
  .media-otro { background: rgba(107, 114, 128, 0.15); color: #6b7280; }
</style>

<div class="container py-4">
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <a href="{{ route('administracion.bitacora-clientes.index') }}" class="btn btn-light rounded-pill border">
            <i class="bi bi-arrow-left me-1"></i> Volver al listado
        </a>
        <div class="d-flex gap-2">
            <a href="{{ route('clientes.entorno.show', $cliente->id) }}" class="btn btn-outline-primary rounded-pill px-4" title="Ver entorno técnico">
                <i class="bi bi-gear-wide-connected me-1"></i> Directorio de Entorno (TI)
            </a>
        </div>
    </div>

    <div class="client-detail-card">
        <div class="client-header">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h1 class="display-6 fw-bold mb-1">{{ $cliente->nombre }}</h1>
                    <p class="mb-0 text-white-50"><i class="bi bi-person-vcard me-2"></i>RNC: {{ $cliente->rnc ?? 'No especificado' }} | Tel: {{ $cliente->telefono_principal ?? 'No especificado' }}</p>
                </div>
                <div class="bg-primary text-white rounded-pill px-4 py-2 fw-bold" style="font-size: 0.9rem;">
                    Área Administrativa
                </div>
            </div>
        </div>

        <div class="client-body">
            <ul class="nav nav-tabs nav-tabs-premium" id="bitacoraTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="docs-tab" data-bs-toggle="tab" data-bs-target="#docs-tab-pane" type="button" role="tab"><i class="bi bi-file-earmark-text me-2"></i>Contratos y Documentos</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="novedades-tab" data-bs-toggle="tab" data-bs-target="#novedades-tab-pane" type="button" role="tab"><i class="bi bi-journal-text me-2"></i>Novedades y Contactos</button>
                </li>
            </ul>

            <div class="tab-content" id="bitacoraTabsContent">
                <!-- TAPA CONTRATOS Y DOCUMENTACIÓN -->
                <div class="tab-pane fade show active" id="docs-tab-pane" role="tabpanel" tabindex="0">
                    <div class="row">
                        <div class="col-md-8">
                            <h4 class="fw-bold mb-4 text-white">Archivos del Cliente</h4>
                            
                            @if($documentos->count() > 0)
                                <div class="list-group list-group-flush rounded-4 overflow-hidden border border-secondary border-opacity-10">
                                    @foreach($documentos as $doc)
                                        @php
                                            $ext = pathinfo($doc->archivo_path, PATHINFO_EXTENSION);
                                            $icon = 'bi-file-earmark';
                                            if (in_array($ext, ['pdf'])) $icon = 'bi-file-earmark-pdf text-danger';
                                            elseif (in_array($ext, ['doc', 'docx'])) $icon = 'bi-file-earmark-word text-primary';
                                            elseif (in_array($ext, ['xls', 'xlsx'])) $icon = 'bi-file-earmark-excel text-success';
                                            elseif (in_array($ext, ['jpg', 'png', 'jpeg'])) $icon = 'bi-file-earmark-image text-info';
                                        @endphp
                                        <div class="list-group-item bg-transparent d-flex justify-content-between align-items-center py-3 border-secondary border-opacity-10">
                                            <div class="d-flex align-items-center gap-3">
                                                <i class="bi {{ $icon }} fs-3"></i>
                                                <div>
                                                    <h6 class="mb-0 fw-bold text-white">{{ $doc->nombre }}</h6>
                                                    <small class="text-muted">Subido el {{ $doc->created_at->format('d/m/Y h:i A') }}</small>
                                                </div>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('administracion.bitacora-clientes.documentos.download', $doc->id) }}" class="btn btn-sm btn-outline-success rounded-pill px-3">
                                                    <i class="bi bi-download me-1"></i> Descargar
                                                </a>
                                                <form action="{{ route('administracion.bitacora-clientes.documentos.destroy', [$cliente->id, $doc->id]) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar este documento?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center p-5 border border-dashed rounded-4 border-secondary border-opacity-20 text-muted">
                                    <i class="bi bi-file-earmark-excel fs-1 d-block mb-3 opacity-50"></i>
                                    No hay contratos ni documentos legales registrados aún.
                                </div>
                            @endif
                        </div>

                        <div class="col-md-4">
                            <div class="card border-0 rounded-4 p-4 shadow-sm" style="background: rgba(255,255,255,0.03); border: 1px solid var(--border-main) !important;">
                                <h5 class="fw-bold mb-3 text-white"><i class="bi bi-cloud-upload me-2 text-primary"></i>Subir Documento</h5>
                                
                                <form action="{{ route('administracion.bitacora-clientes.documentos.store', $cliente->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="nombre" class="form-label fw-bold small text-muted">Descripción del archivo</label>
                                        <input type="text" name="nombre" class="form-control" placeholder="Ej: Contrato de Soporte 2026" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="archivo" class="form-label fw-bold small text-muted">Seleccionar archivo</label>
                                        <input type="file" name="archivo" class="form-control" required>
                                        <small class="text-muted" style="font-size:0.75rem;">Máximo 10MB (PDF, Word, Excel, JPG, PNG)</small>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100 rounded-pill mt-2">
                                        <i class="bi bi-upload me-1"></i> Guardar archivo
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAPA NOVEDADES Y CONTACTOS -->
                <div class="tab-pane fade" id="novedades-tab-pane" role="tabpanel" tabindex="0">
                    <div class="row">
                        <div class="col-md-8">
                            <h4 class="fw-bold mb-4 text-white">Historial de Interacciones</h4>

                            @if($contactos->count() > 0)
                                <div class="timeline-admin ps-4">
                                    @foreach($contactos as $con)
                                        @php
                                            $mediaClass = 'media-otro';
                                            if (strtolower($con->medio) == 'llamada') $mediaClass = 'media-llamada';
                                            elseif (strtolower($con->medio) == 'correo') $mediaClass = 'media-correo';
                                            elseif (strtolower($con->medio) == 'reunión') $mediaClass = 'media-reunion';
                                            elseif (strtolower($con->medio) == 'whatsapp') $mediaClass = 'media-whatsapp';
                                        @endphp
                                        <div class="timeline-item-admin">
                                            <div class="timeline-marker"></div>
                                            <div class="card border-0 rounded-4 p-3 shadow-sm" style="background: rgba(255,255,255,0.02); border: 1px solid var(--border-main) !important;">
                                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <span class="media-badge {{ $mediaClass }}">{{ $con->medio }}</span>
                                                        <span class="text-white fw-bold small"><i class="bi bi-person-circle me-1 text-muted"></i>{{ $con->user->name ?? 'Usuario' }}</span>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-3">
                                                        <small class="text-muted"><i class="bi bi-clock me-1"></i>{{ $con->fecha->format('d/m/Y h:i A') }}</small>
                                                        <form action="{{ route('administracion.bitacora-clientes.contactos.destroy', [$cliente->id, $con->id]) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar esta interacción?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-link text-danger p-0" title="Eliminar registro"><i class="bi bi-trash"></i></button>
                                                        </form>
                                                    </div>
                                                </div>
                                                <div class="text-main" style="white-space: pre-wrap; font-size: 0.9rem;">{{ $con->detalle }}</div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center p-5 border border-dashed rounded-4 border-secondary border-opacity-20 text-muted">
                                    <i class="bi bi-chat-left-dots fs-1 d-block mb-3 opacity-50"></i>
                                    No se han documentado interacciones o contactos manuales aún.
                                </div>
                            @endif
                        </div>

                        <div class="col-md-4">
                            <div class="card border-0 rounded-4 p-4 shadow-sm" style="background: rgba(255,255,255,0.03); border: 1px solid var(--border-main) !important;">
                                <h5 class="fw-bold mb-3 text-white"><i class="bi bi-clipboard2-plus me-2 text-primary"></i>Registrar Interacción</h5>
                                
                                <form action="{{ route('administracion.bitacora-clientes.contactos.store', $cliente->id) }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="fecha" class="form-label fw-bold small text-muted">Fecha del contacto</label>
                                        <input type="datetime-local" name="fecha" class="form-control" value="{{ now()->format('Y-m-d\TH:i') }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="medio" class="form-label fw-bold small text-muted">Medio de contacto</label>
                                        <select name="medio" class="form-select" required>
                                            <option value="Llamada">Llamada telefónica</option>
                                            <option value="Correo">Correo electrónico</option>
                                            <option value="WhatsApp">Mensaje por WhatsApp</option>
                                            <option value="Reunión">Reunión presencial / Virtual</option>
                                            <option value="Otro">Otro</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="detalle" class="form-label fw-bold small text-muted">Detalles de la interacción</label>
                                        <textarea name="detalle" rows="4" class="form-control" placeholder="Describe los acuerdos, temas tratados o novedades..." required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100 rounded-pill mt-2">
                                        <i class="bi bi-check-lg me-1"></i> Registrar contacto
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
