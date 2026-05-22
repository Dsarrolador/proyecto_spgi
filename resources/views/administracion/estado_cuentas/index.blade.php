@extends('layouts.app')

@section('page_title', 'Estado de Cuenta de Clientes')

@section('content')
<style>
  .spgi-bg{ padding: 12px 0 24px 0; }
  .btn-spgi{
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    border: 0; color: #fff !important; min-height:42px; border-radius:12px; padding:0 20px;
    box-shadow: 0 8px 20px rgba(59, 130, 246, 0.2); font-weight:700;
    display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s;
  }
  .btn-spgi:hover{ filter: brightness(1.1); transform: translateY(-1px); }
  
  .btn-pdf{
    background: linear-gradient(135deg, #ef4444, #dc2626);
    border: 0; color: #fff !important; min-height:42px; border-radius:12px; padding:0 20px;
    box-shadow: 0 8px 20px rgba(239, 68, 68, 0.2); font-weight:700;
    display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s;
  }
  .btn-pdf:hover{ filter: brightness(1.1); transform: translateY(-1px); }

  .spgi-toolbar{
    background: var(--bg-surface-glass); border: 1px solid var(--border-main);
    border-radius: 20px; box-shadow: var(--shadow-main); backdrop-filter: blur(16px);
    padding: 20px; margin-bottom: 24px;
  }
  
  .spgi-table-box{
    background: var(--bg-surface-glass); border: 1px solid var(--border-main);
    border-radius: 22px; box-shadow: var(--shadow-main); overflow: hidden; backdrop-filter: blur(16px);
    margin-bottom: 30px;
  }
  
  .spgi-table{ margin-bottom: 0; font-size: 0.85rem; }
  .spgi-table thead th{
    background: #0b1220; color: #fff; text-align:center;
    border-color: rgba(255,255,255,0.08) !important;
    font-size: 0.72rem; text-transform: uppercase; letter-spacing: 1px; padding: 12px 10px;
    font-weight: 700;
  }
  
  .spgi-table tbody td{ 
    border-color: var(--border-main) !important; 
    color: var(--text-main); 
    padding: 10px 8px; 
    text-align:center;
    vertical-align: middle;
  }
  
  .spgi-table tbody td.text-start { text-align:left; }
  .spgi-table tbody td.text-end { text-align:right; }
  
  /* Excel-like states styling */
  .row-pago {
    background-color: rgba(16, 185, 129, 0.06);
  }
  .row-vencido {
    background-color: rgba(239, 68, 68, 0.06);
  }
  .row-pendiente {
    background-color: rgba(245, 158, 11, 0.05);
  }
  
  .badge-pago {
    background-color: rgba(16, 185, 129, 0.15);
    color: #10b981;
    font-weight: 700;
    padding: 6px 12px;
    border-radius: 8px;
    border: 1px solid rgba(16, 185, 129, 0.3);
    font-size: 0.75rem;
    display: inline-block;
  }
  
  .badge-vencido {
    background-color: rgba(239, 68, 68, 0.15);
    color: #ef4444;
    font-weight: 700;
    padding: 6px 12px;
    border-radius: 8px;
    border: 1px solid rgba(239, 68, 68, 0.3);
    font-size: 0.75rem;
    display: inline-block;
  }
  
  .badge-pendiente {
    background-color: rgba(245, 158, 11, 0.15);
    color: #f59e0b;
    font-weight: 700;
    padding: 6px 12px;
    border-radius: 8px;
    border: 1px solid rgba(245, 158, 11, 0.3);
    font-size: 0.75rem;
    display: inline-block;
  }
  
  .dias-red {
    color: #ef4444;
    font-weight: 800;
  }
  .dias-green {
    color: #10b981;
    font-weight: 700;
  }
  
  .client-header-row {
    background: rgba(59, 130, 246, 0.08) !important;
    font-weight: 800;
    color: var(--text-main);
    font-size: 0.95rem;
    text-align: left !important;
  }
  
  .client-header-row td {
    text-align: left !important;
    padding: 12px 16px !important;
    border-bottom: 2px solid rgba(59, 130, 246, 0.2) !important;
  }
  
  .client-subtotal-row {
    background: rgba(0, 0, 0, 0.02) !important;
    font-weight: 700;
    font-size: 0.8rem;
    border-top: 1px solid var(--border-main);
    border-bottom: 2px solid var(--border-main);
  }
  
  .client-subtotal-row td {
    padding: 10px 8px !important;
  }
  
  .acciones .btn{ 
    width: 32px; 
    height: 32px; 
    display: inline-flex; 
    align-items: center; 
    justify-content: center; 
    padding: 0; 
    border-radius: 8px;
    transition: all 0.2s;
  }

  .form-control-spgi, .form-select-spgi {
    background: var(--bg-surface);
    border: 1px solid var(--border-main);
    color: var(--text-main);
    border-radius: 10px;
    min-height: 40px;
    font-size: 0.85rem;
  }
  
  .form-control-spgi:focus, .form-select-spgi:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.15);
    background: var(--bg-surface);
    color: var(--text-main);
  }
