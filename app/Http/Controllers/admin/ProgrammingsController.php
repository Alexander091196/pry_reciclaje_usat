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
        $p = [
            'startdate' => $request->startdate,
            'lastdate' => $request->lastdate,
            'vehicle_id' => $request->vehicle_id,
            'route_id' => $request->route_id,
            'schedule_id' => $request->schedule_id, // Aquí agregamos el turno seleccionado
        ];

        if ($this->searchifexist($p) == 0) {
            $p = Programming::create([
                'startdate' => $request->startdate,
                'lastdate' => $request->lastdate,
                'schedule_id' => $request->schedule_id, // Guardamos el turno
            ]);

            // Crear las rutas y programación
            $begin = new DateTime($request->startdate);
            $fechafinal = date($request->lastdate);
            $final = date("d-m-Y", strtotime($fechafinal . '+ 1 days'));

            $interval = DateInterval::createFromDateString('1 day');
            $period = new DatePeriod($begin, $interval, new DateTime($final));

            foreach ($period as $dt) {
                Vehicleroutes::create([
                    'date_route' => $dt->format("Y-m-d"),
                    'description' => '',
                    'vehicle_id' => $request->vehicle_id,
                    'route_id' => $request->route_id,
                    'routestatus_id' => 1,
                    'programming_id' => $p->id
                ]);
            }

            return redirect()->route('admin.programming.index')->with('success', 'Programación registrada');
        } else {
            return redirect()->route('admin.programming.index')->with('error', 'Ya existe una programación entre esos días, ruta y vehículo, por favor verifique');
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
                            <form action="' . route('admin.programming.destroy', $listado->id) . '" method="POST" class="frmEliminar d-inline">
                                ' . csrf_field() . method_field('DELETE') . '
                                <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                            </form>';
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
        // Buscar el registro de Vehicleroutes con la relación 'programming'
        $vr = Vehicleroutes::with('programming')->find($id);

        // Verificar si se encontró el registro
        if (!$vr) {
            return redirect()->route('admin.programming.index')->with('error', 'Programación no encontrada.');
        }

        // Obtener los datos necesarios para llenar los campos del formulario
        $vehicles = Vehicle::pluck('name', 'id');
        $routes = Route::pluck('name', 'id');
        $schedules = Schedule::pluck('name', 'id');

        // Pasar los datos a la vista
        return view('admin.programming.edit', compact('vr', 'vehicles', 'routes', 'schedules'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Buscar el registro de Vehicleroutes con la relación 'programming'
        $vr = Vehicleroutes::with('programming')->find($id);

        // Verificar si se encontró el registro
        if (!$vr) {
            return redirect()->route('admin.programming.index')->with('error', 'Programación no encontrada.');
        }

        // Validar los datos del formulario si es necesario
        $request->validate([
            'description' => 'required|string|max:255',
            'routestatus_id' => 'required|exists:routestatus,id',
            // Aquí puedes agregar más validaciones si es necesario
        ]);

        // Actualizar los valores de 'Vehicleroutes'
        $vr->update([
            'description' => $request->description,
            'routestatus_id' => $request->routestatus_id,
        ]);

        // Redirigir con un mensaje de éxito
        return redirect()->route('admin.programming.index')->with('success', 'Programación actualizada correctamente.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $programming = Vehicleroutes::find($id);
        $programming->delete();
        return redirect()->route('admin.programming.index')->with('success', 'Programacion eliminada');
    }

    public function searchprogramming(Request $request)
    {
        $listado = DB::select(
            'SELECT 
                vr.id, 
                vr.date_route AS fecha, 
                rs.name AS estado, 
                v.name AS vehiculo, 
                r.name AS ruta, 
                s.name AS turno, -- Traemos el nombre del turno
                vr.description
            FROM vehicleroutes vr
            INNER JOIN routes r ON vr.route_id = r.id
            INNER JOIN vehicles v ON vr.vehicle_id = v.id
            INNER JOIN programmings p ON vr.programming_id = p.id
            INNER JOIN routestatus rs ON vr.routestatus_id = rs.id
            INNER JOIN schedules s ON p.schedule_id = s.id -- Relacionamos con los turnos
                WHERE vr.vehicle_id = ? 
                AND vr.route_id = ? 
                AND vr.date_route BETWEEN ? AND ?',
            [
                $request->vehicle_id,
                $request->route_id,
                $request->startdate,
                $request->lastdate,
            ]
        );

        return view('admin.programming.list', compact('listado'));
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
