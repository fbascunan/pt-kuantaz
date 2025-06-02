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
 *     description="Documentación de la API para la prueba técnica Kuantaz."
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
     *     path="/api/beneficios-por-ano",
     *     summary="Obtener beneficios procesados agrupados por año",
     *     tags={"Beneficios"},
     *     @OA\Response(
     *         response=200,
     *         description="Operación exitosa",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="code", type="integer", example=200, description="Código de estado HTTP"),
     *             @OA\Property(property="success", type="boolean", example=true, description="Indica si la operación fue exitosa"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="year", type="integer", example=2023, description="Año de los beneficios"),
     *                     @OA\Property(property="monto_total_anual", type="number", format="float", example=150000.50, description="Monto total de beneficios en el año"),
     *                     @OA\Property(property="num", type="integer", example=5, description="Número de beneficios en el año"),
     *                     @OA\Property(
     *                         property="beneficios",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id_programa", type="integer", example=147, description="ID del programa"),
     *                             @OA\Property(property="monto", type="number", format="float", example=40656, description="Monto del beneficio"),
     *                             @OA\Property(property="fecha_recepcion", type="string", example="09/11/2023", description="Fecha de recepción del beneficio (formato DD/MM/YYYY)"),
     *                             @OA\Property(property="fecha", type="string", format="date", example="2023-11-09", description="Fecha del beneficio (formato YYYY-MM-DD)"),
     *                             @OA\Property(property="year", type="integer", example=2023, description="Año del beneficio"),
     *                             @OA\Property(property="view", type="boolean", example=true, description="Indica si el beneficio es visible"),
     *                             @OA\Property(
     *                                 property="ficha",
     *                                 type="object",
     *                                 @OA\Property(property="id", type="integer", example=922, description="ID de la ficha"),
     *                                 @OA\Property(property="nombre", type="string", example="Emprende", description="Nombre de la ficha")
     *                             )
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor o fallo al obtener datos externos"
     *     )
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
            // Fetch data from all endpoints, fallback to DB if not reachable
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
            $beneficiosByYear = $processedBeneficios->groupBy('ano');

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

}
