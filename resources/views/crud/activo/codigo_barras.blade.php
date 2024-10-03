<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Código de Barras</title>
    <style>
        body {
            text-align: center;
            font-family: Arial, sans-serif;
        }
        .sticker {
            border: 1px solid #000;
            width: 200px; /* Ajusta el tamaño según tus necesidades */
            padding: 10px;
            margin: 0 auto;
        }
        img {
            width: 100%; /* Escalar el código de barras al ancho del sticker */
        }
    </style>
</head>
<body>
    <div class="sticker">
        <img src="data:image/png;base64,{{ base64_encode($barcode) }}" alt="Código de Barras">
        <p>{{ $codigo }}</p> <!-- Muestra el código del activo -->
    </div>
</body>
</html>
