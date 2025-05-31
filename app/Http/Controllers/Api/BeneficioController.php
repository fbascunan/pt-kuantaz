<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Beneficio;


/**
 * @OA\Info(
 *     title="Kuantaz API",
 *     version="1.0.0",
 *     description="This is the API documentation for the Kuantaz challenge."
 * )
 */
class BeneficioController extends Controller
{
    public function index()
    {
        return response()->json(['ok' => true]);
    }

    /**
     * @OA\Get(
     *     path="/api/analisis-beneficios",
     *     tags={"Beneficios"},
     *     summary="Obtener análisis de beneficios",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de beneficios agrupados por año",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="año", type="integer"),
     *                 @OA\Property(property="total_monto", type="number"),
     *                 @OA\Property(property="total_beneficios", type="integer"),
     *                 @OA\Property(
     *                     property="beneficios",
     *                     type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id_programa", type="integer"),
     *                         @OA\Property(property="monto", type="number"),
     *                         @OA\Property(property="fecha", type="string"),
     *                         @OA\Property(property="ficha", type="object",
     *                             @OA\Property(property="nombre", type="string"),
     *                             @OA\Property(property="descripcion", type="string"),
     *                             @OA\Property(property="categoria", type="string")
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
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
