<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Calificaciones</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 9pt;
            color: #E2E8F0;
            line-height: 1.4;
            background: #0F172A;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding: 25px 20px;
            background: linear-gradient(135deg, #1E293B 0%, #0F172A 100%);
            border-radius: 8px;
            border: 2px solid #38BDF8;
            box-shadow: 0 4px 6px rgba(0, 245, 255, 0.1);
        }
        
        .header h1 {
            color: #00F5FF;
            font-size: 24pt;
            margin-bottom: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            text-shadow: 0 0 20px rgba(0, 245, 255, 0.5);
        }
        
        .header p {
            color: #38BDF8;
            font-size: 10pt;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        
        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        
        .stat-box {
            display: table-cell;
            width: 25%;
            padding: 12px;
            text-align: center;
            border: 3px solid #334155;
            background: #1E293B;
        }
        
        .stat-box:not(:last-child) {
            border-right: none;
        }
        
        .stat-box h3 {
            font-size: 18pt;
            color: #14F195;
            margin-bottom: 4px;
            font-weight: bold;
        }
        
        .stat-box p {
            font-size: 8pt;
            color: #38BDF8;
            text-transform: uppercase;
            font-weight: 600;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        thead {
            background: linear-gradient(135deg, #1E293B 0%, #0F172A 100%);
            color: #00F5FF;
        }
        
        thead th {
            padding: 10px 6px;
            text-align: left;
            font-size: 8.5pt;
            font-weight: bold;
            border: 2px solid #334155;
        }
        
        tbody td {
            padding: 8px 6px;
            border: 1px solid #334155;
            font-size: 8pt;
            background: #1E293B;
            color: #E2E8F0;
        }
        
        tbody tr:nth-child(even) td {
            background: #0F172A;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 7.5pt;
            font-weight: bold;
            text-align: center;
        }
        
        .badge-aprobado {
            background: #14F195;
            color: #0F172A;
        }
        
        .badge-reprobado {
            background: #64748B;
            color: #FFFFFF;
        }
        
        .nota-alta {
            color: #14F195;
            font-weight: bold;
        }
        
        .nota-baja {
            color: #F87171;
            font-weight: bold;
        }
        
        .text-center {
            text-align: center;
        }
        
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 8pt;
            color: #38BDF8;
            padding: 12px 0;
            border-top: 2px solid #38BDF8;
            background: #0F172A;
        }
        
        .page-number:after {
            content: counter(page);
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>REPORTE DE CALIFICACIONES</h1>
        <p>Generado el {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <div class="stats-grid">
        <div class="stat-box">
            <h3>{{ $estadisticas['total_calificaciones'] }}</h3>
            <p>Total</p>
        </div>
        <div class="stat-box">
            <h3>{{ number_format($estadisticas['promedio_general'], 2) }}</h3>
            <p>Promedio</p>
        </div>
        <div class="stat-box">
            <h3>{{ $estadisticas['aprobados'] }}</h3>
            <p>Aprobados</p>
        </div>
        <div class="stat-box">
            <h3>{{ $estadisticas['reprobados'] }}</h3>
            <p>Reprobados</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 20%;">Estudiante</th>
                <th style="width: 15%;">Curso</th>
                <th style="width: 10%;">Sección</th>
                <th style="width: 15%;">Evaluación</th>
                <th style="width: 8%;">Tipo</th>
                <th class="text-center" style="width: 8%;">Nota</th>
                <th class="text-center" style="width: 8%;">%</th>
                <th class="text-center" style="width: 8%;">Puntos</th>
                <th class="text-center" style="width: 8%;">Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($calificaciones as $calif)
                <tr>
                    <td style="font-size: 8pt; font-weight: 500;">{{ $calif->estudiante->name }}</td>
                    <td style="font-size: 7pt; color: #38BDF8;">{{ $calif->evaluacion->seccion->curso->codigo_curso }}</td>
                    <td class="text-center">{{ $calif->evaluacion->seccion->codigo_seccion }}</td>
                    <td style="font-size: 7.5pt;">{{ $calif->evaluacion->nombre }}</td>
                    <td style="font-size: 7pt;">{{ ucfirst($calif->evaluacion->tipo) }}</td>
                    <td class="text-center {{ $calif->esta_aprobada ? 'nota-alta' : 'nota-baja' }}">
                        {{ number_format($calif->nota, 2) }}
                    </td>
                    <td class="text-center">{{ $calif->evaluacion->porcentaje }}%</td>
                    <td class="text-center">
                        {{ number_format($calif->nota * ($calif->evaluacion->porcentaje / 100), 2) }}
                    </td>
                    <td class="text-center">
                        <span class="badge {{ $calif->esta_aprobada ? 'badge-aprobado' : 'badge-reprobado' }}">
                            {{ $calif->esta_aprobada ? 'Aprobado' : 'Reprobado' }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="color: #38BDF8; padding: 20px;">No hay calificaciones registradas</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Colegio Secundario Augusto Pulenta | Página <span class="page-number"></span>
    </div>
</body>
</html>