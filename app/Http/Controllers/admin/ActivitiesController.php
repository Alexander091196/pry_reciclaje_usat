<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activitie;
use App\Models\Horarie;
use App\Models\Programming;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ActivitiesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $activities = Activitie::select(
            'activities.id',
            'activities.name',
            'activities.startdate',
            'activities.lastdate'
        )->get();

        if ($request->ajax()) {

            return DataTables::of($activities)
                ->addColumn('calendar', function ($activitie) {
                    return '
                        <a href="' . route('admin.activities.show', $activitie->id) . '" class="btn btn-secondary btn-sm">
                            <i class="fas fa-calendar-alt"></i>
                        </a>';
                })
                ->addColumn('edit', function ($activitie) {
                    return '
                        <button class="btnEditar btn btn-primary btn-sm" id="' . $activitie->id . '">
                            <i class="fa fa-edit"></i>
                        </button>';
                })
                ->addColumn('delete', function ($activitie) {
                    return '
                        <form action="' . route('admin.activities.destroy', $activitie->id) . '" method="POST" class="frmEliminar d-inline">
                            ' . csrf_field() . method_field('DELETE') . '
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>';
                })
                ->rawColumns(['actions', 'calendar', 'edit', 'delete']) // Declarar columnas que contienen HTML
                ->make(true);
        } else {
            return view('admin.activities.index', compact('activities'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.activities.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'startdate' => 'required|date',
            'lastdate' => 'required|date|after_or_equal:startdate'
        ]);

        Activitie::create($request->all());
        return redirect()->route('admin.activities.index')->with('success', 'Actividad registrada');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

        session(['activitie_id' => $id]);
        $id = session('activitie_id');
        $act = Activitie::find($id);
        $horaries = DB::select('SELECT HR.id, HR.day, v.name as vehicle, U.name as conductor, TM.name as type, HR.starttime as hori, HR.lasttime as horf FROM horaries HR
        INNER JOIN vehicles V ON HR.vehicle_id=V.id
        INNER JOIN vehicleoccupants VO ON V.id=VO.vehicle_id
        INNER JOIN users U ON VO.user_id=U.id
        INNER JOIN activities AC ON HR.activitie_id=AC.id
        INNER JOIN typemantenimientos TM ON HR.typemantenimiento_id=TM.id
        WHERE vo.usertype_id=3 AND HR.activitie_id= ?', [$id]);

        return view('admin.horaries.index', compact('act', 'horaries'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $act = Activitie::find($id);
        return view('admin.activities.edit', compact('act'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required',
            'startdate' => 'required|date',
            'lastdate' => 'required|date|after_or_equal:startdate'
        ]);

        $act = Activitie::find($id);
        $act->update($request->all());
        return redirect()->route('admin.activities.index')->with('success', 'Actividad actualizada');;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $act = Activitie::find($id);

        $hor = Horarie::where('activitie_id', $id)->count();

        if ($hor > 0) {
            return redirect()->route('admin.activities.index')->with('error', 'Mantenimiento contiene horarios asociados');
        } else {
            $act->delete();
            return redirect()->route('admin.activities.index')->with('Success', 'Mantenimiento eliminado');
        }
    }
}
