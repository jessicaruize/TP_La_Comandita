<?php

enum RolUsuario
{
    case Bartender;
    case Cervecero;
    case Cocinero;
    case Mozo;
    case Socio;

    public static function obtenerValor($index){
        $enums = self::cases();
        return $enums[$index]->name;
    } 

}