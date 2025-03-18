<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Error en la aplicación</title>
    <style type="text/css">
        body { font-family: Arial, sans-serif; color: #333; }
        .container { margin: 20px; }
        .header { background-color: #d9534f; color: #fff; padding: 10px; }
        .content { margin-top: 20px; }
        pre { background-color: #f7f7f9; padding: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Error en la Aplicación</h2>
        </div>
        <div class="content">
            <p><strong>Mensaje: </strong>{{ $exceptionMessage }}</p>
            <p><strong>Archivo: </strong>{{ $exceptionFile }}</p>
            <p><strong>Línea: </strong>{{ $exceptionLine }}</p>
            <p><strong>Traza: </strong></p>
            <pre>{{ $exceptionTrace }}</pre>
            <p>Revisa más detalles en <a href="{{ $appUrl }}">{{ $appUrl }}</a></p>
        </div>
    </div>
</body>
</html>
