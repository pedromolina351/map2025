<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Log;

class CuentaContableService
{
    public function crearCuentaContable(array $datos)
    {
        try {
            // Llamar al procedimiento almacenado en SQL Server
            DB::statement('EXEC dbo.spi_CrearCuentaContable
                @codigoCuentaID = :codigoCuentaID,
                @nombre = :nombre,
                @descripcion = :descripcion,
                @nivel = :nivel,
                @id_cuenta_padre = :id_cuenta_padre,
                @centroCostoID = :centroCostoID,
                @modalidad = :modalidad,
                @codigo_sar = :codigo_sar,
                @tipo = :tipo,
                @naturaleza = :naturaleza,
                @categoria = :categoria,
                @id_moneda = :id_moneda,
                @estado = :estado', [
                'codigoCuentaID' => $datos['codigoCuentaID'],
                'nombre' => $datos['nombre'],
                'descripcion' => $datos['descripcion'] ?? null,
                'nivel' => $datos['nivel'],
                'id_cuenta_padre' => $datos['id_cuenta_padre'] ?? null,
                'centroCostoID' => $datos['centroCostoID'],
                'modalidad' => $datos['modalidad'],
                'codigo_sar' => $datos['codigo_sar'] ?? null,
                'tipo' => $datos['tipo'],
                'naturaleza' => $datos['naturaleza'],
                'categoria' => $datos['categoria'],
                'id_moneda' => $datos['id_moneda'],
                'estado' => $datos['estado'] ?? 1,
            ]);

            return [
                'success' => true,
                'message' => 'Cuenta contable creada exitosamente.'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error al crear la cuenta contable: ' . $e->getMessage()
            ];
        }
    }

    public function obtenerCuentaContable(int $cuentaID)
    {
        try {
            $result = DB::select('EXEC dbo.sps_ObtenerCuentaContable @cuentaID = :cuentaID', [
                'cuentaID' => $cuentaID
            ]);

            if (empty($result)) {
                return [
                    'success' => false,
                    'message' => 'La cuenta contable con el ID especificado no existe.'
                ];
            }

            return [
                'success' => true,
                'data' => $result[0]
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error al obtener la cuenta contable: ' . $e->getMessage()
            ];
        }
    }

    public function obtenerCuentasContables () {
        try {
            $result = DB::select('EXEC sps_ObtenerCuentasContables');
            if (empty($result)) {
                return [
                    'success' => false,
                    'message' => 'No se encontraron cuentas contables.',
                    'data' => []
                ];
            }
            return [
                'success' => true,
                'data' => $result
            ];
        } catch (\Throwable $th) {
            return [
                'success'=> false,
                'message'=> 'Error al obtener las cuentas contables'. $th->getMessage()
            ];
        }
    }

    public function actualizarCuentaContable(array $data)
    {
        try {
            DB::beginTransaction(); // Iniciar transacción

            DB::statement('EXEC spu_ActualizarCuentaContable
                @cuentaID = :cuentaID,
                @codigoCuentaID = :codigoCuentaID,
                @nombre = :nombre,
                @descripcion = :descripcion,
                @nivel = :nivel,
                @id_cuenta_padre = :id_cuenta_padre,
                @centroCostoID = :centroCostoID,
                @modalidad = :modalidad,
                @codigo_sar = :codigo_sar,
                @tipo = :tipo,
                @naturaleza = :naturaleza,
                @categoria = :categoria,
                @id_moneda = :id_moneda,
                @estado = :estado', [
                'cuentaID' => $data['cuentaID'],
                'codigoCuentaID' => $data['codigoCuentaID'] ?? null,
                'nombre' => $data['nombre'] ?? null,
                'descripcion' => $data['descripcion'] ?? null,
                'nivel' => $data['nivel'] ?? null,
                'id_cuenta_padre' => $data['id_cuenta_padre'] ?? null,
                'centroCostoID' => $data['centroCostoID'] ?? null,
                'modalidad' => $data['modalidad'] ?? null,
                'codigo_sar' => $data['codigo_sar'] ?? null,
                'tipo' => $data['tipo'] ?? null,
                'naturaleza' => $data['naturaleza'] ?? null,
                'categoria' => $data['categoria'] ?? null,
                'id_moneda' => $data['id_moneda'] ?? null,
                'estado' => $data['estado'] ?? null,
            ]);

            DB::commit(); // Confirmar transacción

            return [
                'success' => true,
                'message' => 'Cuenta contable actualizada exitosamente.'
            ];
        } catch (Exception $e) {
            DB::rollBack(); // Revertir cambios si hay error

            return [
                'success' => false,
                'message' => 'Error al actualizar la cuenta contable: ' . $e->getMessage()
            ];
        }
    }

    public function eliminarCuentaContable($cuentaID)
{
    Log::info('Eliminando cuenta contable con ID: ' . $cuentaID);

    try {
        DB::beginTransaction();

        DB::statement('EXEC spd_EliminarCuentaContable @cuentaID = :cuentaID', [
            'cuentaID' => $cuentaID,
        ]);

        DB::commit();

        return [
            'success' => true,
            'message' => 'La cuenta contable ha sido eliminada correctamente.'
        ];
    }
    catch (\PDOException $e) {
        DB::rollBack();
        if (str_contains($e->getMessage(), 'Error: La cuenta contable con ID')) {
            $message = 'La cuenta contable especificada no existe.';
        } else {
            $message = 'Error al eliminar la cuenta contable. Por favor, intente nuevamente.';
        }

        return [
            'success' => false,
            'message' => $message
        ];
    }
    catch (Exception $e) {
        DB::rollBack();
        return [
            'success' => false,
            'message' => 'Ocurrió un error inesperado al intentar eliminar la cuenta contable.'
        ];
    }
}

}
