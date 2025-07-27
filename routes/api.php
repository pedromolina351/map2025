<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PersonaController;


Route::get('/ejecutar-query', [PersonaController::class, 'ejecutarQuery']);

