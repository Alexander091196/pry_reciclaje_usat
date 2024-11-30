<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $schedule = Schedule::all();

        if ($request->ajax()) {

            return DataTables::of($schedule)
                ->addColumn('actions', function ($schedule) {
                    return '
                        <div class="dropdown">
                            <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-bars"></i>                        
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <button class="dropdown-item btnEditar" id="' . $schedule->id . '"><i class="fas fa-edit"></i>  Editar</button>
                                <form action="' . route('admin.schedules.destroy', $schedule->id) . '" method="POST" class="frmEliminar d-inline">
                                    ' . csrf_field() . method_field('DELETE') . '
                                    <button type="submit" class="dropdown-item"><i class="fas fa-trash"></i> Eliminar</button>
                                </form>
                            </div>
                        </div>';
                })
                ->rawColumns(['actions'])  // Declarar columnas que contienen HTML
                ->make(true);
        } else {
            return view('admin.schedules.index');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.schedules.create');
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([

            'name' => ['required', 'in:Lunes,Martes,Miércoles,Jueves,Viernes,Sábado,Domingo'],
            'description' => 'nullable|string',
        ]);

        try {
            Schedule::create([
                'name' => $request->name,
                'time_start' => $request->time_start,
                'time_end' => $request->time_end,
                'description' => $request->description,
            ]);

            return response()->json(['message' => 'Horario registrado correctamente'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error en el registro: ' . $th->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $schedule = Schedule::findOrFail($id);
        return view('admin.schedules.edit', compact('schedule'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => ['required', 'in:Lunes,Martes,Miércoles,Jueves,Viernes,Sábado,Domingo'],
            'time_start' => 'required|date_format:H:i',
            'time_end' => 'required|date_format:H:i|after:time_start',
            'description' => 'nullable|string',
        ]);

        try {
            $schedule = Schedule::findOrFail($id); // Busca el horario o lanza un error si no existe.

            $schedule->update([
                'name' => $request->name,
                'time_start' => $request->time_start,
                'time_end' => $request->time_end,
                'description' => $request->description,
            ]);

            return response()->json(['message' => 'Horario actualizado correctamente'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error al actualizar el horario: ' . $th->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $schedule = Schedule::findOrFail($id); // Busca el horario o lanza un error si no existe.
            $schedule->delete(); // Elimina el horario.

            return response()->json(['message' => 'Horario eliminado correctamente'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error al eliminar el horario: ' . $th->getMessage()], 500);
        }
    }
}
