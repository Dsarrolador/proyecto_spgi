<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Planilla de Horas Extras - {{ $planilla->titulo }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .header-container {
            border: 2px solid #333;
            border-radius: 8px;
            padding: 12px 20px;
            margin-bottom: 20px;
            text-align: center;
        }
        .header-container h1 {
            font-size: 16px;
            margin: 0 0 3px 0;
            font-weight: 800;
            color: #000;
            text-transform: uppercase;
        }
        .header-container p {
            margin: 2px 0;
            color: #333;
            font-size: 10px;
        }
        .header-info-table {
            width: 100%;
            margin-top: 10px;
            border-top: 1px solid #ccc;
            padding-top: 8px;
        }
        .header-info-table td {
            font-size: 9px;
            padding: 2px 0;
        }
        
        .law-container {
            width: 100%;
            margin-bottom: 15px;
        }
        .law-desc {
            width: 60%;
            vertical-align: top;
            font-size: 8.5px;
            color: #555;
            line-height: 1.3;
        }
        .law-table-wrapper {
            width: 40%;
            text-align: right;
            vertical-align: top;
        }
        table.law-table {
            width: 240px;
            border-collapse: collapse;
            margin-left: auto;
        }
        table.law-table th {
            background-color: #1e293b;
            color: #ffffff;
            font-weight: bold;
            border: 1px solid #475569;
            padding: 4px 6px;
            font-size: 8px;
            text-align: center;
        }
        table.law-table td {
            border: 1px solid #cbd5e1;
            padding: 4px 6px;
            text-align: center;
            font-size: 8px;
            font-weight: bold;
        }
        
        table.excel-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table.excel-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-weight: bold;
            border: 1px solid #334155;
            padding: 8px 6px;
            font-size: 9px;
            text-align: center;
        }
        table.excel-table td {
            border: 1px solid #94a3b8;
            padding: 8px 6px;
            vertical-align: middle;
            font-size: 9px;
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
            background-color: #f8fafc;
            font-weight: bold;
        }
        .totals-row td {
            border-top: 2px solid #475569;
            border-bottom: 2px solid #475569;
            font-size: 9px;
            padding: 10px 6px;
        }
        
        .observations-section {
            margin-top: 20px;
            padding: 8px 10px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 4px solid #3b82f6;
        }
        .observations-section h3 {
            margin: 0 0 4px 0;
            font-size: 9px;
            color: #1e3a8a;
            text-transform: uppercase;
        }
        .observations-section p {
            margin: 0;
            font-size: 9px;
            color: #4b5563;
            line-height: 1.4;
        }

        .footer-signatures {
            margin-top: 60px;
            width: 100%;
        }
        .signature-cell {
            width: 40%;
            text-align: center;
            vertical-align: bottom;
        }
        .signature-line {
            width: 220px;
            border-top: 1px solid #333;
            margin: 0 auto 5px auto;
        }
        .signature-title {
            font-size: 8.5px;
            font-weight: bold;
            text-transform: uppercase;
            color: #4b5563;
        }
        .signature-subtitle {
            font-size: 8px;
            color: #64748b;
        }
    </style>
