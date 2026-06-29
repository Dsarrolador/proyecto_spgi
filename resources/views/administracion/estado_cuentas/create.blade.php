@extends('layouts.app')

@section('page_title', 'Registrar Factura en Estado de Cuenta')

@section('content')
<style>
  .spgi-bg{ padding: 12px 0 24px 0; }
  .form-card{
    background: var(--bg-surface-glass); border: 1px solid var(--border-main);
    border-radius: 24px; box-shadow: var(--shadow-main); backdrop-filter: blur(16px);
    padding: 30px; margin-top: 20px;
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
</style>

<div class="spgi-bg">
  <div class="container" style="max-width: 800px;">
    
    <!-- Encabezado -->
    <div class="d-flex align-items-center justify-content-between mb-4">
      <div>
        <h1 class="h3 mb-1 fw-bold text-gradient">Registrar Factura</h1>
        <p class="text-muted mb-0">Agregar un nuevo registro de facturación para el control de cuentas por cobrar.</p>
      </div>
      <a href="{{ route('estado-cuentas.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
        <i class="bi bi-arrow-left me-2"></i> Cancelar
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

    <!-- Formulario -->
    <div class="form-card animate__animated animate__fadeInUp">
      <form action="{{ route('estado-cuentas.store') }}" method="POST" id="facturaForm">
        @csrf

        <div class="row g-4">
          
          <!-- Cliente Maestro Autocomplete -->
          <div class="col-md-6">
            <label for="cliente_maestro_id" class="form-label">Cliente Maestro (Catálogo)</label>
            <select name="cliente_maestro_id" id="cliente_maestro_id" class="form-select form-select-spgi">
              <option value="">-- Seleccionar cliente del sistema (Opcional) --</option>
              @foreach($clientesMaestros as $c)
                <option value="{{ $c->id }}" {{ old('cliente_maestro_id') == $c->id ? 'selected' : '' }} data-nombre="{{ $c->nombre }}">
                  {{ $c->nombre }}
                </option>
              @endforeach
            </select>
            <div class="form-text small text-muted">Vincular con un cliente del catálogo para mantener la consistencia.</div>
          </div>

          <!-- Nombre Visible del Cliente -->
          <div class="col-md-6">
            <label for="cliente_nombre" class="form-label">Nombre del Cliente en Reporte <span class="text-danger">*</span></label>
            <input type="text" name="cliente_nombre" id="cliente_nombre" value="{{ old('cliente_nombre') }}" class="form-control form-control-spgi" placeholder="Ej: Ocean Brill" required>
            <div class="form-text small text-muted">Nombre que se usará para agrupar en el reporte de Estado de Cuenta.</div>
          </div>

          <div class="col-md-6">
            <label for="factura_no" class="form-label">FCT. NO. (Número de Factura) <span class="text-danger">*</span></label>
            <input type="text" name="factura_no" id="factura_no" value="{{ old('factura_no') }}" class="form-control form-control-spgi" placeholder="Ej: A1023" required>
          </div>

          <div class="col-md-6">
            <label for="nfc" class="form-label">NCF (Comprobante Fiscal)</label>
            <input type="text" name="nfc" id="nfc" value="{{ old('nfc') }}" class="form-control form-control-spgi" placeholder="Ej: B0100001212">
          </div>

          <div class="col-md-6">
            <label for="fecha" class="form-label">Fecha de Emisión <span class="text-danger">*</span></label>
            <input type="date" name="fecha" id="fecha" value="{{ old('fecha', date('Y-m-d')) }}" class="form-control form-control-spgi" required>
          </div>

          <div class="col-md-6">
            <label for="fecha_vencimiento" class="form-label">Fecha de Vencimiento</label>
            <input type="date" name="fecha_vencimiento" id="fecha_vencimiento" value="{{ old('fecha_vencimiento') }}" class="form-control form-control-spgi">
            <div class="form-text small text-muted">Se asignará automáticamente a 30 días si se deja en blanco.</div>
          </div>

          <div class="col-md-12">
            <label for="producto" class="form-label">Producto / Servicio (Detalle o Concepto) <span class="text-danger">*</span></label>
            <input type="text" name="producto" id="producto" value="{{ old('producto') }}" class="form-control form-control-spgi" placeholder="Ej: IGUALA SOPORTE MENSUAL" required>
          </div>

          <div class="col-md-4">
            <label for="moneda" class="form-label">Moneda <span class="text-danger">*</span></label>
            <select name="moneda" id="moneda" class="form-select form-select-spgi" required>
              <option value="DOP" {{ old('moneda', 'DOP') == 'DOP' ? 'selected' : '' }}>DOP (Pesos Dominicanos)</option>
              <option value="USD" {{ old('moneda') == 'USD' ? 'selected' : '' }}>USD (Dólares)</option>
            </select>
          </div>

          <div class="col-md-4">
            <label for="balance" class="form-label">Balance (Monto Facturado) <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text bg-light text-muted fw-bold border-0 rounded-start-4 px-3" id="currency-addon">$</span>
              <input type="number" step="0.01" name="balance" id="balance" value="{{ old('balance') }}" class="form-control form-control-spgi rounded-end-4" placeholder="0.00" required>
            </div>
          </div>

          <!-- T.S (Tasa de cambio) -->
          <div class="col-md-4" id="tasa_cambio_container" style="display: none;">
            <label for="tasa_cambio" class="form-label">T.S (Tasa de Cambio) <span class="text-danger">*</span></label>
            <input type="number" step="0.01" name="tasa_cambio" id="tasa_cambio" value="{{ old('tasa_cambio', '60.00') }}" class="form-control form-control-spgi" placeholder="60.00">
          </div>

        </div>

        <div class="divider border-top my-4 opacity-25"></div>

        <!-- Botones de Acción -->
        <div class="d-flex justify-content-end gap-3">
          <a href="{{ route('estado-cuentas.index') }}" class="btn btn-outline-secondary rounded-pill px-4 py-2">
            Cancelar
          </a>
          <button type="submit" class="btn btn-spgi-save px-5 py-2">
            <i class="bi bi-save me-2"></i> Guardar Factura
          </button>
        </div>

      </form>
    </div>

  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const selectCliente = document.getElementById('cliente_maestro_id');
    const inputNombre = document.getElementById('cliente_nombre');
    const selectMoneda = document.getElementById('moneda');
    const inputTasaCambio = document.getElementById('tasa_cambio');
    const containerTasaCambio = document.getElementById('tasa_cambio_container');
    const inputFechaEmision = document.getElementById('fecha');
    const inputFechaVencimiento = document.getElementById('fecha_vencimiento');
    const currencyAddon = document.getElementById('currency-addon');

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
      } else {
        containerTasaCambio.style.display = 'none';
        inputTasaCambio.removeAttribute('required');
        inputTasaCambio.value = '';
        currencyAddon.innerText = 'RD$';
      }
    }

    selectMoneda.addEventListener('change', checkMoneda);
    checkMoneda(); // Run initially

    // Automatically set expiration date to emission date + 30 days
    function setExpirationDefault() {
      if (inputFechaEmision.value && !inputFechaVencimiento.value) {
        const emissionDate = new Date(inputFechaEmision.value + 'T00:00:00');
        if (!isNaN(emissionDate.getTime())) {
          emissionDate.setDate(emissionDate.getDate() + 30);
          
          // Format as YYYY-MM-DD
          const year = emissionDate.getFullYear();
          const month = String(emissionDate.getMonth() + 1).padStart(2, '0');
          const day = String(emissionDate.getDate()).padStart(2, '0');
          
          inputFechaVencimiento.value = `${year}-${month}-${day}`;
        }
      }
    }

    inputFechaEmision.addEventListener('change', setExpirationDefault);
    setExpirationDefault(); // Run initially
  });
</script>
@endsection
