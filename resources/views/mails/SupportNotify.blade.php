<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificación de Soporte</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            border: 1px solid #e1e6eb;
        }
        .email-header {
            background-color: #d9534f; /* Color rojo/alerta por el "Esto es grave" */
            color: #ffffff;
            padding: 30px 20px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .email-header p {
            margin: 5px 0 0 0;
            opacity: 0.9;
            font-size: 14px;
        }
        .email-body {
            padding: 30px 20px;
            color: #333333;
            line-height: 1.6;
        }
        .problem-box {
            background-color: #fdf7f7;
            border-left: 4px solid #d9534f;
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 0 4px 4px 0;
            font-size: 15px;
            white-space: pre-line;
        }
        .email-footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #777777;
            border-top: 1px solid #e1e6eb;
        }
    </style>
</head>
<body>

    <div class="email-container">
        <div class="email-header">
            <h1>Soporte del Sistema</h1>
            <p>Integración del Sistema - FTI</p>
        </div>

        <div class="email-body">
            <p>Se ha recibido un nuevo reporte del usuario  {{ $userEmail }}  a través del formulario de asistencia. A continuación, se detallan los comentarios del usuario:</p>
            
            <div class="problem-box">
                <strong>Descripción del Problema:</strong><br>
                {!! nl2br(e($messageContent)) !!}
            </div>
            
            <p>Por favor, atiendan este requerimiento a la brevedad posible.</p>
        </div>

        <div class="email-footer">
            <p>Este es un mensaje automático generado por el Sistema de Reportes - FTI.</p>
            <p>&copy; {{ date('Y') }} Fiesta Tours Perú.</p>
        </div>
    </div>

</body>
</html>