</head>
<body>

    <!-- Header Box -->
    <div class="header-container">
        <h1>INTECSOL, SRL</h1>
        <p><strong>RNC: 1-3027434-7</strong></p>
        <p>Ave. Nuñez de Cáceres No. 250, Res. M+B Apto. 2B, El Millón Sto. Dgo</p>
        <p>TELEFONO: 829-598-0119</p>
        
        <table class="header-info-table">
            <tr>
                <td style="width: 50%; text-align: left;">
                    <strong>PLANILLA DE HORAS EXTRAS TRABAJADAS:</strong> {{ $planilla->titulo }}
                </td>
                <td style="width: 50%; text-align: right;">
                    <strong>Fecha Registro:</strong> {{ $planilla->fecha_registro->format('d/m/Y') }} &nbsp;|&nbsp; 
                    <strong>Estado:</strong> {{ strtoupper($planilla->estado) }}
                </td>
            </tr>
        </table>
    </div>

    <!-- Law Reference Info -->
    <table class="law-container">
        <tr>
            <td class="law-desc">
                <strong>CÓDIGO DE TRABAJO REP. DOM. ARTÍCULO 203:</strong><br>
                El pago de las horas extraordinarias se rige por la normativa laboral del país. La jornada semanal estándar cuenta con un recargo correspondiente al 35% y al 100% sobre la tarifa de la hora ordinaria en función de los límites estipulados.
            </td>
            <td class="law-table-wrapper">
                <table class="law-table">
                    <thead>
                        <tr>
                            <th>TOPE DE HORAS</th>
                            <th>% A AUMENTAR</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>DE 45-68 HORAS</td>
                            <td>35%</td>
                        </tr>
                        <tr>
                            <td>DE 69 EN ADELANTE</td>
                            <td>100%</td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

    <!-- Planilla Grid Table -->
    <table class="excel-table">
        <thead>
            <tr>
                <th style="width: 10%;">FECHA</th>
                <th style="width: 23%;">COLABORADOR</th>
                <th style="width: 23%;">CONCEPTO</th>
                <th style="width: 10%;">HORA INICIO</th>
                <th style="width: 10%;">HORA SALIDA</th>
                <th style="width: 8%;">TOTAL HORAS</th>
                <th style="width: 16%;">TARIFA / HORA (A LAPIZERO)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($planilla->detalles as $det)
            <tr>
                <td class="text-center">{{ $det->fecha->format('d/m/Y') }}</td>
                <td class="text-left" style="font-weight: bold;">{{ $det->colaborador }}</td>
                <td class="text-left" style="color: #475569;">{{ $det->concepto }}</td>
                <td class="text-center">{{ date('h:i A', strtotime($det->hora_inicio)) }}</td>
                <td class="text-center">{{ date('h:i A', strtotime($det->hora_salida)) }}</td>
                <td class="text-center" style="font-weight: bold; color: #1e3a8a;">{{ number_format($det->total_horas, 2) }}</td>
                <td class="text-center">
                    <span style="border-bottom: 1px dotted #333; display: inline-block; width: 100px; height: 12px; margin-top: 5px;"></span>
                </td>
            </tr>
            @endforeach
            
            <!-- Totals row -->
            <tr class="totals-row">
                <td colspan="5" class="text-right" style="text-transform: uppercase;">Totales:</td>
                <td class="text-center" style="color: #1e3a8a;">{{ number_format($planilla->total_horas, 2) }} hrs</td>
                <td class="text-center">
                    <span style="border-bottom: 1px dotted #333; display: inline-block; width: 100px; height: 12px; margin-top: 5px;"></span>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Observations Section if present -->
    @if($planilla->observaciones)
    <div class="observations-section">
        <h3>Observaciones</h3>
        <p>{{ $planilla->observaciones }}</p>
    </div>
    @endif

    <!-- Signatures section at the bottom, side-by-side using a table -->
    <table class="footer-signatures">
        <tr>
            <td class="signature-cell">
                <div class="signature-line"></div>
                <span class="signature-title">Firma del Empleado</span><br>
                <span class="signature-subtitle">Colaborador / Reportante</span>
            </td>
            <td style="width: 20%;"></td>
            <td class="signature-cell">
                <div class="signature-line"></div>
                <span class="signature-title">Firma Aprobatoria</span><br>
                <span class="signature-subtitle">Supervisor / Administrador</span><br>
                <span class="signature-subtitle" style="font-weight: bold; color: #333;">
                    @if($planilla->responsable)
                        {{ $planilla->responsable->name }} ({{ $planilla->updated_at->format('d/m/Y') }})
                    @else
                        Pendiente de Aprobación
                    @endif
                </span>
            </td>
        </tr>
    </table>

</body>
</html>
