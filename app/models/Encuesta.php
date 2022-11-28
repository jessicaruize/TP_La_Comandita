<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Encuesta extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'id';
    protected $table = 'Encuestas';
    public $incrementing = true;
    public $timestamps = true;

    const CREATED_AT = 'fecha_alta';
    const UPDATED_AT = 'fecha_modificacion';
    const DELETED_AT = 'fecha_baja';

    protected $fillable = [
        'factura_id',
        'mesa_id',  
        'mesa', 
        'restaurante', 
        'mozo', 
        'cocinero', 
        'comentario', 
        'fecha_alta', 
        'fecha_modificacion', 
        'fecha_baja'
    ];
}