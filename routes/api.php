<?php

use App\Http\Controllers\Api\CentroVotacionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PersonaController;


Route::get('/centros/{municipioid}', [CentroVotacionController::class, 'obtenerCentrosPorMunicipio']);
Route::get('/votos-centro/{centroid}', [CentroVotacionController::class, 'obtenerVotosPorCentro']);
Route::get('/colonias/{municipioid}', [CentroVotacionController::class, 'consultarColoniasMunicipio']);
Route::get('/centros-colonias/{coloniaid}', [CentroVotacionController::class, 'consultarCentrosPorColonia']);
Route::get('/municipios', [CentroVotacionController::class, 'obtenerMunicipios']);
