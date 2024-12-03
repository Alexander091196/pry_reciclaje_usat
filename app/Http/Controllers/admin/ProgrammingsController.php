<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Programming;
use App\Models\Route;
use App\Models\Routestatus;
use App\Models\Schedule;
use App\Models\Vehicle;
use App\Models\Vehicleroutes;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ProgrammingsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //$schedules = Schedule::pluck('name', 'id');
        $programmings = DB::select('select p.id, p.startdate, p.lastdate, s.name as turno from programmings p inner join schedules s on p.schedule_id  = s.id ');

        if ($request->ajax()) {

            return DataTables::of($programmings)
                ->addColumn('ver', function ($programming) {
                    return '<a href="' . route('admin.programming.show', $programming->id) . '" class="btn btn-danger btn-sm"><i class="fas fa-eye"></i></a>';
                })
                ->rawColumns(['ver'])  // Declarar columnas que contienen HTML
                ->make(true);
        } else {
            return view('admin.programming.index');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $vehicles = Vehicle::pluck('name', 'id');
        $routes = Route::pluck('name', 'id');
        $schedules = Schedule::pluck('name', 'id');

        return view('admin.programming.create', compact('vehicles', 'routes', 'schedules'));
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validar que la fecha de inicio sea menor o igual a la fecha final
        if (strtotime($request->startdate) > strtotime($request->lastdate)) {
            return redirect()->route('admin.programming.index')->with('error', 'La fecha de inicio no puede ser mayor que la fecha final.');
        }

        $p = [
            'startdate' => $request->startdate,
            'lastdate' => $request->lastdate,
            'vehicle_id' => $request->vehicle_id,
            'route_id' => $request->route_id,
            'schedule_id' => $request->schedule_id,
        ];

        // Validar si ya existe una programación
        if ($this->searchifexist($p) == 0) {
            $programming = Programming::create([
                'startdate' => $request->startdate,
                'lastdate' => $request->lastdate,
                'schedule_id' => $request->schedule_id,
            ]);

            // Crear rutas y programaciones
            $begin = new DateTime($request->startdate);
            $end = new DateTime($request->lastdate);
            $end->modify('+1 day'); // Incluir la última fecha en el período

            $interval = DateInterval::createFromDateString('1 days');
            $period = new DatePeriod($begin, $interval, $end);

            foreach ($period as $dt) {
                // Verificar si ya existe una ruta para esta fecha
                $existingRoute = Vehicleroutes::where('date_route', $dt->format("Y-m-d"))
                    ->where('vehicle_id', $request->vehicle_id)
                    ->where('route_id', $request->route_id)
                    ->first();

                if (!$existingRoute) {
                    // Crear solo si no existe
                    Vehicleroutes::create([
                        'date_route' => $dt->format("Y-m-d"),
                        'description' => '',
                        'vehicle_id' => $request->vehicle_id,
                        'route_id' => $request->route_id,
                        'routestatus_id' => 1,
                        'programming_id' => $programming->id,
                    ]);
                }
            }

            return redirect()->route('admin.programming.index')->with('success', 'Programación registrada y rutas actualizadas.');
        } else {
            return redirect()->route('admin.programming.index')->with('error', 'Ya existe una programación entre esos días, ruta y vehículo, por favor verifique.');
        }
    }



    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $programming = Programming::findOrFail($id);

        $listado = DB::select("
        select 
            v.id, 
            v.date_route as fecha, 
            v2.name as vehiculo, 
            r2.name as ruta,  
            r.name as estado, 
            s.name as turno 
        from programmings p 
        inner join vehicleroutes v on v.programming_id = p.id 
        inner join vehicles v2 on v2.id =v.vehicle_id 
        inner join routes r2 on r2.id = v.route_id 
        inner join routestatus r on r.id = v.routestatus_id 
        inner join schedules s on s.id =p.schedule_id 
        where p.id =?
    ", [$id]);

        if ($request->ajax()) {

            return DataTables::of($listado)
                ->addColumn('actions', function ($listado) {
                    return '      
                            <button class="btnEditar btn btn-primary btn-sm" id="' . $listado->id . '">
                            <i class="fa fa-edit"></i>
                        </button>';
                })
                ->rawColumns(['actions'])  // Declarar columnas que contienen HTML
                ->make(true);
        } else {
            return view('admin.programming.show', compact('programming', 'listado'));
        }
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // $programming = Vehicleroutes::find($id);
        // $programming->delete();
        // return redirect()->route('admin.programming.index')->with('success', 'Programacion eliminada');

    }

    public function searchifexist($p)
    {
        $programacion = DB::select(
            'select * from vehicleroutes where vehicle_id=? and route_id=? and date_route between ? and ?',
            [
                $p['vehicle_id'],
                $p['route_id'],
                $p['startdate'],
                $p['lastdate']
            ]
        );
        $exist = count($programacion);
        if ($exist > 0) {
            return 1;
        } else {
            return 0;
        }
    }
    
}
