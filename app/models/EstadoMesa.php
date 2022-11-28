<?php

enum EstadoMesa
{
    case EsperandoPedido;
    case Comiendo;
    case Pagando;
    case Cerrada;

    public static function obtenerValor($index){
        $enums = self::cases();
        return $enums[$index];
    } 

}