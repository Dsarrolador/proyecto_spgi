<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Conduce #{{ $conduce->id }}</title>
    <style>
        @page { margin: 1cm; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; margin: 0; padding: 0; color: #333; line-height: 1.4; }
        
        .header { width: 100%; margin-bottom: 10px; position: relative; }
        .logo-container { text-align: center; margin-bottom: 5px; }
        .logo-img { width: 140px; height: auto; }
        
        .header-info { text-align: center; margin-bottom: 15px; }
        .company-name { font-size: 18px; font-weight: bold; margin: 0; color: #000; text-transform: uppercase; }
        .company-details { font-size: 9px; margin: 2px 0; color: #444; }
        
        .document-title { 
            text-align: center; font-size: 14px; font-weight: bold; 
            background: #f4f4f4; padding: 6px; margin: 10px 0; 
            border: 1px solid #333; text-transform: uppercase; letter-spacing: 1px;
        }
        
        .main-info { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .main-info td { border: 1px solid #333; padding: 6px 8px; }
        .label { font-weight: bold; background: #f9f9f9; width: 15%; text-transform: uppercase; font-size: 10px; }
        
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; table-layout: fixed; }
        .items-table th, .items-table td { border: 1px solid #333; padding: 6px; text-align: left; overflow: hidden; }
        .items-table th { background: #f4f4f4; text-align: center; font-size: 10px; text-transform: uppercase; }
        .text-center { text-align: center; }
        
        .time-section { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .time-section td { border: 1px solid #333; padding: 8px; }
        
        .section-title { font-weight: bold; margin-top: 10px; margin-bottom: 4px; font-size: 11px; text-transform: uppercase; }
        .text-area { border: 1px solid #333; min-height: 50px; padding: 8px; background: #fff; font-size: 10px; }
        
        .footer { margin-top: 60px; width: 100%; }
        .signature-box { width: 40%; float: left; text-align: center; border-top: 1px solid #333; padding-top: 6px; margin: 0 5%; font-weight: bold; font-size: 10px; text-transform: uppercase; }
        
        .clear { clear: both; }
        
        .subtitle { font-size: 9px; color: #666; font-weight: bold; text-align: center; margin-top: -10px; margin-bottom: 10px; text-transform: uppercase; }
    </style>
</head>
@php
    $logoPath = base_path('resources/img/logo intecsol.jpeg');
    $logoData = "";
    if (file_exists($logoPath)) {
        try {
            $logoData = base64_encode(file_get_contents($logoPath));
        } catch (\Exception $e) {
            $logoData = "";
        }
    }
@endphp
<body>
    <div class="header">
        <div class="logo-container">
            @if($logoData)
                <img src="data:image/jpeg;base64,{{ $logoData }}" class="logo-img">
            @else
                <div style="font-size: 24px; font-weight: 900;"><span style="color: #1a56db;">INTEC</span><span style="color: #e02424;">SOL</span></div>
            @endif
        </div>
        
        <div class="header-info">
            <p class="company-name">INTECSOL SRL.</p>
            <div class="subtitle" style="margin-top: 2px; margin-bottom: 5px;">SOLUCIONES TECNOLÓGICAS</div>
            <p class="company-details">Av. Nuñez de Caceres no. 256, esq. Francisco Pratz Ramirez, Sto. Dgo. D.N.</p>
            <p class="company-details">Tel: (829) 590-0119 &bull; RNC: 130-27434-7</p>
        </div>
        <div style="height: 2px; background: #333; width: 100%;"></div>
    </div>

    <div class="document-title">
        {{ $conduce->tipo === 'hora' ? 'CONDUCE POR HORA' : 'CONDUCE DE TRABAJO' }}
    </div>

    <table class="main-info">
        <tr>
            <td class="label">CLIENTE:</td>
            <td style="width: 45%;">{{ $conduce->cliente->nombre ?? '---' }}</td>
            <td class="label">FECHA:</td>
            <td style="width: 25%;">{{ $fecha }}</td>
        </tr>
        <tr>
            <td class="label">CONTACTO:</td>
            <td colspan="3">{{ $conduce->contacto_nombre ?? '---' }}</td>
        </tr>
    </table>

    @if($conduce->tipo === 'hora')
    <table class="main-info" style="margin-top: -16px; border-top: 0;">
        <tr>
            <td class="label" style="width: 25%;">HORA DE ENTRADA:</td>
            <td style="width: 75%;">{{ $conduce->hora_entrada ? \Carbon\Carbon::parse($conduce->hora_entrada)->format('h:i A') : '---' }}</td>
        </tr>
        <tr>
            <td class="label">HORA DE SALIDA:</td>
            <td>{{ $conduce->hora_salida ? \Carbon\Carbon::parse($conduce->hora_salida)->format('h:i A') : '---' }}</td>
        </tr>
        <tr>
            <td class="label">CANTIDAD DE HORAS:</td>
            <td style="font-weight: bold; font-size: 12px;">{{ $conduce->cantidad_horas ?? '---' }}</td>
        </tr>
    </table>
    @endif

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 12%;">CANT.</th>
                <th style="width: 53%;">{{ $conduce->tipo === 'hora' ? 'SERVICIO' : 'PRODUCTO / SERVICIO' }}</th>
                <th style="width: 20%;"># COT.</th>
                <th style="width: 15%;">FACTURAR</th>
            </tr>
        </thead>
        <tbody>
            @foreach($conduce->items as $item)
            <tr>
                <td class="text-center" style="text-align: center; vertical-align: middle;">{{ number_format($item->cantidad, 0) }}</td>
                <td class="text-center">{{ $item->descripcion }}</td>
                <td class="text-center" style="text-align: center; text-transform: uppercase;">{{ $item->num_cotizacion ?? '---' }}</td>
                <td class="text-center" style="text-align: center; vertical-align: middle;">{{ $item->facturar ? 'SI' : 'NO' }}</td>
            </tr>
            @endforeach
            @for($i = count($conduce->items); $i < ($conduce->tipo === 'hora' ? 5 : 8); $i++)
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            @endfor
        </tbody>
    </table>

    <div class="section-title">Trabajo a Realizar:</div>
    <div class="text-area">
        {!! nl2br(e($conduce->trabajo_realizar)) !!}
        @if(empty($conduce->trabajo_realizar)) <br><br> @endif
    </div>

    <div class="section-title">Observaciones:</div>
    <div class="text-area">
        {!! nl2br(e($conduce->observaciones)) !!}
        @if(empty($conduce->observaciones)) <br><br> @endif
    </div>

    <div class="footer">
        <div class="signature-box">FIRMA TECNICO</div>
        <div class="signature-box">FIRMA CLIENTE</div>
        <div class="clear"></div>
    </div>
</body>
</html>
