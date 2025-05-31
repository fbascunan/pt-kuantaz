<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Filtro;

class FiltroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $filtros = [
            [
                'id_programa' => 147,
                'tramite' => 'Emprende',
                'min' => 0,
                'max' => 50000,
                'ficha_id' => 922
            ],
            [
                'id_programa' => 146,
                'tramite' => 'Crece',
                'min' => 0,
                'max' => 30000,
                'ficha_id' => 903
            ],
            [
                'id_programa' => 130,
                'tramite' => 'Subsidio Único Familiar',
                'min' => 5000,
                'max' => 180000,
                'ficha_id' => 2042
            ]
        ];

        foreach ($filtros as $filtro) {
            Filtro::create($filtro);
        }
    }
}
