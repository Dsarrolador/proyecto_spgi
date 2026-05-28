<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; color: #333; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background-color: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        h2 { color: #0056b3; }
        .info { background-color: #f9f9f9; border-left: 4px solid #0056b3; padding: 15px; margin: 20px 0; }
        p { line-height: 1.6; }
        .footer { margin-top: 30px; font-size: 12px; color: #999; text-align: center; border-top: 1px solid #eee; padding-top: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Reporte de Visita Técnica</h2>
        <p>Estimado/a <strong>{{ $visita->nombre_visitado }}</strong>,</p>
        <p>Adjunto a este correo encontrará el reporte correspondiente a la visita técnica realizada recientemente.</p>
        
        <div class="info">
            <p style="margin: 0 0 10px 0;"><strong>Detalles de la Visita:</strong></p>
            <ul style="margin: 0; padding-left: 20px;">
                <li><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($visita->created_at)->format('d/m/Y h:i A') }}</li>
                <li><strong>Recibido por:</strong> {{ $visita->nombre_recibio ?? 'No especificado' }}</li>
                <li><strong>Diagnóstico:</strong> {{ $visita->estado_cliente ?? 'Estable' }}</li>
            </ul>
        </div>
        
        <p>Si tiene alguna pregunta sobre este reporte o requiere asistencia adicional, no dude en contactarnos.</p>
        
        <p>Atentamente,<br>
        <strong>El Equipo Comercial</strong><br>
        SPGI</p>
        
        <div class="footer">
            Este es un mensaje automático generado por el Sistema de Gestión SPGI. Por favor no responda a este correo si la dirección de remitente no está habilitada para recibir respuestas.
        </div>
    </div>
</body>
</html>
