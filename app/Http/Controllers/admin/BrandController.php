<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /*public function index()
    {
        $brands = Brand::all();
        return view('admin.brands.index', compact('brands'));
    }*/

    public function index(Request $request)
    {
        $brands = Brand::all();

        if ($request->ajax()) {

            return DataTables::of($brands)
                ->addColumn('logo', function ($brand) {
                    return '<img src="' . ($brand->logo == '' ? asset('storage/brand_logo/no_image.png') : asset($brand->logo)) . '" width="100px" height="70px" class="card">';
                })
                ->addColumn('actions', function ($brand) {
                    return '
                    <div class="dropdown">
                        <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-bars"></i>                        
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <button class="dropdown-item btnEditar" id="' . $brand->id . '"><i class="fas fa-edit"></i>  Editar</button>
                            <form action="' . route('admin.brands.destroy', $brand->id) . '" method="POST" class="frmEliminar d-inline">
                                ' . csrf_field() . method_field('DELETE') . '
                                <button type="submit" class="dropdown-item"><i class="fas fa-trash"></i> Eliminar</button>
                            </form>
                        </div>
                    </div>';
                })
                ->rawColumns(['logo', 'actions'])  // Declarar columnas que contienen HTML
                ->make(true);
        } else {
            return view('admin.brands.index', compact('brands'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.brands.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        try {
            $logo = '';
            if ($request->logo != '') {
                $image = $request->file('logo')->store('public/brand_logo');
                $logo = Storage::url($image);
            }

            Brand::create([
                'name' => $request->name,
                'logo' => $logo,
                'description' => $request->description
            ]);

            return response()->json(['message' => 'Marca registra correctamente'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error en el registro: ' . $th->getMessage()], 500);
        }

        /*return redirect()->route('admin.brands.index')
            ->with('success', 'Marca registrada correctamente');*/
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
        $brand = Brand::find($id);
        return view('admin.brands.edit', compact('brand'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        try {
            $brand = Brand::find($id);
            /*$brand->update($request->all());*/

            $logo = '';
            if ($request->logo != '') {
                $image = $request->file('logo')->store('public/brand_logo');
                $logo = Storage::url($image);

                $brand->update([
                    'name' => $request->name,
                    'logo' => $logo,
                    'description' => $request->description
                ]);
            } else {
                $brand->update([
                    'name' => $request->name,
                    'description' => $request->description
                ]);
            }

            return response()->json(['message' => 'Marca actualizada correctamente'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error actualizar: ' . $th->getMessage()], 500);
        }



        /*return redirect()->route('admin.brands.index')
            ->with('success', "Marca actualizada correctamente");*/
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            // Verificar si existen modelos asociados a la marca
            $modelosAsociados = DB::table('brandmodels')
                ->where('brand_id', $id)
                ->exists();
    
            if ($modelosAsociados) {
                return response()->json(['message' => 'No se puede eliminar la marca porque tiene modelos asociados'], 400);
            }
    
            // Eliminar la marca si no tiene modelos asociados
            DB::table('brands')->where('id', $id)->delete();
    
            return response()->json(['message' => 'Marca eliminada correctamente'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error de eliminación'], 500);
        }
    }
}
