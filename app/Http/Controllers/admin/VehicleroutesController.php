<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Programming;
use App\Models\Route;
use App\Models\Routestatus;
use App\Models\Schedule;
use App\Models\Vehicle;
use App\Models\Vehicleroutes;
use Illuminate\Http\Request;

class VehicleroutesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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

        $vehicleroute = Vehicleroutes::findOrFail($id);

        // Listar las opciones necesarias para los selects
        $vehicles = Vehicle::pluck('name', 'id');
        $routes = Route::pluck('name', 'id');
        $routestatus = Routestatus::pluck('name', 'id');
        $schedules = Schedule::pluck('name', 'id');

        return view('admin.vehicleroutes.edit', compact('vehicleroute', 'vehicles', 'routes', 'routestatus', 'schedules'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $vehicleroute = Vehicleroutes::findOrFail($id);

        // Validar los datos ingresados
        $request->validate([
            'routestatus_id' => 'required|exists:routestatus,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'route_id' => 'required|exists:routes,id',
        ]);

        // Actualizar los datos
        $vehicleroute->update([
            'routestatus_id' => $request->routestatus_id,
            'vehicle_id' => $request->vehicle_id,
            'route_id' => $request->route_id,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.programming.index')->with('success', 'Ruta de vehículo actualizada exitosamente.');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function massUpdate(Request $request, string $id)
    {
        // Obtener el registro de la programación por su ID
        $programming = Programming::findOrFail($id);


        // Listar las opciones necesarias para los selects
        $vehicles = Vehicle::pluck('name', 'id');
        $routes = Route::pluck('name', 'id');
        $routestatus = Routestatus::pluck('name', 'id');
        $schedules = Schedule::pluck('name', 'id');

        $description = $request->description; // Si es necesario

        // Obtener las fechas desde la programación original
        $startDate = $programming->start_date;
        $endDate = $programming->end_date;

        // Buscar los registros de Vehicleroutes dentro del rango de fechas
        $vehicleroutes = Vehicleroutes::where('programming_id', $programming->id)
            ->whereBetween('date_route', [$startDate, $endDate])
            ->get();

        // Actualizar los registros con los nuevos valores proporcionados
        foreach ($vehicleroutes as $vehicleroute) {
            $vehicleroute->update([
                'routestatus_id' => $routestatus,
                'vehicle_id' => $vehicles,
                'schedule_id' => $schedules,
                'route_id' => $routes,
                'description' => $description ?? $vehicleroute->description, // Mantener la descripción si no se proporciona
            ]);
        }

        return redirect()->route('admin.programming.show', $programming->id )
            ->with('success', 'Las programaciones han sido actualizadas exitosamente.');
    }
}
