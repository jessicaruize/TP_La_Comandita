<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pedido extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'id';
    protected $table = 'Pedidos';
    public $incrementing = true;
    public $timestamps = true;

    const CREATED_AT = 'fecha_alta';
    const UPDATED_AT = 'fecha_modificacion';
    const DELETED_AT = 'fecha_baja';

    protected $fillable = [
        'preparador_id', 
        'factura_id', 
        'producto_id',
        'detalle', 
        'cantidad', 
        'estado', 
        'demora_estimada', 
        'entregado', 
        'fecha_alta', 
        'fecha_modificacion', 
        'fecha_baja'
    ];
}