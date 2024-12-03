<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $u1 = new User();
        $u1->name = 'Administrador Sistema';
        $u1->email = 'admin@sistema.com';
        $u1->password = Hash::make('123456');
        $u1->dni = '12345678';
        $u1->birthdate = '1990-01-01';
        $u1->license = 'A-I';
        $u1->address = 'Dirección Administrador';
        $u1->usertype_id = 1;
        $u1->email_verified_at = now();
        $u1->save();

        $u2 = new User();
        $u2->name = 'Conductor Principal';
        $u2->email = 'conductor@sistema.com';
        $u2->password = Hash::make('123456');
        $u2->dni = '87654321';
        $u2->birthdate = '1985-05-15';
        $u2->license = 'A-IIIb';
        $u2->address = 'Dirección Conductor';
        $u2->usertype_id = 2;
        $u2->email_verified_at = now();
        $u2->save();

        $u3 = new User();
        $u3->name = 'Recolector Principal';
        $u3->email = 'recolector@sistema.com';
        $u3->password = Hash::make('123456');
        $u3->dni = '45678912';
        $u3->birthdate = '1988-08-20';
        $u3->license = 'A-IIa';
        $u3->address = 'Dirección Recolector';
        $u3->usertype_id = 3;
        $u3->email_verified_at = now();
        $u3->save();

        $u4 = new User();
        $u4->name = 'Ciudadano Demo';
        $u4->email = 'ciudadano@sistema.com';
        $u4->password = Hash::make('123456');
        $u4->dni = '32165498';
        $u4->birthdate = '1995-12-10';
        $u4->license = 'A-I';
        $u4->address = 'Dirección Ciudadano';
        $u4->usertype_id = 4;
        $u4->email_verified_at = now();
        $u4->save();
    }
}
