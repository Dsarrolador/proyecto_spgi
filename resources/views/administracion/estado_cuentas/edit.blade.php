@extends('layouts.app')

@section('page_title', 'Editar / Conciliar Factura')

@section('content')
<style>
  .spgi-bg{ padding: 12px 0 24px 0; }
  
  .form-card{
    background: var(--bg-surface-glass); border: 1px solid var(--border-main);
    border-radius: 24px; box-shadow: var(--shadow-main); backdrop-filter: blur(16px);
    padding: 30px; margin-top: 20px;
  }
  
  .reconciliation-card{
    background: rgba(16, 185, 129, 0.03); 
    border: 1px solid rgba(16, 185, 129, 0.2);
    border-radius: 24px; box-shadow: var(--shadow-main); backdrop-filter: blur(16px);
    padding: 30px; margin-top: 20px;
    position: relative;
    overflow: hidden;
  }
  
  .reconciliation-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 6px;
    height: 100%;
    background: #10b981;
  }

  .form-control-spgi, .form-select-spgi {
    background: var(--bg-surface);
    border: 1px solid var(--border-main);
    color: var(--text-main);
    border-radius: 12px;
    min-height: 46px;
  }
  .form-control-spgi:focus, .form-select-spgi:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.15);
    background: var(--bg-surface);
    color: var(--text-main);
  }
  
  .form-label {
    font-weight: 700;
    color: var(--text-main);
    font-size: 0.85rem;
    margin-bottom: 6px;
  }
  
  .btn-spgi-save {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    border: 0; color: #fff !important; min-height:46px; border-radius:14px; padding:0 30px;
    box-shadow: 0 8px 20px rgba(59, 130, 246, 0.2); font-weight:700;
    transition: all 0.2s;
  }
  .btn-spgi-save:hover { filter: brightness(1.1); transform: translateY(-1px); }
  
  .btn-pay-today {
    background-color: rgba(16, 185, 129, 0.1);
    border: 1px solid rgba(16, 185, 129, 0.3);
    color: #10b981;
    font-weight: 700;
    border-radius: 10px;
    padding: 8px 16px;
    font-size: 0.8rem;
    transition: all 0.2s;
  }
  .btn-pay-today:hover {
    background-color: #10b981;
    color: white;
  }
</style>

