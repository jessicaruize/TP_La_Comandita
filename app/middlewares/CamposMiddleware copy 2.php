<?php
require_once './models/Validaciones.php';

use App\Models\Usuario;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Mesa;
use App\Models\Encuesta;
use App\Models\Comanda;
use App\Models\Suspension;
use App\Models\Pedido;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class CamposMiddlelware
{

  //----------------------------USUARIOS------------------------------------------------------------------------
    public function login(Request $request, RequestHandler $handler): Response
    {
      try {
        $response = new Response();
        $body = $request->getParsedBody();
        $response = $response->withStatus(401);
        $errores = [];
        $errores['error_mail'] = Validaciones::errorMail($body['mail']);
        $errores['error_clave'] =Validaciones::errorClave($body['clave']);
        $mensajesErr = array_filter($errores, fn($e) => $e != 0, ARRAY_FILTER_USE_BOTH);
        if(count($mensajesErr) == 0){
          $response = $handler->handle($request);
        }
        else{
          $response->getBody()->write(json_encode($mensajesErr));
          $response = $response->withStatus(401);
        }
        return $response->withHeader('Content-Type', 'application/json');
      } catch (Throwable $e) {
        var_dump(array("Error" => "error, intente nuevamente más tarde. ($e)"));
      }
    }

    public function cargarUsuario(Request $request, RequestHandler $handler): Response
    {
      $response = new Response();
      try {
        $body = $request->getParsedBody();
        $response = $response->withStatus(401);
        $errores = [];
        $errores['error_mail'] = Validaciones::errorMail($body['mail']);
        $errores['error_dni'] = Validaciones::errorDni($body['dni']);
        if(!Usuario::where('dni', $body['dni'])->first() && !Usuario::where('mail', $body['mail'])->first()){
          $errores['error_clave'] = Validaciones::errorClave($body['clave']);
          $errores['error_nombre'] = Validaciones::errorNombre($body['nombre']);
          $errores['error_apellido'] = Validaciones::errorApellido($body['apellido']);
          $errores['error_telefono'] = Validaciones::errorTelefono($body['telefono']);
          $errores['error_rol'] = Validaciones::errorRol($body['rol']);
          $errores['error_sector'] = Validaciones::errorSector($body['sector']);
          $mensajesErr = array_filter($errores, fn($e) => $e != 0, ARRAY_FILTER_USE_BOTH);
          if(count($mensajesErr) == 0){
            $response = $handler->handle($request);
          }
          else{
            $response->getBody()->write(json_encode($mensajesErr));
            $response = $response->withStatus(401);
          }
        }
        else{
          $response->getBody()->write(json_encode(array('error' =>  'El usuario ya existe (dni y/o mail existentes).')));
          $response = $response->withStatus(401);
        }
        return $response->withHeader('Content-Type', 'application/json');
      } catch (Throwable $e) {
        var_dump(array("Error" =>  "error, intente nuevamente más tarde. ($e)"));
      }
    }

    public function modificarUsuario(Request $request, RequestHandler $handler): Response
    {
      $response = new Response();
      try {
        $body = $request->getParsedBody();
        $response = $response->withStatus(401);
        $errores = [];
        if(!(isset($body['mail']) || isset($body['dni']) || isset($body['clave']) || isset($body['nombre']) 
        || isset($body['apellido']) || isset($body['telefono']) || isset($body['rol']) || isset($body['sector'])))
        {
            $response->getBody()->write(json_encode(array('error' => 'Debe modificar al menos un dato.')));
            $response = $response->withStatus(401);
            return $response->withHeader('Content-Type', 'application/json');
        }
        if(isset($body['mail'])){
          $errores['error_mail'] = Validaciones::errorMail($body['mail']);
          if(Usuario::where('mail', $body['mail'])->first()){
            $response->getBody()->write(json_encode(array('error' => 'No puede ingresar un mail existente.')));
            $response = $response->withStatus(401);
            return $response->withHeader('Content-Type', 'application/json');
          }
        }
        if(isset($body['dni'])){
          $errores['error_dni'] = Validaciones::errorDni($body['dni']);
          if(Usuario::where('dni', $body['dni'])->first()){
            $response->getBody()->write(json_encode(array('error' => 'No puede ingresar un dni existente.')));
            $response = $response->withStatus(401);
            return $response->withHeader('Content-Type', 'application/json');
          }
        }
        if(isset($body['clave'])){
          $errores['error_clave'] = Validaciones::errorClave($body['clave']);
        }
        if(isset($body['nombre'])){
          $errores['error_nombre'] = Validaciones::errorNombre($body['nombre']);
        }
        if(isset($body['apellido'])){
          $errores['error_apellido'] = Validaciones::errorApellido($body['apellido']);
        }
        if(isset($body['telefono'])){
          $errores['error_telefono'] = Validaciones::errorTelefono($body['telefono']);
        }
        if(isset($body['rol'])){
          $errores['error_rol'] = Validaciones::errorRol($body['rol']);
        }
        if(isset($body['sector'])){
          $errores['error_sector'] = Validaciones::errorSector($body['sector']);
        }
        $mensajesErr = array_filter($errores, fn($e) => $e != 0, ARRAY_FILTER_USE_BOTH);
        if(count($mensajesErr) == 0){
          $response = $handler->handle($request);
        }
        else{
          $response->getBody()->write(json_encode($mensajesErr));
          $response = $response->withStatus(401);
        }
        return $response->withHeader('Content-Type', 'application/json');
      } catch (Throwable $e) {
        var_dump(array("Error" => "error, intente nuevamente más tarde. ($e)"));
      }
    }

    //-----------------CLIENTES-----------------------------------------------------------------------------------
    public function cargarCliente(Request $request, RequestHandler $handler): Response
    {
      $response = new Response();
      try {
        $body = $request->getParsedBody();
        $response = $response->withStatus(401);
        $errores = [];
        $errores['error_dni'] = Validaciones::errorDni($body['dni']);
        if(!Cliente::where('dni', $body['dni'])->first()){
          $errores['error_nombre'] = Validaciones::errorNombre($body['nombre']);
          $errores['error_apellido'] = Validaciones::errorApellido($body['apellido']);
          $errores['error_telefono'] = Validaciones::errorTelefono($body['telefono']);
          $mensajesErr = array_filter($errores, fn($e) => $e != 0, ARRAY_FILTER_USE_BOTH);
          if(count($mensajesErr) == 0){
            $response = $handler->handle($request);
          }
          else{
            $response->getBody()->write(json_encode($mensajesErr));
            $response = $response->withStatus(401);
          }
        }
        else{
          $response->getBody()->write(json_encode(array('error' => 'El cliente ya existe (dni existente).')));
          $response = $response->withStatus(401);
        }
        return $response->withHeader('Content-Type', 'application/json');
      } catch (Throwable $e) {
        var_dump(array("Error" => "error, intente nuevamente más tarde. ($e)"));
      }
    }
    public function modificarCliente(Request $request, RequestHandler $handler): Response
    {
      $response = new Response();
      try {
        $body = $request->getParsedBody();
        $response = $response->withStatus(401);
        $errores = [];
        if(!(isset($body['dni']) || isset($body['nombre']) || isset($body['apellido']) || isset($body['telefono'])))
        {
            $response->getBody()->write(json_encode(array('error' => 'Debe modificar al menos un dato.')));
            $response = $response->withStatus(401);
            return $response->withHeader('Content-Type', 'application/json');
        }
        if(isset($body['dni'])){
          $errores['error_dni'] = Validaciones::errorDni($body['dni']);
          if(Cliente::where('dni', $body['dni'])->first()){
            $response->getBody()->write(json_encode(array('error' => 'No puede ingresar un dni existente.')));
            $response = $response->withStatus(401);
            return $response->withHeader('Content-Type', 'application/json');
          }
        }
        if(isset($body['nombre'])){
          $errores['error_nombre'] = Validaciones::errorNombre($body['nombre']);
        }
        if(isset($body['apellido'])){
          $errores['error_apellido'] = Validaciones::errorApellido($body['apellido']);
        }
        if(isset($body['telefono'])){
          $errores['error_telefono'] = Validaciones::errorTelefono($body['telefono']);
        }
        $mensajesErr = array_filter($errores, fn($e) => $e != 0, ARRAY_FILTER_USE_BOTH);
        if(count($mensajesErr) == 0){
          $response = $handler->handle($request);
        }
        else{
          $response->getBody()->write(json_encode($mensajesErr));
          $response = $response->withStatus(401);
        }
        return $response->withHeader('Content-Type', 'application/json');
      } catch (Throwable $e) {
        var_dump(array("Error" => "error, intente nuevamente más tarde. ($e)"));
      }
    }
    //-----------------------------------MESAS--------------------------------------------------------------
    public function cargarMesa(Request $request, RequestHandler $handler): Response
    {
      $response = new Response();
      try {
        $body = $request->getParsedBody();
        $response = $response->withStatus(401);
        $errores = [];
        $errores['error_capacidad']  = Validaciones::errorCantidad($body['capacidad'], 2, 20);
        if(!$errores['error_capacidad'] == 0){
          $response = $handler->handle($request);
        }
        else{
          $response->getBody()->write(json_encode($errores));
          $response = $response->withStatus(401);
        }
        return $response->withHeader('Content-Type', 'application/json');
      } catch (Throwable $e) {
        var_dump(array('Error' => 'error, intente nuevamente más tarde. ($e)'));
      }
    }

    public function modificarMesa(Request $request, RequestHandler $handler): Response
    {
      $response = new Response();
      try {
        $body = $request->getParsedBody();
        $response = $response->withStatus(401);
        $errores = [];
        if(!(isset($body['capacidad']) || isset($body['estado'])))
        {
            $response->getBody()->write(json_encode(array('error' => 'Debe modificar al menos un dato.')));
            $response = $response->withStatus(401);
            return $response->withHeader('Content-Type', 'application/json');
        }
        if(isset($body['capacidad'])){
          $errores['error_capacidad'] = Validaciones::errorCantidad($body['capacidad'], 2, 20);
        }
        if(isset($body['estado'])){
          $errores['error_estado'] = Validaciones::errorEstadoMesa($body['estado']);
        }
        $mensajesErr = array_filter($errores, fn($e) => $e != 0, ARRAY_FILTER_USE_BOTH);
        if(count($mensajesErr) == 0){
          $response = $handler->handle($request);
        }
        else{
          $response->getBody()->write(json_encode($mensajesErr));
          $response = $response->withStatus(401);
        }
        return $response->withHeader('Content-Type', 'application/json');
      } catch (Throwable $e) {
        var_dump(array("Error" => "error, intente nuevamente más tarde. ($e)"));
      }
    }
    //----------------------------USUARIOS------------------------------------------------------------------------
    public function cargarProducto(Request $request, RequestHandler $handler): Response
    {
      $response = new Response();
      try {
        $body = $request->getParsedBody();
        $response = $response->withStatus(401);
        $errores = [];
        $errores['error_nombre'] = Validaciones::errorNombre($body['nombre']);
        if(!Producto::where('nombre', $body['nombre'])->first()){
          $errores['error_cantidad'] = Validaciones::errorCantidad($body['cantidad'], 1, 100000);
          $errores['error_sector'] = Validaciones::errorSector($body['sector']);
          $errores['error_precio'] = Validaciones::errorPrecio($body['precio'], 0.10, 20000);
          $mensajesErr = array_filter($errores, fn($e) => $e != 0, ARRAY_FILTER_USE_BOTH);
          if(count($mensajesErr) == 0){
            $response = $handler->handle($request);
          }
          else{
            $response->getBody()->write(json_encode($mensajesErr));
            $response = $response->withStatus(401);
          }
        }
        else{
          $response->getBody()->write(json_encode(array('error' => 'El producto ya existe (nombre existentes).')));
          $response = $response->withStatus(401);
        }
        return $response->withHeader('Content-Type', 'application/json');
      } catch (Throwable $e) {
        var_dump(array('Error' => "error, intente nuevamente más tarde. ($e)"));
      }
    }

    public function modificarProducto(Request $request, RequestHandler $handler): Response
    {
      $response = new Response();
      try {
        $body = $request->getParsedBody();
        $response = $response->withStatus(401);
        $errores = [];
        if(!(isset($body['nombre']) || isset($body['cantidad']) || isset($body['sector']) || isset($body['precio']))){
            $response->getBody()->write(json_encode(array('error' => 'Debe modificar al menos un dato.')));
            $response = $response->withStatus(401);
            return $response->withHeader('Content-Type', 'application/json');
        }
        if(isset($body['nombre'])){
          $errores['error_nombre'] = Validaciones::errorNombre($body['nombre']);
          if(Producto::where('nombre', $body['nombre'])->first()){
            $response->getBody()->write(json_encode(array('error' => 'El producto ya existe (nombre existentes).')));
            $response = $response->withStatus(401);
            return $response->withHeader('Content-Type', 'application/json');
          }
        }
        if(isset($body['cantidad'])){
          $errores['error_cantidad'] =  Validaciones::errorCantidad($body['cantidad'], -100000, 100000);
        }
        if(isset($body['sector'])){
          $errores['error_sector'] = Validaciones::errorSector($body['sector']);
        }
        if(isset($body['precio'])){
          $errores['error_precio'] = Validaciones::errorPrecio($body['precio'], 0.10, 20000);
        }
        $mensajesErr = array_filter($errores, fn($e) => $e != 0, ARRAY_FILTER_USE_BOTH);
        if(count($mensajesErr) == 0){
          $response = $handler->handle($request);
        }
        else{
          $response->getBody()->write(json_encode($mensajesErr));
          $response = $response->withStatus(401);
        }
        return $response->withHeader('Content-Type', 'application/json');
      } catch (Throwable $e) {
        var_dump(array("Error" => "error, intente nuevamente más tarde. ($e)"));
      }
    }

    //----------------------------------------------COMANDA-----------------------------------------------------------------------------
    
    public function cargarComanda(Request $request, RequestHandler $handler): Response
    {
      $response = new Response();
      try {
        $body = $request->getParsedBody();
        $response = $response->withStatus(401);
        if(isset($body['cliente_dni'], $body['codigo_mesa'])){
          if(Cliente::where('dni', $body['cliente_dni'])->first() 
          && Mesa::where('codigo_mesa', $body['codigo_mesa'])->first()->estado == EstadoMesa::Cerrada->name){
              $response = $handler->handle($request);
          }
          else{
            $response->getBody()->write(json_encode(array('error' =>  'El cliente y la mesa deben existir, además, la mesa debe estar cerrada .')));
          }
        }
        else{
          $response->getBody()->write(json_encode(array('error' =>  'Debe igresar los campos: codigo_mesa y cliente_dni.')));
        }
        return $response->withHeader('Content-Type', 'application/json');
      } catch (Throwable $e) {
        var_dump(array("Error" =>  "error, intente nuevamente más tarde. ($e)"));
      }
    }

    public function modificarComanda(Request $request, RequestHandler $handler): Response
    {
      $response = new Response();
      try {
        $body = $request->getParsedBody();
        $response = $response->withStatus(401);
        if(!(isset($body['cliente_dni']) || isset($body['mozo_dni'])|| isset($body['codigo_mesa'])))
        {
            $response->getBody()->write(json_encode(array('error' => 'Debe modificar al menos un dato(cliente_dni, mozo_dni, codigo_mesa).')));
            $response = $response->withStatus(401);
            return $response->withHeader('Content-Type', 'application/json');
        }
        if(isset($body['cliente_dni'])){
          if(!Cliente::where('dni', $body['cliente_dni'])->first()){
            $response->getBody()->write(json_encode(array('error' => 'No existe cliente con ese DNI.')));
            $response = $response->withStatus(401);
            return $response->withHeader('Content-Type', 'application/json');
          }
        }
        if(isset($body['mozo_dni'])){
          if(Usuario::where('dni', $body['mozo_dni'])->first()->rol == 'mozo'){
            $response->getBody()->write(json_encode(array('error' => 'No existe mozo con ese DNI.')));
            $response = $response->withStatus(401);
            return $response->withHeader('Content-Type', 'application/json');
          }
        }
        if(isset($body['codigo_mesa'])){
          if(!Mesa::where('codigo_mesa', $body['codigo_mesa'])->first()){
            $response->getBody()->write(json_encode(array('error' => 'No existe mesa con ese codigo.')));
            $response = $response->withStatus(401);
            return $response->withHeader('Content-Type', 'application/json');
          }
        }
        $response = $handler->handle($request);
        return $response->withHeader('Content-Type', 'application/json');
      } catch (Throwable $e) {
        var_dump(array("Error" => "error, ($e)"));
      }
    }

     //----------------------------------------------PEDIDO-----------------------------------------------------------------------------
    
    public function cargarPedido(Request $request, RequestHandler $handler): Response
    {
      $response = new Response();
      try {
        $body = $request->getParsedBody();
        $response = $response->withStatus(401);
        $errores = [];
        if(isset($body['codigo_comanda']) && isset($body['producto_id'])
        && isset($body['cantidad'])){
          $errores['error_codigo_comanda'] = Validaciones::errorLargoCadena($body['codigo_comanda'], 5, 5);
          $comanda = Comanda::where('codigo_comanda', $body['codigo_comanda'])->first();
          $producto = Producto::find($body['producto_id']);
          if($producto)
          {
            $errores['error_cantidad'] = Validaciones::errorCantidad($body['cantidad'], 1, $producto->cantidad);
          }
          if(isset($body['detalle'])){
            $errores['error_detalle'] = Validaciones::errorLargoCadena($body['detalle'], 0, 66);
          }
          if($comanda && $producto){
            $mensajesErr = array_filter($errores, fn($e) => $e != 0, ARRAY_FILTER_USE_BOTH);
            if(count($mensajesErr) == 0){
              $response = $handler->handle($request);
            }
            else{
              $response->getBody()->write(json_encode($mensajesErr));
              $response = $response->withStatus(401);
            }
          }
          else{
            $response->getBody()->write(json_encode(array('error' =>  'Comanda y/o producto no existen, corrobore los datos.')));
            $response = $response->withStatus(401);
          }
        }
        else{
          $response->getBody()->write(json_encode(array('error' =>  'Debe ingresar: codigo_comanda, producto_id y cantidad.')));
          $response = $response->withStatus(401);
        }
        return $response->withHeader('Content-Type', 'application/json');
      } catch (Throwable $e) {
        var_dump(array("Error" =>  "error, intente nuevamente más tarde. ($e)"));
      }
    }

    public function modificarPedido(Request $request, RequestHandler $handler): Response
    {
      $response = new Response();
      try {
        $body = $request->getParsedBody();
        $response = $response->withStatus(401);
        $errores = [];
        if(!(isset($body['preparador_dni']) || isset($body['codigo_comanda']) || isset($body['producto_id'])
        || isset($body['detalle']) || isset($body['cantidad']) || isset($body['estado']) 
        || isset($body['demora_estimada'])))
        {
            $response->getBody()->write(json_encode(array('error' => 'Debe modificar al menos un dato.')));
            $response = $response->withStatus(401);
            return $response->withHeader('Content-Type', 'application/json');
        }
        if(isset($body['preparador_dni'])){
          if(!Usuario::where('dni', $body['preparador_dni'])->first()){
            $errores['error_preparador'] = 'No existe usuario con ese DNI.';
          }
        }
        if(isset($body['codigo_comanda'])){
          if(!Comanda::where('codigo_comanda', $body['codigo_comanda'])->first()){
            $errores['error_preparador'] =  'No existe comanda con ese código.';
          }
        }
        if(isset($body['producto_id'])){
          if(!Producto::find($body['producto_id'])){
            $errores['error_producto'] = 'No existe el producto.';
          }
          else{
            if(isset($body['cantidad'])){
              $errores['error_cantidad'] = Validaciones::errorCantidad($body['cantidad'], 1, Producto::find($body['producto_id'])->cantidad);
            }
          }
        }
        if(isset($body['detalle'])){
          $errores['error_detalle'] = Validaciones::errorLargoCadena($body['detalle'], 0, 66);
        }
        if(isset($body['estado'])){
          $errores['error_estado'] = Validaciones::errorEstadoPedido($body['estado']);
        }
        if(isset($body['demora_estimada'])){
          $errores['error_demora_estimada'] = Validaciones::errorDemora($body['demora_estimada'], 10, 120);
        }
        if(isset($body['entregado'])){
          $errores['error_entregado'] = Validaciones::errorEntregado($body['entregado']);
        }
        $mensajesErr = array_filter($errores, fn($e) => $e != 0, ARRAY_FILTER_USE_BOTH);
        if(count($mensajesErr) == 0){
          $response = $handler->handle($request);
        }
        else{
          $response->getBody()->write(json_encode($mensajesErr));
          $response = $response->withStatus(401);
        }
        return $response->withHeader('Content-Type', 'application/json');
      } catch (Throwable $e) {
        var_dump(array("Error" => "error, intente nuevamente más tarde. ($e)"));
      }
    }

    //---------------------------------------------ENCUESTA-----------------------------------------------------------------------------

    public function cargarEncuesta(Request $request, RequestHandler $handler): Response
    {
      $response = new Response();
      try {
        $body = $request->getParsedBody();
        $response = $response->withStatus(401);
        $errores = [];
        if(isset($body['codigo_comanda']) && isset($body['codigo_mesa']) && isset($body['mesa']) 
        && isset($body['restaurante']) && isset($body['mozo']) && isset($body['cocinero'])){
          // $errores['error_codigo_mesa'] = Validaciones::errorLargoCadena($body['codigo_mesa'], 5, 5);
          // $errores['error_codigo_comanda'] = Validaciones::errorLargoCadena($body['codigo_comanda'], 5, 5);
          //con el codigo de comanda podemos obtener de todas formas el codigo de mesa correspondiente
          if(Mesa::where('codigo_mesa', $body['codigo_mesa'])->first() && Comanda::where('codigo_comanda', $body['codigo_comanda'])->first()){
            $errores['error_mesa'] = Validaciones::errorCantidad($body['mesa'], 1, 10);
            $errores['error_restaurante'] = Validaciones::errorCantidad($body['restaurante'], 1, 10);
            $errores['error_mozo'] = Validaciones::errorCantidad($body['mozo'], 1, 10);
            $errores['error_cocinero'] = Validaciones::errorCantidad($body['cocinero'], 1, 10);
            if(isset($body['comentario'])){
              $errores['error_comentario'] = Validaciones::errorLargoCadena($body['comentario'], 10, 66);
            }
            $mensajesErr = array_filter($errores, fn($e) => $e != 0, ARRAY_FILTER_USE_BOTH);
            if(count($mensajesErr) == 0){
              $response = $handler->handle($request);
            }
            else{
              $response->getBody()->write(json_encode($mensajesErr));
              $response = $response->withStatus(401);
            }
          }
          else{
            $response->getBody()->write(json_encode(array('error' =>  'Error, debe existir la mesa y la comanda')));
            $response = $response->withStatus(401);
          }
      }
      else{
        $response->getBody()->write(json_encode(array('error' =>  'Error, debe cargas los siguientes campos: codigo_comanda, codigo_mesa, mesa, restaurante, mozo y cocinero')));
            $response = $response->withStatus(401);
      }
        return $response->withHeader('Content-Type', 'application/json');
      } catch (Throwable $e) {
        var_dump(array("Error" =>  "error, intente nuevamente más tarde. ($e)"));
      }
    }

    public function modificarEncuesta(Request $request, RequestHandler $handler): Response
    {
      $response = new Response();
      try {
        $body = $request->getParsedBody();
        $response = $response->withStatus(401);
        $errores = [];
        if(!(isset($body['codigo_comanda']) || isset($body['codigo_mesa']) || isset($body['mesa']) || isset($body['restaurante']) 
        || isset($body['mozo']) || isset($body['cocinero']) || isset($body['comentario'])))
        {
            $response->getBody()->write(json_encode(array('error' => 'Debe modificar al menos un dato.')));
            $response = $response->withStatus(401);
            return $response->withHeader('Content-Type', 'application/json');
        }
        if(isset($body['codigo_comanda'])){
          //$errores['error_codigo_comanda'] = Validaciones::errorLargoCadena($body['codigo_comanda'], 5, 5);
          if(!Comanda::where('codigo_comanda', $body['codigo_comanda'])->first()){
            $response->getBody()->write(json_encode(array('error' => 'Debe ingresar un código de comanda existente.')));
            $response = $response->withStatus(401);
            return $response->withHeader('Content-Type', 'application/json');
          }
        }
        if(isset($body['codigo_mesa'])){
          //$errores['error_dni'] = Validaciones::errorDni($body['dni']);
          if(!Mesa::where('codigo_mesa', $body['codigo_mesa'])->first()){
            $response->getBody()->write(json_encode(array('error' => 'Debe ingresar un código de mesa existente.')));
            $response = $response->withStatus(401);
            return $response->withHeader('Content-Type', 'application/json');
          }
        }
        if(isset($body['mesa'])){
          $errores['error_mesa'] = Validaciones::errorCantidad($body['mesa'], 1, 10);
        }
        if(isset($body['restaurante'])){
          $errores['error_restaurante'] = Validaciones::errorCantidad($body['restaurante'], 1, 10);
        }
        if(isset($body['mozo'])){
          $errores['error_mozo'] = Validaciones::errorCantidad($body['mozo'], 1, 10);
        }
        if(isset($body['cocinero'])){
          $errores['error_cocinero'] = Validaciones::errorCantidad($body['cocinero'], 1, 10);
        }
        if(isset($body['comentario'])){
          $errores['error_comentario'] = Validaciones::errorLargoCadena($body['comentario'], 10, 66);
        }
        $mensajesErr = array_filter($errores, fn($e) => $e != 0, ARRAY_FILTER_USE_BOTH);
        if(count($mensajesErr) == 0){
          $response = $handler->handle($request);
        }
        else{
          $response->getBody()->write(json_encode($mensajesErr));
          $response = $response->withStatus(401);
        }
        return $response->withHeader('Content-Type', 'application/json');
      } catch (Throwable $e) {
        var_dump(array("Error" => "error, intente nuevamente más tarde. ($e)"));
      }
    }
    //---------------------------------------------SUSPENSION-----------------------------------------------------------------------------

    public function cargarSuspension(Request $request, RequestHandler $handler): Response
    {
      $response = new Response();
      try {
        $body = $request->getParsedBody();
        $response = $response->withStatus(401);
        $errores = [];
        if(isset($body['usuario_id']) && isset($body['motivo'])){
          if(Usuario::find($body['usuario_id'])){
            $errores['error_motivo'] = Validaciones::errorLargoCadena($body['motivo'], 3, 66);
            $mensajesErr = array_filter($errores, fn($e) => $e != 0, ARRAY_FILTER_USE_BOTH);
            if(count($mensajesErr) == 0){
              $response = $handler->handle($request);
            }
            else{
              $response->getBody()->write(json_encode($mensajesErr));
              $response = $response->withStatus(401);
            }
          }
          else{
            $response->getBody()->write(json_encode(array('error' =>  'Error, debe existir el usuario.')));
            $response = $response->withStatus(401);
          }
      }
      else{
        $response->getBody()->write(json_encode(array('error' =>  'Error, debe cargas los siguientes campos: usuario_id y motivo')));
            $response = $response->withStatus(401);
      }
        return $response->withHeader('Content-Type', 'application/json');
      } catch (Throwable $e) {
        var_dump(array("Error" =>  "error, intente nuevamente más tarde. ($e)"));
      }
    }

    public function modificarSuspension(Request $request, RequestHandler $handler): Response
    {
      try {
        $response = new Response();
        $body = $request->getParsedBody();
        $response = $response->withStatus(401);
        $errores = [];
        if(!(isset($body['usuario_id']) || isset($body['motivo'])))
        {
            $response->getBody()->write(json_encode(array('error' => 'Debe modificar al menos un dato.')));
            $response = $response->withStatus(401);
            return $response->withHeader('Content-Type', 'application/json');
        }
        if(isset($body['usuario_id'])){
          if(!Usuario::find($body['usuario_id'])){
            $errores['error_usuario_id'] = "El usuario no existe";
          }
        }
        if(isset($body['motivo'])){
          $errores['error_motivo'] = Validaciones::errorLargoCadena($body['motivo'], 3, 66);
        }
        $mensajesErr = array_filter($errores, fn($e) => $e != 0, ARRAY_FILTER_USE_BOTH);
        if(count($mensajesErr) == 0){
          $response = $handler->handle($request);
        }
        else{
          $response->getBody()->write(json_encode($mensajesErr));
          $response = $response->withStatus(401);
        }
        return $response->withHeader('Content-Type', 'application/json');
      } catch (Throwable $e) {
        var_dump(array("Error" => "error, intente nuevamente más tarde. ($e)"));
      }
    }
}