<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Horario;
use App\Models\Seccion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HorarioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (auth()->user()->tipo_usuario !== 'administrador') {
                abort(403, 'Acceso denegado.');
            }
            return $next($request);
        });
    }

    /**
     * Mostrar horarios de una sección específica
     */
    public function index($seccionId)
    {
        $seccion = Seccion::with(['curso', 'profesor', 'horarios' => function($query) {
            $query->ordenadoPorDia()->ordenadoPorHora();
        }])->findOrFail($seccionId);

        // Organizar horarios por día
        $horariosPorDia = [
            'Lunes' => [],
            'Martes' => [],
            'Miércoles' => [],
            'Jueves' => [],
            'Viernes' => [],
            'Sábado' => [],
        ];

        foreach ($seccion->horarios as $horario) {
            if (isset($horariosPorDia[$horario->dia_semana])) {
                $horariosPorDia[$horario->dia_semana][] = $horario;
            }
        }

        return view('admin.horarios.index', compact('seccion', 'horariosPorDia'));
    }

    /**
     * Mostrar formulario para crear nuevo horario
     */
    public function create($seccionId)
    {
        $seccion = Seccion::with(['curso', 'profesor'])->findOrFail($seccionId);
        
        $diasSemana = [
            'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'
        ];

        return view('admin.horarios.create', compact('seccion', 'diasSemana'));
    }

    /**
     * Guardar nuevo horario
     */
    public function store(Request $request, $seccionId)
    {
        $seccion = Seccion::findOrFail($seccionId);

        $validator = Validator::make($request->all(), [
            'dia_semana' => 'required|in:Lunes,Martes,Miércoles,Jueves,Viernes,Sábado',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'aula' => 'nullable|string|max:100',
        ], [
            'dia_semana.required' => 'Debe seleccionar un día de la semana.',
            'dia_semana.in' => 'El día seleccionado no es válido.',
            'hora_inicio.required' => 'La hora de inicio es obligatoria.',
            'hora_inicio.date_format' => 'La hora de inicio debe tener formato HH:MM.',
            'hora_fin.required' => 'La hora de fin es obligatoria.',
            'hora_fin.date_format' => 'La hora de fin debe tener formato HH:MM.',
            'hora_fin.after' => 'La hora de fin debe ser posterior a la hora de inicio.',
            'aula.max' => 'El aula no puede exceder 100 caracteres.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Validar conflictos de horario
        $conflicto = $this->validarConflictoHorario(
            $seccionId,
            $request->dia_semana,
            $request->hora_inicio,
            $request->hora_fin
        );

        if ($conflicto) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', $conflicto);
        }

        try {
            Horario::create([
                'seccion_id' => $seccionId,
                'dia_semana' => $request->dia_semana,
                'hora_inicio' => $request->hora_inicio,
                'hora_fin' => $request->hora_fin,
                'aula' => $request->aula,
            ]);

            return redirect()->route('admin.horarios.index', $seccionId)
                           ->with('success', 'Horario agregado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Error al crear el horario: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar formulario para editar horario
     */
    public function edit($seccionId, $horarioId)
    {
        $seccion = Seccion::with(['curso', 'profesor'])->findOrFail($seccionId);
        $horario = Horario::where('seccion_id', $seccionId)->findOrFail($horarioId);
        
        $diasSemana = [
            'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'
        ];

        return view('admin.horarios.edit', compact('seccion', 'horario', 'diasSemana'));
    }

    /**
     * Actualizar horario
     */
    public function update(Request $request, $seccionId, $horarioId)
    {
        $seccion = Seccion::findOrFail($seccionId);
        $horario = Horario::where('seccion_id', $seccionId)->findOrFail($horarioId);

        $validator = Validator::make($request->all(), [
            'dia_semana' => 'required|in:Lunes,Martes,Miércoles,Jueves,Viernes,Sábado',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'aula' => 'nullable|string|max:100',
        ], [
            'dia_semana.required' => 'Debe seleccionar un día de la semana.',
            'dia_semana.in' => 'El día seleccionado no es válido.',
            'hora_inicio.required' => 'La hora de inicio es obligatoria.',
            'hora_inicio.date_format' => 'La hora de inicio debe tener formato HH:MM.',
            'hora_fin.required' => 'La hora de fin es obligatoria.',
            'hora_fin.date_format' => 'La hora de fin debe tener formato HH:MM.',
            'hora_fin.after' => 'La hora de fin debe ser posterior a la hora de inicio.',
            'aula.max' => 'El aula no puede exceder 100 caracteres.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Validar conflictos de horario (excluyendo el horario actual)
        $conflicto = $this->validarConflictoHorario(
            $seccionId,
            $request->dia_semana,
            $request->hora_inicio,
            $request->hora_fin,
            $horarioId
        );

        if ($conflicto) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', $conflicto);
        }

        try {
            $horario->update([
                'dia_semana' => $request->dia_semana,
                'hora_inicio' => $request->hora_inicio,
                'hora_fin' => $request->hora_fin,
                'aula' => $request->aula,
            ]);

            return redirect()->route('admin.horarios.index', $seccionId)
                           ->with('success', 'Horario actualizado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Error al actualizar el horario: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar horario
     */
    public function destroy($seccionId, $horarioId)
    {
        try {
            $horario = Horario::where('seccion_id', $seccionId)->findOrFail($horarioId);
            $horario->delete();

            return redirect()->route('admin.horarios.index', $seccionId)
                           ->with('success', 'Horario eliminado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Error al eliminar el horario: ' . $e->getMessage());
        }
    }

    /**
     * Validar conflictos de horario
     */
    private function validarConflictoHorario($seccionId, $diaSemana, $horaInicio, $horaFin, $excluirHorarioId = null)
    {
        $query = Horario::where('seccion_id', $seccionId)
                       ->where('dia_semana', $diaSemana)
                       ->where(function($q) use ($horaInicio, $horaFin) {
                           $q->whereBetween('hora_inicio', [$horaInicio, $horaFin])
                             ->orWhereBetween('hora_fin', [$horaInicio, $horaFin])
                             ->orWhere(function($subq) use ($horaInicio, $horaFin) {
                                 $subq->where('hora_inicio', '<=', $horaInicio)
                                      ->where('hora_fin', '>=', $horaFin);
                             });
                       });

        if ($excluirHorarioId) {
            $query->where('id', '!=', $excluirHorarioId);
        }

        $conflicto = $query->first();

        if ($conflicto) {
            return "Ya existe un horario para {$diaSemana} de {$conflicto->hora_inicio} a {$conflicto->hora_fin} que se superpone con el horario propuesto.";
        }

        return null;
    }
}
