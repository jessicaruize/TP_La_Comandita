<?php

enum EstadoPedido
{
    case Pendiente;
    case Preparacion;
    case Listo;
    case Entregado;

    public static function obtenerValor($index){
        $enums = self::cases();
        return $enums[$index]->name;
    } 

}