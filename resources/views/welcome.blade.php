<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code</title>
</head>
<body>
    <h1>QR Code Generado</h1>

    @if(isset($qrCodeBase64))
        <img src="data:image/png;base64, {{ $qrCodeBase64 }}" alt="Código QR">
    @else
        <p>No se pudo generar el código QR.</p>
    @endif
</body>
</html>
