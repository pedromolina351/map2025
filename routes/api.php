<?php

use App\Http\Controllers\Api\CentroVotacionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PersonaController;


Route::get('/centros/{municipioid}', [CentroVotacionController::class, 'obtenerCentrosPorMunicipio']);

