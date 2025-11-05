<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Horario de Clases - {{ $estudiante->nombre_completo }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 10pt;
            color: #E2E8F0;
            line-height: 1.5;
            background: #0F172A;
            padding: 15px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 25px;
            padding: 30px 20px;
            background: linear-gradient(135deg, #1E293B 0%, #0F172A 100%);
            border-radius: 0;
            border: 3px solid #00F5FF;
            box-shadow: 0 0 30px rgba(0, 245, 255, 0.3);
        }
        
        .header h1 {
            color: #00F5FF;
            font-size: 28pt;
            margin-bottom: 15px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 3px;
            text-shadow: 0 0 30px rgba(0, 245, 255, 0.8), 0 0 60px rgba(0, 245, 255, 0.4);
        }
        
        .header p {
            color: #38BDF8;
            font-size: 11pt;
            font-weight: 600;
            letter-spacing: 1px;
            margin: 8px 0;
        }
        
        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 25px;
            border: 3px solid #00F5FF;
        }
        
        .stat-box {
            display: table-cell;
            width: 33.33%;
            padding: 20px;
            text-align: center;
            border-right: 3px solid #00F5FF;
            background: #1E293B;
        }
        
        .stat-box:last-child {
            border-right: none;
        }
        
        .stat-box h3 {
            font-size: 32pt;
            color: #00F5FF;
            margin-bottom: 8px;
            font-weight: 900;
            text-shadow: 0 0 20px rgba(0, 245, 255, 0.8);
        }
        
        .stat-box p {
            font-size: 10pt;
            color: #94A3B8;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 1px;
        }
        
        .section-title {
            color: #00F5FF;
            font-size: 16pt;
            font-weight: 900;
            margin: 25px 0 15px 0;
            padding: 15px;
            background: #1E293B;
            border: 3px solid #00F5FF;
            text-transform: uppercase;
            letter-spacing: 2px;
            text-shadow: 0 0 20px rgba(0, 245, 255, 0.6);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            border: 3px solid #00F5FF;
        }
        
        thead {
            background: #1E293B;
        }
        
        thead th {
            padding: 15px 10px;
            text-align: left;
            font-size: 10pt;
            font-weight: 900;
            border: 2px solid #00F5FF;
            color: #00F5FF;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        tbody td {
            padding: 12px 10px;
            border: 2px solid #334155;
            font-size: 9.5pt;
            background: #1E293B;
            color: #E2E8F0;
        }
        
        tbody tr:nth-child(odd) td {
            background: #0F172A;
        }
        
        .subject-name {
            font-weight: 700;
            color: #00F5FF;
            font-size: 10pt;
        }
        
        .subject-code {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 0;
            font-size: 9pt;
            font-weight: 900;
            background: #00F5FF;
            color: #0F172A;
            border: 2px solid #00F5FF;
            letter-spacing: 1px;
        }
        
        .day-badge {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 0;
            font-size: 9pt;
            font-weight: 900;
            background: transparent;
            color: #00F5FF;
            border: 2px solid #00F5FF;
            letter-spacing: 0.5px;
        }
        
        .time-cell {
            color: #14F195;
            font-weight: 700;
            font-size: 10pt;
        }
        
        .text-center {
            text-align: center;
        }
        
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 9pt;
            color: #00F5FF;
            padding: 15px 0;
            border-top: 3px solid #00F5FF;
            background: #0F172A;
            font-weight: 700;
            letter-spacing: 1px;
        }
        
        .page-number:after {
            content: counter(page);
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>HORARIO DE CLASES</h1>
        <p><strong>{{ $estudiante->nombre_completo }}</strong></p>
        <p style="font-size: 10pt; margin-top: 8px;">
            Generado el {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}
        </p>
    </div>

    @php
        $totalHoras = 0;
        $totalUnidades = 0;
        foreach($inscripciones as $ins) {
            $totalHoras += $ins->seccion->curso->horas_semanales ?? 0;
            $totalUnidades += $ins->seccion->curso->creditos ?? 0;
        }
    @endphp

    <div class="stats-grid">
        <div class="stat-box">
            <h3>{{ $inscripciones->count() }}</h3>
            <p>Materias</p>
        </div>
        <div class="stat-box">
            <h3>{{ $totalHoras }}</h3>
            <p>Horas/Semana</p>
        </div>
        <div class="stat-box">
            <h3>{{ $totalUnidades }}</h3>
            <p>Unidades</p>
        </div>
    </div>

    <h2 class="section-title">DISTRIBUCIÓN SEMANAL</h2>

    <table>
        <thead>
            <tr>
                <th style="width: 20%;">MATERIA</th>
                <th style="width: 10%;">CÓDIGO</th>
                <th style="width: 22%;">PROFESOR</th>
                <th class="text-center" style="width: 12%;">DÍA</th>
                <th class="text-center" style="width: 16%;">HORARIO</th>
                <th class="text-center" style="width: 10%;">AULA</th>
                <th class="text-center" style="width: 5%;">HRS</th>
                <th class="text-center" style="width: 5%;">UND</th>
            </tr>
        </thead>
        <tbody>
            @forelse($inscripciones as $inscripcion)
                @foreach($inscripcion->seccion->horarios as $index => $horario)
                    <tr>
                        @if($index == 0)
                            <td rowspan="{{ $inscripcion->seccion->horarios->count() }}" class="subject-name">
                                {{ $inscripcion->seccion->curso->nombre }}
                            </td>
                            <td rowspan="{{ $inscripcion->seccion->horarios->count() }}" class="text-center">
                                <span class="subject-code">{{ $inscripcion->seccion->curso->codigo_curso }}</span>
                            </td>
                            <td rowspan="{{ $inscripcion->seccion->horarios->count() }}" style="font-size: 9pt; font-weight: 600;">
                                {{ $inscripcion->seccion->profesor->nombre_completo }}
                            </td>
                        @endif
                        <td class="text-center">
                            <span class="day-badge">{{ strtoupper($horario->dia_semana) }}</span>
                        </td>
                        <td class="text-center time-cell">
                            {{ substr($horario->hora_inicio, 0, 5) }} - {{ substr($horario->hora_fin, 0, 5) }}
                        </td>
                        <td class="text-center" style="font-weight: 700; color: #38BDF8; font-size: 10pt;">
                            {{ $horario->aula ?? 'POR ASIGNAR' }}
                        </td>
                        @if($index == 0)
                            <td rowspan="{{ $inscripcion->seccion->horarios->count() }}" class="text-center" style="font-weight: 700; color: #00F5FF;">
                                {{ $inscripcion->seccion->curso->horas_semanales ?? 0 }}
                            </td>
                            <td rowspan="{{ $inscripcion->seccion->horarios->count() }}" class="text-center" style="font-weight: 700; color: #00F5FF;">
                                {{ $inscripcion->seccion->curso->creditos ?? 0 }}
                            </td>
                        @endif
                    </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="color: #38BDF8; padding: 30px; font-size: 11pt; font-weight: 600;">
                        NO HAY HORARIOS REGISTRADOS
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Colegio Secundario Augusto Pulenta | PÁGINA <span class="page-number"></span>
    </div>
</body>
</html>