<div class="spgi-bg">
  <div class="container" style="max-width: 1000px;">
    
    <!-- Encabezado -->
    <div class="d-flex align-items-center justify-content-between mb-4">
      <div>
        <h1 class="h3 mb-1 fw-bold text-gradient">Editar y Conciliar Factura</h1>
        <p class="text-muted mb-0">Modifica los datos iniciales o registra información de cobros de forma rápida.</p>
      </div>
      <a href="{{ route('estado-cuentas.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
        <i class="bi bi-arrow-left me-2"></i> Volver al Listado
      </a>
    </div>

    <!-- Errores de Validación -->
    @if ($errors->any())
      <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4">
        <div class="fw-bold mb-2"><i class="bi bi-exclamation-triangle-fill me-2"></i> Por favor corrige los siguientes errores:</div>
        <ul class="mb-0 small">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form action="{{ route('estado-cuentas.update', $record->id) }}" method="POST" id="editFacturaForm">
      @csrf
      @method('PUT')

      <div class="row">
        
        <!-- CARD 1: FACTURACIÓN -->
        <div class="col-lg-6">
          <h5 class="fw-bold text-muted mb-1 text-uppercase small">Datos Facturación</h5>
          <div class="form-card animate__animated animate__fadeInLeft h-100 mt-2">
            <div class="row g-3">
              <div class="col-md-12">
                <label for="cliente_maestro_id" class="form-label">Cliente Maestro (Catálogo)</label>
                <select name="cliente_maestro_id" id="cliente_maestro_id" class="form-select form-select-spgi">
                  <option value="">-- Seleccionar cliente del sistema --</option>
                  @foreach($clientesMaestros as $c)
                    <option value="{{ $c->id }}" {{ old('cliente_maestro_id', $record->cliente_maestro_id) == $c->id ? 'selected' : '' }} data-nombre="{{ $c->nombre }}">
                      {{ $c->nombre }}
                    </option>
                  @endforeach
                </select>
              </div>

              <div class="col-md-12">
                <label for="cliente_nombre" class="form-label">Nombre del Cliente en Reporte <span class="text-danger">*</span></label>
                <input type="text" name="cliente_nombre" id="cliente_nombre" value="{{ old('cliente_nombre', $record->cliente_nombre) }}" class="form-control form-control-spgi" required>
              </div>

              <div class="col-md-6">
                <label for="factura_no" class="form-label">FCT. NO. <span class="text-danger">*</span></label>
                <input type="text" name="factura_no" id="factura_no" value="{{ old('factura_no', $record->factura_no) }}" class="form-control form-control-spgi" required>
              </div>

              <div class="col-md-6">
                <label for="nfc" class="form-label">NCF</label>
                <input type="text" name="nfc" id="nfc" value="{{ old('nfc', $record->nfc) }}" class="form-control form-control-spgi">
              </div>

              <div class="col-md-6">
                <label for="fecha" class="form-label">Fecha Emisión <span class="text-danger">*</span></label>
                <input type="date" name="fecha" id="fecha" value="{{ old('fecha', $record->fecha ? $record->fecha->format('Y-m-d') : '') }}" class="form-control form-control-spgi" required>
              </div>

              <div class="col-md-6">
                <label for="fecha_vencimiento" class="form-label">Fecha Vencimiento <span class="text-danger">*</span></label>
                <input type="date" name="fecha_vencimiento" id="fecha_vencimiento" value="{{ old('fecha_vencimiento', $record->fecha_vencimiento ? $record->fecha_vencimiento->format('Y-m-d') : '') }}" class="form-control form-control-spgi" required>
              </div>

              <div class="col-md-12">
                <label for="producto" class="form-label">Producto / Concepto <span class="text-danger">*</span></label>
                <input type="text" name="producto" id="producto" value="{{ old('producto', $record->producto) }}" class="form-control form-control-spgi" required>
              </div>

              <div class="col-md-4">
                <label for="moneda" class="form-label">Moneda <span class="text-danger">*</span></label>
                <select name="moneda" id="moneda" class="form-select form-select-spgi" required>
                  <option value="DOP" {{ old('moneda', $record->moneda) == 'DOP' ? 'selected' : '' }}>DOP</option>
                  <option value="USD" {{ old('moneda', $record->moneda) == 'USD' ? 'selected' : '' }}>USD</option>
                </select>
              </div>

              <div class="col-md-4">
                <label for="balance" class="form-label">Balance Facturado <span class="text-danger">*</span></label>
                <div class="input-group">
                  <span class="input-group-text bg-light text-muted fw-bold border-0 rounded-start-4 px-2.5" id="currency-addon">$</span>
                  <input type="number" step="0.01" name="balance" id="balance" value="{{ old('balance', $record->balance) }}" class="form-control form-control-spgi rounded-end-4" required>
                </div>
              </div>

              <div class="col-md-4" id="tasa_cambio_container">
                <label for="tasa_cambio" class="form-label">T.S (Tasa Cambio)</label>
                <input type="number" step="0.01" name="tasa_cambio" id="tasa_cambio" value="{{ old('tasa_cambio', $record->tasa_cambio) }}" class="form-control form-control-spgi">
              </div>
            </div>
          </div>
        </div>

        <!-- CARD 2: RECONCILIACIÓN -->
        <div class="col-lg-6 mt-4 mt-lg-0">
          <div class="d-flex align-items-center justify-content-between">
            <h5 class="fw-bold text-success mb-1 text-uppercase small">Conciliación de Pago (Cobro)</h5>
            <button type="button" class="btn btn-pay-today animate__animated animate__pulse animate__infinite animate__slower" id="btnPayToday">
              <i class="bi bi-lightning-fill"></i> Marcar Pago Hoy
            </button>
          </div>
          
          <div class="reconciliation-card animate__animated animate__fadeInRight h-100 mt-2">
            <div class="row g-3">
              
              <div class="col-md-6">
                <label for="fecha_pago" class="form-label">Fecha de Cobro / Pago</label>
                <input type="date" name="fecha_pago" id="fecha_pago" value="{{ old('fecha_pago', $record->fecha_pago ? $record->fecha_pago->format('Y-m-d') : '') }}" class="form-control form-control-spgi">
                <div class="form-text small text-muted">Cuándo ingresó el pago a banco o caja.</div>
              </div>

              <div class="col-md-6">
                <label for="fecha_aplicado" class="form-label">Fecha Aplicado</label>
                <input type="date" name="fecha_aplicado" id="fecha_aplicado" value="{{ old('fecha_aplicado', $record->fecha_aplicado ? $record->fecha_aplicado->format('Y-m-d') : '') }}" class="form-control form-control-spgi">
                <div class="form-text small text-muted">Fecha contable de la aplicación.</div>
              </div>

              <div class="col-md-6">
                <label for="recibo_no" class="form-label">Recibo de Cobro No.</label>
                <input type="text" name="recibo_no" id="recibo_no" value="{{ old('recibo_no', $record->recibo_no) }}" class="form-control form-control-spgi" placeholder="Ej: RC-102">
              </div>

              <div class="col-md-6">
                <label for="total_pagado" class="form-label">Monto Total Pagado (Diferencias)</label>
                <div class="input-group">
                  <span class="input-group-text bg-light text-muted fw-bold border-0 rounded-start-4 px-2.5" id="currency-addon-pay">$</span>
                  <input type="number" step="0.01" name="total_pagado" id="total_pagado" value="{{ old('total_pagado', $record->total_pagado) }}" class="form-control form-control-spgi rounded-end-4" placeholder="Dejar vacío para liquidar total">
                </div>
                <div class="form-text small text-muted">Suele ser igual al balance. Modifica si hay comisiones o ajustes.</div>
              </div>
              
              <div class="col-12 mt-4 text-center">
                <div class="alert alert-light border-0 shadow-sm rounded-4 text-start">
                  <h6 class="fw-bold mb-1"><i class="bi bi-info-circle text-primary me-2"></i> Lógica de Estados</h6>
                  <p class="mb-0 small text-muted">
                    - Si la <strong>Fecha de Pago</strong> se llena, el estado pasa a <strong class="text-success">PAGO</strong> y los días pasan a ser nulos.<br>
                    - Si se borra la <strong>Fecha de Pago</strong>, volverá a calcularse dinámicamente como <strong class="text-danger">VENCIDO</strong> o <strong class="text-warning">PENDIENTE</strong> según los días de vencimiento.
                  </p>
                </div>
              </div>

            </div>
          </div>
        </div>

      </div>

      <div class="divider border-top my-4 opacity-25"></div>

      <!-- Botones de Acción -->
      <div class="d-flex justify-content-end gap-3 mb-5">
        <a href="{{ route('estado-cuentas.index') }}" class="btn btn-outline-secondary rounded-pill px-4 py-2">
          Cancelar
        </a>
        <button type="submit" class="btn btn-spgi-save px-5 py-2">
          <i class="bi bi-check2-circle me-2"></i> Guardar Cambios
        </button>
      </div>

    </form>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const selectCliente = document.getElementById('cliente_maestro_id');
    const inputNombre = document.getElementById('cliente_nombre');
    const selectMoneda = document.getElementById('moneda');
    const inputTasaCambio = document.getElementById('tasa_cambio');
    const containerTasaCambio = document.getElementById('tasa_cambio_container');
    const inputBalance = document.getElementById('balance');
    const inputFechaPago = document.getElementById('fecha_pago');
    const inputFechaAplicado = document.getElementById('fecha_aplicado');
    const inputTotalPagado = document.getElementById('total_pagado');
    const currencyAddon = document.getElementById('currency-addon');
    const currencyAddonPay = document.getElementById('currency-addon-pay');
    const btnPayToday = document.getElementById('btnPayToday');

    // Autocomplete client name when master client selected
    selectCliente.addEventListener('change', function () {
      const selectedOption = this.options[this.selectedIndex];
      if (selectedOption.value) {
        inputNombre.value = selectedOption.getAttribute('data-nombre');
      }
    });

    // Toggle Tasa de Cambio visibility depending on currency
    function checkMoneda() {
      if (selectMoneda.value === 'USD') {
        containerTasaCambio.style.display = 'block';
        inputTasaCambio.setAttribute('required', 'required');
        currencyAddon.innerText = 'US$';
        currencyAddonPay.innerText = 'US$';
      } else {
        containerTasaCambio.style.display = 'none';
        inputTasaCambio.removeAttribute('required');
        currencyAddon.innerText = 'RD$';
        currencyAddonPay.innerText = 'RD$';
      }
    }

    selectMoneda.addEventListener('change', checkMoneda);
    checkMoneda(); // Run initially

    // Fast Mark Payment as Today action
    btnPayToday.addEventListener('click', function () {
      const todayStr = new Date().toISOString().substring(0, 10);
      inputFechaPago.value = todayStr;
      inputFechaAplicado.value = todayStr;
      inputTotalPagado.value = inputBalance.value;
      
      // Focus on receipt number for convenient input
      document.getElementById('recibo_no').focus();
    });

    // Auto-fill pay rules on fecha_pago input
    inputFechaPago.addEventListener('change', function () {
      if (this.value) {
        if (!inputFechaAplicado.value) {
          inputFechaAplicado.value = this.value;
        }
        if (!inputTotalPagado.value) {
          inputTotalPagado.value = inputBalance.value;
        }
      }
    });
  });
</script>
@endsection
