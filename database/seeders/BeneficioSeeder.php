<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Beneficio;

class BeneficioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $beneficios = [
            ['id_programa' => 147, 'monto' => 40656, 'fecha' => '2023-11-09'],
            ['id_programa' => 147, 'monto' => 60000, 'fecha' => '2023-10-10'],
            ['id_programa' => 130, 'monto' => 40656, 'fecha' => '2023-09-08'],
            ['id_programa' => 147, 'monto' => 40656, 'fecha' => '2023-07-10'],
            ['id_programa' => 130, 'monto' => 40656, 'fecha' => '2023-06-08'],
            ['id_programa' => 130, 'monto' => 1000, 'fecha' => '2023-05-09'],
            ['id_programa' => 147, 'monto' => 33656, 'fecha' => '2023-04-11'],
            ['id_programa' => 130, 'monto' => 33656, 'fecha' => '2023-03-08'],
            ['id_programa' => 130, 'monto' => 32836, 'fecha' => '2023-02-08'],
            ['id_programa' => 130, 'monto' => 32836, 'fecha' => '2023-01-10'],
            ['id_programa' => 130, 'monto' => 1000, 'fecha' => '2022-11-09'],
            ['id_programa' => 130, 'monto' => 49254, 'fecha' => '2022-10-11'],
            ['id_programa' => 146, 'monto' => 49254, 'fecha' => '2022-10-11'],
            ['id_programa' => 146, 'monto' => 49254, 'fecha' => '2022-10-11'],
        ];

        foreach ($beneficios as $beneficio) {
            Beneficio::create($beneficio);
        }
    }
}
