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

    public function obtenerVotosPorCentro($idCentro)
    {
        try {
            $votos = DB::select('EXEC [sp_consultar_votos_por_centro] ?', [$idCentro]);

            return response()->json([
                'success' => true,
                'data' => $votos,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los votos: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function consultarColoniasMunicipio($idMunicipio)
    {
        try {
            $colonias = DB::select('EXEC [sp_consultar_colonias_municipio] ?', [$idMunicipio]);

            return response()->json([
                'success' => true,
                'data' => $colonias,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las colonias: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function consultarCentrosPorColonia($idColonia)
    {
        try {
            $centros = DB::select('EXEC [sp_consultar_centros_colonia] ?', [$idColonia]);

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

    public function obtenerMunicipios()
    {
        try {
            $municipios = DB::select('EXEC [sp_consultar_municipios]');

            return response()->json([
                'success' => true,
                'data' => $municipios,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los municipios: ' . $e->getMessage(),
            ], 500);
        }
    }
}
