<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Qr Code</title>
</head>
<body>
    <div style="display: flex; justify-content: center; align-items: center">
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
                {!! QrCode::size(300)->generate($get_qr_data); !!}
        </div>
    </div>
</body>
</html>