</style>

<div class="spgi-bg">
  <div class="container-fluid px-4">
    
    <!-- Encabezado -->
    <div class="d-flex align-items-center justify-content-between mb-4">
      <div>
        <h1 class="h3 mb-1 fw-bold text-gradient">Estado de Cuenta</h1>
        <p class="text-muted mb-0">Reconciliación de cobros, facturación y control de ingresos agrupado por clientes.</p>
      </div>
      <div class="d-flex gap-2">
        <a href="{{ route('administracion.bienvenido') }}" class="btn btn-outline-secondary rounded-pill px-4">
          <i class="bi bi-arrow-left me-2"></i> Dashboard Admin
        </a>
      </div>
    </div>

    <!-- Alertas -->
    @if(session('success'))
      <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4 animate__animated animate__fadeIn">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
      </div>
    @endif

    <!-- Barra de Filtros -->
    <div class="spgi-toolbar animate__animated animate__fadeInDown">
      <form action="{{ route('estado-cuentas.index') }}" method="GET" class="row g-3 align-items-end">
        <div class="col-md-3">
          <label class="form-label small fw-bold text-muted">Cliente</label>
          <input type="text" name="cliente_nombre" value="{{ request('cliente_nombre') }}" class="form-control form-control-spgi" placeholder="Buscar cliente..." list="clientesList">
          <datalist id="clientesList">
            @foreach($clientesFiltro as $nombre)
              <option value="{{ $nombre }}">
            @endforeach
          </datalist>
        </div>

        <div class="col-md-2">
          <label class="form-label small fw-bold text-muted">Estado</label>
          <select name="estado" class="form-select form-select-spgi">
            <option value="">-- Todos --</option>
            <option value="PAGO" {{ request('estado') === 'PAGO' ? 'selected' : '' }}>PAGO</option>
            <option value="PENDIENTE" {{ request('estado') === 'PENDIENTE' ? 'selected' : '' }}>PENDIENTE</option>
            <option value="VENCIDO" {{ request('estado') === 'VENCIDO' ? 'selected' : '' }}>VENCIDO</option>
          </select>
        </div>

        <div class="col-md-2">
          <label class="form-label small fw-bold text-muted">Moneda</label>
          <select name="moneda" class="form-select form-select-spgi">
            <option value="">-- Todas --</option>
            <option value="DOP" {{ request('moneda') === 'DOP' ? 'selected' : '' }}>DOP</option>
            <option value="USD" {{ request('moneda') === 'USD' ? 'selected' : '' }}>USD</option>
          </select>
        </div>

        <div class="col-md-2">
          <label class="form-label small fw-bold text-muted">Desde (Emisión)</label>
          <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}" class="form-control form-control-spgi">
        </div>

        <div class="col-md-2">
          <label class="form-label small fw-bold text-muted">Hasta</label>
          <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}" class="form-control form-control-spgi">
        </div>

        <div class="col-md-1 d-flex gap-2">
          <button type="submit" class="btn btn-primary rounded-3 w-100 fw-bold" style="min-height: 40px;" title="Buscar">
            <i class="bi bi-search"></i>
          </button>
          <a href="{{ route('estado-cuentas.index') }}" class="btn btn-outline-secondary rounded-3 w-100 d-flex align-items-center justify-content-center" style="min-height: 40px;" title="Limpiar Filtros">
            <i class="bi bi-x-lg"></i>
          </a>
        </div>
      </form>
      
      <div class="divider border-top my-3 opacity-25"></div>
      
      <!-- Acciones Globales -->
      <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
          <span class="badge bg-dark px-3 py-2 rounded-3 text-uppercase font-monospace small">
            FECHA DE HOY: {{ \Carbon\Carbon::today()->format('d/m/Y') }}
          </span>
        </div>
        <div class="d-flex gap-2">
          <a href="{{ route('estado-cuentas.pdf', request()->all()) }}" target="_blank" class="btn btn-pdf">
            <i class="bi bi-file-earmark-pdf-fill"></i> Exportar Excel / PDF
          </a>
          <a href="{{ route('estado-cuentas.create') }}" class="btn btn-spgi">
            <i class="bi bi-plus-lg"></i> Registrar Factura
          </a>
        </div>
      </div>
    </div>

    <!-- Cuadrícula Principal (Estilo Excel Premium) -->
    <div class="spgi-table-box animate__animated animate__fadeInUp">
      <div class="table-responsive">
        <table class="table spgi-table align-middle table-hover">
          <thead>
            <tr>
              <th style="min-width: 100px;">FCT. NO.</th>
              <th>NCF</th>
              <th>FECHA</th>
              <th>VENCIMIENTO</th>
              <th>PRODUCTO</th>
              <th class="text-end">BALANCE</th>
              <th>MONEDA</th>
              <th>T.S</th>
              <th>FECHA PAGO</th>
              <th>FECHA APL.</th>
              <th>RECIBO NO.</th>
              <th class="text-end">TOTAL PAGADO</th>
              <th>ESTADO</th>
              <th>DÍAS</th>
              <th style="width: 110px;">ACCIONES</th>
            </tr>
          </thead>
          <tbody>
            @php
              // Global totals for the current query results
              $globalDopBalance = 0;
              $globalUsdBalance = 0;
              $globalDopPagado = 0;
              $globalUsdPagado = 0;
            @endphp

            @forelse($groupedRecords as $cliente => $facturas)
              <!-- Cabecera del Cliente -->
              <tr class="client-header-row">
                <td colspan="15">
                  <i class="bi bi-person-fill text-primary me-2"></i> {{ $cliente }}
                </td>
              </tr>

              @php
                $subtotalDopBalance = 0;
                $subtotalUsdBalance = 0;
                $subtotalDopPagado = 0;
                $subtotalUsdPagado = 0;
              @endphp

              @foreach($facturas as $f)
                @php
                  // Accumulate subtotals
                  if ($f->moneda === 'DOP') {
                      $subtotalDopBalance += $f->balance;
                      $subtotalDopPagado += ($f->total_pagado ?? 0);
                      $globalDopBalance += $f->balance;
                      $globalDopPagado += ($f->total_pagado ?? 0);
                  } else {
                      $subtotalUsdBalance += $f->balance;
                      $subtotalUsdPagado += ($f->total_pagado ?? 0);
                      $globalUsdBalance += $f->balance;
                      $globalUsdPagado += ($f->total_pagado ?? 0);
                  }

                  // Classify row classes
                  $rowClass = '';
                  if ($f->estado_calculado === 'PAGO') {
                      $rowClass = 'row-pago';
                  } elseif ($f->estado_calculado === 'VENCIDO') {
                      $rowClass = 'row-vencido';
                  } else {
                      $rowClass = 'row-pendiente';
                  }
                @endphp
                <tr class="{{ $rowClass }}">
                  <td class="fw-bold font-monospace">{{ $f->factura_no }}</td>
                  <td class="font-monospace text-muted">{{ $f->nfc ?? 'N/A' }}</td>
                  <td>{{ $f->fecha ? $f->fecha->format('d/m/Y') : '' }}</td>
                  <td>{{ $f->fecha_vencimiento ? $f->fecha_vencimiento->format('d/m/Y') : '' }}</td>
                  <td class="text-start text-truncate" style="max-width: 180px;">{{ $f->producto }}</td>
                  <td class="text-end fw-bold font-monospace">
                    {{ number_format($f->balance, 2) }}
                  </td>
                  <td>
                    <span class="badge {{ $f->moneda === 'USD' ? 'bg-primary' : 'bg-secondary' }} rounded-2 small fw-bold">
                      {{ $f->moneda }}
                    </span>
                  </td>
                  <td class="font-monospace text-muted">
                    {{ $f->tasa_cambio ? number_format($f->tasa_cambio, 2) : '-' }}
                  </td>
                  <td>
                    @if($f->fecha_pago)
                      {{ $f->fecha_pago->format('d/m/Y') }}
                    @else
                      <span class="text-muted small">-</span>
                    @endif
                  </td>
                  <td>
                    @if($f->fecha_aplicado)
                      {{ $f->fecha_aplicado->format('d/m/Y') }}
                    @else
                      <span class="text-muted small">-</span>
                    @endif
                  </td>
                  <td class="font-monospace">{{ $f->recibo_no ?? '-' }}</td>
                  <td class="text-end fw-bold font-monospace text-success">
                    {{ $f->total_pagado ? number_format($f->total_pagado, 2) : '-' }}
                  </td>
                  <td>
                    @if($f->estado_calculado === 'PAGO')
                      <span class="badge-pago"><i class="bi bi-check2-circle"></i> PAGO</span>
                    @elseif($f->estado_calculado === 'VENCIDO')
                      <span class="badge-vencido"><i class="bi bi-exclamation-octagon"></i> VENCIDO</span>
                    @else
                      <span class="badge-pendiente"><i class="bi bi-clock"></i> PENDIENTE</span>
                    @endif
                  </td>
                  <td>
                    @if($f->dias === null)
                      <span class="text-muted font-monospace">-</span>
                    @elseif($f->dias < 0)
                      <span class="dias-red font-monospace" title="Vencida hace {{ abs($f->dias) }} días">{{ $f->dias }}</span>
                    @else
                      <span class="dias-green font-monospace" title="{{ $f->dias }} días para vencer">{{ $f->dias }}</span>
                    @endif
                  </td>
                  <td class="acciones text-center">
                    <a href="{{ route('estado-cuentas.edit', $f->id) }}" class="btn btn-outline-primary" title="Editar / Conciliar Pago">
                      <i class="bi bi-pencil-square"></i>
                    </a>
                    <form action="{{ route('estado-cuentas.destroy', $f->id) }}" method="POST" class="d-inline">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-outline-danger" onclick="return confirm('¿Seguro que deseas eliminar este registro de factura?')" title="Eliminar factura">
                        <i class="bi bi-trash"></i>
                      </button>
                    </form>
                  </td>
                </tr>
              @endforeach

              <!-- Subtotal del Cliente (Exactamente alineado como en Excel) -->
              @if($subtotalDopBalance > 0 || $subtotalDopPagado > 0)
                <tr class="client-subtotal-row">
                  <td class="text-start fw-bold text-uppercase">TOTAL</td>
                  <td colspan="4"></td>
                  <td class="text-end fw-bold font-monospace bg-light">{{ number_format($subtotalDopBalance, 2) }}</td>
                  <td class="fw-bold"><span class="badge bg-secondary rounded-1 font-monospace">DOP</span></td>
                  <td colspan="4"></td>
                  <td class="text-end fw-bold font-monospace text-success bg-light">{{ number_format($subtotalDopPagado, 2) }}</td>
                  <td colspan="3"></td>
                </tr>
              @endif
              @if($subtotalUsdBalance > 0 || $subtotalUsdPagado > 0)
                <tr class="client-subtotal-row">
                  <td class="text-start fw-bold text-uppercase">TOTAL</td>
                  <td colspan="4"></td>
                  <td class="text-end fw-bold font-monospace bg-light">{{ number_format($subtotalUsdBalance, 2) }}</td>
                  <td class="fw-bold"><span class="badge bg-primary rounded-1 font-monospace text-white">USD</span></td>
                  <td colspan="4"></td>
                  <td class="text-end fw-bold font-monospace text-success bg-light">{{ number_format($subtotalUsdPagado, 2) }}</td>
                  <td colspan="3"></td>
                </tr>
              @endif
            @empty
              <tr>
                <td colspan="15" class="text-center py-5 text-muted">
                  <i class="bi bi-wallet2 display-4 d-block mb-3 opacity-50"></i>
                  <p class="mb-0">No se encontraron facturas o ingresos registrados con los filtros aplicados.</p>
                </td>
              </tr>
            @endforelse
          </tbody>

          @if($groupedRecords->isNotEmpty())
            <!-- Totales Generales de la Consulta (Alineado a las columnas correctas) -->
            <tfoot>
              @if($globalDopBalance > 0 || $globalDopPagado > 0)
                <tr class="bg-dark text-white fw-bold table-dark">
                  <td class="text-start text-uppercase py-3">TOTAL GENERAL</td>
                  <td colspan="4" class="py-3"></td>
                  <td class="text-end fw-bold font-monospace py-3 text-warning">{{ number_format($globalDopBalance, 2) }}</td>
                  <td class="fw-bold py-3"><span class="badge bg-light text-dark rounded-1 font-monospace">DOP</span></td>
                  <td colspan="4" class="py-3"></td>
                  <td class="text-end fw-bold font-monospace text-success py-3">{{ number_format($globalDopPagado, 2) }}</td>
                  <td colspan="3" class="py-3"></td>
                </tr>
              @endif
              @if($globalUsdBalance > 0 || $globalUsdPagado > 0)
                <tr class="bg-dark text-white fw-bold table-dark">
                  <td class="text-start text-uppercase py-3">TOTAL GENERAL</td>
                  <td colspan="4" class="py-3"></td>
                  <td class="text-end fw-bold font-monospace py-3 text-warning">{{ number_format($globalUsdBalance, 2) }}</td>
                  <td class="fw-bold py-3"><span class="badge bg-primary rounded-1 font-monospace text-white">USD</span></td>
                  <td colspan="4" class="py-3"></td>
                  <td class="text-end fw-bold font-monospace text-success py-3">{{ number_format($globalUsdPagado, 2) }}</td>
                  <td colspan="3" class="py-3"></td>
                </tr>
              @endif
            </tfoot>
          @endif
        </table>
      </div>
    </div>

  </div>
</div>
@endsection
