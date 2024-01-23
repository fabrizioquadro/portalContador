<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportXml extends Model
{
    use HasFactory;

    protected $table = "imports_xml";

    protected $fillable = [
        'id_import',
        'id_cliente',
        'arquivo',
        'tp_xml',
        'st_xml',
        'retorno',
    ];
}
