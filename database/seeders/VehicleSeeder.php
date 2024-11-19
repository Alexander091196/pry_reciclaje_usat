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
