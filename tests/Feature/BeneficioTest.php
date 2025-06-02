<?php

test('beneficios-por-ano endpoint returns expected structure', function () {
    $response = $this->getJson('/api/beneficios-por-ano');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'code',
            'success',
            'data' => [
                '*' => [
                    'year',
                    'monto_total_anual',
                    'num',
                    'beneficios' => [
                        '*' => [
                            'id_programa',
                            'monto',
                            'fecha_recepcion',
                            'fecha',
                            'ano',
                            'view',
                            'ficha' => [
                                'id',
                                'nombre',
                            ]
                        ]
                    ]
                ]
            ]
        ]);
});
