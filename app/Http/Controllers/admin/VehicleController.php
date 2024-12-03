<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Brandmodel;
use App\Models\Vehicle;
use App\Models\Vehiclecolor;
use App\Models\Vehicleimage;
use App\Models\Vehicletype;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class VehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
{
    $vehicles = DB::select('CALL sp_vehicles');

    // Asignar un valor predeterminado a logo si es null
    foreach ($vehicles as $vehicle) {
        $vehicle->logo = $vehicle->logo ?? 'storage/brand_logo/no_image.png';
    }

    if ($request->ajax()) {
        return DataTables::of($vehicles)
            ->addColumn('logo', function ($vehicle) {
                return '<img src="' . asset($vehicle->logo) . '" width="100px" height="70px" class="card">';
            })
            ->addColumn('status', function ($vehicle) {
                return $vehicle->status == 1 ? '<div style="color: green"><i class="fas fa-check"></i> Activo</div>' : '<div style="color: red"><i class="fas fa-times"></i> Inactivo</div>';
            })
            ->addColumn('actions', function ($vehicle) {
                return '
                <div class="dropdown">
                    <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-bars"></i>                        
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <button class="dropdown-item btnEditar" id="' . $vehicle->id . '"><i class="fas fa-edit"></i>  Editar</button>
                        <button class="dropdown-item btnImagenes" id="' . $vehicle->id . '"><i class="fas fa-image"></i>  Imágenes</button>
                        <form action="' . route('admin.vehicles.destroy', $vehicle->id) . '" method="POST" class="frmEliminar d-inline">
                            ' . csrf_field() . method_field('DELETE') . '
                            <button type="submit" class="dropdown-item"><i class="fas fa-trash"></i> Eliminar</button>
                        </form>
                    </div>
                </div>';
            })
            ->addColumn('occupants', function ($vehicle) {
                return '<a href="' . route('admin.vehicles.occupants', $vehicle->id) . '" class="btn btn-success btn-sm">
                            <i class="fas fa-people-arrows"></i>&nbsp;&nbsp;(0)
                        </a>';
            })            
            ->rawColumns(['logo', 'status', 'occupants', 'actions'])  // Declarar columnas que contienen HTML
            ->make(true);
    }

    return view('admin.vehicles.index', compact('vehicles'));
}


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        $brandsSQL = Brand::whereRaw("id IN (SELECT brand_id FROM brandmodels)");
        $brands = $brandsSQL->pluck("name", "id");
        $models = Brandmodel::where("brand_id", $brandsSQL->first()->id)->pluck("name", "id");
        $types = Vehicletype::pluck("name", "id");
        $colors = Vehiclecolor::all()->mapWithKeys(function ($color) {
            return [$color->id => [
                'name' => $color->name,
                'rgb' => "rgb({$color->red},{$color->green},{$color->blue})"
            ]];
        });
        return view("admin.vehicles.create", compact("brands", "models", "types", "colors"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {

            $request->validate([
                "name" => "unique:vehicles",
                "code" => "unique:vehicles",
                "plate" => "required|string|max:7|unique:vehicles"
                
            ]);

            if (!isset($request->status)) {
                $status = 0;
            } else {
                $status = 1;
            }

            $vehicle = Vehicle::create($request->except("image") + ["status" => $status]);

            if ($request->image != "") {
                $image = $request->file("image")->store("public/vehicles_images/" . $vehicle->id);
                $urlImage = Storage::url($image);
                Vehicleimage::create([
                    "image" => $urlImage,
                    "profile" => 1,
                    "vehicle_id" => $vehicle->id
                ]);
            }

            return response()->json(['message' => 'Vehículo registrado correctamente'], 200);
        } catch (\Throwable $th) {

            return response()->json(['message' => 'Error en el registro: ' . $th->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
{
    $vehicle = Vehicle::findOrFail($id);

    $occupants = DB::select("
        SELECT 
            o.id, 
            u.name AS usernames, 
            ut.name AS usertype, 
            o.assignment_date AS date
        FROM vehicleoccupants o  
        INNER JOIN vehicles v ON o.vehicle_id = v.id
        INNER JOIN users u ON o.user_id = u.id
        INNER JOIN usertypes ut ON o.usertype_id = ut.id
        WHERE o.status = 1
        AND v.id = ?
    ", [$id]);

    $images = Vehicleimage::where("vehicle_id", $id)->get();

    return view("admin.vehicles.show", compact("vehicle", "occupants", "images"));
}



    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
       
    $vehicle = Vehicle::findOrFail($id); // Encuentra el vehículo
    $brands = Brand::pluck('name', 'id'); // Marcas
    $models = Brandmodel::where('brand_id', $vehicle->brand_id)->pluck('name', 'id'); // Modelos según la marca
    $types = Vehicletype::pluck('name', 'id'); // Tipos de vehículo
    $colors = Vehiclecolor::all()->mapWithKeys(function ($color) {
        return [$color->id => [
            'name' => $color->name,
            'rgb' => "rgb({$color->red},{$color->green},{$color->blue})"
        ]];
    });

    return view('admin.vehicles.edit', compact('vehicle', 'brands', 'models', 'types', 'colors'));
    
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $request->validate([
                "name" => "unique:vehicles,name," . $id,
                "code" => "unique:vehicles,code," . $id,
                "plate" => "required|string|max:7|size:7|unique:vehicles,plate," . $id,
                "color_id" => "required|exists:vehiclecolors,id" // Validar que el color existe
            ]);
    
            $status = $request->has('status') ? 1 : 0;
    
            $vehicle = Vehicle::findOrFail($id);
    
            $vehicle->update($request->except("image") + ["status" => $status]);
    
            if ($request->hasFile('image')) {
                $image = $request->file("image")->store("public/vehicles_images/" . $vehicle->id);
                $urlImage = Storage::url($image);
    
                // Actualizar imagen de perfil
                DB::table("vehicleimages")->where("vehicle_id", $id)->update(["profile" => 0]);
                Vehicleimage::create([
                    "image" => $urlImage,
                    "profile" => 1,
                    "vehicle_id" => $vehicle->id
                ]);
            }
    
            return response()->json(['message' => 'Vehículo actualizado correctamente'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error en la actualización: ' . $th->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

        try {
            $vehicle = Vehicle::find($id);
            $vehicle->delete();
            return response()->json(['message' => 'Vehículo eliminado correctamente'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error la eliminación: ' . $th->getMessage()], 500);
        }
    }
} 
