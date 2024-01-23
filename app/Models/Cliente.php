<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'fantasia',
        'cnpj',
        'email',
        'tel',
        'cel',
    ];

    public static function listarClientesUsuario(){
        if(auth()->user()->tipo == "Administrador"){
            return \DB::table('clientes')->orderBy('nome')->get();
        }
        else{
            $sql = "SELECT c.id, c.nome,c.cnpj, c.email, c.tel, c.cel FROM clientes AS c, clientes_contadores AS cc WHERE
            c.id=cc.id_cliente AND
            cc.id_user=".auth()->user()->id." ORDER BY nome";
            return \DB::select($sql);
        }
    }
}
