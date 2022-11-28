<?php

enum Sector
{
    case BarraTragosVinos;
    case BarraChoperas;
    case Cocina;
    case CandyBar;
    case Salon;
    case Administracion;
    
    public static function obtenerValor($index){
        $enums = self::cases();
        return $enums[$index]->name;
    } 
}