<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rendición de Gastos - {{ $rendicion->titulo }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .header {
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 18px;
            margin: 0 0 5px 0;
            color: #1e3a8a;
            text-transform: uppercase;
        }
        .header p {
            margin: 0;
            color: #666;
            font-size: 12px;
        }
        .meta-info {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .meta-info td {
            padding: 4px 0;
            font-size: 11px;
        }
        .meta-info .label {
            font-weight: bold;
            color: #4b5563;
            width: 12%;
        }
        .meta-info .value {
            width: 38%;
        }
        
        table.expenses-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table.expenses-table th {
            background-color: #f3f4f6;
            color: #1f2937;
            font-weight: bold;
            border: 1px solid #d1d5db;
            padding: 8px 6px;
            font-size: 10px;
            text-align: center;
        }
        table.expenses-table td {
            border: 1px solid #d1d5db;
            padding: 8px 6px;
            vertical-align: middle;
            font-size: 10px;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .text-left {
            text-align: left;
        }
        .totals-row {
            background-color: #f9fafb;
            font-weight: bold;
        }
        .totals-row td {
            border-top: 2px solid #9ca3af;
            border-bottom: 2px solid #9ca3af;
        }
        .footer {
            margin-top: 90px;
            width: 100%;
        }
        .signature-box {
            width: 100%;
            text-align: center;
            border-top: 1px solid #9ca3af;
            padding-top: 8px;
            font-size: 10px;
            color: #4b5563;
        }
        .observations-section {
            margin-top: 20px;
            padding: 8px 10px;
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-left: 4px solid #1e3a8a;
        }
        .observations-section h3 {
            margin: 0 0 4px 0;
            font-size: 10px;
            color: #1e3a8a;
            text-transform: uppercase;
        }
        .observations-section p {
            margin: 0;
            font-size: 10px;
            color: #4b5563;
            line-height: 1.4;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Rendición de Gastos</h1>
        <p>{{ $rendicion->titulo }}</p>
    </div>

    <table class="meta-info">
        <tr>
            <td class="label">Reportado por:</td>
            <td class="value" colspan="3">{{ $rendicion->user->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Estado:</td>
            <td class="value">{{ $rendicion->estado }}</td>
            <td class="label">Fecha Reporte:</td>
            <td class="value">{{ $rendicion->created_at->format('d/m/Y') }}</td>
        </tr>
        @if($rendicion->fecha_aprobacion)
        <tr>
            <td class="label">Fecha Aprobación:</td>
            <td class="value" colspan="3">{{ $rendicion->fecha_aprobacion->format('d/m/Y') }}</td>
        </tr>
        @endif
    </table>

    <table class="expenses-table">
        <thead>
            <tr>
                <th style="width: 8%;">Fecha</th>
                <th style="width: 22%;">Concepto / Descripción</th>
                <th style="width: 18%;">Proveedor / Lugar</th>
                <th style="width: 10%;">Monto (RD$)</th>
                <th style="width: 10%;">Forma de Pago<br>Efectivo</th>
                <th style="width: 12%;">Forma de Pago<br>Tarjeta</th>
                <th style="width: 10%;">Reembolso / Otros</th>
                <th style="width: 10%;">Observaciones</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalMonto = 0;
                $totalEfectivo = 0;
                $totalTarjeta = 0;
                $totalReembolso = 0;
            @endphp
            @foreach($rendicion->gastos as $g)
                @php
                    $monto = $g->monto;
                    $efectivo = 0;
                    $tarjeta = 0;
                    $reembolso = 0;

                    if ($g->metodoPago) {
                        if ($g->metodoPago->nombre == 'Efectivo') {
                            $efectivo = $monto;
                        } elseif ($g->metodoPago->requiere_tarjeta) {
                            $tarjeta = $monto;
                        } else {
                            $reembolso = $monto;
                        }
                    }

                    $totalMonto += $monto;
                    $totalEfectivo += $efectivo;
                    $totalTarjeta += $tarjeta;
                    $totalReembolso += $reembolso;
                @endphp
                <tr>
                    <td class="text-center">{{ $g->fecha->format('d/m/Y') }}</td>
                    <td class="text-left">{{ $g->concepto }}</td>
                    <td class="text-left">{{ $g->proveedor }}</td>
                    <td class="text-right">RD$ {{ number_format($monto, 2) }}</td>
                    <td class="text-right">{{ $efectivo > 0 ? 'RD$ ' . number_format($efectivo, 2) : '' }}</td>
                    <td class="text-right">
                        @if($tarjeta > 0)
                            RD$ {{ number_format($tarjeta, 2) }}
                            @if($g->tarjeta_ultimos_4)
                                <br><small style="color: #666; font-size: 8px;">({{ $g->tarjeta_ultimos_4 }})</small>
                            @endif
                        @endif
                    </td>
                    <td class="text-right">{{ $reembolso > 0 ? 'RD$ ' . number_format($reembolso, 2) : '' }}</td>
                    <td class="text-left" style="font-size: 9px; color: #555;">{{ $g->observaciones ?? '' }}</td>
                </tr>
            @endforeach
            
            <tr class="totals-row">
                <td colspan="3" class="text-right">Totales</td>
                <td class="text-right">RD$ {{ number_format($totalMonto, 2) }}</td>
                <td class="text-right">RD$ {{ number_format($totalEfectivo, 2) }}</td>
                <td class="text-right">RD$ {{ number_format($totalTarjeta, 2) }}</td>
                <td class="text-right">RD$ {{ number_format($totalReembolso, 2) }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>

    @if($rendicion->observaciones)
    <div class="observations-section">
        <h3>Observaciones Generales</h3>
        <p>{{ $rendicion->observaciones }}</p>
    </div>
    @endif

    <table class="footer" style="width: 100%;">
        <tr>
            <td style="width: 30%;">
                <div class="signature-box">
                    Firma Responsable
                </div>
            </td>
            <td style="width: 5%;"></td>
            <td style="width: 30%;">
                <div class="signature-box">
                    Firma Aprobada
                </div>
            </td>
            <td style="width: 5%;"></td>
            <td style="width: 30%;">
                <div class="signature-box">
                    Firma Gerente General
                </div>
            </td>
        </tr>
    </table>

</body>
</html>
