<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClienteContador extends Model
{
    use HasFactory;

    protected $table = 'clientes_contadores';

    protected $fillable = [
        'id_cliente',
        'id_user',
    ];

    public static function verificaClienteContador($id_cliente, $id_user){
        $conta = \DB::table('clientes_contadores')
            ->where('id_cliente', $id_cliente)
            ->where('id_user', $id_user)
            ->count();

        return $conta > 0 ? "checked" : '';
    }
}
