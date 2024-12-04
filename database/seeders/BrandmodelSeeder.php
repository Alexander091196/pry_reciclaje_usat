<?php

namespace Database\Seeders;

use App\Models\Brandmodel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BrandmodelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $models = [
            [
                'name' => 'Corolla',
                'code' => 'CRL001',
                'description' => 'Modelo popular de Toyota.',
                'brand_id' => 1, // ID de Toyota
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Hilux',
                'code' => 'HLX002',
                'description' => 'Camioneta de alta resistencia de Toyota.',
                'brand_id' => 1, // ID de Toyota
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'F-150',
                'code' => 'F15001',
                'description' => 'Camioneta icónica de Ford.',
                'brand_id' => 2, // ID de Ford
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Silverado',
                'code' => 'SLV002',
                'description' => 'Camioneta robusta de Chevrolet.',
                'brand_id' => 3, // ID de Chevrolet
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'FH16',
                'code' => 'FH1601',
                'description' => 'Camión pesado de Volvo Trucks.',
                'brand_id' => 4, // ID de Volvo Trucks
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($models as $model) {
            Brandmodel::create($model);
        }
    }
}
