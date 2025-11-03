<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Asistencias</title>
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
        
        .info-section {
            background: #1E293B;
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 6px;
            border-left: 4px solid #00F5FF;
        }
        
        .info-section p {
            margin: 4px 0;
            font-size: 9pt;
            color: #E2E8F0;
        }
        
        .info-section strong {
            color: #00F5FF;
            font-weight: 700;
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
        
        .badge-excelente {
            background: #14F195;
            color: #0F172A;
        }
        
        .badge-bueno {
            background: #00F5FF;
            color: #0F172A;
        }
        
        .badge-regular {
            background: #38BDF8;
            color: #0F172A;
        }
        
        .badge-critico {
            background: #64748B;
            color: #FFFFFF;
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
        <h1>REPORTE DE ASISTENCIAS</h1>
        <p>Generado el {{ date('d/m/Y H:i:s') }}</p>
        @if(isset($fechaInicio) && isset($fechaFin))
            <p style="font-size: 9pt; margin-top: 5px;">
                Período: {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}
            </p>
        @endif
    </div>

    @if(isset($estadisticas))
    <div class="info-section">
        <p><strong>Total Registros:</strong> {{ $estadisticas['total_registros'] ?? 0 }}</p>
        <p><strong>Presentes:</strong> {{ $estadisticas['presentes'] ?? 0 }}</p>
        <p><strong>Ausentes:</strong> {{ $estadisticas['ausentes'] ?? 0 }}</p>
        <p><strong>Tardanzas:</strong> {{ $estadisticas['tardanzas'] ?? 0 }}</p>
        <p><strong>Justificadas:</strong> {{ $estadisticas['justificadas'] ?? 0 }}</p>
        <p><strong>% Asistencia:</strong> {{ $estadisticas['porcentaje_asistencia'] ?? 0 }}%</p>
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th style="width: 25%;">Estudiante</th>
                <th style="width: 20%;">Email</th>
                <th class="text-center" style="width: 10%;">Total</th>
                <th class="text-center" style="width: 9%;">Presentes</th>
                <th class="text-center" style="width: 9%;">Ausentes</th>
                <th class="text-center" style="width: 9%;">Tardanzas</th>
                <th class="text-center" style="width: 9%;">Justif.</th>
                <th class="text-center" style="width: 9%;">% Asist.</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($reportePorEstudiante) && count($reportePorEstudiante) > 0)
                @foreach($reportePorEstudiante as $reporte)
                    @php
                        $porcentaje = $reporte['porcentaje'] ?? 0;
                        $badgeClass = $porcentaje >= 90 ? 'badge-excelente' : 
                                     ($porcentaje >= 75 ? 'badge-bueno' : 
                                     ($porcentaje >= 60 ? 'badge-regular' : 'badge-critico'));
                    @endphp
                    <tr>
                        <td style="font-weight: 500;">{{ $reporte['estudiante'] ?? 'N/A' }}</td>
                        <td style="font-size: 7pt; color: #38BDF8;">{{ $reporte['email'] ?? 'N/A' }}</td>
                        <td class="text-center"><strong>{{ $reporte['total_dias'] ?? 0 }}</strong></td>
                        <td class="text-center" style="color: #14F195; font-weight: 600;">{{ $reporte['presentes'] ?? 0 }}</td>
                        <td class="text-center" style="color: #F87171; font-weight: 600;">{{ $reporte['ausentes'] ?? 0 }}</td>
                        <td class="text-center" style="color: #FBBF24; font-weight: 600;">{{ $reporte['tardanzas'] ?? 0 }}</td>
                        <td class="text-center" style="color: #38BDF8;">{{ $reporte['justificadas'] ?? 0 }}</td>
                        <td class="text-center">
                            <span class="badge {{ $badgeClass }}">{{ number_format($porcentaje, 1) }}%</span>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="8" class="text-center" style="color: #38BDF8; padding: 20px;">No hay datos de asistencia disponibles</td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="footer">
        Colegio Secundario Augusto Pulenta | Página <span class="page-number"></span>
    </div>
</body>
</html>