<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Vehiclecolor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class VehiclecolorsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
           // Consulta los colores desde la base de datos utilizando DB::select
    $vehiclecolors = DB::select('select id, name, red, green, blue, description from vehiclecolors');

    if ($request->ajax()) {
        return DataTables::of($vehiclecolors)
            ->addColumn('actions', function ($vehiclecolor) {
                return '
                <div class="dropdown">
                    <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton-' . $vehiclecolor->id . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-bars"></i>                        
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton-' . $vehiclecolor->id . '">
                        <button class="dropdown-item btnEditar" id="' . $vehiclecolor->id . '">
                            <i class="fas fa-edit"></i> Editar
                        </button>
                        <form action="' . route('admin.vehiclecolors.destroy', $vehiclecolor->id) . '" method="POST" class="frmEliminar">
                            ' . csrf_field() . method_field('DELETE') . '
                            <button type="submit" class="dropdown-item">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </form>
                    </div>
                </div>';
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    return view('admin.vehiclecolors.index');
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.vehiclecolors.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validación de los datos de entrada
        $request->validate([
            'name' => 'required|string|max:100',
            'red' => 'required|integer|between:0,255',   // Validación para el valor de rojo
            'green' => 'required|integer|between:0,255', // Validación para el valor de verde
            'blue' => 'required|integer|between:0,255',  // Validación para el valor de azul
            'description' => 'nullable|string|max:255',
        ]);

        try {
            Vehiclecolor::create([
                'name' => $request->name,
                'red' => $request->red,
                'green' => $request->green,
                'blue' => $request->blue,
                'description' => $request->description,
            ]);

            return response()->json(['message' => 'Color registrado correctamente'], 200);
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
        $vehiclecolor = Vehiclecolor::findOrFail($id);

        // Convertir valores RGB a formato HEX para el input de color
        $vehiclecolor->color_code = sprintf("#%02x%02x%02x", $vehiclecolor->red, $vehiclecolor->green, $vehiclecolor->blue);
    
        return view('admin.vehiclecolors.edit', compact('vehiclecolor'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'red' => 'required|integer|between:0,255',
            'green' => 'required|integer|between:0,255',
            'blue' => 'required|integer|between:0,255',
            'description' => 'nullable|string|max:255',
        ]);
    
        try {
            $vehiclecolor = Vehiclecolor::findOrFail($id);
    
            // Actualizar únicamente los campos necesarios
            $vehiclecolor->update([
                'name' => $request->name,
                'red' => $request->red,
                'green' => $request->green,
                'blue' => $request->blue,
                'description' => $request->description,
            ]);
    
            return response()->json(['message' => 'Color actualizado correctamente'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error al actualizar: ' . $th->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            // Verificar si el color de vehículo está asociado a algún vehículo
            $asignadoAVehiculo = DB::table('vehicles')
                ->where('color_id', $id)
                ->exists();
    
            if ($asignadoAVehiculo) {
                return response()->json([
                    'message' => 'No se puede eliminar el color porque está asociado a un vehículo'
                ], 400);
            }
    
            // Eliminar el color si no está asociado
            DB::table('vehiclecolors')->where('id', $id)->delete();
    
            return response()->json(['message' => 'Color eliminado correctamente'], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error de eliminación',
                'error' => $th->getMessage() // Para depuración, opcional
            ], 500);
        }
    }
}
