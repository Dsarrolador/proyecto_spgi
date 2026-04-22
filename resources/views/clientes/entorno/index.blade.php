@extends('layouts.app')

@section('page_title', 'Entorno: ' . $cliente->nombre)

@section('content')
@section('content')
<style>
  .spgi-bg{ padding: 24px 0; }
  .spgi-title{ font-weight: 900; font-size: 1.8rem; color: var(--text-main); letter-spacing: -1px; margin:0; }
  .spgi-subtitle{ color: var(--text-muted); font-size: 1rem; margin-top: 4px; }
  
  .spgi-header-card{
    background: var(--bg-surface-glass); border: 1px solid var(--border-main);
    border-radius: 24px; box-shadow: var(--shadow-main); backdrop-filter: blur(24px);
    padding: 32px; margin-bottom: 24px;
  }
  .header-icon-box{
    width: 64px; height: 64px; background: rgba(var(--spgi-primary), 0.1);
    border-radius: 18px; display: flex; align-items: center; justify-content: center;
    border: 1px solid var(--border-main); color: var(--spgi-primary);
  }

  .nav-pills .nav-link{
    background: var(--bg-surface); color: var(--text-main); border: 1px solid var(--border-main);
    padding: 12px 24px; border-radius: 14px; font-weight: 700; transition: all 0.3s ease;
  }
  .nav-pills .nav-link:hover{ background: rgba(var(--spgi-primary), 0.05); }
  .nav-pills .nav-link.active{ background: var(--spgi-primary); color: #fff; border-color: var(--spgi-primary); box-shadow: 0 10px 20px var(--spgi-primary-glow); }

  .spgi-card{
    background: var(--bg-surface-glass); border: 1px solid var(--border-main);
    border-radius: 22px; box-shadow: var(--shadow-main); backdrop-filter: blur(16px);
    overflow: hidden;
  }
  .card-header-spgi{
    padding: 24px 32px; border-bottom: 1px solid var(--border-main);
    display: flex; justify-content: space-between; align-items: center;
  }
  .card-header-spgi h5{ font-weight: 800; color: var(--text-main); margin: 0; }

  .table-spgi{ margin: 0; }
  .table-spgi thead th{
    background: #0b1220; color:#fff; border-color: rgba(255,255,255,0.08) !important;
    font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; padding: 16px;
  }
  .table-spgi tbody td{ border-color: var(--border-main) !important; color: var(--text-main); padding: 16px; }
  .table-spgi tbody tr:hover{ background: rgba(var(--spgi-primary), 0.05); }

  .btn-spgi{
    background: linear-gradient(135deg, var(--spgi-primary), #2563eb);
    border: 0; color: #fff !important; min-height:44px; border-radius:12px; padding:0 20px;
    box-shadow: 0 10px 25px var(--spgi-primary-glow); font-weight:700;
  }
  .btn-spgi:hover{ filter: brightness(1.1); transform: translateY(-1px); }

  /* Modal Styling */
  .modal-content{ 
    background: var(--bg-surface-glass); border: 1px solid var(--border-main);
    backdrop-filter: blur(20px); border-radius: 24px; color: var(--text-main);
  }
  .modal-header{ border-bottom: 1px solid var(--border-main); padding: 24px; }
  .modal-footer{ border-top: 1px solid var(--border-main); padding: 20px 24px; }
  
  .form-label{ font-weight: 800; color: var(--text-muted); font-size: .75rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; }
  .form-control, .form-select{
    background: rgba(var(--text-main), 0.02) !important; color: var(--text-main) !important;
    border-radius:12px !important; border:1px solid var(--border-main) !important;
    padding:.75rem 1rem !important; box-shadow: none !important;
  }
</style>

<div class="spgi-bg">
    <div class="container">

        <!-- Header / Resumen -->
        <div class="spgi-header-card">
            <div class="d-flex align-items-center justify-content-between position-relative" style="z-index: 2;">
                <div class="d-flex align-items-center gap-4">
                    <div class="header-icon-box">
                        <i class="bi bi-gear-wide-connected fs-2"></i>
                    </div>
                    <div>
                        <h3 class="spgi-title">Directorio de Entorno</h3>
                        <div class="d-flex align-items-center gap-2 mt-1">
                            <span class="badge" style="background: rgba(var(--spgi-primary), 0.1); color: var(--spgi-primary); border: 1px solid var(--border-main); padding: 6px 16px; border-radius: 10px; font-weight: 800;">{{ $cliente->nombre }}</span>
                            <span class="text-muted small">RNC: {{ $cliente->rnc ?: 'N/A' }}</span>
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ $historicoUrl }}" class="btn btn-outline-spgi" style="border-radius: 12px; font-weight: 700; border: 1px solid var(--border-main); color: var(--text-main); padding: 10px 20px;">
                        <i class="bi bi-clock-history me-1"></i> Histórico
                    </a>
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
                            <div class="spgi-card">
                                <div class="card-header-spgi">
                                    <h5>Listado de IP, Mapas y Credenciales</h5>
                                    <button class="btn btn-spgi" data-bs-toggle="modal" data-bs-target="#modalAddDoc">
                                        <i class="bi bi-plus-lg me-1"></i> Agregar Registro
                                    </button>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-spgi align-middle">
                                            <thead>
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
                                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-3 rounded-pill">{{ $doc->tipo }}</span>
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
                                                                <a href="{{ route('clientes.entorno.documento.download', ['cliente'=>$cliente->id, 'id'=>$doc->id]) }}" class="btn btn-outline-spgi btn-sm rounded-pill px-3" style="height:34px; padding:0 16px;">
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
                                                                <button class="btn btn-outline-danger btn-sm rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 32px; height: 32px;"><i class="bi bi-trash"></i></button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr><td colspan="5" class="p-4 text-center text-muted">No hay documentos o credenciales registradas.</td></tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="p-3 border-top border-secondary border-opacity-10">
                                        {{ $documentos->appends(['page_equipos'=>$equipos->currentPage(), 'page_anydesk'=>$anydesks->currentPage(), 'page_bitacora'=>$bitacoras->currentPage()])->fragment('docs')->links() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- INVENTARIO DE EQUIPOS -->
                <div class="tab-pane fade" id="equipos" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold m-0"><i class="bi bi-pc-display me-2 text-primary"></i>Inventario de Estaciones</h5>
                        <button class="btn btn-spgi" data-bs-toggle="modal" data-bs-target="#modalAddEquipo" onclick="resetWizard()">
                            <i class="bi bi-plus-lg me-1"></i> Registrar Equipo Base
                        </button>
                    </div>

                    <div class="row g-4">
                        @forelse($equipos as $pc)
                            <div class="col-12">
                                <div class="spgi-card" style="border-left: 5px solid var(--spgi-primary);">
                                    <div class="card-header-spgi py-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; color: var(--spgi-primary);">
                                                <i class="bi bi-cpu fs-4"></i>
                                            </div>
                                            <div>
                                                <h6 class="m-0 fw-bold">{{ $pc->alias ?: $pc->catalogo->nombre }}</h6>
                                                <small class="text-muted">{{ $pc->catalogo->marca }} {{ $pc->catalogo->modelo }} | S/N: <strong>{{ $pc->serie ?: 'N/A' }}</strong></small>
                                            </div>
                                        </div>
                                        <div class="d-flex gap-2 align-items-center">
                                            <button class="btn btn-primary btn-sm rounded-circle shadow-sm me-2" 
                                                    title="Agregar componente o recurso a este equipo"
                                                    onclick="openWizardWithParent('{{ $pc->id }}', '{{ $pc->alias ?: $pc->catalogo->nombre }}')">
                                                <i class="bi bi-plus-lg"></i>
                                            </button>
                                            <div class="vr mx-2 opacity-10"></div>
                                            @if($pc->wikiDocument)
                                                <a href="{{ route('wiki.download', $pc->wikiDocument->id) }}" class="btn btn-sm btn-outline-info rounded-pill px-3" title="Instalar Sistema">
                                                    <i class="bi bi-download me-1"></i> Sistema
                                                </a>
                                            @endif

                                            @php
                                                $driverId = $pc->driver_id ?: ($pc->catalogo->driver_doc_id ?? null);
                                                $driverLabel = $pc->driver_id ? ($pc->driver_nombre ?: 'Driver Específico') : 'Driver Base';
                                            @endphp

                                            @if($driverId)
                                                <a href="{{ route('wiki.download', $driverId) }}" class="btn btn-sm btn-outline-success rounded-pill px-2" title="Driver: {{ $driverLabel }}">
                                                    <i class="bi bi-download"></i> 
                                                </a>
                                            @elseif($pc->catalogo->driver_url)
                                                <a href="{{ $pc->catalogo->driver_url }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-2" title="Driver Web">
                                                    <i class="bi bi-link-45deg"></i>
                                                </a>
                                            @endif

                                            @if($pc->extraSystem)
                                                <a href="{{ route('wiki.download', $pc->extraSystem->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-2" title="Herramientas: {{ $pc->extra_system_nombre ?: 'Extra' }}">
                                                    <i class="bi bi-download"></i> 
                                                </a>
                                            @endif

                                            <button class="btn btn-outline-warning btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalEditEquipo{{ $pc->id }}">
                                                <i class="bi bi-pencil me-1"></i> Editar
                                            </button>
                                            <form action="{{ route('clientes.entorno.equipo.destroy', ['cliente' => $cliente->id, 'id' => $pc->id]) }}" method="POST" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill" onclick="return confirm('¿Eliminar PC y todos sus periféricos?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="card-body p-4">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="small text-uppercase fw-bold text-muted mb-2" style="font-size: 0.65rem; letter-spacing: 1px;">Configuración Específica</div>
                                                <div class="p-3 rounded-3 border border-secondary border-opacity-10 small" style="min-height: 80px;">
                                                    {{ $pc->configuracion_especifica ?: 'No hay configuraciones especiales registradas.' }}
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="small text-uppercase fw-bold text-muted mb-2 d-flex justify-content-between" style="font-size: 0.65rem; letter-spacing: 1px;">
                                                    Componentes, Accesorios y Vinculados
                                                    <span class="badge rounded-pill bg-primary" style="font-size: 0.6rem;">{{ $pc->peripherals->count() }} ítems</span>
                                                </div>
                                                <div class="table-responsive border border-secondary border-opacity-10 rounded-3">
                                                    <table class="table table-sm table-spgi m-0" style="font-size: 0.85rem;">
                                                        <thead class="table-dark">
                                                            <tr>
                                                                <th>Tipo / Categoría</th>
                                                                <th>Marca / Modelo</th>
                                                                <th>S/N</th>
                                                                <th class="text-center">Recursos</th>
                                                                <th class="text-end">Acciones</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @forelse($pc->peripherals as $item)
                                                                @php
                                                                    $esSoftware = $item->extra_system_id && !$item->serie;
                                                                    $esDriver = $item->driver_id && !$item->serie && !$item->extra_system_id;
                                                                    
                                                                    if ($esSoftware) {
                                                                        $tipoLabel = 'Software / Tool';
                                                                        $nombreDisplay = $item->extra_system_nombre ?: 'Sistema Extra';
                                                                        $badgeClass = 'bg-info';
                                                                    } elseif ($esDriver) {
                                                                        $tipoLabel = 'Driver / Manual';
                                                                        $nombreDisplay = $item->driver_nombre ?: 'Driver';
                                                                        $badgeClass = 'bg-success';
                                                                    } else {
                                                                        $tipoLabel = 'Equipo';
                                                                        $nombreDisplay = $item->catalogo->marca . ' ' . $item->catalogo->modelo;
                                                                        $badgeClass = 'bg-secondary';
                                                                    }
                                                                @endphp
                                                                <tr>
                                                                    <td><span class="badge {{ $badgeClass }} rounded-pill">{{ $tipoLabel }}</span></td>
                                                                    <td><span class="fw-bold">{{ $nombreDisplay }}</span></td>
                                                                    <td><code class="small">{{ $item->serie ?: '-' }}</code></td>
                                                                    <td class="text-center">
                                                                        <div class="d-flex justify-content-center gap-1">
                                                                            @php
                                                                                $pDriver = $item->driver_id ?: ($item->catalogo->driver_doc_id ?? null);
                                                                            @endphp
                                                                            @if($pDriver)
                                                                                <a href="{{ route('wiki.download', $pDriver) }}" class="text-success mx-1" title="Driver: {{ $item->driver_nombre ?: 'Descargar' }}">
                                                                                    <i class="bi bi-download"></i>
                                                                                </a>
                                                                            @endif
                                                                            @if($item->extraSystem)
                                                                                <a href="{{ route('wiki.download', $item->extraSystem->id) }}" class="text-primary mx-1" title="Herramientas: {{ $item->extra_system_nombre ?: 'Extra' }}">
                                                                                    <i class="bi bi-download"></i>
                                                                                </a>
                                                                            @endif
                                                                        </div>
                                                                    </td>
                                                                    <td class="text-end">
                                                                        <button class="btn btn-link btn-sm p-0 text-warning me-2" data-bs-toggle="modal" data-bs-target="#modalEditEquipo{{ $item->id }}"><i class="bi bi-pencil"></i></button>
                                                                        <form action="{{ route('clientes.entorno.equipo.destroy', ['cliente' => $cliente->id, 'id' => $item->id]) }}" method="POST" class="d-inline">
                                                                            @csrf @method('DELETE')
                                                                            <button type="submit" class="btn btn-link btn-sm p-0 text-danger" onclick="return confirm('¿Remover periférico?')"><i class="bi bi-trash"></i></button>
                                                                        </form>
                                                                    </td>
                                                                </tr>
                                                            @empty
                                                                <tr><td colspan="4" class="text-center py-3 text-muted">No hay periféricos asignados a esta unidad.</td></tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @if($pc->notas)
                                        <div class="card-footer py-2 px-4 border-0" style="background: rgba(var(--text-main), 0.03);">
                                            <small class="text-muted"><i class="bi bi-info-circle me-1"></i> <strong>Notas:</strong> {{ $pc->notas }}</small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="p-5 text-center bg-light bg-opacity-5 rounded-4 border border-dashed">
                                    <i class="bi bi-box-seam fs-1 text-muted mb-3 d-block"></i>
                                    <h5 class="text-muted">No hay equipos registrados para este cliente.</h5>
                                    <p class="text-muted small">Haz clic en "Asignar Nuevo Equipo" para comenzar el inventario.</p>
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-4">
                        {{ $equipos->appends(['page_docs'=>$documentos->currentPage(), 'page_anydesk'=>$anydesks->currentPage(), 'page_bitacora'=>$bitacoras->currentPage()])->fragment('equipos')->links() }}
                    </div>
                </div>

                <!-- ANYDESK -->
                <div class="tab-pane fade" id="anydesk" role="tabpanel">
                    <div class="spgi-card">
                        <div class="card-header-spgi">
                            <h5>Listado de AnyDesk IDs</h5>
                            <button class="btn btn-spgi" data-bs-toggle="modal" data-bs-target="#modalAddAnydesk">
                                <i class="bi bi-plus-lg me-1"></i> Agregar AnyDesk
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-spgi align-middle">
                                    <thead>
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
                                                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; background: rgba(var(--spgi-primary), 0.1); color: var(--spgi-primary);">
                                                            <i class="bi bi-display small"></i>
                                                        </div>
                                                        <span class="fw-bold" style="color:var(--text-main);">{{ $ad->alias ?: 'Principal' }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-1">
                                                        <span class="font-monospace border rounded-2 px-2 py-1 small" style="letter-spacing: 1px; background: rgba(var(--text-main), 0.05); color: var(--spgi-primary); border-color: var(--border-main);">
                                                            {{ $ad->anydesk_id }}
                                                        </span>
                                                        <button class="btn btn-link btn-sm p-1 text-muted" 
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
                                        @empty
                                            <tr><td colspan="4" class="p-4 text-center text-muted">No hay AnyDesk registrados.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="p-3 border-top border-secondary border-opacity-10">
                                {{ $anydesks->appends(['page_docs'=>$documentos->currentPage(), 'page_equipos'=>$equipos->currentPage(), 'page_bitacora'=>$bitacoras->currentPage()])->fragment('anydesk')->links() }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- BITACORA -->
                <div class="tab-pane fade" id="bitacora" role="tabpanel">
                    <div class="spgi-card">
                        <div class="card-header-spgi">
                            <h5>Bitácora de Notas de Sistema</h5>
                            <button class="btn btn-spgi" data-bs-toggle="modal" data-bs-target="#modalAddBitacora">
                                <i class="bi bi-plus-lg me-1"></i> Nueva Nota
                            </button>
                        </div>
                        <div class="card-body p-4">
                            <div class="timeline">
                                @forelse($bitacoras as $nota)
                                    <div class="mb-4 pb-3 border-bottom border-secondary border-opacity-10 position-relative">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 0.75rem; background: rgba(var(--spgi-primary), 0.1); color: var(--spgi-primary);">
                                                    {{ strtoupper(substr($nota->user->name, 0, 1)) }}
                                                </div>
                                                <span class="fw-bold" style="color:var(--text-main);">{{ $nota->user->name }}</span>
                                            </div>
                                            <small class="text-muted">{{ $nota->created_at->format('d/m/Y H:i') }}</small>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-start">
                                            <p class="mb-0" style="white-space: pre-wrap; color: var(--text-muted); flex: 1;">{{ $nota->nota }}</p>
                                            @if($nota->user_id == auth()->id() || auth()->user()->cod_roleUser == 1)
                                                <div class="d-flex gap-2 ms-3">
                                                    <button class="btn btn-link btn-sm p-0 text-warning" data-bs-toggle="modal" data-bs-target="#modalEditBitacora{{ $nota->id }}">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <form action="{{ route('clientes.entorno.bitacora.destroy', ['cliente' => $cliente->id, 'id' => $nota->id]) }}" method="POST">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-link btn-sm p-0 text-danger" onclick="return confirm('¿Eliminar esta nota?')">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-5 text-muted">Aún no hay notas en la bitácora.</div>
                                @endforelse
                            </div>
                            <div class="mt-3">
                                {{ $bitacoras->appends(['page_docs'=>$documentos->currentPage(), 'page_equipos'=>$equipos->currentPage(), 'page_anydesk'=>$anydesks->currentPage()])->fragment('bitacora')->links() }}
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
        <div class="modal-content">
            <form action="{{ route('clientes.entorno.documento.store', $cliente->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Agregar Registro / Documento</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Tipo</label>
                            <select name="tipo" class="form-select" required>
                                <option value="IP">Listado de IP</option>
                                <option value="Mapa">Mapa Conceptual</option>
                                <option value="Credencial">Usuario / Clave</option>
                                <option value="Reporte">Informe / Reporte</option>
                                <option value="Driver">Driver de Equipo</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nombre / Descripción</label>
                            <input type="text" name="nombre" class="form-control" required placeholder="Ej: Router Principal">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Usuario (Opcional)</label>
                            <input type="text" name="usuario" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Clave (Opcional)</label>
                            <input type="password" name="clave" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Archivo (Si aplica)</label>
                            <input type="file" name="archivo" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">URL / Link (Si aplica)</label>
                            <input type="url" name="url" class="form-control" placeholder="https://...">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-spgi">Guardar Registro</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Add AnyDesk -->
<div class="modal fade" id="modalAddAnydesk" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('clientes.entorno.anydesk.store', $cliente->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Agregar ID AnyDesk</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">AnyDesk ID</label>
                            <input type="text" name="anydesk_id" class="form-control" required placeholder="Ej: 123 456 789">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Alias / Hostname</label>
                            <input type="text" name="alias" class="form-control" placeholder="Ej: PC Recepción">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notas (Opcional)</label>
                            <textarea name="notas" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-spgi">Agregar Acceso</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Add Bitacora -->
<div class="modal fade" id="modalAddBitacora" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('clientes.entorno.bitacora.store', $cliente->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Nueva Nota de Sistema</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <textarea name="nota" class="form-control" rows="6" placeholder="Escribe aquí las observaciones técnicas..." required></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-spgi">Publicar Nota</button>
                </div>
            </form>
        </div>
    </div>
</div>

@foreach($equiposInventario as $inv)
<div class="modal fade" id="modalEditEquipo{{ $inv->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form action="{{ route('clientes.entorno.equipo.update', ['cliente' => $cliente->id, 'id' => $inv->id]) }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-pencil-fill me-2 text-warning"></i>Editar Registro de Equipo
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nombre / Alias del Equipo</label>
                            <input type="text" name="alias" class="form-control" value="{{ $inv->alias }}" placeholder="Ej: PC Principal, Laptop Gerencia...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Número de Serie (S/N)</label>
                            <input type="text" name="serie" class="form-control" value="{{ $inv->serie }}" placeholder="Ej: ABC123XYZ">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Archivo de Sistema (Manual/Driver para Wiki)</label>
                            <input type="file" name="sistema_file" class="form-control">
                            @if($inv->wikiDocument)
                                <div class="mt-1 text-info small"><i class="bi bi-info-circle"></i> Sustituir sistema actual</div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Driver Específico</label>
                            <input type="file" name="driver_file" class="form-control">
                            @if($inv->driver)
                                <div class="mt-1 text-success small"><i class="bi bi-check-circle"></i> Driver específico cargado</div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nombre del Sistema Extra</label>
                            <input type="text" name="extra_system_nombre" class="form-control" value="{{ $inv->extra_system_nombre }}" placeholder="Ej: Office, SQL, etc.">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Herramienta Extra (Archivo)</label>
                            <input type="file" name="sistema_extra_file" class="form-control">
                            @if($inv->extraSystem)
                                <div class="mt-1 text-primary small"><i class="bi bi-check-circle"></i> Herramientas cargadas</div>
                            @endif
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Configuración Específica (IP, Puertos, etc.)</label>
                            <textarea name="configuracion_especifica" class="form-control" rows="3">{{ $inv->configuracion_especifica }}</textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Notas Adicionales</label>
                            <textarea name="notas" class="form-control" rows="2">{{ $inv->notas }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-spgi">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@foreach($anydesks as $ad)
<div class="modal fade" id="modalEditAnydesk{{ $ad->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('clientes.entorno.anydesk.update', ['cliente' => $cliente->id, 'id' => $ad->id]) }}" method="POST">
                @csrf @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Actualizar AnyDesk</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">AnyDesk ID</label>
                            <input type="text" name="anydesk_id" class="form-control" value="{{ $ad->anydesk_id }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Alias / Hostname</label>
                            <input type="text" name="alias" class="form-control" value="{{ $ad->alias }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notas</label>
                            <textarea name="notas" class="form-control" rows="2">{{ $ad->notas }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-spgi">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<!-- Modal Add Equipo -->
<div class="modal fade" id="modalAddEquipo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form action="{{ route('clientes.entorno.equipo.store', $cliente->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="bi bi-magic me-2"></i>Asistente de Inventario</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <!-- Barra de Progreso -->
                <div class="progress rounded-0" style="height: 4px;">
                    <div id="wizardProgress" class="progress-bar bg-primary" role="progressbar" style="width: 33%;"></div>
                </div>

                <div class="modal-body p-0">
                    <!-- Paso 1: Selección de Tipo -->
                    <div id="step1" class="p-4 wizard-step">
                        <div class="text-center mb-4">
                            <h6 class="fw-bold">¿Qué desea registrar hoy?</h6>
                            <p class="text-muted small">Seleccione una opción para guiarle en el proceso</p>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="wizard-option p-4 border rounded-3 text-center cursor-pointer h-100" onclick="selectWizardMode('hardware')">
                                    <i class="bi bi-pc-display fs-1 d-block mb-2 text-primary"></i>
                                    <span class="fw-bold">Equipo Físico</span>
                                    <small class="d-block text-muted mt-1">PC, Impresora, UPS...</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="wizard-option p-4 border rounded-3 text-center cursor-pointer h-100" onclick="selectWizardMode('software')">
                                    <i class="bi bi-window-stack fs-1 d-block mb-2 text-primary"></i>
                                    <span class="fw-bold">Software / Tool</span>
                                    <small class="d-block text-muted mt-1">Office, Antivirus, SQL...</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="wizard-option p-4 border rounded-3 text-center cursor-pointer h-100" onclick="selectWizardMode('recurso')">
                                    <i class="bi bi-file-earmark-zip fs-1 d-block mb-2 text-primary"></i>
                                    <span class="fw-bold">Driver / Manual</span>
                                    <small class="d-block text-muted mt-1">Documentación base</small>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="wizard_mode" id="wizardMode" value="hardware">
                    </div>

                    <!-- Paso 2: Datos Específicos -->
                    <div id="step2" class="p-4 wizard-step d-none">
                        <div id="fieldsHardware" class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-bold">1. Selección de Equipo (Catálogo)</label>
                                <select name="cat_equipo_id" id="wizardCatId" class="form-select">
                                    <option value="">-- Buscar equipo --</option>
                                    @foreach($catalogoEquipos as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->nombre }} ({{ $cat->marca }} - {{ $cat->modelo }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nombre / Alias (Ej: PC Recepción)</label>
                                <input type="text" name="alias" class="form-control" placeholder="Opcional">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">¿Donde se ubica? (Padre)</label>
                                <select name="parent_id" id="parent_id_select" class="form-select">
                                    <option value="">Es un Equipo Principal / Estación</option>
                                    @foreach($equiposPadre as $p)
                                        <option value="{{ $p->id }}">Dentro de: {{ $p->alias ?: $p->catalogo->nombre }} ({{ $p->serie }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Número de Serie (S/N)</label>
                                <input type="text" name="serie" class="form-control" placeholder="Ej: ABC123XYZ">
                            </div>
                        </div>

                        <div id="fieldsSoftware" class="row g-3 d-none">
                             <div class="col-md-12">
                                <label class="form-label fw-bold">Nombre del Programa / Herramienta</label>
                                <input type="text" name="extra_system_nombre" id="extraSystemNombre" class="form-control" placeholder="Ej: Office, SQL Server...">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Archivo Instalador</label>
                                <input type="file" name="sistema_extra_file" class="form-control">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Vincular a Equipo (Opcional)</label>
                                <select name="parent_id_soft" id="parent_id_soft_select" class="form-select">
                                    <option value="">Equipo General / Servidor</option>
                                    @foreach($equiposPadre as $p)
                                        <option value="{{ $p->id }}">{{ $p->alias ?: $p->catalogo->nombre }} ({{ $p->serie }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div id="fieldsRecurso" class="row g-3 d-none">
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Nombre del Driver / Manual</label>
                                <input type="text" name="driver_nombre" class="form-control" placeholder="Ej: Driver Zebra, Manual Configuración...">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Archivo (PDF / ZIP)</label>
                                <input type="file" name="driver_file" class="form-control">
                            </div>
                        </div>
                    </div>

                    <!-- Paso 3: Configuración -->
                    <div id="step3" class="p-4 wizard-step d-none">
                        <div class="row g-3">
                            <div id="fieldsConfig" class="col-md-12">
                                <label class="form-label">Configuración Técnica (IP, Puertos, etc.)</label>
                                <textarea name="configuracion_especifica" class="form-control" rows="3" placeholder="Si aplica..."></textarea>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Notas Adicionales</label>
                                <textarea name="notas" class="form-control" rows="2" placeholder="Cualquier aclaración..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" id="wizardPrev" class="btn btn-outline-secondary px-4 rounded-pill d-none" onclick="prevWizard()"><i class="bi bi-chevron-left"></i> Anterior</button>
                    <button type="button" id="wizardNext" class="btn btn-primary px-4 rounded-pill" onclick="nextWizard()">Siguiente <i class="bi bi-chevron-right"></i></button>
                    <button type="submit" id="wizardSubmit" class="btn btn-spgi px-4 rounded-pill d-none">Finalizar y Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@foreach($bitacoras as $nota)
<div class="modal fade" id="modalEditBitacora{{ $nota->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('clientes.entorno.bitacora.update', ['cliente' => $cliente->id, 'id' => $nota->id]) }}" method="POST">
                @csrf @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Editar Nota de Bitácora</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <textarea name="nota" class="form-control" rows="6" required>{{ $nota->nota }}</textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-spgi">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

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
            btn.classList.add('text-success');
            setTimeout(() => {
                btn.innerHTML = originalHtml;
                btn.classList.remove('text-success');
            }, 2000);
        }).catch(err => {
            console.error('Error al copiar: ', err);
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const hash = window.location.hash;
        if (hash) {
            const triggerEl = document.querySelector('button[data-bs-target="' + hash + '"]');
            if (triggerEl) {
                const tab = new bootstrap.Tab(triggerEl);
                tab.show();
            }
        }

        const tabBtns = document.querySelectorAll('button[data-bs-toggle="tab"]');
        tabBtns.forEach(btn => {
            btn.addEventListener('shown.bs.tab', function(event) {
                const targetHash = event.target.getAttribute('data-bs-target');
                history.replaceState(null, null, targetHash);
            });
        });
    });

    function resetWizard() {
        currentStep = 1;
        document.getElementById('wizardMode').value = 'hardware';
        document.getElementById('parent_id_select').value = '';
        document.getElementById('parent_id_soft_select').value = '';
        
        // Show selection step
        document.querySelectorAll('.wizard-step').forEach(s => s.classList.add('d-none'));
        document.getElementById('step1').classList.remove('d-none');
        
        updateWizardUI();
    }

    function openWizardWithParent(parentId, parentName) {
        resetWizard();
        // Pre-fill parent selection
        const pSelect = document.getElementById('parent_id_select');
        const pSoftSelect = document.getElementById('parent_id_soft_select');
        
        if (pSelect) pSelect.value = parentId;
        if (pSoftSelect) pSoftSelect.value = parentId;

        // Open modal manually if needed (already handled by data-bs-toggle if added)
        const modal = new bootstrap.Modal(document.getElementById('modalAddEquipo'));
        modal.show();
    }

    function selectWizardMode(mode) {
        document.getElementById('wizardMode').value = mode;
        
        // Visual feedback
        document.querySelectorAll('.wizard-option').forEach(opt => opt.classList.remove('border-primary', 'bg-primary', 'bg-opacity-10'));
        event.currentTarget.classList.add('border-primary', 'bg-primary', 'bg-opacity-10');
        
        // Toggle fields
        document.getElementById('fieldsHardware').classList.add('d-none');
        document.getElementById('fieldsSoftware').classList.add('d-none');
        document.getElementById('fieldsRecurso').classList.add('d-none');

        if (mode === 'hardware') document.getElementById('fieldsHardware').classList.remove('d-none');
        if (mode === 'software') document.getElementById('fieldsSoftware').classList.remove('d-none');
        if (mode === 'recurso') document.getElementById('fieldsRecurso').classList.remove('d-none');

        nextWizard(); 
    }

    function nextWizard() {
        if (currentStep < 3) {
            document.getElementById('step' + currentStep).classList.add('d-none');
            currentStep++;
            document.getElementById('step' + currentStep).classList.remove('d-none');
            updateWizardUI();
        }
    }

    function prevWizard() {
        if (currentStep > 1) {
            document.getElementById('step' + currentStep).classList.add('d-none');
            currentStep--;
            document.getElementById('step' + currentStep).classList.remove('d-none');
            updateWizardUI();
        }
    }

    function updateWizardUI() {
        // Progress bar
        const progress = (currentStep / 3) * 100;
        document.getElementById('wizardProgress').style.width = progress + '%';

        // Buttons
        document.getElementById('wizardPrev').classList.toggle('d-none', currentStep === 1);
        document.getElementById('wizardNext').classList.toggle('d-none', currentStep === 3);
        document.getElementById('wizardSubmit').classList.toggle('d-none', currentStep !== 3);
    }
</script>

<style>
    .wizard-option {
        transition: all 0.3s ease;
        cursor: pointer;
        background: var(--bg-surface);
        color: var(--text-main);
    }
    .wizard-option:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-main);
        border-color: var(--spgi-primary) !important;
    }
    .spgi-card {
        background-color: var(--bg-surface);
        border: 1px solid var(--border-main);
        color: var(--text-main);
    }
    .card-header-spgi {
        background-color: var(--bg-surface);
        border-bottom: 1px solid var(--border-main);
        color: var(--text-main);
    }
    .btn-outline-info, .btn-outline-success, .btn-outline-primary, .btn-outline-warning, .btn-outline-danger {
        background: transparent;
    }
    .bg-light {
        background-color: var(--bg-master) !important;
    }
    .table-spgi {
        color: var(--text-main);
    }
    .table-spgi thead th {
        background-color: var(--sidebar-bg);
        color: #fff;
    }
    .modal-content {
        background-color: var(--bg-surface);
        color: var(--text-main);
        border: 1px solid var(--border-main);
    }
    .modal-header, .modal-footer {
        background-color: var(--bg-surface) !important;
        border-color: var(--border-main);
    }
    .form-control, .form-select {
        background-color: var(--bg-surface);
        color: var(--text-main);
        border-color: var(--border-main);
    }
    .form-control:focus, .form-select:focus {
        background-color: var(--bg-surface);
        color: var(--text-main);
    }
</style>
@endpush

@endsection
