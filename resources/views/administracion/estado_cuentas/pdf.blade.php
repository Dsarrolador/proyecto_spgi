<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Estado de Cuenta de Clientes</title>
    <style>
        @page {
            margin: 20px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 8.5px;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .header-container {
            border: 2px solid #0f172a;
            border-radius: 8px;
            padding: 10px 15px;
            margin-bottom: 15px;
            text-align: center;
            background-color: #fafafa;
        }
        .header-container h1 {
            font-size: 15px;
            margin: 0 0 3px 0;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header-container p {
            margin: 1px 0;
            color: #4b5563;
            font-size: 9px;
        }
        .header-info-table {
            width: 100%;
            margin-top: 8px;
            border-top: 1px solid #e2e8f0;
            padding-top: 5px;
        }
        .header-info-table td {
            font-size: 8.5px;
            padding: 2px 0;
        }
        
        table.excel-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        table.excel-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-weight: bold;
            border: 1px solid #334155;
            padding: 6px 4px;
            font-size: 8px;
            text-align: center;
            text-transform: uppercase;
        }
        table.excel-table td {
            border: 1px solid #cbd5e1;
            padding: 5px 4px;
            vertical-align: middle;
            font-size: 7.8px;
            text-align: center;
        }
        table.excel-table td.text-start {
            text-align: left;
        }
        table.excel-table td.text-end {
            text-align: right;
        }
        
        /* Row background colors */
        .row-pago {
            background-color: #f0fdf4; /* Very light green */
        }
        .row-vencido {
            background-color: #fef2f2; /* Very light red */
        }
        .row-pendiente {
            background-color: #fffbeb; /* Very light amber */
        }
        
        /* Pastel status pills for DomPDF */
        .status-pago {
            background-color: #d1fae5;
            color: #065f46;
            font-weight: bold;
            padding: 2px 5px;
            border-radius: 4px;
            border: 1px solid #a7f3d0;
            font-size: 7.5px;
            display: inline-block;
        }
        .status-vencido {
            background-color: #fee2e2;
            color: #991b1b;
            font-weight: bold;
            padding: 2px 5px;
            border-radius: 4px;
            border: 1px solid #fecaca;
            font-size: 7.5px;
            display: inline-block;
        }
        .status-pendiente {
            background-color: #fef3c7;
            color: #92400e;
            font-weight: bold;
            padding: 2px 5px;
            border-radius: 4px;
            border: 1px solid #fde68a;
            font-size: 7.5px;
            display: inline-block;
        }

        .dias-red {
            color: #b91c1c;
            font-weight: bold;
        }
        .dias-green {
            color: #047857;
            font-weight: bold;
        }
        
        .client-header-row {
            background-color: #f1f5f9;
            font-weight: bold;
            font-size: 9px;
            text-align: left !important;
        }
        .client-header-row td {
            text-align: left !important;
            padding: 6px 8px !important;
            border-bottom: 2px solid #cbd5e1 !important;
        }
        
        .client-subtotal-row {
            background-color: #f8fafc;
            font-weight: bold;
            font-size: 7.5px;
            border-top: 1px solid #cbd5e1;
            border-bottom: 2px solid #94a3b8;
        }
        .client-subtotal-row td {
            padding: 6px 4px !important;
        }
        
        .totals-row {
            background-color: #0f172a;
            color: #ffffff;
            font-weight: bold;
            font-size: 8px;
        }
        .totals-row td {
            border: 1px solid #334155 !important;
            padding: 8px 6px !important;
        }
        
        .currency-pill {
            background-color: #e2e8f0;
            color: #334155;
            padding: 1px 3px;
            border-radius: 2px;
            font-weight: bold;
            font-size: 7px;
        }
        .currency-pill-usd {
            background-color: #dbeafe;
            color: #1e40af;
            padding: 1px 3px;
            border-radius: 2px;
            font-weight: bold;
            font-size: 7px;
        }
    </style>
</head>
<body>

    <!-- Header Box -->
    <div class="header-container">
        <h1>INTECSOL, SRL</h1>
        <p><strong>RNC: 1-3027434-7</strong></p>
        <p>Ave. Nuñez de Cáceres No. 250, Res. M+B Apto. 2B, El Millón Sto. Dgo</p>
        <p>TELÉFONO: 829-598-0119 &nbsp;|&nbsp; EMAIL: info@intecsol.com</p>
        
        <table class="header-info-table">
            <tr>
                <td style="width: 50%; text-align: left; font-weight: bold;">
                    CONTROL DE ESTADO DE CUENTAS POR COBRAR (CLIENTES)
                </td>
                <td style="width: 50%; text-align: right; font-weight: bold; text-transform: uppercase;">
                    FECHA DEL REPORTE: {{ $fechaReporte }}
                </td>
            </tr>
        </table>
    </div>

    <!-- Spreadsheet Table -->
    <table class="excel-table">
        <thead>
            <tr>
                <th style="width: 8%;">FCT. NO.</th>
                <th style="width: 10%;">NCF</th>
                <th style="width: 8%;">FECHA</th>
                <th style="width: 8%;">VENC.</th>
                <th style="width: 15%;">PRODUCTO</th>
                <th style="width: 9%; text-align: right;">BALANCE</th>
                <th style="width: 4%;">MON.</th>
                <th style="width: 5%;">T.S</th>
                <th style="width: 8%;">F. PAGO</th>
                <th style="width: 8%;">F. APLIC.</th>
                <th style="width: 8%;">RECIBO</th>
                <th style="width: 9%; text-align: right;">PAGADO</th>
                <th style="width: 8%;">ESTADO</th>
                <th style="width: 4%;">DÍAS</th>
            </tr>
        </thead>
        <tbody>
            @php
              $globalDopBalance = 0;
              $globalUsdBalance = 0;
              $globalDopPagado = 0;
              $globalUsdPagado = 0;
            @endphp

            @foreach($groupedRecords as $cliente => $facturas)
                <!-- Group Header -->
                <tr class="client-header-row">
                    <td colspan="14">
                        CLIENTE: {{ $cliente }}
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
                        <td style="font-weight: bold;">{{ $f->factura_no }}</td>
                        <td style="color: #4b5563;">{{ $f->nfc ?? 'N/A' }}</td>
                        <td>{{ $f->fecha ? $f->fecha->format('d/m/Y') : '' }}</td>
                        <td>{{ $f->fecha_vencimiento ? $f->fecha_vencimiento->format('d/m/Y') : '' }}</td>
                        <td class="text-start">{{ $f->producto }}</td>
                        <td class="text-end" style="font-weight: bold;">
                            {{ number_format($f->balance, 2) }}
                        </td>
                        <td>
                            <span class="{{ $f->moneda === 'USD' ? 'currency-pill-usd' : 'currency-pill' }}">
                                {{ $f->moneda }}
                            </span>
                        </td>
                        <td style="color: #4b5563;">
                            {{ $f->tasa_cambio ? number_format($f->tasa_cambio, 2) : '-' }}
                        </td>
                        <td>
                            {{ $f->fecha_pago ? $f->fecha_pago->format('d/m/Y') : '-' }}
                        </td>
                        <td>
                            {{ $f->fecha_aplicado ? $f->fecha_aplicado->format('d/m/Y') : '-' }}
                        </td>
                        <td>{{ $f->recibo_no ?? '-' }}</td>
                        <td class="text-end" style="font-weight: bold; color: #15803d;">
                            {{ $f->total_pagado ? number_format($f->total_pagado, 2) : '-' }}
                        </td>
                        <td>
                            @if($f->estado_calculado === 'PAGO')
                                <span class="status-pago">PAGO</span>
                            @elseif($f->estado_calculado === 'VENCIDO')
                                <span class="status-vencido">VENCIDO</span>
                            @else
                                <span class="status-pendiente">PENDIENTE</span>
                            @endif
                        </td>
                        <td>
                            @if($f->dias === null)
                                <span style="color: #64748b;">-</span>
                            @elseif($f->dias < 0)
                                <span class="dias-red">{{ $f->dias }}</span>
                            @else
                                <span class="dias-green">{{ $f->dias }}</span>
                            @endif
                        </td>
                    </tr>
                @endforeach

                <!-- Subtotals aligned with Excel columns -->
                @if($subtotalDopBalance > 0 || $subtotalDopPagado > 0)
                    <tr class="client-subtotal-row">
                        <td class="text-start" style="text-transform: uppercase;">TOTAL</td>
                        <td colspan="4"></td>
                        <td class="text-end" style="font-weight: bold;">{{ number_format($subtotalDopBalance, 2) }}</td>
                        <td style="font-weight: bold;"><span class="currency-pill">DOP</span></td>
                        <td colspan="4"></td>
                        <td class="text-end" style="font-weight: bold; color: #15803d;">{{ number_format($subtotalDopPagado, 2) }}</td>
                        <td colspan="2"></td>
                    </tr>
                @endif
                @if($subtotalUsdBalance > 0 || $subtotalUsdPagado > 0)
                    <tr class="client-subtotal-row">
                        <td class="text-start" style="text-transform: uppercase;">TOTAL</td>
                        <td colspan="4"></td>
                        <td class="text-end" style="font-weight: bold;">{{ number_format($subtotalUsdBalance, 2) }}</td>
                        <td style="font-weight: bold;"><span class="currency-pill-usd">USD</span></td>
                        <td colspan="4"></td>
                        <td class="text-end" style="font-weight: bold; color: #15803d;">{{ number_format($subtotalUsdPagado, 2) }}</td>
                        <td colspan="2"></td>
                    </tr>
                @endif
            @endforeach

            <!-- Gran Total aligned with Excel columns -->
            @if($groupedRecords->isNotEmpty())
                @if($globalDopBalance > 0 || $globalDopPagado > 0)
                    <tr class="totals-row">
                        <td class="text-start" style="text-transform: uppercase; font-size: 8px;">TOTAL GENERAL</td>
                        <td colspan="4"></td>
                        <td class="text-end" style="font-weight: bold; color: #facc15;">{{ number_format($globalDopBalance, 2) }}</td>
                        <td style="font-weight: bold;"><span style="background-color: #ffffff; color: #0f172a; padding: 1px 3px; border-radius: 2px; font-size: 7px;">DOP</span></td>
                        <td colspan="4"></td>
                        <td class="text-end" style="font-weight: bold; color: #4ade80;">{{ number_format($globalDopPagado, 2) }}</td>
                        <td colspan="2"></td>
                    </tr>
                @endif
                @if($globalUsdBalance > 0 || $globalUsdPagado > 0)
                    <tr class="totals-row">
                        <td class="text-start" style="text-transform: uppercase; font-size: 8px;">TOTAL GENERAL</td>
                        <td colspan="4"></td>
                        <td class="text-end" style="font-weight: bold; color: #facc15;">{{ number_format($globalUsdBalance, 2) }}</td>
                        <td style="font-weight: bold;"><span style="background-color: #3b82f6; color: #ffffff; padding: 1px 3px; border-radius: 2px; font-size: 7px;">USD</span></td>
                        <td colspan="4"></td>
                        <td class="text-end" style="font-weight: bold; color: #4ade80;">{{ number_format($globalUsdPagado, 2) }}</td>
                        <td colspan="2"></td>
                    </tr>
                @endif
            @endif
        </tbody>
    </table>

</body>
</html>
