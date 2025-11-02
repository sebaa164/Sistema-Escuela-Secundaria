<?php

namespace Database\Seeders;

use App\Models\Configuracion;
use Illuminate\Database\Seeder;

class ConfiguracionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $configuraciones = [
            [
                'clave' => 'nota_minima_aprobacion',
                'valor' => '70',
                'tipo' => 'number',
                'descripcion' => 'Nota mínima que debe obtener un estudiante para aprobar una materia'
            ],
            [
                'clave' => 'max_estudiantes_seccion',
                'valor' => '30',
                'tipo' => 'number',
                'descripcion' => 'Cantidad máxima de estudiantes permitidos por sección'
            ],
            [
                'clave' => 'sistema_nombre',
                'valor' => 'Sistema de Gestión Académica',
                'tipo' => 'string',
                'descripcion' => 'Nombre del sistema que aparece en títulos y encabezados'
            ],
            [
                'clave' => 'timezone',
                'valor' => 'America/Argentina/Buenos_Aires',
                'tipo' => 'string',
                'descripcion' => 'Zona horaria del sistema para fechas y horas'
            ],
            [
                'clave' => 'formato_fecha',
                'valor' => 'Y-m-d',
                'tipo' => 'string',
                'descripcion' => 'Formato de visualización de fechas en el sistema'
            ],
        ];

        foreach ($configuraciones as $config) {
            Configuracion::updateOrCreate(
                ['clave' => $config['clave']],
                $config
            );
        }
    }
}