<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistema de Calificaciones')</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 0;
            font-size: 12px;
            line-height: 1.4;
        }

        * {
            box-sizing: border-box;
        }

        @page {
            margin: 1cm;
        }

        @yield('pdf-styles')
    </style>
</head>
<body>
    @yield('content')
</body>
</html>
