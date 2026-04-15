@extends('layouts.app')

@section('page_title', 'Entorno: ' . $cliente->nombre)

@section('content')
<div class="container-fluid pb-5">

    <!-- Header / Resumen -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-dark text-white overflow-hidden" style="border-radius: 20px;">
                <div class="card-body p-4 position-relative">
                    <div class="d-flex align-items-center justify-content-between position-relative" style="z-index: 2;">
                        <div class="d-flex align-items-center gap-4">
                            <div class="bg-primary rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                                <i class="bi bi-gear-wide-connected fs-2"></i>
                            </div>
                            <div>
                                <h3 class="fw-bold mb-1">Directorio de Entorno</h3>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-primary px-3 rounded-pill">{{ $cliente->nombre }}</span>
                                    <span class="text-white-50 fs-7">RNC: {{ $cliente->rnc ?: 'N/A' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ $historicoUrl }}" class="btn btn-outline-light rounded-pill px-3">
                                <i class="bi bi-clock-history me-1"></i> Histórico Requerimientos
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navegación por Tabs -->
    <div class="row">
        <div class="col-12">
            <ul class="nav nav-pills mb-4 gap-2" id="entornoTabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active rounded-pill px-4" id="docs-tab" data-bs-toggle="tab" data-bs-target="#docs" type="button">
                        <i class="bi bi-file-earmark-lock me-1"></i> Documentos y Claves
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link rounded-pill px-4" id="equipos-tab" data-bs-toggle="tab" data-bs-target="#equipos" type="button">
                        <i class="bi bi-box-seam me-1"></i> Inventario de Equipos
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link rounded-pill px-4" id="anydesk-tab" data-bs-toggle="tab" data-bs-target="#anydesk" type="button">
                        <i class="bi bi-headset me-1"></i> AnyDesk List
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link rounded-pill px-4" id="bitacora-tab" data-bs-toggle="tab" data-bs-target="#bitacora" type="button">
                        <i class="bi bi-journal-text me-1"></i> Bitácora de Notas
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="entornoTabsContent">
                
                <!-- DOCUMENTOS Y CLAVES -->
                <div class="tab-pane fade show active" id="docs" role="tabpanel">
                    <div class="row g-4">
                        <div class="col-md-12">
                            <div class="card border-0 shadow-sm rounded-4">
                                <div class="card-header bg-white border-0 p-4 d-flex justify-content-between align-items-center">
                                    <h5 class="fw-bold mb-0">Listado de IP, Mapas y Credenciales</h5>
                                    <button class="btn btn-primary btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalAddDoc">
                                        <i class="bi bi-plus-lg me-1"></i> Agregar Registro
                                    </button>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table align-middle mb-0">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th class="ps-4">Tipo</th>
                                                    <th>Descripción / Nombre</th>
                                                    <th>Usuario / Clave</th>
                                                    <th>Adjunto / Link</th>
                                                    <th class="text-end pe-4">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($documentos as $doc)
                                                    <tr>
                                                        <td class="ps-4">
                                                            <span class="badge bg-secondary-subtle text-secondary border px-3 rounded-pill">{{ $doc->tipo }}</span>
                                                        </td>
                                                        <td>
                                                            <div class="fw-bold">{{ $doc->nombre }}</div>
                                                        </td>
                                                        <td>
                                                            @if($doc->usuario)
                                                                <div class="small"><strong>U:</strong> {{ $doc->usuario }}</div>
                                                                <div class="small d-flex align-items-center gap-2">
                                                                    <strong>C:</strong> 
                                                                    <span class="password-hidden" id="pass-{{ $doc->id }}">••••••••</span>
                                                                    <button class="btn btn-link btn-sm p-0" onclick="togglePassword('{{ $doc->id }}', '{{ $doc->clave_desencriptada }}')">
                                                                        <i class="bi bi-eye"></i>
                                                                    </button>
                                                                </div>
                                                            @else
                                                                <span class="text-muted small italic">N/A</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($doc->archivo_path)
                                                                <a href="{{ route('clientes.entorno.documento.download', ['cliente'=>$cliente->id, 'id'=>$doc->id]) }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                                                    <i class="bi bi-download me-1"></i> Descargar
                                                                </a>
                                                            @elseif($doc->url)
                                                                <a href="{{ $doc->url }}" target="_blank" class="btn btn-outline-info btn-sm rounded-pill px-3">
                                                                    <i class="bi bi-box-arrow-up-right me-1"></i> Abrir Link
                                                                </a>
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td class="text-end pe-4">
                                                            <form action="{{ route('clientes.entorno.documento.destroy', ['cliente' => $cliente->id, 'id' => $doc->id]) }}" method="POST">
                                                                @csrf @method('DELETE')
                                                                <button class="btn btn-outline-danger btn-sm rounded-circle" style="width: 32px; height: 32px;"><i class="bi bi-trash"></i></button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr><td colspan="5" class="p-4 text-center text-muted">No hay documentos o credenciales registradas.</td></tr>
                                                @endforelse
                                        </table>
                                    </div>
                                    <div class="p-3">
                                        {{ $documentos->appends(['page_equipos'=>$equipos->currentPage(), 'page_anydesk'=>$anydesks->currentPage(), 'page_bitacora'=>$bitacoras->currentPage()])->links() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- INVENTARIO DE EQUIPOS -->
                <div class="tab-pane fade" id="equipos" role="tabpanel">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-white border-0 p-4 d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold mb-0">Equipos Asignados (Configuración y Drivers)</h5>
                            <button class="btn btn-primary btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalAddEquipo">
                                <i class="bi bi-plus-lg me-1"></i> Asignar Equipo
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-4">Equipo</th>
                                            <th>Serie</th>
                                            <th>Configuración Específica</th>
                                            <th>Notas</th>
                                            <th class="text-end pe-4">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($equipos as $inv)
                                            <tr>
                                                <td class="ps-4">
                                                    <div class="fw-bold">{{ $inv->catalogo->nombre }}</div>
                                                    <div class="small text-muted">{{ $inv->catalogo->marca }} {{ $inv->catalogo->modelo }}</div>
                                                </td>
                                                <td><code class="text-primary">{{ $inv->serie ?: '-' }}</code></td>
                                                <td>
                                                    <small class="text-dark">{{ $inv->configuracion_especifica ?: 'Usa config. estándar' }}</small>
                                                </td>
                                                <td><small class="text-muted">{{ $inv->notas ?: '-' }}</small></td>
                                                <td class="text-end pe-4">
                                                    <form action="{{ route('clientes.entorno.equipo.destroy', ['cliente' => $cliente->id, 'id' => $inv->id]) }}" method="POST">
                                                        @csrf @method('DELETE')
                                                        <button class="btn btn-outline-danger btn-sm rounded-circle" style="width: 32px; height: 32px;"><i class="bi bi-trash"></i></button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="5" class="p-4 text-center text-muted">No hay equipos asignados a este cliente.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="p-3 border-top">
                                {{ $equipos->appends(['page_docs'=>$documentos->currentPage(), 'page_anydesk'=>$anydesks->currentPage(), 'page_bitacora'=>$bitacoras->currentPage()])->links() }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ANYDESK -->
                <div class="tab-pane fade" id="anydesk" role="tabpanel">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-white border-0 p-4 d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold mb-0">Listado de AnyDesk IDs</h5>
                            <button class="btn btn-primary btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalAddAnydesk">
                                <i class="bi bi-plus-lg me-1"></i> Agregar AnyDesk
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-4" style="width: 250px;">Host / Alias</th>
                                            <th style="width: 300px;">AnyDesk ID</th>
                                            <th>Notas de Acceso / Observaciones</th>
                                            <th class="text-end pe-4" style="width: 100px;">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($anydesks as $ad)
                                            <tr>
                                                <td class="ps-4">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="bg-info-subtle text-info rounded-circle d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                                                            <i class="bi bi-display small"></i>
                                                        </div>
                                                        <span class="text-dark fw-semibold small">{{ $ad->alias ?: 'Principal' }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-1">
                                                        <span class="font-monospace text-primary border rounded-2 px-2 py-1 bg-white small" style="letter-spacing: 1px;">
                                                            {{ $ad->anydesk_id }}
                                                        </span>
                                                        <button class="btn btn-link btn-sm p-1 text-secondary" 
                                                                onclick="copyToClipboard('{{ $ad->anydesk_id }}', this)"
                                                                title="Copiar ID">
                                                            <i class="bi bi-clipboard"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="text-muted small text-truncate" style="max-width: 500px;" title="{{ $ad->notas }}">
                                                        {{ $ad->notas ?: '-' }}
                                                    </div>
                                                </td>
                                                <td class="text-end pe-4">
                                                    <div class="d-flex justify-content-end gap-1">
                                                        <button class="btn btn-outline-warning btn-sm rounded-circle d-flex align-items-center justify-content-center" 
                                                                style="width: 30px; height: 30px;"
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#modalEditAnydesk{{ $ad->id }}">
                                                            <i class="bi bi-pencil-fill small"></i>
                                                        </button>
                                                        
                                                        <form action="{{ route('clientes.entorno.anydesk.destroy', ['cliente' => $cliente->id, 'id' => $ad->id]) }}" method="POST">
                                                            @csrf @method('DELETE')
                                                            <button class="btn btn-outline-danger btn-sm rounded-circle d-flex align-items-center justify-content-center" 
                                                                    style="width: 30px; height: 30px;"
                                                                    onclick="return confirm('¿Seguro que deseas eliminar este AnyDesk?')">
                                                                <i class="bi bi-trash-fill small"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- Modal Edit AnyDesk -->
                                            <div class="modal fade" id="modalEditAnydesk{{ $ad->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                                                        <form action="{{ route('clientes.entorno.anydesk.update', ['cliente' => $cliente->id, 'id' => $ad->id]) }}" method="POST">
                                                            @csrf @method('PUT')
                                                            <div class="modal-header bg-warning text-dark border-0">
                                                                <h5 class="modal-title fw-bold">Actualizar AnyDesk</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body p-4">
                                                                <div class="row g-3">
                                                                    <div class="col-md-6">
                                                                        <label class="form-label fw-bold small">AnyDesk ID</label>
                                                                        <input type="text" name="anydesk_id" class="form-control rounded-3" value="{{ $ad->anydesk_id }}" required>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label class="form-label fw-bold small">Alias / Hostname</label>
                                                                        <input type="text" name="alias" class="form-control rounded-3" value="{{ $ad->alias }}">
                                                                    </div>
                                                                    <div class="col-12">
                                                                        <label class="form-label fw-bold small">Notas</label>
                                                                        <textarea name="notas" class="form-control rounded-3" rows="2">{{ $ad->notas }}</textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer border-0 p-4">
                                                                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                                                                <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold">Actualizar</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <tr><td colspan="4" class="p-4 text-center text-muted">No hay AnyDesk registrados.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="p-3 border-top">
                                {{ $anydesks->appends(['page_docs'=>$documentos->currentPage(), 'page_equipos'=>$equipos->currentPage(), 'page_bitacora'=>$bitacoras->currentPage()])->links() }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- BITACORA -->
                <div class="tab-pane fade" id="bitacora" role="tabpanel">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-white border-0 p-4 d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold mb-1">Bitácora de Notas de Sistema</h5>
                            <button class="btn btn-primary btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalAddBitacora">
                                <i class="bi bi-plus-lg me-1"></i> Nueva Nota
                            </button>
                        </div>
                        <div class="card-body p-4">
                            <div class="timeline">
                                @forelse($bitacoras as $nota)
                                    <div class="mb-4 pb-3 border-bottom position-relative">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 30px; height: 30px; font-size: 0.7rem;">
                                                    {{ strtoupper(substr($nota->user->name, 0, 1)) }}
                                                </div>
                                                <span class="fw-bold small">{{ $nota->user->name }}</span>
                                            </div>
                                            <small class="text-muted">{{ $nota->created_at->format('d/m/Y H:i') }}</small>
                                        </div>
                                        <p class="mb-0 text-dark" style="white-space: pre-wrap;">{{ $nota->nota }}</p>
                                    </div>
                                @empty
                                    <div class="text-center py-5 text-muted">Aún no hay notas en la bitácora.</div>
                                @endforelse
                            </div>
                            <div class="mt-3">
                                {{ $bitacoras->appends(['page_docs'=>$documentos->currentPage(), 'page_equipos'=>$equipos->currentPage(), 'page_anydesk'=>$anydesks->currentPage()])->links() }}
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- MODALES -->

<!-- Modal Add Doc -->
<div class="modal fade" id="modalAddDoc" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <form action="{{ route('clientes.entorno.documento.store', $cliente->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-dark text-white border-0">
                    <h5 class="modal-title fw-bold">Agregar Registro / Documento</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Tipo</label>
                            <select name="tipo" class="form-select rounded-3" required>
                                <option value="IP">Listado de IP</option>
                                <option value="Mapa">Mapa Conceptual</option>
                                <option value="Credencial">Usuario / Clave</option>
                                <option value="Reporte">Informe / Reporte</option>
                                <option value="Driver">Driver de Equipo</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Nombre / Descripción</label>
                            <input type="text" name="nombre" class="form-control rounded-3" required placeholder="Ej: Router Principal">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Usuario (Opcional)</label>
                            <input type="text" name="usuario" class="form-control rounded-3">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Clave (Opcional)</label>
                            <input type="password" name="clave" class="form-control rounded-3">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small">Archivo (Si aplica)</label>
                            <input type="file" name="archivo" class="form-control rounded-3">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small">URL / Link (Si aplica)</label>
                            <input type="url" name="url" class="form-control rounded-3" placeholder="https://...">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Add AnyDesk -->
<div class="modal fade" id="modalAddAnydesk" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <form action="{{ route('clientes.entorno.anydesk.store', $cliente->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-info text-white border-0">
                    <h5 class="modal-title fw-bold">Agregar ID AnyDesk</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">AnyDesk ID</label>
                            <input type="text" name="anydesk_id" class="form-control rounded-3" required placeholder="Ej: 123 456 789 o usuario@ad">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Alias / Hostname</label>
                            <input type="text" name="alias" class="form-control rounded-3" placeholder="Ej: PC Recepción">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small">Notas (Opcional)</label>
                            <textarea name="notas" class="form-control rounded-3" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-info text-white rounded-pill px-4 fw-bold">Agregar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Add Bitacora -->
<div class="modal fade" id="modalAddBitacora" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <form action="{{ route('clientes.entorno.bitacora.store', $cliente->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title fw-bold">Nueva Nota de Sistema</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <textarea name="nota" class="form-control rounded-4" rows="6" placeholder="Escribe aquí las observaciones técnicas..." required></textarea>
                </div>
                <div class="modal-footer border-0 p-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Publicar Nota</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Add Equipo -->
<div class="modal fade" id="modalAddEquipo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <form action="{{ route('clientes.entorno.equipo.store', $cliente->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-dark text-white border-0">
                    <h5 class="modal-title fw-bold">Asignar Equipo al Inventario</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold small">Seleccionar Equipo del Catálogo</label>
                            <select name="cat_equipo_id" class="form-select rounded-3" required>
                                <option value="">-- Buscar equipo --</option>
                                @foreach($catalogoEquipos as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->nombre }} ({{ $cat->marca }} - {{ $cat->modelo }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Número de Serie (S/N)</label>
                            <input type="text" name="serie" class="form-control rounded-3" placeholder="Ej: ABC123XYZ">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold small">Configuración Específica (IP, Puertos, etc.)</label>
                            <textarea name="configuracion_especifica" class="form-control rounded-3" rows="3" placeholder="Si es diferente a la estándar del catálogo..."></textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold small">Notas Adicionales</label>
                            <textarea name="notas" class="form-control rounded-3" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Asignar Equipo</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function togglePassword(id, realPassword) {
        const span = document.getElementById('pass-' + id);
        if (span.classList.contains('password-hidden')) {
            span.textContent = realPassword;
            span.classList.remove('password-hidden');
            span.classList.add('text-primary', 'fw-bold');
        } else {
            span.textContent = '••••••••';
            span.classList.add('password-hidden');
            span.classList.remove('text-primary', 'fw-bold');
        }
    }

    function copyToClipboard(text, btn) {
        navigator.clipboard.writeText(text).then(() => {
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<i class="bi bi-check-lg"></i>';
            btn.classList.replace('btn-outline-primary', 'btn-success text-white');
            setTimeout(() => {
                btn.innerHTML = originalHtml;
                btn.classList.replace('btn-success', 'btn-outline-primary');
                btn.classList.remove('text-white');
            }, 2000);
        }).catch(err => {
            console.error('Error al copiar: ', err);
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        // 1. Activar pestaña según el hash de la URL al cargar
        const hash = window.location.hash;
        if (hash) {
            const triggerEl = document.querySelector('button[data-bs-target="' + hash + '"]');
            if (triggerEl) {
                const tab = new bootstrap.Tab(triggerEl);
                tab.show();
            }
        }

        // 2. Actualizar el hash de la URL cuando el usuario cambia de pestaña manualmente
        const tabBtns = document.querySelectorAll('button[data-bs-toggle="tab"]');
        tabBtns.forEach(btn => {
            btn.addEventListener('shown.bs.tab', function(event) {
                const targetHash = event.target.getAttribute('data-bs-target');
                history.replaceState(null, null, targetHash); // Cambia el hash sin saltar la página
            });
        });
    });
</script>
<style>
    .nav-pills .nav-link {
        color: #64748b;
        background-color: #f1f5f9;
        font-weight: 600;
        transition: all 0.2s;
        border: 1px solid transparent;
    }
    .nav-pills .nav-link.active {
        background-color: #0f172a;
        color: #fff;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.2);
    }
    .nav-pills .nav-link:hover:not(.active) {
        background-color: #e2e8f0;
        border-color: #cbd5e1;
    }
</style>
@endpush

@endsection
