<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Usertype;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class UsertypesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $usertypes = DB::select('select id, name, description from usertypes');

        if ($request->ajax()) {
            return DataTables::of($usertypes)
                ->addColumn('actions', function ($usertype) {
                    // Verificar si el tipo de usuario está protegido usando el método estático
                    if (Usertype::isProtectedId($usertype->id)) {
                        return ''; // No mostrar acciones para tipos de usuario protegidos
                    }

                    return '
                    <div class="dropdown">
                        <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton-' . $usertype->id . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-bars"></i>                        
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton-' . $usertype->id . '">
                            <button class="dropdown-item btnEditar" id="' . $usertype->id . '">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            <form action="' . route('admin.usertypes.destroy', $usertype->id) . '" method="POST" class="frmEliminar">
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

        return view('admin.usertypes.index');
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.usertypes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            Usertype::create([
                'name' => $request->name,
                'description' => $request->description
            ]);

            return response()->json(['message' => 'Tipo de personal registra correctamente'], 200);
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
        $usertypes = Usertype::find($id);
        return view('admin.usertypes.edit', compact('usertypes'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $usertypes = Usertype::find($id);

            $usertypes->update([
                'name' => $request->name,
                'description' => $request->description
            ]);

            return response()->json(['message' => 'Tipo de personal  actualizada correctamente'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error actualizar: ' . $th->getMessage()], 500);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function destroy(string $id)
    {
        try {
            // Verificar si el tipo de usuario está asignado a algún usuario
            $usertypeAssigned = DB::table('users')->where('usertype_id', $id)->exists();

            if ($usertypeAssigned) {
                return response()->json(['message' => 'No se puede eliminar el tipo de usuario porque está asignado a un usuario'], 400);
            }

            // Eliminar el tipo de usuario si no está asignado a ningún usuario
            DB::table('usertypes')->where('id', $id)->delete();

            return response()->json(['message' => 'Tipo de usuario eliminado correctamente'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error de eliminación: ' . $th->getMessage()], 500);
        }
    }
}
