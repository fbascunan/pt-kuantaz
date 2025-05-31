<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Beneficio;

class BeneficioController extends Controller
{
    public function index()
    {
        return response()->json(['ok' => true]);
    }

    public function analisis()
    {
        $beneficios = Beneficio::with(['filtro.ficha'])->get();

        $filtrados = $beneficios->filter(function ($b) {
            if (!$b->filtro) return false;
            return $b->monto >= $b->filtro->min && $b->monto <= $b->filtro->max;
        });

        $grouped = $filtrados->groupBy(function ($b) {
            return \Carbon\Carbon::parse($b->fecha)->format('Y');
        })->sortKeysDesc();

        $resultado = $grouped->map(function ($items, $year) {
            return [
                'anio' => $year,
                'monto_total' => $items->sum('monto'),
                'total_beneficios' => $items->count(),
                'beneficios' => $items->map(function ($b) {
                    return [
                        'monto' => $b->monto,
                        'fecha' => $b->fecha,
                        'ficha' => [
                            'nombre' => $b->filtro->ficha->nombre ?? null,
                            'url' => $b->filtro->ficha->url ?? null,
                            'categoria' => $b->filtro->ficha->categoria ?? null,
                            'descripcion' => $b->filtro->ficha->descripcion ?? null,
                        ],
                    ];
                })->values(),
            ];
        })->values();

        return response()->json($resultado);
    }
}
