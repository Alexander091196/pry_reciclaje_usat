<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Action;
use App\Models\Activitie;
use App\Models\Horarie;
use App\Models\Typemantenimiento;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HorariesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $id = session('activitie_id');
        $act = Activitie::find($id);

        $horaries = DB::select('select h.id, h.day,v.name as vehicle,  t.name as type, h.starttime as hori, h.lasttime as horf
from horaries h
inner join vehicles v on h.vehicle_id = v.id 
inner join activities a on a.id = h.activitie_id 
inner join typemantenimientos t on t.id = h.typemantenimiento_id 
            WHERE  h.activitie_id = ?', [$id]);


        return view('admin.horaries.index', compact('act', 'horaries'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $vehicles = Vehicle::pluck('name', 'id');
        $types = Typemantenimiento::pluck('name', 'id');
        return view('admin.horaries.create', compact('types', 'vehicles'));
    }

    // public function store(Request $request)
    // {
    //     $request['activitie_id'] = session('activitie_id');
    //     Horarie::create($request->all());
    //     return redirect()->route('admin.horaries.index')->with('success', 'Horario registrado');
    // }

    public function store(Request $request)
    {
        $request['activitie_id'] = session('activitie_id');

        // Convertir starttime y lasttime a formato de 24 horas
        $starttime = date('H:i:s', strtotime($request->starttime));
        $lasttime = date('H:i:s', strtotime($request->lasttime));

        // Verificar si ya existe un horario con el mismo día, tipo de mantenimiento y rango de hora
        $existingHorarie = Horarie::where('day', $request->day)
            ->where('typemantenimiento_id', $request->typemantenimiento_id)
            ->where('vehicle_id', $request->vehicle_id)
            ->where(function ($query) use ($starttime, $lasttime) {
                $query->whereBetween('starttime', [$starttime, $lasttime])
                    ->orWhereBetween('lasttime', [$starttime, $lasttime])
                    ->orWhere(function ($query) use ($starttime, $lasttime) {
                        $query->where('starttime', '<=', $starttime)
                            ->where('lasttime', '>=', $lasttime);
                    });
            })
            ->exists();

        if ($existingHorarie) {
            return redirect()->route('admin.horaries.index')->with('error', 'Ya existe un horario registrado con el mismo día, tipo de mantenimiento y dentro del mismo rango de horas');

            //return redirect()->back()->withErrors(['error' => 'Ya existe un horario registrado con el mismo día, tipo de mantenimiento y dentro del mismo rango de horas.'])->withInput();
        }

        Horarie::create($request->all());
        return redirect()->route('admin.horaries.index')->with('success', 'Horario registrado');
    }




    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        session(['horarie_id' => $id]);
        $id = session('horarie_id');
        $hor = Horarie::find($id);
        //$act = Activitie::find($id);

        $actions = DB::select('SELECT AC.id, AC.date, AC.description 
        FROM actions AC
        INNER JOIN horaries H ON AC.horarie_id=H.id
        WHERE AC.horarie_id= ?', [$id]);

        return view('admin.actions.index', compact('hor', 'actions'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $hor = Horarie::find($id);
        $vehicles = Vehicle::pluck('name', 'id');
        $types = Typemantenimiento::pluck('name', 'id');
        return view('admin.horaries.edit', compact('hor', 'vehicles', 'types'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request['activitie_id'] = session('activitie_id');

        // Convertir starttime y lasttime a formato de 24 horas
        $starttime = date('H:i:s', strtotime($request->starttime));
        $lasttime = date('H:i:s', strtotime($request->lasttime));

        // Verificar si ya existe un horario con el mismo día, tipo de mantenimiento y rango de hora
        $existingHorarie = Horarie::where('day', $request->day)
            ->where('typemantenimiento_id', $request->typemantenimiento_id)
            ->where('vehicle_id', $request->vehicle_id)
            ->where(function ($query) use ($starttime, $lasttime) {
                $query->whereBetween('starttime', [$starttime, $lasttime])
                    ->orWhereBetween('lasttime', [$starttime, $lasttime])
                    ->orWhere(function ($query) use ($starttime, $lasttime) {
                        $query->where('starttime', '<=', $starttime)
                            ->where('lasttime', '>=', $lasttime);
                    });
            })
            ->where('id', '!=', $id) // Asegúrate de excluir el horario actual
            ->exists();

        if ($existingHorarie) {
            return redirect()->route('admin.horaries.index')->with('error', 'Ya existe un horario registrado con el mismo día, tipo de mantenimiento y dentro del mismo rango de horas');
        }

        $horary = Horarie::find($id);
        $horary->update($request->all());
        return redirect()->route('admin.horaries.index')->with('success', 'Horario actualizado');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $horary = Horarie::find($id);
        $action = Action::where('horarie_id', $id)->count();
        if ($action > 0) {
            return redirect()->route('admin.horaries.index')->with('error', 'Horario contiene activiades asociados.');
        } else {
            $horary->delete();
            return redirect()->route('admin.horaries.index')->with('success', 'Horario eliminado correctamente.');
        }
    }
}
