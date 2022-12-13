<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comanda extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'id';
    protected $table = 'Comandas';
    public $incrementing = true;
    public $timestamps = true;

    const CREATED_AT = 'fecha_alta';
    const UPDATED_AT = 'fecha_modificacion';
    const DELETED_AT = 'fecha_baja';

    protected $fillable = [
        'codigo_comanda', 
        'cliente_id', 
        'mozo_id', 
        'mesa_id', 
        'foto', 
        'monto', 
        'fecha_alta', 
        'fecha_modificacion', 
        'fecha_baja'
    ];
    public static function obtener_codigo(){
        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle($permitted_chars), 0, 5);
    }
}