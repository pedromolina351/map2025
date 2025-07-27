<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Cliente extends Model
{
    protected $table = 'Clientes';
    protected $primaryKey = 'clienteID';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;
    protected $fillable = [
        'codigo_cliente',
        'personaID',
        'agremiadoID',
        'tipoClienteID',
        'departamentoID',
        'municipioID',
        'nombre_comercial',
        'estadoID'
    ];
}


