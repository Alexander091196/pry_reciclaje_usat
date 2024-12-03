<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use App\Models\User;
use App\Models\Usertype;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Seleccionar los datos de usuarios con su tipo de usuario
        $users = DB::select("
            SELECT users.id, users.dni, users.license,users.name, users.email, usertypes.name AS usertype
            FROM users
            INNER JOIN usertypes ON users.usertype_id = usertypes.id
        ");

        if ($request->ajax()) {
            return DataTables::of($users)
                ->addColumn('actions', function ($user) {
                    // Verificar si el tipo de usuario está protegido usando el método estático

                    return '
                    <div class="dropdown">
                        <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton-' . $user->id . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-bars"></i>                        
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton-' . $user->id . '">
                            <button class="dropdown-item btnEditar" id="' . $user->id . '">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            <form action="' . route('admin.users.destroy', $user->id) . '" method="POST" class="frmEliminar">
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

        return view('admin.users.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Obtener todos los tipos de usuario para el select
        $usertypes = UserType::pluck('name', 'id');
        return view('admin.users.create', compact('usertypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validar los datos de entrada
        $request->validate([
            'dni' => 'required|string|max:8|unique:users,dni',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'license' => 'nullable|string',
            'usertype_id' => 'required|exists:usertypes,id',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Crear el usuario con los datos validados
        $user = new User();
        $user->dni = $request->dni;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->license = $request->license;
        $user->usertype_id = $request->usertype_id;
        $user->password = bcrypt($request->password); // Encriptar la contraseña
        $user->save();

        // Redirigir o enviar una respuesta con un mensaje de éxito
        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario registrado exitosamente.');
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
        // Buscar el usuario por ID
        $user = User::findOrFail($id);

        // Obtener todos los tipos de usuario para el select
        $usertypes = UserType::pluck('name', 'id');

        // Pasar el usuario y los tipos de usuario a la vista
        return view('admin.users.edit', compact('user', 'usertypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validar los datos de entrada
        $request->validate([
            'dni' => 'required|string|max:8|unique:users,dni,' . $id,
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'license' => 'nullable|string',
            'usertype_id' => 'required|exists:usertypes,id',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Buscar el usuario por ID
        $user = User::findOrFail($id);

        // Actualizar los datos del usuario excepto la contraseña
        $user->dni = $request->dni;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->license = $request->license;
        $user->usertype_id = $request->usertype_id;

        // Actualizar la contraseña solo si se ha proporcionado una nueva
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        // Redirigir o enviar una respuesta con un mensaje de éxito
        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            // Verificar si el usuario está asignado a un vehículo
            $usuarioAsignadoAVehiculo = DB::table('vehicleoccupants')
                ->where('user_id', $id) // Asumimos que 'user_id' es el campo que asocia el usuario al vehículo
                ->exists(); // Verifica si hay algún vehículo asociado a este usuario

            if ($usuarioAsignadoAVehiculo) {
                return response()->json(['message' => 'No se puede eliminar el usuario porque está asignado a un vehículo'], 400);
            }

            // Eliminar el usuario si no está asignado a un vehículo
            DB::table('users')->where('id', $id)->delete();

            return response()->json(['message' => 'Usuario eliminado correctamente'], 200);
        } catch (\Throwable $th) {
            // Manejar el error en caso de que ocurra
            return response()->json(['message' => 'Error de eliminación: ' . $th->getMessage()], 500);
        }
    }
}
