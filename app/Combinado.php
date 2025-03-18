<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Combinado extends Model
{
    protected $table = "combinado";
    protected $primaryKey = "id";
    protected $keyType = 'string';

    public $timestamps = false;

    protected $guarded = [

    ];

    public function alimentos()
    {
        return $this->belongsToMany(Alimento::class, 'alimento_combinado')->withTimestamps();
    }

    public function subproductos()
    {
        return $this->belongsToMany(Subproducto::class, 'combinado_subproducto');
    }
    
    public function subproductosCombinados()
    {
        return $this->belongsToMany(Subproducto::class, 'subproducto_combinado')
                    ->withPivot('padre_producto_id');
    }public function alimentoPadre()
    {
        return $this->belongsTo(Alimento::class, 'padre_producto_id');
    }
    public function subcombinados()
    {
        return $this->hasMany(SubproductoCombinado::class, 'combinado_id')
            ->orderBy('orden')
            ->with('subproducto');
    }
}
