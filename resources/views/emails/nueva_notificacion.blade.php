<!DOCTYPE html>
<html>
<head>
    <title>Nueva Notificación</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
        <h2 style="color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px;">Nueva Notificación de SPGI</h2>
        
        <p style="font-size: 16px; color: #555;">Hola,</p>
        <p style="font-size: 16px; color: #555;">Tienes una nueva notificación en el sistema SPGI, enviada por <strong>{{ $remitenteNombre }}</strong>:</p>
        
        <div style="background-color: #e9ecef; padding: 15px; border-left: 4px solid #007bff; margin-top: 20px; margin-bottom: 20px; border-radius: 4px;">
            <p style="font-size: 16px; color: #333; margin: 0;">{{ $mensaje }}</p>
        </div>

        <p style="font-size: 14px; color: #888;">Para más detalles, por favor inicia sesión en la plataforma SPGI.</p>

        <div style="margin-top: 30px; border-top: 1px solid #ddd; padding-top: 15px; text-align: center;">
            <p style="font-size: 12px; color: #aaa; margin: 0;">
                Este es un mensaje automático generado por el sistema. Por favor no respondas a este correo.
            </p>
        </div>
    </div>
</body>
</html>
