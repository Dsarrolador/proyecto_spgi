@extends('layouts.app')

@section('page_title', 'Detalle de Rendición')

@section('content')
<style>
  .spgi-bg{ padding: 24px 0; }
  .glass-card-premium {
    background: var(--bg-surface-glass); border: 1px solid var(--border-main);
    border-radius: 24px; box-shadow: var(--shadow-main); backdrop-filter: blur(20px);
    padding: 32px; margin-bottom: 24px;
  }
  .form-label{ font-weight: 600; color: var(--text-main); font-size: 0.9rem; }
  .form-control, .form-select{
    height: 44px; border-radius: 12px; border: 1px solid var(--border-main);
    background: var(--bg-surface); color: var(--text-main); transition: all 0.2s;
  }
  .form-control:focus, .form-select:focus{
    border-color: var(--spgi-primary); box-shadow: 0 0 0 4px var(--spgi-primary-glow);
  }
  .btn-spgi{
    background: linear-gradient(135deg, #3b82f6, #2563eb); border: 0; color: #fff;
    min-height: 44px; border-radius: 12px; padding: 0 24px; font-weight: 700;
    box-shadow: 0 10px 25px rgba(59, 130, 246, 0.2); transition: all 0.3s;
  }
  .btn-spgi:hover{ transform: translateY(-1px); box-shadow: 0 12px 30px rgba(59, 130, 246, 0.3); color: #fff; }
  
  .spgi-table{ margin-bottom: 0; }
  .spgi-table thead th{
    background: #0b1220; color: #fff; text-align:center;
    border-color: rgba(255,255,255,0.08) !important;
    font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; padding: 12px;
  }
  .spgi-table tbody td{ border-color: var(--border-main) !important; color: var(--text-main); padding: 12px; text-align:center; }
  .spgi-table tbody td.text-start { text-align:left; }
  .spgi-table tbody tr:hover{ background: rgba(59, 130, 246, 0.05); }

  .status-borrador { background: rgba(245, 158, 11, 0.1) !important; color: #f59e0b !important; }
  .status-enviado { background: rgba(59, 130, 246, 0.1) !important; color: #3b82f6 !important; }
  .status-aprobado { background: rgba(16, 185, 129, 0.1) !important; color: #10b981 !important; }
  .status-rechazado { background: rgba(239, 68, 68, 0.1) !important; color: #ef4444 !important; }

  select.status-select {
    border: 1px solid transparent !important;
    cursor: pointer;
    text-align: center;
    text-align-last: center;
    text-transform: uppercase;
  }
  select.status-select:focus {
    box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.25) !important;
  }
</style>

<div class="spgi-bg">
  <div class="container">
    
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold text-gradient">{{ $rendicion->titulo }}</h1>
            <p class="text-muted mb-0 d-flex align-items-center flex-wrap gap-2">
                <span>Rendición de Gastos #{{ $rendicion->id }}</span>
                <span>• Estado:</span>
                @php
                    $statusClass = 'status-' . strtolower(str_replace(' ', '-', $rendicion->estado));
                @endphp
                <select class="form-select form-select-sm status-select {{ $statusClass }}" data-rendicion-id="{{ $rendicion->id }}" style="display: inline-block; width: auto; font-size: 0.75rem; padding: 2px 24px 2px 8px; height: 28px; border-radius: 999px; font-weight: 800; text-transform: uppercase;">
                    <option value="Borrador" {{ $rendicion->estado == 'Borrador' ? 'selected' : '' }}>Borrador</option>
                    <option value="Enviado" {{ $rendicion->estado == 'Enviado' ? 'selected' : '' }}>Enviado</option>
                    <option value="Aprobado" {{ $rendicion->estado == 'Aprobado' ? 'selected' : '' }}>Aprobado</option>
                    <option value="Rechazado" {{ $rendicion->estado == 'Rechazado' ? 'selected' : '' }}>Rechazado</option>
                </select>
                @if($rendicion->responsable)
                    <span>• Encargado: <span class="badge bg-secondary px-2.5 py-1.5 rounded-pill">{{ $rendicion->responsable->name }}</span></span>
                @endif
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('rendiciones.pdf', $rendicion->id) }}" target="_blank" class="btn btn-outline-primary rounded-pill">
                <i class="bi bi-file-earmark-pdf me-2"></i> Generar PDF
            </a>
            <a href="{{ route('rendiciones.index') }}" class="btn btn-outline-secondary rounded-pill">
                <i class="bi bi-arrow-left me-2"></i> Volver
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4 animate__animated animate__fadeIn">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4 animate__animated animate__fadeIn">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li><i class="bi bi-exclamation-triangle-fill me-2"></i> {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- INFORMACIÓN GENERAL -->
    <div class="glass-card-premium mb-4 animate__animated animate__fadeIn">
        <h4 class="fw-bold mb-3" style="color: var(--text-main);"><i class="bi bi-info-circle text-primary me-2"></i>Información General de la Rendición</h4>
        <form action="{{ route('rendiciones.general-info', $rendicion->id) }}" method="POST">
            @csrf @method('PUT')
            <div class="row align-items-end">
                <div class="col-md-3 mb-3">
                    <label class="form-label">Fecha de Aprobación</label>
                    <input type="date" name="fecha_aprobacion" class="form-control" value="{{ $rendicion->fecha_aprobacion ? $rendicion->fecha_aprobacion->format('Y-m-d') : '' }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Observaciones Generales (Se muestra al final del reporte PDF)</label>
                    <input type="text" name="observaciones" class="form-control" placeholder="Ej. Gastos correspondientes al evento de mercadeo del mes..." value="{{ $rendicion->observaciones }}">
                </div>
                <div class="col-md-3 mb-3">
                    <button type="submit" class="btn btn-spgi w-100">
                        <i class="bi bi-check-lg me-2"></i> Guardar Cambios
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="row">
        <!-- AGREGAR GASTO -->
        <div class="col-lg-4">
            <div class="glass-card-premium animate__animated animate__fadeInLeft">
                <h4 class="fw-bold mb-4" style="color: var(--text-main);"><i class="bi bi-plus-circle text-primary me-2"></i>Agregar Gasto</h4>
                
                <form action="{{ route('rendiciones.gastos.store', $rendicion->id) }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label">Fecha <span class="text-danger">*</span></label>
                        <input type="date" name="fecha" class="form-control" required value="{{ date('Y-m-d') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Concepto / Descripción <span class="text-danger">*</span></label>
                        <input type="text" name="concepto" class="form-control" placeholder="Ej. Uber a Oficina, Materiales..." required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Proveedor / Lugar <span class="text-danger">*</span></label>
                        <input type="text" name="proveedor" class="form-control" placeholder="Ej. Uber, Omega Tech, Supermercado..." required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Monto (RD$) <span class="text-danger">*</span></label>
                        <input type="number" name="monto" step="0.01" min="0.01" class="form-control" placeholder="0.00" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Método / Forma de Pago <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <select name="metodo_pago_id" id="metodo_pago_select" class="form-select" required>
                                <option value="">-- Seleccionar --</option>
                                @foreach($metodosPago as $m)
                                    <option value="{{ $m->id }}" data-card="{{ $m->requiere_tarjeta ? '1' : '0' }}">{{ $m->nombre }}</option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addPaymentMethodModal" title="Agregar nuevo método de pago">
                                <i class="bi bi-plus-lg"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Últimos 4 dígitos de la tarjeta (se muestra dinámicamente) -->
                    <div class="mb-3 d-none" id="card_digits_group">
                        <label class="form-label">Últimos 4 dígitos de la Tarjeta <span class="text-danger">*</span></label>
                        <input type="text" name="tarjeta_ultimos_4" id="tarjeta_ultimos_4" class="form-control" maxlength="4" placeholder="Ej. 1704" pattern="[0-9]{4}">
                        <div class="form-text text-muted">Ingrese exactamente 4 dígitos numéricos.</div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Observaciones (Opcional)</label>
                        <textarea name="observaciones" class="form-control" style="height: 80px;" placeholder="Detalles extra..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-spgi w-100">
                        <i class="bi bi-plus-lg me-2"></i> Guardar Gasto
                    </button>
                </form>
            </div>
        </div>

        <!-- LISTADO DE GASTOS -->
        <div class="col-lg-8">
            <div class="glass-card-premium animate__animated animate__fadeInRight">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h4 class="fw-bold m-0" style="color: var(--text-main);"><i class="bi bi-list-check text-primary me-2"></i>Gastos Registrados</h4>
                    <span class="fs-5 fw-bold text-gradient">Total: RD$ {{ number_format($rendicion->total, 2) }}</span>
                </div>

                <div class="table-responsive">
                    <table class="table spgi-table align-middle">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th class="text-start">Concepto</th>
                                <th>Proveedor</th>
                                <th>Monto</th>
                                <th>Pago</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rendicion->gastos as $g)
                            <tr>
                                <td>{{ $g->fecha->format('d/m/Y') }}</td>
                                <td class="text-start">
                                    <span class="fw-bold">{{ $g->concepto }}</span>
                                    @if($g->observaciones)
                                        <br><small class="text-muted"><i class="bi bi-info-circle me-1"></i>{{ $g->observaciones }}</small>
                                    @endif
                                </td>
                                <td>{{ $g->proveedor }}</td>
                                <td class="fw-bold">RD$ {{ number_format($g->monto, 2) }}</td>
                                <td>
                                    @if($g->metodoPago)
                                        @if($g->metodoPago->requiere_tarjeta)
                                            <span class="badge bg-primary rounded-pill">{{ $g->metodoPago->nombre }} ({{ $g->tarjeta_ultimos_4 ?? 'N/A' }})</span>
                                        @elseif($g->metodoPago->nombre == 'Efectivo')
                                            <span class="badge bg-success rounded-pill">{{ $g->metodoPago->nombre }}</span>
                                        @else
                                            <span class="badge bg-warning text-dark rounded-pill">{{ $g->metodoPago->nombre }}</span>
                                        @endif
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <form action="{{ route('rendiciones.gastos.destroy', ['id' => $rendicion->id, 'gasto_id' => $g->id]) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar este gasto?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="bi bi-journal-x display-4 text-muted mb-3 d-block"></i>
                                    <p class="text-muted">Aún no se han agregado gastos a esta rendición.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

  </div>
</div>

<!-- Modal para agregar Método de Pago -->
<div class="modal fade" id="addPaymentMethodModal" tabindex="-1" aria-labelledby="addPaymentMethodModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg" style="background: var(--bg-surface); color: var(--text-main);">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" id="addPaymentMethodModalLabel">
                    <i class="bi bi-plus-circle-fill text-primary me-2"></i> Nuevo Método de Pago
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: var(--bs-theme-dark-btn-close-filter);"></button>
            </div>
            <div class="modal-body py-4">
                <div class="alert alert-danger d-none" id="modal-error-alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> <span id="modal-error-message"></span>
                </div>
                
                <form id="addPaymentMethodForm">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre del Método <span class="text-danger">*</span></label>
                        <input type="text" id="new_method_name" class="form-control" placeholder="Ej. Tarjeta 1704, Tarjeta Corporativa, Caja Chica..." required>
                    </div>
                    
                    <div class="mb-3 form-check form-switch pt-2">
                        <input class="form-check-input" type="checkbox" id="new_method_requires_card" value="1">
                        <label class="form-check-label fw-bold" for="new_method_requires_card">
                            ¿Es una Tarjeta? (Requiere ingresar los últimos 4 dígitos)
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" onclick="submitNewPaymentMethod()" class="btn btn-primary rounded-pill fw-bold px-4">
                    Guardar Método
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Status Change Handler
        document.querySelectorAll('.status-select').forEach(select => {
            select.addEventListener('change', async function() {
                const rendicionId = this.dataset.rendicionId;
                const newStatus = this.value;
                const selectEl = this;
                
                selectEl.className = `form-select form-select-sm status-select status-${newStatus.toLowerCase()}`;
                
                try {
                    const response = await fetch(`/rendiciones/${rendicionId}/update-status`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ estado: newStatus })
                    });
                    
                    const data = await response.json();
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Estado Actualizado',
                            text: `La rendición ahora está en estado: ${newStatus}`,
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                    } else {
                        throw new Error(data.error || 'Error al actualizar');
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.message || 'No se pudo actualizar el estado'
                    });
                }
            });
        });

        const selectElement = document.getElementById('metodo_pago_select');
        const digitsGroup = document.getElementById('card_digits_group');
        const digitsInput = document.getElementById('tarjeta_ultimos_4');

        selectElement.addEventListener('change', function () {
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            const isCard = selectedOption.getAttribute('data-card') === '1';

            if (isCard) {
                digitsGroup.classList.remove('d-none');
                digitsInput.setAttribute('required', 'required');
            } else {
                digitsGroup.classList.add('d-none');
                digitsInput.removeAttribute('required');
                digitsInput.value = '';
            }
        });
    });

    function submitNewPaymentMethod() {
        const name = document.getElementById('new_method_name').value.trim();
        const requiresCard = document.getElementById('new_method_requires_card').checked ? 1 : 0;
        const errorAlert = document.getElementById('modal-error-alert');
        const errorMessage = document.getElementById('modal-error-message');

        if (!name) {
            errorAlert.classList.remove('d-none');
            errorMessage.textContent = 'Debe ingresar un nombre para el método de pago.';
            return;
        }

        errorAlert.classList.add('d-none');

        fetch("{{ route('rendiciones.metodo-pago.ajax') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                nombre: name,
                requiere_tarjeta: requiresCard
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Agregar al select
                const selectElement = document.getElementById('metodo_pago_select');
                const newOption = document.createElement('option');
                newOption.value = data.metodo.id;
                newOption.textContent = data.metodo.nombre;
                newOption.setAttribute('data-card', data.metodo.requiere_tarjeta ? '1' : '0');
                
                selectElement.appendChild(newOption);
                selectElement.value = data.metodo.id;

                // Forzar trigger del cambio de tarjeta
                selectElement.dispatchEvent(new Event('change'));

                // Limpiar form y cerrar modal
                document.getElementById('new_method_name').value = '';
                document.getElementById('new_method_requires_card').checked = false;
                
                const modalEl = document.getElementById('addPaymentMethodModal');
                const modal = bootstrap.Modal.getInstance(modalEl);
                modal.hide();
            } else {
                errorAlert.classList.remove('d-none');
                errorMessage.textContent = 'Hubo un error al registrar el método.';
            }
        })
        .catch(error => {
            errorAlert.classList.remove('d-none');
            errorMessage.textContent = 'Ya existe un método de pago con ese nombre o hubo un problema de conexión.';
        });
    }
</script>
@endsection
