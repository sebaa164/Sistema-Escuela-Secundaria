<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Estudiantes - {{ $seccion->codigo_seccion }}</title>
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
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 7pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge.approved {
            background: rgba(16, 185, 129, 0.2);
            color: #10B981;
            border: 1px solid #10B981;
        }

        .badge.failed {
            background: rgba(239, 68, 68, 0.2);
            color: #EF4444;
            border: 1px solid #EF4444;
        }

        .badge.pending {
            background: rgba(245, 158, 11, 0.2);
            color: #F59E0B;
            border: 1px solid #F59E0B;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #334155;
            text-align: center;
            font-size: 8pt;
            color: #94A3B8;
        }

        .footer p {
            margin: 5px 0;
        }

        .footer strong {
            color: #00F5FF;
        }

        .section-info {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .section-info > div {
            display: table-cell;
            width: 25%;
            vertical-align: top;
        }

        .section-info .info-box {
            background: #1E293B;
            padding: 10px;
            margin: 0 5px 10px 0;
            border-radius: 6px;
            border-left: 3px solid #00F5FF;
        }

        .section-info .info-box strong {
            color: #00F5FF;
            font-size: 8pt;
            display: block;
            margin-bottom: 3px;
        }

        .section-info .info-box span {
            color: #E2E8F0;
            font-size: 9pt;
        }

        /* Responsive para impresión */
        @media print {
            body {
                background: white !important;
                color: black !important;
            }

            .header {
                background: #f0f0f0 !important;
                border: 1px solid #ccc !important;
                -webkit-print-color-adjust: exact;
            }

            .header h1 {
                color: #0066cc !important;
            }

            .info-section {
                background: #f9f9f9 !important;
                border: 1px solid #ddd !important;
            }

            table {
                border: 1px solid #ccc !important;
            }

            thead th {
                background: #e0e0e0 !important;
                border: 1px solid #999 !important;
                color: #333 !important;
            }

            tbody td {
                border: 1px solid #ccc !important;
                background: white !important;
                color: #333 !important;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>Lista de Estudiantes</h1>
        <p>{{ $seccion->curso->nombre }} - {{ $seccion->codigo_seccion }}</p>
        <p>Reporte generado el {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>
    </div>

    <!-- Información de la Sección -->
    <div class="section-info">
        <div>
            <div class="info-box">
                <strong>Curso:</strong>
                <span>{{ $seccion->curso->nombre }}</span>
            </div>
        </div>
        <div>
            <div class="info-box">
                <strong>Código:</strong>
                <span>{{ $seccion->codigo_seccion }}</span>
            </div>
        </div>
        <div>
            <div class="info-box">
                <strong>Aula:</strong>
                <span>{{ $seccion->aula ?? 'No asignada' }}</span>
            </div>
        </div>
        <div>
            <div class="info-box">
                <strong>Período:</strong>
                <span>{{ $seccion->periodo->nombre ?? 'N/A' }}</span>
            </div>
        </div>
    </div>

    <div class="info-section">
        <p><strong>Total de Estudiantes:</strong> {{ $estudiantes->count() }}</p>
        <p><strong>Cupo Máximo:</strong> {{ $seccion->cupo_maximo }}</p>
        <p><strong>Estudiantes con Notas:</strong> {{ $estudiantes->whereNotNull('nota_final')->count() }}/{{ $estudiantes->count() }}</p>
        <p><strong>Profesor:</strong> {{ auth()->user()->nombre_completo }}</p>
    </div>

    <!-- Tabla de Estudiantes -->
    <table>
        <thead>
            <tr>
                <th style="width: 4%;">#</th>
                <th style="width: 18%;">Estudiante</th>
                <th style="width: 10%;">Estado</th>
                <th style="width: 14%;">Email</th>
                <th style="width: 10%;">Representante</th>
                <th style="width: 12%;">Fecha Inscripción</th>
                <th style="width: 8%;">Nota Final</th>
                <th style="width: 10%;">Estado Nota</th>
            </tr>
        </thead>
        <tbody>
            @foreach($estudiantes as $index => $inscripcion)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $inscripcion->estudiante->nombre_completo }}</strong>
                    </td>
                    <td>
                        @if($inscripcion->estudiante->estado_estudiante)
                            <span class="badge {{ $inscripcion->estudiante->estado_estudiante === 'regular' ? 'approved' : ($inscripcion->estudiante->estado_estudiante === 'suspendido' ? 'failed' : 'pending') }}">
                                {{ ucfirst($inscripcion->estudiante->estado_estudiante) }}
                            </span>
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $inscripcion->estudiante->email }}</td>
                    <td>
                        @if($inscripcion->estudiante->tutor)
                            {{ $inscripcion->estudiante->tutor->nombre_completo }}
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $inscripcion->fecha_inscripcion->format('d/m/Y') }}</td>
                    <td>
                        @if($inscripcion->nota_final)
                            {{ number_format($inscripcion->nota_final, 1) }}
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if($inscripcion->estado_nota === 'Aprobado')
                            <span class="badge approved">Aprobado</span>
                        @elseif($inscripcion->estado_nota === 'Reprobado')
                            <span class="badge failed">Reprobado</span>
                        @else
                            <span class="badge pending">Sin calificar</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p><strong>Colegio Secundario Augusto Pulenta</strong></p>
        <p>Reporte generado automáticamente - {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</p>
        <p>Profesor: {{ auth()->user()->nombre_completo }}</p>
    </div>
</body>
</html>
