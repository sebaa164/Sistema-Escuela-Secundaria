<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Estudiantes</title>
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
        <h1>REPORTE DE ESTUDIANTES</h1>
        <p>Generado el {{ date('d/m/Y H:i:s') }}</p>
    </div>

    <div class="info-section">
        @php
            $totalEstudiantes = is_countable($reporteEstudiantes) ? count($reporteEstudiantes) : 0;
            $totalInscripciones = 0;
            $totalAprobadas = 0;
            $sumaPromedios = 0;
            $contadorPromedios = 0;
            
            foreach($reporteEstudiantes as $rep) {
                if(isset($rep['total_materias']) && is_numeric($rep['total_materias'])) {
                    $totalInscripciones += (int)$rep['total_materias'];
                }
                if(isset($rep['aprobadas']) && is_numeric($rep['aprobadas'])) {
                    $totalAprobadas += (int)$rep['aprobadas'];
                }
                // Verificar si el promedio es string o número
                $promedio = isset($rep['promedio']) ? $rep['promedio'] : null;
                if($promedio !== null && $promedio !== 'N/A' && is_numeric($promedio) && $promedio > 0) {
                    $sumaPromedios += (float)$promedio;
                    $contadorPromedios++;
                }
            }
            
            $promedioGlobal = $contadorPromedios > 0 ? $sumaPromedios / $contadorPromedios : 0;
        @endphp
        <p><strong>Total de Estudiantes:</strong> {{ $totalEstudiantes }}</p>
        <p><strong>Total Inscripciones:</strong> {{ $totalInscripciones }}</p>
        <p><strong>Materias Aprobadas:</strong> {{ $totalAprobadas }}</p>
        <p><strong>Promedio General:</strong> {{ number_format($promedioGlobal, 2) }}</p>
        @if(isset($filtros['carrera']) && $filtros['carrera'])
            <p><strong>Carrera Filtrada:</strong> {{ $filtros['carrera'] }}</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 18%;">Nombre</th>
                <th style="width: 18%;">Email</th>
                <th style="width: 12%;">Carrera</th>
                <th class="text-center" style="width: 8%;">Materias</th>
                <th class="text-center" style="width: 8%;">Aprobadas</th>
                <th class="text-center" style="width: 8%;">Reprobadas</th>
                <th class="text-center" style="width: 8%;">Hrs/Sem</th>
                <th class="text-center" style="width: 10%;">Promedio</th>
                <th class="text-center" style="width: 10%;">Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reporteEstudiantes as $reporte)
                @php
                    // Los datos ya vienen procesados del controlador
                    $nombre = $reporte['nombre'] ?? 'N/A';
                    $email = $reporte['email'] ?? 'N/A';
                    $carrera = $reporte['carrera'] ?? 'N/A';
                    $totalMaterias = $reporte['total_materias'] ?? 0;
                    $aprobadas = $reporte['aprobadas'] ?? 0;
                    $reprobadas = $reporte['reprobadas'] ?? 0;
                    $horasSemanales = $reporte['horas_semanales'] ?? 0;
                    $promedio = $reporte['promedio'] ?? 'N/A';
                    $estado = $reporte['estado'] ?? 'N/A';
                    $porcentaje = $reporte['porcentaje'] ?? 0;
                    
                    // Determinar badge class
                    if ($porcentaje >= 80) {
                        $badgeClass = 'badge-excelente';
                    } elseif ($porcentaje >= 60) {
                        $badgeClass = 'badge-bueno';
                    } elseif ($porcentaje >= 40) {
                        $badgeClass = 'badge-regular';
                    } else {
                        $badgeClass = 'badge-critico';
                    }
                @endphp
                <tr>
                    <td style="font-weight: 500;">{{ $nombre }}</td>
                    <td style="font-size: 7pt; color: #38BDF8;">{{ $email }}</td>
                    <td style="font-size: 7.5pt;">{{ $carrera }}</td>
                    <td class="text-center"><strong>{{ $totalMaterias }}</strong></td>
                    <td class="text-center" style="color: #14F195; font-weight: 600;">{{ $aprobadas }}</td>
                    <td class="text-center" style="color: #F87171; font-weight: 600;">{{ $reprobadas }}</td>
                    <td class="text-center">{{ $horasSemanales }}</td>
                    <td class="text-center">
                        <strong style="color: #00F5FF;">{{ $promedio }}</strong>
                    </td>
                    <td class="text-center">
                        <span class="badge {{ $badgeClass }}">{{ $estado }}</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="color: #38BDF8; padding: 20px;">No hay datos disponibles</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Sistema de Gestión Académica | Página <span class="page-number"></span>
    </div>
