<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // TUS USUARIOS ORIGINALES
        Usuario::firstOrCreate(
            ['email' => 'admin@sistema.edu'],
            [
                'nombre' => 'Admin',
                'apellido' => 'Sistema',
                'password' => Hash::make('password'),
                'tipo_usuario' => 'administrador',
                'estado' => 'activo',
            ]
        );

        $profesorTest = Usuario::firstOrCreate(
            ['email' => 'profesor@test.com'],
            [
                'nombre' => 'Profesor',
                'apellido' => 'Test',
                'password' => Hash::make('password'),
                'tipo_usuario' => 'profesor',
                'estado' => 'activo',
            ]
        );

        $estudianteTest = Usuario::firstOrCreate(
            ['email' => 'estudiante@test.com'],
            [
                'nombre' => 'Estudiante',
                'apellido' => 'Test',
                'password' => Hash::make('password'),
                'tipo_usuario' => 'estudiante',
                'estado' => 'activo',
            ]
        );

        // CREAR DATOS DE PRUEBA
        $this->crearDatosPrueba($profesorTest->id, $estudianteTest->id);

        // Llamar al seeder de configuraciones si existe
        if (class_exists('\Database\Seeders\ConfiguracionSeeder')) {
            $this->call([
                ConfiguracionSeeder::class,
            ]);
        }
    }

    private function crearDatosPrueba($profesorTestId, $estudianteTestId)
    {
        // Solo crear si no existen períodos
        if (DB::table('periodos_academicos')->count() > 0) {
            $this->command->info('Ya existen datos, saltando creación...');
            return;
        }

        // 1. CREAR MÁS PROFESORES (SIN ACENTOS EN EMAILS)
        $profesores = [$profesorTestId];
        $nombresProfesores = [
            ['Juan', 'Perez', 'juan.perez'],
            ['Maria', 'Gonzalez', 'maria.gonzalez'],
            ['Carlos', 'Rodriguez', 'carlos.rodriguez'],
        ];

        foreach ($nombresProfesores as $nombre) {
            $profesor = Usuario::create([
                'nombre' => $nombre[0],
                'apellido' => $nombre[1],
                'email' => $nombre[2] . '@escuela.com',
                'password' => Hash::make('12345678'),
                'tipo_usuario' => 'profesor',
                'estado' => 'activo',
            ]);
            $profesores[] = $profesor->id;
        }

        // 2. CREAR MÁS ESTUDIANTES (SIN ACENTOS EN EMAILS)
        $estudiantes = [$estudianteTestId];
        $nombresEstudiantes = [
            ['Pedro', 'Sanchez', 'pedro.sanchez'],
            ['Laura', 'Fernandez', 'laura.fernandez'],
            ['Diego', 'Ramirez', 'diego.ramirez'],
            ['Sofia', 'Torres', 'sofia.torres'],
            ['Miguel', 'Flores', 'miguel.flores'],
            ['Valentina', 'Castro', 'valentina.castro'],
        ];

        foreach ($nombresEstudiantes as $nombre) {
            $estudiante = Usuario::create([
                'nombre' => $nombre[0],
                'apellido' => $nombre[1],
                'email' => $nombre[2] . '@estudiante.com',
                'password' => Hash::make('12345678'),
                'tipo_usuario' => 'estudiante',
                'fecha_nacimiento' => Carbon::now()->subYears(rand(18, 25))->format('Y-m-d'),
                'estado' => 'activo',
            ]);
            $estudiantes[] = $estudiante->id;
        }

        // 3. CREAR PERÍODO ACADÉMICO
        $periodo = DB::table('periodos_academicos')->insertGetId([
            'codigo' => 'P2025-1',
            'nombre' => 'Primer Semestre 2025',
            'fecha_inicio' => '2025-03-01',
            'fecha_fin' => '2025-07-31',
            'estado' => 'activo',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 4. CREAR CURSOS
        $cursos = [];
        $datosCursos = [
            ['MAT101', 'Matematica I', 'Fundamentos de algebra', 4],
            ['FIS101', 'Fisica I', 'Mecanica y cinematica', 4],
            ['PROG101', 'Programacion I', 'Intro a programacion', 5],
        ];

        foreach ($datosCursos as $curso) {
            $cursos[] = DB::table('cursos')->insertGetId([
                'codigo_curso' => $curso[0],
                'nombre' => $curso[1],
                'descripcion' => $curso[2],
                'creditos' => $curso[3],
                'nivel' => 1,
                'estado' => 'activo',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 5. CREAR SECCIONES
        $secciones = [];
        foreach ($cursos as $i => $cursoId) {
            $secciones[] = DB::table('secciones')->insertGetId([
                'curso_id' => $cursoId,
                'profesor_id' => $profesores[$i % count($profesores)],
                'periodo_academico_id' => $periodo,
                'nombre' => 'A',
                'codigo_seccion' => 'SEC-' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                'horario' => 'Lunes y Miercoles 8:00-10:00',
                'aula' => 'Aula ' . ($i + 1),
                'cupo_maximo' => 30,
                'estado' => 'activo',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 6. INSCRIBIR ESTUDIANTES
        foreach ($secciones as $seccionId) {
            foreach ($estudiantes as $estudianteId) {
                DB::table('inscripciones')->insert([
                    'estudiante_id' => $estudianteId,
                    'seccion_id' => $seccionId,
                    'fecha_inscripcion' => now(),
                    'estado' => 'inscrito',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // 7. CREAR EVALUACIONES
        $tiposEvaluacion = DB::table('tipos_evaluacion')->pluck('id')->toArray();
        
        if (empty($tiposEvaluacion)) {
            $this->command->error('No hay tipos de evaluacion. Ejecuta primero la migracion de tipos_evaluacion');
            return;
        }

        foreach ($secciones as $seccionId) {
            $evaluacionesData = [
                ['Examen Parcial 1', 25, Carbon::now()->addDays(10)],
                ['Proyecto 1', 20, Carbon::now()->addDays(20)],
                ['Examen Parcial 2', 25, Carbon::now()->addDays(40)],
                ['Examen Final', 30, Carbon::now()->addDays(60)],
            ];

            foreach ($evaluacionesData as $evalData) {
                DB::table('evaluaciones')->insert([
                    'seccion_id' => $seccionId,
                    'tipo_evaluacion_id' => $tiposEvaluacion[array_rand($tiposEvaluacion)],
                    'nombre' => $evalData[0],
                    'descripcion' => 'Evaluacion correspondiente a ' . $evalData[0],
                    'fecha_evaluacion' => $evalData[2]->format('Y-m-d'),
                    'fecha_limite' => $evalData[2]->addHours(2),
                    'nota_maxima' => 100.00,
                    'porcentaje' => $evalData[1],
                    'estado' => 'programada',
                    'instrucciones' => 'Instrucciones para ' . $evalData[0],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $totalEvaluaciones = DB::table('evaluaciones')->count();
        
        $this->command->info('✅ Datos de prueba creados exitosamente!');
        $this->command->info('📝 Total de evaluaciones creadas: ' . $totalEvaluaciones);
        $this->command->info('');
        $this->command->info('Usuarios disponibles:');
        $this->command->info('- admin@sistema.edu / password');
        $this->command->info('- profesor@test.com / password');
        $this->command->info('- estudiante@test.com / password');
    }
}