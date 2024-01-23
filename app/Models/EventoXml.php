<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventoXml extends Model
{
    use HasFactory;
    protected $table = 'eventos_xml';

    protected $fillable = [
        'id_xml',
        'chNFe',
        'nNFIni',
        'tp_evento',
        'dhEvento',
        'processamento',
    ];
}
