<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CentroVotacionController extends Controller
{
    public function obtenerCentrosPorMunicipio($idMunicipio)
    {
        try {
            $centros = DB::select('EXEC [sp_consultar_centros_por_municipio] ?', [$idMunicipio]);

            return response()->json([
                'success' => true,
                'data' => $centros,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los centros: ' . $e->getMessage(),
            ], 500);
        }
    }
}
