<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Usertype;
use App\Models\Vehicle;
use App\Models\Vehicleoccupant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OccupantsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $occ = Vehicleoccupant::all();
        return view('admin.vehicles.index', compact('occ'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.occupant.create');
    }

    /**
     * Store a newly created resource in storage.
     */


    public function store(Request $request)
    {
        $assignment_date = $request->input('fecha');
        $vehicle_id = $request->input('vehicle_id');
        $dni = $request->user_id;

        $occupantSQL = DB::table('users')
            ->select('id', 'name', 'usertype_id')
            ->where('dni', $dni)
            ->first();

        $id = $occupantSQL->id;
        $usertype_id = $occupantSQL->usertype_id;

        // Verificar si el usuario ya está en otro vehículo
        $existingOccupant = Vehicleoccupant::where('user_id', $id)
            ->where('status', '1')
            ->first();

        if ($existingOccupant) {
            // Pasar los datos a la sesión
            session()->flash('warning', 'El usuario ya está asignado a otro vehículo. ¿Desea continuar?');
            session()->flash('user_id', $id);
            session()->flash('vehicle_id', $vehicle_id);
            session()->flash('assignment_date', $assignment_date);
            session()->flash('usertype_id', $usertype_id);

            return redirect()->back();
            //return redirect()->route('admin.vehicles.show', $request->vehicle_id)->with('success', 'Asignación registrada!');

        }

        // Verificar si ya existe un registro activo para este usuario y vehículo
        $existingOccupantV = Vehicleoccupant::where('user_id', $id)
            ->where('status', '1')
            ->where('vehicle_id', $vehicle_id)
            ->first();

        if ($existingOccupantV) {
            // Mostrar la advertencia en la vista
            return redirect()->back()->with('warning', 'El usuario ya está asignado a este vehículo.');
        }

        // Obtener la capacidad del vehículo
        $vehicle = Vehicle::find($vehicle_id);
        $vehicleCapacity = $vehicle->occupant_capacity;

        // Contar el número de ocupantes
        $activeOccupants = Vehicleoccupant::where('vehicle_id', $vehicle_id)
            ->where('status', '1')
            ->count();

        // Validar si se puede agregar un nuevo ocupante
        if ($activeOccupants >= $vehicleCapacity) {
            return redirect()->back()->with('error', 'No se puede asignar más ocupantes. La capacidad máxima del vehículo es de ' . $vehicleCapacity . ' personas.');
        }

        // Crear la nueva asignación
        $newOccupant = new Vehicleoccupant();
        $newOccupant->assignment_date = $assignment_date;
        $newOccupant->status = '1';
        $newOccupant->vehicle_id = $vehicle_id;
        $newOccupant->user_id = $id;
        $newOccupant->usertype_id = $usertype_id;
        $newOccupant->save();

        return redirect()->route('admin.vehicles.show', $request->vehicle_id)->with('success', 'Asignación registrada!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $vehicle = Vehicle::find($id);
        $useres = User::find($id);
        $usertype = Usertype::find($id);
        return view('admin.occupant.create', compact('vehicle', 'useres', 'usertype'));
    }

    /**
     * Update the specified resource in storage.
     */

    public function update($occupantId)
    {
        // Encontrar el ocupante por ID
        $occupant = Vehicleoccupant::with('vehicle')->findOrFail($occupantId);

        if ($occupant->vehicle) {
            $occupant->status = 0;
            $occupant->save();

            return redirect()->route('admin.vehicles.show', $occupant->vehicle->id)
                ->with('success', 'Ocupante dado de baja correctamente.');
        } else {
            return redirect()->route('admin.occupants.index')
                ->with('error', 'No se pudo dar de baja al ocupante porque no tiene un vehículo relacionado.');
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }


    public function searchbydni(string $id)
    {
        $user = User::where('dni', $id)->first();

        if ($user) {
            $usertype = '';
            if ($user->usertype_id == 3) {
                $usertype = 'Conductor';
            } elseif ($user->usertype_id == 4) {
                $usertype = 'Recolector';
            }

            return response()->json([
                'usertype_id' => $usertype,
                'name' => $user->name,
                'lastname' => $user->lastname,
            ]);
        }
        //session()->flash('error', 'Tipo de usuario no válido.');
        return response()->json([]); 
    }

    //Funcion de confirmacion antes de dar de baja
    public function confirmAssignment(Request $request)
    {
        $user_id = $request->input('user_id');
        $vehicle_id = $request->input('vehicle_id');
        $assignment_date = $request->input('assignment_date');
        $usertype_id = $request->input('usertype_id');

        // Dar de baja al usuario del vehículo anterior
        $existingOccupant = Vehicleoccupant::where('user_id', $user_id)
            ->where('status', '1')
            ->first();

        if ($existingOccupant) {
            $existingOccupant->status = '0';
            $existingOccupant->save();
        }

        // Obtener la capacidad del vehículo
        $vehicle = Vehicle::find($vehicle_id);
        $vehicleCapacity = $vehicle->occupant_capacity;

        // Contar el número de ocupantes activos para este vehículo
        $activeOccupants = Vehicleoccupant::where('vehicle_id', $vehicle_id)
            ->where('status', '1')
            ->count();

        // Validar si se puede agregar un nuevo ocupante
        if ($activeOccupants >= $vehicleCapacity) {
            return response()->json(['status' => 'error', 'message' => 'No se puede asignar más ocupantes. La capacidad máxima del vehículo es de ' . $vehicleCapacity . ' personas.']);
        }
        // Crear la nueva asignación
        $newOccupant = new Vehicleoccupant();
        $newOccupant->assignment_date = $assignment_date;
        $newOccupant->status = '1';
        $newOccupant->vehicle_id = $vehicle_id;
        $newOccupant->user_id = $user_id;
        $newOccupant->usertype_id = $usertype_id;
        $newOccupant->save();

        return response()->json(['status' => 'success', 'message' => 'Asignación registrada!']);
    }
}
