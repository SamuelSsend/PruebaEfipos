<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SeccionUno extends Model
{
    protected $table = "seccion_uno";
    protected $primaryKey = "id";

    public $timestamps = false;

    protected $fillable=[    
        'titulo',    
        'subtitulo',
        'portada',
        'estado'
    ];

    protected $guarded=[

    ];
}
