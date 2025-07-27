<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreatePersonaRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PersonaController extends Controller
{
    public function ejecutarQuery(Request $request)
    {
        // Validar la consulta SQL 
        $query = $request->input('query');
        if (empty($query)) {
            return response()->json(['error' => 'La consulta SQL no puede estar vacía.'], 400);
        }

        // Ejecutar la consulta SQL

        try {
            $result = DB::select($query);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al ejecutar la consulta: ' . $e->getMessage()], 500);
        }
        // Devolver el resultado
        return response()->json($result);
    }

}
