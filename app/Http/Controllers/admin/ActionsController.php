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
            // Obtener horario y actividad
            $horarie = Horarie::find(session('horarie_id'));
            $activitie = Activitie::find($horarie->activitie_id);

            // Validar rango de fecha y día de la semana
            if ($request->date < $activitie->startdate || $request->date > $activitie->lastdate) {
                return redirect()->route('admin.actions.index')
                    ->with('error', 'La fecha de la acción debe estar dentro del rango de fechas de la actividad.');
            }

            $dayOfWeek = date('l', strtotime($request->date));
            if ($dayOfWeek !== $horarie->day) {
                return redirect()->route('admin.actions.index')
                    ->with('error', 'La fecha de la acción debe coincidir con el día del horario.');
            }

            // Guardar imagen
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('public/actions');
                $imagePath = Storage::url($imagePath);
            }

            // Guardar la acción
            Action::create([
                'date' => $request->date,
                'description' => $request->description,
                'image' => $imagePath,
                'horarie_id' => session('horarie_id')
            ]);

            return redirect()->route('admin.actions.index')->with('success', 'Actividad registrada correctamente');
        } catch (\Throwable $th) {
            return redirect()->route('admin.actions.index')->with('error', 'Error en el registro: ' . $th->getMessage());
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
            // Validación
            $validatedData = $request->validate([
                'date' => 'required|date',
                'description' => 'required|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            // Verificar horario y actividad
            $horarie = Horarie::find(session('horarie_id'));
            $activitie = Activitie::find($horarie->activitie_id);

            // Validar que la fecha esté dentro del rango
            if ($request->date < $activitie->startdate || $request->date > $activitie->lastdate) {
                return redirect()->route('admin.actions.index')
                    ->with('error', 'La fecha de la acción debe estar dentro del rango de fechas de la actividad.');
            }

            $dayOfWeek = date('l', strtotime($request->date));
            if ($dayOfWeek !== $horarie->day) {
                return redirect()->route('admin.actions.index')
                    ->with('error', 'La fecha de la acción debe coincidir con el día del horario.');
            }

            // Obtener acción existente
            $action = Action::findOrFail($id);

            // Procesar la imagen (si se sube una nueva)
            $imagePath = $action->image;
            if ($request->hasFile('image')) {
                if ($action->image && file_exists(storage_path('app/public/' . $action->image))) {
                    unlink(storage_path('app/public/' . $action->image)); // Eliminar la imagen anterior
                }
                $imagePath = $request->file('image')->store('public/actions');
                $imagePath = Storage::url($imagePath);
            }

            // Actualizar la acción
            $action->update([
                'date' => $request->date,
                'description' => $request->description,
                'image' => $imagePath,
                'horarie_id' => session('horarie_id')
            ]);

            return redirect()->route('admin.actions.index')->with('success', 'Actividad actualizada correctamente');
        } catch (\Throwable $th) {
            return redirect()->route('admin.actions.index')->with('error', 'Error en la actualización: ' . $th->getMessage());
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
                unlink(storage_path('app/public/' . $action->image)); // Eliminar imagen
            }
            $action->delete();

            return redirect()->route('admin.actions.index')->with('success', 'Actividad eliminada correctamente');
        } catch (\Throwable $th) {
            return redirect()->route('admin.actions.index')->with('error', 'Error al eliminar la actividad: ' . $th->getMessage());
        }
    }
}