</body>
</html><!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Estudiantes</title>
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
        <h1>REPORTE DE ESTUDIANTES</h1>
        <p>Generado el {{ date('d/m/Y H:i:s') }}</p>
    </div>

    <div class="info-section">
        @php
            $totalEstudiantes = is_countable($reporteEstudiantes) ? count($reporteEstudiantes) : 0;
            $totalInscripciones = 0;
            $totalAprobadas = 0;
            $sumaPromedios = 0;
            $contadorPromedios = 0;
            
            foreach($reporteEstudiantes as $rep) {
                if(isset($rep['total_materias']) && is_numeric($rep['total_materias'])) {
                    $totalInscripciones += (int)$rep['total_materias'];
                }
                if(isset($rep['materias_aprobadas']) && is_numeric($rep['materias_aprobadas'])) {
                    $totalAprobadas += (int)$rep['materias_aprobadas'];
                }
                if(isset($rep['promedio_general']) && is_numeric($rep['promedio_general']) && $rep['promedio_general'] > 0) {
                    $sumaPromedios += (float)$rep['promedio_general'];
                    $contadorPromedios++;
                }
            }
            
            $promedioGlobal = $contadorPromedios > 0 ? $sumaPromedios / $contadorPromedios : 0;
        @endphp
        <p><strong>Total de Estudiantes:</strong> {{ $totalEstudiantes }}</p>
        <p><strong>Total Inscripciones:</strong> {{ $totalInscripciones }}</p>
        <p><strong>Materias Aprobadas:</strong> {{ $totalAprobadas }}</p>
        <p><strong>Promedio General:</strong> {{ number_format($promedioGlobal, 2) }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 18%;">Nombre</th>
                <th style="width: 18%;">Email</th>
                <th style="width: 12%;">Carrera</th>
                <th class="text-center" style="width: 8%;">Materias</th>
                <th class="text-center" style="width: 8%;">Aprobadas</th>
                <th class="text-center" style="width: 8%;">Reprobadas</th>
                <th class="text-center" style="width: 8%;">Hrs/Sem</th>
                <th class="text-center" style="width: 10%;">Promedio</th>
                <th class="text-center" style="width: 10%;">Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reporteEstudiantes as $reporte)
                @php
                    // Asegurar que todos los valores sean numéricos y seguros
                    $totalMaterias = isset($reporte['total_materias']) && is_numeric($reporte['total_materias']) 
                        ? (int) $reporte['total_materias'] 
                        : 0;
                    
                    $aprobadas = isset($reporte['materias_aprobadas']) && is_numeric($reporte['materias_aprobadas']) 
                        ? (int) $reporte['materias_aprobadas'] 
                        : 0;
                    
                    $reprobadas = isset($reporte['materias_reprobadas']) && is_numeric($reporte['materias_reprobadas']) 
                        ? (int) $reporte['materias_reprobadas'] 
                        : 0;
                    
                    $horasSemanales = isset($reporte['horas_semanales_aprobadas']) && is_numeric($reporte['horas_semanales_aprobadas']) 
                        ? (int) $reporte['horas_semanales_aprobadas'] 
                        : 0;
                    
                    $promedio = isset($reporte['promedio_general']) && is_numeric($reporte['promedio_general']) && $reporte['promedio_general'] > 0
                        ? (float) $reporte['promedio_general'] 
                        : null;
                    
                    // Calcular porcentaje de aprobación
                    $porcentaje = $totalMaterias > 0 
                        ? ($aprobadas / $totalMaterias) * 100 
                        : 0;
                    
                    // Determinar badge y estado
                    if ($porcentaje >= 80) {
                        $badgeClass = 'badge-excelente';
                        $estado = 'Excelente';
                    } elseif ($porcentaje >= 60) {
                        $badgeClass = 'badge-bueno';
                        $estado = 'Bueno';
                    } elseif ($porcentaje >= 40) {
                        $badgeClass = 'badge-regular';
                        $estado = 'Regular';
                    } else {
                        $badgeClass = 'badge-critico';
                        $estado = 'Crítico';
                    }
                    
                    // Obtener datos del estudiante de forma segura
                    $nombre = isset($reporte['estudiante']) && isset($reporte['estudiante']->name) 
                        ? $reporte['estudiante']->name 
                        : 'N/A';
                    
                    $email = isset($reporte['estudiante']) && isset($reporte['estudiante']->email) 
                        ? $reporte['estudiante']->email 
                        : 'N/A';
                    
                    $carrera = isset($reporte['estudiante']) && isset($reporte['estudiante']->carrera) 
                        ? $reporte['estudiante']->carrera 
                        : 'N/A';
                @endphp
                <tr>
                    <td style="font-weight: 500;">{{ $nombre }}</td>
                    <td style="font-size: 7pt; color: #38BDF8;">{{ $email }}</td>
                    <td style="font-size: 7.5pt;">{{ $carrera }}</td>
                    <td class="text-center"><strong>{{ $totalMaterias }}</strong></td>
                    <td class="text-center" style="color: #14F195; font-weight: 600;">{{ $aprobadas }}</td>
                    <td class="text-center" style="color: #F87171; font-weight: 600;">{{ $reprobadas }}</td>
                    <td class="text-center">{{ $horasSemanales }}</td>
                    <td class="text-center">
                        @if($promedio !== null)
                            <strong style="color: #00F5FF;">{{ number_format($promedio, 2) }}</strong>
                        @else
                            <span style="color: #64748B;">N/A</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="badge {{ $badgeClass }}">{{ $estado }}</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="color: #38BDF8; padding: 20px;">No hay datos disponibles</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Colegio Secundario Augusto Pulenta | Página <span class="page-number"></span>
    </div>
</body>
</html>