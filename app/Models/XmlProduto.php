<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class XmlProduto extends Model
{
    use HasFactory;

    protected $table = 'xml_produtos';

    protected $fillable = [
        'id_cliente',
        'id_xml',
        'cProd',
        'cEAN',
        'xProd',
        'NCM',
        'EXTIPI',
        'CFOP',
        'uCom',
        'qCom',
        'vUnCom',
        'vProd',
        'vDesc',
        'vOutro',
        'cEANTrib',
        'uTrib',
        'qTrib',
        'vUnTrib',
        'indTot',
    ];
}
