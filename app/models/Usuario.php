<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Usuario extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'id';
    protected $table = 'Usuarios';
    public $incrementing = true;
    public $timestamps = true;

    const CREATED_AT = 'fecha_alta';
    const UPDATED_AT = 'fecha_modificacion';
    const DELETED_AT = 'fecha_baja';

    protected $fillable = [
        'mail', 
        'clave',
        'dni', 
        'nombre', 
        'apellido', 
        'telefono', 
        'rol', 
        'sector',
        'fecha_alta', 
        'fecha_modificacion', 
        'fecha_baja'
    ];
}