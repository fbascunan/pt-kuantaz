<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Ficha;


class FichaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fichas = [
            [
                'id' => 903,
                'nombre' => 'Crece',
                'id_programa' => 146,
                'url' => 'crece',
                'categoria' => 'trabajo',
                'descripcion' => 'Subsidio para implementar plan de trabajo en empresas'
            ],
            [
                'id' => 922,
                'nombre' => 'Emprende',
                'id_programa' => 147,
                'url' => 'emprende',
                'categoria' => 'trabajo',
                'descripcion' => 'Fondos concursables para nuevos negocios'
            ],
            [
                'id' => 2042,
                'nombre' => 'Subsidio Familiar (SUF)',
                'id_programa' => 130,
                'url' => 'subsidio_familiar_suf',
                'categoria' => 'bonos',
                'descripcion' => 'Beneficio económico mensual entregado a madres, padres o tutores que no cuentan con previsión social.'
            ]
        ];

        foreach ($fichas as $ficha) {
            Ficha::create($ficha);
        }
    }
}
