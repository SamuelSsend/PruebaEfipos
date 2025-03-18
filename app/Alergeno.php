<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Alergeno extends Model
{
    protected $table = "alergeno";
    protected $primaryKey = "id";
	
    public $timestamps = false;

    /**
     * Relación muchos-a-muchos con el modelo Alimento.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function alimentos()
    {
        return $this->belongsToMany(
            Alimento::class, 
            'alimento_alergeno', // Nombre de la tabla pivote
            'alergeno_id',       // Clave foránea del alergeno en la tabla pivote
            'alimento_id'        // Clave foránea del alimento en la tabla pivote
        );
    }
    
}
