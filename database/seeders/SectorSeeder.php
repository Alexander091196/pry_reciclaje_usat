<?php

namespace Database\Seeders;

use App\Models\Sector;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SectorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $s1 = new Sector();
        $s1->name = 'Sector A';
        $s1->area = '100';
        $s1->district_id = 1;
        $s1->description = 'Descripción del Sector A'; // Agregar la descripción
        $s1->save();

        $s2 = new Sector();
        $s2->name = 'Sector B';
        $s2->area = '80';
        $s2->district_id = 2;
        $s2->description = 'Descripción del Sector B'; // Agregar la descripción
        $s2->save();

        $s3 = new Sector();
        $s3->name = 'Sector C';
        $s3->area = '120';
        $s3->district_id = 5;
        $s3->description = 'Descripción del Sector C'; // Agregar la descripción
        $s3->save();
    }
}
