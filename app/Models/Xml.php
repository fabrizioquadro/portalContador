<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Xml extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_import',
        'id_cliente',
        'mes_ano',
        'mes',
        'ano',
        'arquivo',
        'situacao',
        'modDoc',
        'serie',
        'numero',
        'dhEmi',
        'destXnome',
        'destCpfCnpj',
        'chNFe',
        'vPag',
    ];

    public static function listarRelacaoXml($id_cliente, $dtInc, $dtFn, $mod, $in_situacao){
        $dhInc = $dtInc." 00:00:00";
        $dhFn = $dtFn." 23:59:59";
        $dados_filtro = array();
        $dados_filtro[] = $id_cliente;
        $dados_filtro[] = $dhInc;
        $dados_filtro[] = $dhFn;

        $sql = "SELECT * FROM xmls WHERE id_cliente=? AND dhEmi>=? AND
        dhEmi<=?";

        if($mod == 1){
            $sql .= " AND (modDoc='65' OR modDoc='55')";
        }
        elseif($mod == 2){
            $sql .= " AND modDoc='55'";
        }
        elseif($mod == 3){
            $sql .= " AND modDoc='65'";
        }

        //$sql .= " AND (situacao='A' OR situacao='C' OR situacao='I')";

        if($in_situacao){
            $sql .= " AND situacao IN ($in_situacao)";
        }

        $sql .= " ORDER BY dhEmi";

        return \DB::select($sql,$dados_filtro);
    }

    public static function listarRelacaoVendas($id_cliente, $dtInc, $dtFn, $mod, $in_situacao){
        $dhInc = $dtInc." 00:00:00";
        $dhFn = $dtFn." 23:59:59";
        $dados_filtro = array();
        $dados_filtro[] = $id_cliente;
        $dados_filtro[] = $dhInc;
        $dados_filtro[] = $dhFn;

        $sql = "SELECT * FROM xmls WHERE id_cliente=? AND dhEmi>=? AND
        dhEmi<=? AND vPag<>''";

        if($mod == 1){
            $sql .= " AND (modDoc='65' OR modDoc='55')";
        }
        elseif($mod == 2){
            $sql .= " AND modDoc='55'";
        }
        elseif($mod == 3){
            $sql .= " AND modDoc='65'";
        }

        //$sql .= " AND (situacao='A' OR situacao='C' OR situacao='I')";

        if($in_situacao){
            $sql .= " AND situacao IN ($in_situacao)";
        }

        $sql .= " ORDER BY dhEmi";

        return \DB::select($sql,$dados_filtro);

        /*
        $dhInc = $dtInc." 00:00:00";
        $dhFn = $dtFn." 23:59:59";
        $dados_filtro = array();
        $dados_filtro[] = $id_cliente;
        $dados_filtro[] = $dhInc;
        $dados_filtro[] = $dhFn;

        $sql = "SELECT * FROM xmls WHERE id_cliente=? AND dhEmi>=? AND
        dhEmi<=? AND vPag<>''";

        if($mod){
            $sql .= " AND modDoc IN (?)";
            $dados_filtro[] = $mod;
        }

        $sql .= " ORDER BY dhEmi";

        return \DB::select($sql,$dados_filtro);
        */
    }

    public static function listarUltimasXml($id_cliente, $dtInc, $dtFn){
        $dhInc = $dtInc." 00:00:00";
        $dhFn = $dtFn." 23:59:59";
        $dados_filtro = array();
        $dados_filtro[] = $id_cliente;
        $dados_filtro[] = $dhInc;
        $dados_filtro[] = $dhFn;

        $sql = "SELECT * FROM xmls WHERE id_cliente=? AND dhEmi>=? AND
        dhEmi<=? AND modDoc=55";

        $sql .= " ORDER BY dhEmi DESC LIMIT 1";

        $res = \DB::select($sql,$dados_filtro);

        $dados['numeroNFe'] = count($res) > 0 ? $res[0]->numero : '';
        $dados['dhEmiNFe'] = count($res) > 0 ? $res[0]->dhEmi : '';

        $sql = "SELECT * FROM xmls WHERE id_cliente=? AND dhEmi>=? AND
        dhEmi<=? AND modDoc=65";

        $sql .= " ORDER BY dhEmi DESC LIMIT 1";

        $res = \DB::select($sql,$dados_filtro);

        $dados['numeroNFCe'] = count($res) > 0 ? $res[0]->numero : '';
        $dados['dhEmiNFCe'] = count($res) > 0 ? $res[0]->dhEmi : '';

        return $dados;
    }

}
