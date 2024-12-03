<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Action;
use App\Models\Activitie;
use App\Models\Horarie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class ActionsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $id = session('horarie_id');
        $hor = Horarie::find($id);

        $actions = DB::select('
            SELECT AC.id, AC.image, AC.date, AC.description 
            FROM actions AC
            INNER JOIN horaries H ON AC.horarie_id=H.id
            WHERE AC.horarie_id= ?', [$id]);

        if ($request->ajax()) {
            $actionsCollection = collect($actions);

            return DataTables::of($actionsCollection)
                ->addColumn('image', function ($action) {
                    return '<img src="' . ($action->image == '' ? asset('storage/actions/no_image.png') : asset($action->image)) . '" width="100px" height="70px" class="card">';
                })
                ->addColumn('edit', function ($action) {
                    return '
                        <button class="btnEditar btn btn-primary btn-sm" id="' . $action->id . '">
                            <i class="fa fa-edit"></i>
                        </button>';
                })
                ->addColumn('delete', function ($action) {
                    return '
                        <form action="' . route('admin.actions.destroy', $action->id) . '" method="POST" class="frmEliminar d-inline">
                            ' . csrf_field() . method_field('DELETE') . '
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>';
                })
                ->rawColumns(['image', 'edit', 'delete']) // Declarar columnas que contienen HTML
                ->make(true);
        } else {
            return view('admin.actions.index', compact('hor', 'actions'));
        }
    }


    public function create()
    {
        return view('admin.actions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validar datos enviados
            $request->validate([
                'date' => 'required|date',
                'description' => 'required|string|max:255',
                'image' => 'nullable|image|max:2048',
                'horarie_id' => 'required|exists:horaries,id',
            ]);
    
            // Obtener el horario y la actividad asociada
            $horario = Horarie::findOrFail($request->horarie_id);
            $actividad = Activitie::findOrFail($horario->activitie_id);
    
            // Validar que el día de la fecha coincida con el horario
            $selectedDate = \Carbon\Carbon::parse($request->date);
            $allowedDay = array_search(strtoupper($horario->day), [
                "DOMINGO", "LUNES", "MARTES", "MIERCOLES", "JUEVES", "VIERNES", "SABADO",
            ]);
    
            if ($selectedDate->dayOfWeek !== $allowedDay) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'La fecha seleccionada no corresponde al día permitido (' . $horario->day . ').',
                ], 422);
            }
    
            // Validar que la fecha pertenece al mes permitido
            $allowedMonth = \Carbon\Carbon::parse($actividad->startdate)->format('Y-m');
            if ($selectedDate->format('Y-m') !== $allowedMonth) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'La fecha seleccionada no corresponde al mes permitido (' . $allowedMonth . ').',
                ], 422);
            }

             // Procesar la imagen (si existe)
        $imagePath = null; // Valor por defecto si no se sube imagen
        if ($request->hasFile('image')) {
            // Guardar la nueva imagen
            $imagePath = $request->file('image')->store('public/actions');
            $imagePath = Storage::url($imagePath);
        }

    
            // Crear acción
            Action::create([
                'date' => $request->date,
                'description' => $request->description,
                'image' => $imagePath,
                'horarie_id' => $request->horarie_id,
            ]);
    
            return response()->json([
                'status' => 'success',
                'message' => 'Actividad registrada correctamente.',
            ], 200);
        } catch (\Exception $e) {
            
            return response()->json([
                'status' => 'error',
                'message' => 'Ocurrió un error al registrar la actividad.',
            ], 500);
        }
    }




    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $act = Action::find($id);
        return view('admin.actions.edit', compact('act'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            // Validar los datos del formulario
            $request->validate([
                'date' => 'required|date',
                'description' => 'required|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
    
            // Obtener el horario y la actividad asociados
            $horarie = Horarie::findOrFail(session('horarie_id'));
            $activitie = Activitie::findOrFail($horarie->activitie_id);
    
            // Validar que la fecha esté dentro del rango de la actividad
            $startDate = \Carbon\Carbon::parse($activitie->startdate);
            $endDate = \Carbon\Carbon::parse($activitie->lastdate);
            $requestDate = \Carbon\Carbon::parse($request->date);
    
            if ($requestDate->lt($startDate) || $requestDate->gt($endDate)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'La fecha de la acción debe estar dentro del rango de fechas de la actividad.',
                ], 422);
            }
    
            // Validar que el día de la semana de la fecha coincida con el día del horario
            $allowedDay = array_search(strtoupper($horarie->day), [
                "DOMINGO", "LUNES", "MARTES", "MIERCOLES", "JUEVES", "VIERNES", "SABADO",
            ]);
    
            if ($requestDate->dayOfWeek !== $allowedDay) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'La fecha seleccionada no corresponde al día permitido (' . $horarie->day . ').',
                ], 422);
            }
    
            // Obtener la acción existente
            $action = Action::findOrFail($id);
    
            // Procesar la imagen (si se sube una nueva)
            $imagePath = $action->image;
            if ($request->hasFile('image')) {
                // Eliminar la imagen anterior si existe
                if ($action->image && file_exists(storage_path('app/public/' . $action->image))) {
                    unlink(storage_path('app/public/' . $action->image));
                }
                // Guardar la nueva imagen
                $imagePath = $request->file('image')->store('public/actions');
                $imagePath = Storage::url($imagePath);
            }
    
            // Actualizar la acción
            $action->update([
                'date' => $request->date,
                'description' => $request->description,
                'image' => $imagePath,
            ]);
    
            return response()->json([
                'status' => 'success',
                'message' => 'Actividad actualizada correctamente.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error en la actualización: ' . $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $action = Action::findOrFail($id);
    
            // Eliminar la imagen si existe
            if ($action->image && file_exists(storage_path('app/public/' . $action->image))) {
                unlink(storage_path('app/public/' . $action->image));
            }
    
            $action->delete();
    
            return response()->json([
                'status' => 'success',
                'message' => 'Actividad eliminada correctamente.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al eliminar la actividad: ' . $e->getMessage(),
            ], 500);
        }
    }
}
