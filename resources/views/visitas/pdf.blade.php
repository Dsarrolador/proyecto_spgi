<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Visita Técnica</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #0056b3; padding-bottom: 10px; }
        .header h1 { margin: 0; color: #0056b3; font-size: 24px; }
        .header p { margin: 5px 0 0; color: #666; }
        
        .section-title { font-size: 14px; font-weight: bold; background-color: #f4f4f4; padding: 8px; border-left: 4px solid #0056b3; margin-top: 20px; margin-bottom: 10px; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f9f9f9; width: 30%; font-weight: bold; }
        
        .badge { padding: 4px 8px; border-radius: 4px; font-weight: bold; color: #fff; font-size: 10px; text-transform: uppercase; }
        .bg-critico { background-color: #ef4444; }
        .bg-regular { background-color: #f59e0b; }
        .bg-optimo { background-color: #10b981; }
        .bg-estable { background-color: #10b981; }
        
        .question-block { margin-bottom: 15px; page-break-inside: avoid; border: 1px solid #ddd; padding: 10px; border-radius: 4px; }
        .question-text { font-weight: bold; margin-bottom: 8px; font-size: 13px; }
        .answer-text { margin-bottom: 5px; color: #0056b3; font-weight: bold; }
        .obs-text { font-size: 11px; color: #555; }
        
        .footer { position: fixed; bottom: -30px; left: 0px; right: 0px; height: 30px; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #ddd; padding-top: 10px; }
        .page-number:after { content: counter(page); }
    </style>
</head>
<body>
    <div class="footer">
        Generado el {{ \Carbon\Carbon::now()->format('d/m/Y h:i A') }} | Página <span class="page-number"></span>
    </div>

    <div class="header">
        <h1>Reporte de Visita Técnica</h1>
        <p>Cuestionario: {{ $visita->template->nombre ?? 'N/A' }}</p>
    </div>

    <div class="section-title">Información General</div>
    <table>
        <tr>
            <th>Entidad / Persona Visitada</th>
            <td>{{ $visita->nombre_visitado }}</td>
        </tr>
        <tr>
            <th>Correo Electrónico</th>
            <td>{{ $visita->correo_visitado ?? 'No registrado' }}</td>
        </tr>
        <tr>
            <th>Recibió en Terreno</th>
            <td>{{ $visita->nombre_recibio ?? 'No especificado' }} (Tel: {{ $visita->telefono_recibio ?? 'N/A' }})</td>
        </tr>
        <tr>
            <th>Fecha y Hora</th>
            <td>{{ \Carbon\Carbon::parse($visita->created_at)->format('d/m/Y h:i A') }}</td>
        </tr>
        <tr>
            <th>Registrado por</th>
            <td>{{ $visita->user->name ?? 'Sistema' }}</td>
        </tr>
    </table>

    <div class="section-title">Resultado de Evaluación</div>
    <table>
        <tr>
            <th>Puntos Obtenidos</th>
            <td style="font-size: 18px; font-weight: bold; color: #0056b3;">{{ $visita->total_puntos }}</td>
        </tr>
        <tr>
            <th>Diagnóstico</th>
            <td>
                @php
                    $estado = strtolower(str_replace(['ó', 'á', 'é', 'í', 'ú'], ['o', 'a', 'e', 'i', 'u'], $visita->estado_cliente ?? 'estable'));
                @endphp
                <span class="badge bg-{{ $estado }}">{{ $visita->estado_cliente ?? 'Estable' }}</span>
            </td>
        </tr>
    </table>

    <div class="section-title">Plan de Acción Sugerido</div>
    <div style="border: 1px solid #ddd; padding: 10px; border-radius: 4px; background-color: #f9f9f9; margin-bottom: 20px;">
        {!! nl2br(e($visita->accion_sugerida ?? 'No se ha detallado ningún plan de acción.')) !!}
    </div>

    <div class="section-title">Detalle del Cuestionario</div>
    @php
        $answersMap = $visita->respuestas->keyBy('question_id');
    @endphp
    
    @foreach($visita->template->questions as $idx => $question)
        @php
            $ans = $answersMap->get($question->id);
        @endphp
        <div class="question-block">
            <div class="question-text">{{ $idx + 1 }}. {{ $question->pregunta }}</div>
            <div class="answer-text">
                Respuesta: {{ $ans ? $ans->respuesta_seleccionada : 'Sin contestar' }} 
                <span style="float: right; color: #666; font-size: 11px;">+{{ $ans ? $ans->puntos : 0 }} pts</span>
            </div>
            @if($ans && ($ans->observaciones || $ans->recomendacion))
                <hr style="border: 0; border-top: 1px solid #eee; margin: 8px 0;">
                <table style="width: 100%; border: none; margin-bottom: 0;">
                    <tr>
                        @if($ans->observaciones)
                            <td style="border: none; padding: 0; width: 50%; vertical-align: top;">
                                <div class="obs-text"><b>Observación:</b> {!! e($ans->observaciones) !!}</div>
                            </td>
                        @endif
                        @if($ans->recomendacion)
                            <td style="border: none; padding: 0; width: 50%; vertical-align: top;">
                                <div class="obs-text"><b>Recomendación:</b> {!! e($ans->recomendacion) !!}</div>
                            </td>
                        @endif
                    </tr>
                </table>
            @endif
        </div>
    @endforeach

</body>
</html>
