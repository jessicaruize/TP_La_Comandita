<?php
date_default_timezone_set('America/Buenos_Aires');
class Validaciones
{
    public static function errorMail($mail)
    {
        if(isset($mail) && !empty($mail)){
            if(filter_var($mail, FILTER_VALIDATE_EMAIL)){
                return 0;
            }
            else{
                return "Debe ingresar un mail válido.";
            }
        }
        else{
            return "Campo vacio.";
        }
    }

    public static function errorClave($clave)
    {
        if(!Validaciones::errorLargoCadena($clave, 6, 20)){
            return 0;
        }
        else{
            return "La clave debe tener un mínimo de 6 y un máximo de 20 caracteres.";
        }
    }

    public static function errorNombre($nombre)
    {
        if(!Validaciones::errorLargoCadena($nombre, 3, 20)){
            return 0;
        }
        else{
            return "El nombre debe tener un mínimo de 3 y un máximo de 20 caracteres.";
        }
    }

    public static function errorApellido($apellido)
    {
        if(!Validaciones::errorLargoCadena($apellido, 3, 20)){
            return 0;
        }
        else{
            return "El apellido debe tener un mínimo de 3 y un máximo de 20 caracteres.";
        }
    }

    public static function errorDni($dni)
    {
        if(is_numeric($dni) && !Validaciones::errorLargoCadena($dni, 6, 9)){
            return 0;
        }
        else{
            return "Ingrese el DNI sin puntos y debe tener un mínimo de 6 y un máximo de 9 caracteres.";
        }
    }

    public static function errorTelefono($telefono)
    {
        if(preg_match("/^((\+54\s?)?(\s?9\s?)?\d{2,3}[\s-]?\d{3,4}-?\d{3,4}|\d{10,11}|(\d{3,4}[\s-]){1,2}\d{3,4})$/", $telefono)){
            return 0;
        }
        else{
            return "Error al ingresar telefono, ejemplos correctos: 0800-333-4578, 0800 3334 578, 08003334578, +541154758695, +5491235458548, +54 9 114534-8569, +54 9 11 4534-8569, +54 336-5466-354, +54 336 5466-354.";
        }
    }
    public static function errorRol($rol)
    {
        if($rol == '0'  || $rol == '1' || $rol == '2' 
        || $rol == '3' || $rol == '4'){
            return 0;
        }
        else{
            return "Roles disponibles: 0- Bartender, 1- Cervecero/a, 2- Cocinero/a, 3- Mozo/a y 4- Socio/a.";
        }
    }

    public static function errorSector($sector)
    {
        if($sector == '0' || $sector == '1' || $sector == '2' 
        || $sector == '3' || $sector == '4' || $sector == '5'){
            return 0;
        }
        else{
            return "Sectores disponibles: 0- Barra de tragos y vinos, 1- Barra de choperas de cerveza artesanal, 2- Cocina, 3- Candy bar, 4- Salon y 5- Administración";
        }
    }

    public static function errorEstadoPedido($sector)
    {
        if($sector == '0' || $sector == '1' || $sector == '2' 
        || $sector == '3'){
            return 0;
        }
        else{
            return "Estados disponibles: 0- Pedido pendiente, 1- Pedido en preparación, 2- Pedido listo para servir y 3- Pedido entregado.";
        }
    }

    public static function errorLargoCadena($cadena, $minimo, $maximo)
    { 
        if(isset($cadena) && !empty($cadena) && is_numeric($minimo) && is_numeric($maximo)){
            if(strlen($cadena) >= $minimo  && strlen($cadena) <= $maximo){
                return 0;
            }
        }
        return "Debe ingresar un texto con un largo mínimo de $minimo y  máximo de $maximo.";
    }
    public static function errorCantidad($valor, $minimo, $maximo)
    {
        if((int)$valor >= (int)$minimo  && (int)$valor <= (int)$maximo){
            return 0;
        }
        return "Debe ingresar una cantidad con un valor mínimo $minimo y máximo $maximo.";
    }

    public static function errorDemora($valor, $minimo, $maximo)
    {
        if((int)$valor >= (int)$minimo  && (int)$valor <= (int)$maximo){
            return 0;
        }
        return "Debe ingresar una demora en minutos con un valor mínimo de $minimo y máximo de $maximo.";
    }

    public static function errorEstadoMesa($sector)
    {
        if($sector == '0' || $sector == '1' || $sector == '2' 
        || $sector == '3'){
            return 0;
        }
        else{
            return "Estados disponibles: 0- Cliente esperando pedido, 1- Cliente comiento, 2- Cliente pagando y 3- Cerrada";
        }
    }

    public static function errorPrecio($valor, $minimo, $maximo)
    {
        if((double)$valor >= (double)$minimo  && (double)$valor <= (double)$maximo){
            return 0;
        }
        return "Debe ingresar una cantidad con un valor mínimo $minimo y máximo $maximo.";
    }

    public static function errorEntregado($valor)
    {
        if(strtolower($valor) == "si" || strtolower($valor) == "no" )
        return "Debe ingresar si o no";
    }
    

}