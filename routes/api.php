<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Api\BeneficioController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/beneficios-por-ano', [BeneficioController::class, 'getBeneficiosPerYear']);


Route::get('/debug-beneficios', function () {
    $beneficios = Http::get('https://run.mocky.io/v3/8f75c4b5-ad90-49bb-bc52-f1fc0b4aad02')->json();
    $filtros    = Http::get('https://run.mocky.io/v3/b0ddc735-cfc9-410e-9365-137e04e33fcf')->json();
    $fichas     = Http::get('https://run.mocky.io/v3/4654cafa-58d8-4846-9256-79841b29a687')->json();

    return compact('beneficios', 'filtros', 'fichas');
});
