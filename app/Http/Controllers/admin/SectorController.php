<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\Sector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class SectorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sector = Sector::all();

        if ($request->ajax()) {

            return DataTables::of($sector)
                ->addColumn('actions', function ($sector) {
                    return '
                        <div class="dropdown">
                            <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-bars"></i>                        
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <button class="dropdown-item btnEditar" id="' . $sector->id . '"><i class="fas fa-edit"></i>  Editar</button>
                                <form action="' . route('admin.sectors.destroy', $sector->id) . '" method="POST" class="frmEliminar d-inline">
                                    ' . csrf_field() . method_field('DELETE') . '
                                    <button type="submit" class="dropdown-item"><i class="fas fa-trash"></i> Eliminar</button>
                                </form>
                            </div>
                        </div>';
                })
                ->addColumn('coords', function ($sector) {
                    return '<button class="btn btn-danger btn-sm btnMap" id=' . $sector->id . '><i class="fas fa-map-marked-alt"></i></button>';
                })
                ->rawColumns(['actions', 'coords'])  // Declarar columnas que contienen HTML
                ->make(true);
        } else {
            return view('admin.sectors.index', compact('sector'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        $districts = District::pluck('name', 'id'); // Genera un array clave-valor
        return view('admin.sectors.create', compact('districts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'area' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'district_id' => 'required|exists:districts,id',
        ]);

        try {
            Sector::create([
                'name' => $request->name,
                'area' => $request->area,
                'description' => $request->description,
                'district_id' => $request->district_id,
            ]);

            return response()->json(['message' => 'Sector registrado correctamente'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error en el registro: ' . $th->getMessage()], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $zones = collect(DB::select("CALL sp_zones(3," . $id . ")"));

        $groupedZones = $zones->groupBy("zone");

        $perimeter = $groupedZones->map(function ($zone) {

            $coords = $zone->map(function ($item) {
                return [
                    'lat' => $item->latitude,
                    'lng' => $item->longitude
                ];
            })->toArray();

            return [
                'name' => $zone[0]->zone,
                'coords' => $coords
            ];
        })->values();


        return view('admin.sectors.show', compact('perimeter'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $sector = Sector::findOrFail($id); // Encuentra el sector o lanza un error si no existe
        $districts = District::pluck('name', 'id'); // Obtiene los distritos en formato clave-valor
        return view('admin.sectors.edit', compact('sector', 'districts'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'area' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'district_id' => 'required|exists:districts,id',
        ]);

        try {
            $sector = Sector::findOrFail($id);

            $sector->update([
                'name' => $request->name,
                'area' => $request->area,
                'description' => $request->description,
                'district_id' => $request->district_id,
            ]);

            return response()->json(['message' => 'Sector actualizado correctamente'], 200);
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
            // Verificar si el sector está asignado a alguna zona
            $sectorAssigned = DB::table('zones')->where('sector_id', $id)->exists();
    
            if ($sectorAssigned) {
                return response()->json(['message' => 'No se puede eliminar el sector porque está asignado a una zona'], 400);
            }
    
            // Si no está asignado a ninguna zona, proceder con la eliminación
            $sector = Sector::findOrFail($id);
            $sector->delete();
    
            return response()->json(['message' => 'Sector eliminado correctamente'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error al eliminar: ' . $th->getMessage()], 500);
        }
    }
    
}
