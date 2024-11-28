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
use Yajra\DataTables\Facades\DataTables;

class HorariesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $id = session('activitie_id');
        $act = Activitie::find($id);
    
        $horaries = DB::select('
            select 
                h.id, 
                h.day, 
                v.name as vehicle,  
                t.name as type, 
                h.starttime as hori, 
                h.lasttime as horf
            from horaries h
            inner join vehicles v on h.vehicle_id = v.id 
            inner join activities a on a.id = h.activitie_id 
            inner join typemantenimientos t on t.id = h.typemantenimiento_id 
            WHERE h.activitie_id = ?', [$id]);
    
        // Verificar si la solicitud es AJAX para DataTables
        if ($request->ajax()) {
            // Convertir el resultado de la consulta en una colección para facilitar el manejo
            $horariesCollection = collect($horaries);
    
            return DataTables::of($horariesCollection)
                ->addColumn('calendar', function ($horary) {
                    return '
                        <a href="' . route('admin.horaries.show', $horary->id) . '" class="btn btn-secondary btn-sm">
                            <i class="fas fa-wrench"></i>
                        </a>';
                })
                ->addColumn('edit', function ($horary) {
                    return '
                        <button class="btnEditar btn btn-primary btn-sm" id="' . $horary->id . '">
                            <i class="fa fa-edit"></i>
                        </button>';
                })
                ->addColumn('delete', function ($horary) {
                    return '
                        <form action="' . route('admin.horaries.destroy', $horary->id) . '" method="POST" class="frmEliminar d-inline">
                            ' . csrf_field() . method_field('DELETE') . '
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>';
                })
                ->rawColumns(['calendar', 'edit', 'delete']) // Declarar columnas que contienen HTML
                ->make(true);
        } else {
            // Si no es AJAX, devolver la vista normalmente
            return view('admin.horaries.index', compact('act', 'horaries'));
        }
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
