<?php

namespace Database\Seeders;

use App\Models\Usertype;
use App\Models\Usertypes;

use Illuminate\Database\Seeder;

class UsertypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $u1 = new Usertype();
        $u1->name = 'Administrador';
        $u1->description = 'Usuario con permisos completos para gestionar el sistema y los usuarios.';
        $u1->save();
    
        $u2 = new Usertype();
        $u2->name = 'Conductor';
        $u2->description = 'Usuario responsable de operar los vehículos asignados para el transporte de carga o pasajeros.';
        $u2->save();
    
        $u3 = new Usertype();
        $u3->name = 'Recolector';
        $u3->description = 'Usuario encargado de la recolección de residuos o materiales en las rutas asignadas.';
        $u3->save();
    
        $u4 = new Usertype();
        $u4->name = 'Ciudadano';
        $u4->description = 'Usuario con acceso limitado a la plataforma, generalmente para realizar solicitudes o reportes.';
        $u4->save();
    }
    
}
