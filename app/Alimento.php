<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Alimento extends Model
{
    protected $table = "alimento";
    protected $primaryKey = "id";
    protected $keyType = 'string';
    
    public $timestamps = false;

    protected $guarded = [

    ];

    public function combinados()
    {
        return $this->belongsToMany(Combinado::class, 'alimento_combinado', 'alimento_id', 'combinado_id')
                    ->withPivot('multiplicidad', 'orden', 'obligatorio')
                    ->orderBy('alimento_combinado.orden', 'asc');
    }

    public function combinadosA()
    {
        return $this->belongsToMany(Combinado::class, 'alimento_combinado', 'alimento_id', 'combinado_id')
            ->withPivot('orden', 'obligatorio')
            ->orderBy('alimento_combinado.orden');
    }

    // Relación directa a subcombinados nivel 2
    public function subcombinados()
    {
        return $this->hasMany(SubproductoCombinado::class, 'padre_producto_id')
            ->orderBy('orden')
            ->with('subproducto');
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function getDescripcionAttribute()
    {
        return $this->attributes['descripcion_hosteltactil'] ?: $this->attributes['descripcion_manual'];
    }

    public function getEstaActivoAttribute()
    {
        return $this->attributes['estado'] == 'Disponible' and $this->attributes['activo_hosteltactil'];
    }
    
    public function alergenos()
    {
        return $this->belongsToMany(
            Alergeno::class, 
            'alimento_alergeno', // Nombre de la tabla pivote
            'alimento_id',       // Clave foránea del alimento en la tabla pivote
            'alergeno_id'        // Clave foránea del alergeno en la tabla pivote
        );
    }
}
