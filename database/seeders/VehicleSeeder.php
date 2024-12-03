<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {


        DB::table('vehicles')->insert([
            [
                'name' => 'Camión Ford',
                'code' => 'FORD123',
                'plate' => 'ABC-123',
                'year' => '2020',
                'occupant_capacity' => 3,
                'load_capacity' => 12000.50,
                'description' => 'Camión para transporte de carga pesada',
                'status' => 1, // 1: Activo
                'brand_id' => 1,
                'model_id' => 1,
                'type_id' => 1,
                'color_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Van Toyota',
                'code' => 'TOYOTA567',
                'plate' => 'XYZ-789',
                'year' => '2018',
                'occupant_capacity' => 2,
                'load_capacity' => 2000.00,
                'description' => 'Van para transporte de personal',
                'status' => 1, // 1: Activo
                'brand_id' => 2,
                'model_id' => 2,
                'type_id' => 2,
                'color_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
        
        // Crea el procedimiento almacenado
        DB::unprepared('
            CREATE PROCEDURE `recicla_usat`.`sp_vehicles`()
            BEGIN
                SELECT 
                    v.id, 
                    v3.image AS logo,
                    v.name AS name, 
                    b.name AS brand, 
                    b2.name AS model, 
                    v2.name AS vtype, 
                    v.plate AS plate, 
                    v.status AS status 
                FROM vehicles v 
                INNER JOIN brands b ON v.brand_id = b.id 
                INNER JOIN brandmodels b2 ON v.model_id = b2.id
                INNER JOIN vehicletypes v2 ON v.type_id = v2.id 
                LEFT JOIN vehicleimages v3 ON v.id = v3.vehicle_id AND v3.profile = 1;
            END
        ');
    }
}
