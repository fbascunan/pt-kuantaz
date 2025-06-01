<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;
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
    // URLs for the external APIs
    private string $beneficiosUrl = 'https://run.mocky.io/v3/8f75c4b5-ad90-49bb-bc52-f1fc0b4aad02';
    private string $filtrosUrl = 'https://run.mocky.io/v3/b0ddc735-cfc9-410e-9365-137e04e33fcf';
    private string $fichasUrl = 'https://run.mocky.io/v3/4654cafa-58d8-4846-9256-79841b29a687';

    /**
     * @OA\Get(
     * path="/api/processed-benefits",
     * summary="Get processed benefits grouped by year",
     * tags={"Benefits"},
     * @OA\Response(
     * response=200,
     * description="Successful operation",
     * @OA\JsonContent(
     * type="object",
     * @OA\Property(property="code", type="integer", example=200),
     * @OA\Property(property="success", type="boolean", example=true),
     * @OA\Property(
     * property="data",
     * type="array",
     * @OA\Items(
     * type="object",
     * @OA\Property(property="year", type="integer", example=2023),
     * @OA\Property(property="monto_total_anual", type="number", format="float", example=150000.50),
     * @OA\Property(property="num", type="integer", example=5),
     * @OA\Property(
     * property="beneficios",
     * type="array",
     * @OA\Items(
     * type="object",
     * @OA\Property(property="id_programa", type="integer", example=147),
     * @OA\Property(property="monto", type="number", format="float", example=40656),
     * @OA\Property(property="fecha_recepcion", type="string", example="09/11/2023"),
     * @OA\Property(property="fecha", type="string", format="date", example="2023-11-09"),
     * @OA\Property(property="year", type="integer", example=2023),
     * @OA\Property(property="view", type="boolean", example=true),
     * @OA\Property(
     * property="ficha",
     * type="object",
     * @OA\Property(property="id", type="integer", example=922),
     * @OA\Property(property="nombre", type="string", example="Emprende"),
     * )
     * )
     * )
     * )
     * )
     * )
     * ),
     * @OA\Response(
     * response=500,
     * description="Internal server error or failed to fetch external data"
     * )
     * )
     */
    public function getBeneficiosPerYear()
    {
        //todos:
        // 1. Fetch data from all endpoints
        // 2. Work with collections for easier data access
        // 3. Filter benefits that meet min/max amounts
        // 4. Add ficha to the benefit
        // 5. Group benefits by year (ensure 'year' is integer for proper grouping)

        // extra:
        // Create lookup maps for quick access
        // use a lot of collections methods for better performance
        // Order by year, from mayor to menor (descending)
        // add the total amount per year


        try {
            // Fetch data from all endpoints
            $beneficiosResponse = Http::get($this->beneficiosUrl);
            $filtrosResponse = Http::get($this->filtrosUrl);
            $fichasResponse = Http::get($this->fichasUrl);

            if (!$beneficiosResponse->successful() || !$filtrosResponse->successful() || !$fichasResponse->successful()) {
                return response()->json([
                    'code' => 500,
                    'success' => false,
                    'message' => 'Failed to fetch data from one or more external services.'
                ], 500);
            }

            $beneficiosData = collect($beneficiosResponse->json()['data'] ?? []);
            $filtrosData = collect($filtrosResponse->json()['data'] ?? []);
            $fichasData = collect($fichasResponse->json()['data'] ?? []);

            // Create lookup maps for quick access
            $filtrosLookup = $filtrosData->keyBy('id_programa');
            $fichasLookup = $fichasData->keyBy('id');

            $processedBeneficios = collect([]);

            foreach ($beneficiosData as $beneficio) {
                $beneficio = collect($beneficio);
                $idPrograma = $beneficio->get('id_programa');
                $montoBeneficio = (float) $beneficio->get('monto');

                // Find the corresponding filter (matching 'id_programa')
                $filtro = $filtrosLookup->get($idPrograma);

                if ($filtro) {
                    $filtro = collect($filtro); // Work with collection
                    $montoMinimo = (float) $filtro->get('min');
                    $montoMaximo = (float) $filtro->get('max');

                    // Filter benefits that meet min/max amounts
                    if ($montoBeneficio >= $montoMinimo && $montoBeneficio <= $montoMaximo) {
                        $idFicha = $filtro->get('ficha_id');

                        // Find the corresponding ficha
                        $ficha = $fichasLookup->get($idFicha);
                        $beneficio['view'] = true; // Set 'view' to true for processed benefits
                        if ($ficha) {
                            if ($beneficio->has('fecha')) {
                                $beneficio['ano'] = Carbon::parse($beneficio->get('fecha'))->year;
                            } else {
                                $beneficio['ano'] = Carbon::parse($beneficio->get('fecha_recepcion'), 'd/m/Y')->year;
                            }
                            // Add ficha to the benefit
                            $beneficio['ficha'] = collect($ficha);
                            $processedBeneficios->push($beneficio);
                        }
                    }
                }
            }

            // Group benefits by year
            $beneficiosByYear = $processedBeneficios->groupBy('year');

            $resultData = $beneficiosByYear->map(function ($yearBeneficios, $year) {
                // Number of benefits per year
                $numBeneficios = $yearBeneficios->count();
                // // Total amount per year
                $montoTotalAnual = $yearBeneficios->sum('monto');

                return [
                    'year' => (int) $year,
                    'monto_total_anual' => $montoTotalAnual,
                    'num' => $numBeneficios,
                    'beneficios' => $yearBeneficios->values()->all() // Get items as a simple array
                ];
            })
            // Order by year, from mayor to menor (descending)
            ->sortByDesc('year')
            ->values(); // Reset keys to be a simple array

            return response()->json([
                'code' => 200,
                'success' => true,
                'data' => $resultData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'success' => false,
                'message' => 'An internal server error occurred.',
                'error_details' => $e->getMessage() // Optional: for development
            ], 500);
        }
    }

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
