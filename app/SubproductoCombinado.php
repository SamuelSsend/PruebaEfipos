<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class SubproductoCombinado extends Model
{
    protected $table = 'subproducto_combinado';
    public $timestamps = true;
    protected $primaryKey = ['padre_producto_id', 'subproducto_id'];
    public $incrementing = false;

    protected $fillable = [
        'padre_producto_id',
        'combinado_id',
        'nombre_subcategoria',
        'multiplicidad',
        'seccionobligatoria',
        'subproducto_id',
        'orden'
    ];

    public function alimentos()
    {
        return $this->belongsToMany(Alimento::class, 'subproducto_combinado', 'subproducto_id', 'padre_producto_id');
    }

    public function padreProducto()
    {
        return $this->belongsTo(Alimento::class, 'padre_producto_id');
    }

    public function combinado()
    {
        return $this->belongsTo(Combinado::class, 'combinado_id');
    }

    public function subproducto()
    {
        return $this->belongsTo(Subproducto::class, 'subproducto_id');
    }
}