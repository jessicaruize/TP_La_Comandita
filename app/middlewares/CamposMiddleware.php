<?php
require_once './models/Validaciones.php';

use App\Models\Usuario;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Mesa;
use App\Models\Encuesta;
use App\Models\Factura;
use App\Models\Suspension;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class CamposMiddlelware
{

  //----------------------------USUARIOS------------------------------------------------------------------------
    public function camposLogin(Request $request, RequestHandler $handler): Response
    {
      try {
        $response = new Response();
        $body = $request->getParsedBody();
        $response = $response->withStatus(401);
        $errores = [];
        $errores['error mail'] = Validaciones::errorMail($body['mail']);
        $errores['error clave'] =Validaciones::errorClave($body['clave']);
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

    public function camposCargarUsuario(Request $request, RequestHandler $handler): Response
    {
      $response = new Response();
      try {
        $body = $request->getParsedBody();
        $response = $response->withStatus(401);
        $errores = [];
        $errores['error mail'] = Validaciones::errorMail($body['mail']);
        $errores['error dni'] = Validaciones::errorDni($body['dni']);
        if(!Usuario::where('dni', $body['dni'])->first() && !Usuario::where('mail', $body['mail'])->first()){
          $errores['error clave'] = Validaciones::errorClave($body['clave']);
          $errores['error nombre'] = Validaciones::errorNombre($body['nombre']);
          $errores['error apellido'] = Validaciones::errorApellido($body['apellido']);
          $errores['error telefono'] = Validaciones::errorTelefono($body['telefono']);
          $errores['error rol'] = Validaciones::errorRol($body['rol']);
          $errores['error sector'] = Validaciones::errorSector($body['sector']);
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

    public function camposModificarUsuario(Request $request, RequestHandler $handler): Response
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
          $errores['error mail'] = Validaciones::errorMail($body['mail']);
          if(Usuario::where('mail', $body['mail'])->first()){
            $response->getBody()->write(json_encode(array('error' => 'No puede ingresar un mail existente.')));
            $response = $response->withStatus(401);
            return $response->withHeader('Content-Type', 'application/json');
          }
        }
        if(isset($body['dni'])){
          $errores['error dni'] = Validaciones::errorDni($body['dni']);
          if(Usuario::where('dni', $body['dni'])->first()){
            $response->getBody()->write(json_encode(array('error' => 'No puede ingresar un dni existente.')));
            $response = $response->withStatus(401);
            return $response->withHeader('Content-Type', 'application/json');
          }
        }
        if(isset($body['clave'])){
          $errores['error clave'] = Validaciones::errorClave($body['clave']);
        }
        if(isset($body['nombre'])){
          $errores['error nombre'] = Validaciones::errorNombre($body['nombre']);
        }
        if(isset($body['apellido'])){
          $errores['error apellido'] = Validaciones::errorApellido($body['apellido']);
        }
        if(isset($body['telefono'])){
          $errores['error telefono'] = Validaciones::errorTelefono($body['telefono']);
        }
        if(isset($body['rol'])){
          $errores['error rol'] = Validaciones::errorRol($body['rol']);
        }
        if(isset($body['sector'])){
          $errores['error sector'] = Validaciones::errorSector($body['sector']);
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
    public function camposCargarCliente(Request $request, RequestHandler $handler): Response
    {
      $response = new Response();
      try {
        $body = $request->getParsedBody();
        $response = $response->withStatus(401);
        $errores = [];
        $errores['error dni'] = Validaciones::errorDni($body['dni']);
        if(!Cliente::where('dni', $body['dni'])->first()){
          $errores['error nombre'] = Validaciones::errorNombre($body['nombre']);
          $errores['error apellido'] = Validaciones::errorApellido($body['apellido']);
          $errores['error telefono'] = Validaciones::errorTelefono($body['telefono']);
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
    public function camposModificarCliente(Request $request, RequestHandler $handler): Response
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
          $errores['error dni'] = Validaciones::errorDni($body['dni']);
          if(Cliente::where('dni', $body['dni'])->first()){
            $response->getBody()->write(json_encode(array('error' => 'No puede ingresar un dni existente.')));
            $response = $response->withStatus(401);
            return $response->withHeader('Content-Type', 'application/json');
          }
        }
        if(isset($body['nombre'])){
          $errores['error nombre'] = Validaciones::errorNombre($body['nombre']);
        }
        if(isset($body['apellido'])){
          $errores['error apellido'] = Validaciones::errorApellido($body['apellido']);
        }
        if(isset($body['telefono'])){
          $errores['error telefono'] = Validaciones::errorTelefono($body['telefono']);
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
    public function camposCargarMesa(Request $request, RequestHandler $handler): Response
    {
      $response = new Response();
      try {
        $body = $request->getParsedBody();
        $response = $response->withStatus(401);
        $errores = [];
        $errores['error capacidad']  = Validaciones::errorCantidad($body['capacidad'], 2, 20);
        if(!$errores['error capacidad'] == 0){
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

    public function camposModificarMesa(Request $request, RequestHandler $handler): Response
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
          $errores['error capacidad'] = Validaciones::errorCantidad($body['capacidad'], 2, 20);
        }
        if(isset($body['estado'])){
          $errores['error estado'] = Validaciones::errorEstadoMesa($body['estado']);
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
    public function camposCargarProducto(Request $request, RequestHandler $handler): Response
    {
      $response = new Response();
      try {
        $body = $request->getParsedBody();
        $response = $response->withStatus(401);
        $errores = [];
        $errores['error nombre'] = Validaciones::errorNombre($body['nombre']);
        if(!Producto::where('nombre', $body['nombre'])->first()){
          $errores['error cantidad'] = Validaciones::errorCantidad($body['cantidad'], 1, 100000);
          $errores['error sector'] = Validaciones::errorSector($body['sector']);
          $errores['error precio'] = Validaciones::errorPrecio($body['precio'], 0.10, 20000);
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

    public function camposModificarProducto(Request $request, RequestHandler $handler): Response
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
          $errores['error nombre'] = Validaciones::errorNombre($body['nombre']);
          if(Producto::where('nombre', $body['nombre'])->first()){
            $response->getBody()->write(json_encode(array('error' => 'El producto ya existe (nombre existentes).')));
            $response = $response->withStatus(401);
            return $response->withHeader('Content-Type', 'application/json');
          }
        }
        if(isset($body['cantidad'])){
          $errores['error cantidad'] =  Validaciones::errorCantidad($body['cantidad'], -100000, 100000);
        }
        if(isset($body['sector'])){
          $errores['error sector'] = Validaciones::errorSector($body['sector']);
        }
        if(isset($body['precio'])){
          $errores['error precio'] = Validaciones::errorPrecio($body['precio'], 0.10, 20000);
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

    //----------------------------------------------FACTURA-----------------------------------------------------------------------------
    
    public function camposCargarFactura(Request $request, RequestHandler $handler): Response
    {
      $response = new Response();
      try {
        $body = $request->getParsedBody();
        $response = $response->withStatus(401);
        $errores = [];
        if(Cliente::where('dni', $body['cliente_dni'])->first() 
        && Mesa::find($body['mesa_id'])->estado == EstadoMesa::Cerrada->name){
            $response = $handler->handle($request);
        }
        else{
          $response->getBody()->write(json_encode(array('error' =>  'El cliente y la mesa deben existir, además, la mesa debe estar cerrada .')));
          $response = $response->withStatus(401);
        }
        return $response->withHeader('Content-Type', 'application/json');
      } catch (Throwable $e) {
        var_dump(array("Error" =>  "error, intente nuevamente más tarde. ($e)"));
      }
    }

    public function camposModificarFactura(Request $request, RequestHandler $handler): Response
    {
      $response = new Response();
      try {
        $body = $request->getParsedBody();
        $response = $response->withStatus(401);
        $errores = [];
        if(!(isset($body['cliente_dni']) || isset($body['mozo_id'])))
        {
            $response->getBody()->write(json_encode(array('error' => 'Debe modificar al menos un dato.')));
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
        if(isset($body['mozo_id'])){
          if(Usuario::find($body['mozo_id'])->rol == 'mozo'){
            $response->getBody()->write(json_encode(array('error' => 'No se encontro al mozo.')));
            $response = $response->withStatus(401);
            return $response->withHeader('Content-Type', 'application/json');
          }
        }
        $response = $handler->handle($request);
        return $response->withHeader('Content-Type', 'application/json');
      } catch (Throwable $e) {
        var_dump(array("Error" => "error, intente nuevamente más tarde. ($e)"));
      }
    }

     //----------------------------------------------PEDIDO-----------------------------------------------------------------------------
    
    public function camposCargarPedido(Request $request, RequestHandler $handler): Response
    {
      $response = new Response();
      try {
        $body = $request->getParsedBody();
        $response = $response->withStatus(401);
        $errores = [];
        if(isset($body['codigo_factura']) && isset($body['producto_id'])
        && isset($body['cantidad'])){
          $errores['error codigo_factura'] = Validaciones::errorLargoCadena($body['codigo_factura'], 5, 5);
          $factura = Factura::where('codigo_factura', $body['codigo_factura'])->first();
          $producto = Producto::find($body['producto_id']);
          $errores['error cantidad'] = Validaciones::errorCantidad($body['cantidad'], 1, 15);
          if(isset($body['detalle'])){
            $errores['error detalle'] = Validaciones::errorLargoCadena($body['detalle'], 0, 66);
          }
          if($factura && $producto){
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
            $response->getBody()->write(json_encode(array('error' =>  'Factura y/o producto no existen, corrobore los datos.')));
            $response = $response->withStatus(401);
          }
        }
        else{
          $response->getBody()->write(json_encode(array('error' =>  'Debe ingresar: codigo_factura, producto_id, sector, detalle y cantidad.')));
          $response = $response->withStatus(401);
        }
        return $response->withHeader('Content-Type', 'application/json');
      } catch (Throwable $e) {
        var_dump(array("Error" =>  "error, intente nuevamente más tarde. ($e)"));
      }
    }

    public function camposModificarPedido(Request $request, RequestHandler $handler): Response
    {
      $response = new Response();
      try {
        $body = $request->getParsedBody();
        $response = $response->withStatus(401);
        $errores = [];
        if(!(isset($body['preparador_id']) || isset($body['codigo_factura']) || isset($body['producto_id'])
        || isset($body['sector']) || isset($body['detalle']) || isset($body['cantidad'])
        || isset($body['estado']) || isset($body['demora_estimada']) || isset($body['entregado'])))
        {
            $response->getBody()->write(json_encode(array('error' => 'Debe modificar al menos un dato.')));
            $response = $response->withStatus(401);
            return $response->withHeader('Content-Type', 'application/json');
        }
        if(isset($body['preparador_id'])){
          if(!Usuario::find($body['preparador_id'])){
            $response->getBody()->write(json_encode(array('error' => 'No se encontro al preparador.')));
            $response = $response->withStatus(401);
            return $response->withHeader('Content-Type', 'application/json');
          }
        }
        if(isset($body['codigo_factura'])){
          if(!Factura::where('dni', $body['cliente_dni'])->first()){
            $response->getBody()->write(json_encode(array('error' => 'No existe cliente con ese DNI.')));
            $response = $response->withStatus(401);
            return $response->withHeader('Content-Type', 'application/json');
          }
        }
        if(isset($body['producto_id'])){
          if(!Producto::find($body['producto_id'])){
            $response->getBody()->write(json_encode(array('error' => 'No existe el producto.')));
            $response = $response->withStatus(401);
            return $response->withHeader('Content-Type', 'application/json');
          }
        }
        if(isset($body['sector'])){
          $errores['error sector'] = Validaciones::errorSector($body['sector']);
        }
        if(isset($body['detalle'])){
          $errores['error detalle'] = Validaciones::errorLargoCadena($body['detalle'], 0, 66);
        }
        if(isset($body['cantidad'])){
          $errores['error cantidad'] = Validaciones::errorCantidad($body['cantidad'], 1, 15);
        }
        if(isset($body['estado'])){
          $errores['error estado'] = Validaciones::errorEstadoPedido($body['estado']);
        }
        if(isset($body['demora_estimada'])){
          $errores['error demora_estimada'] = Validaciones::errorDemora($body['demora_estimada'], 10, 120);
        }
        if(isset($body['sector'])){
          $errores['error sector'] = Validaciones::errorSector($body['sector']);
        }
        if(isset($body['entregado'])){
          $errores['error entregado'] = Validaciones::errorEntregado($body['entregado']);
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

    public function camposCargarEncuesta(Request $request, RequestHandler $handler): Response
    {
      $response = new Response();
      try {
        $body = $request->getParsedBody();
        $response = $response->withStatus(401);
        $errores = [];
        if(isset($body['codigo_factura']) && isset($body['codigo_mesa']) && isset($body['mesa']) 
        && isset($body['restaurante']) && isset($body['mozo']) && isset($body['cocinero'])){
          // $errores['error codigo_mesa'] = Validaciones::errorLargoCadena($body['codigo_mesa'], 5, 5);
          // $errores['error codigo_factura'] = Validaciones::errorLargoCadena($body['codigo_factura'], 5, 5);
          //con el codigo de factura podemos obtener de todas formas el codigo de mesa correspondiente
          if(Mesa::where('codigo_mesa', $body['codigo_mesa'])->first() && Factura::where('codigo_factura', $body['codigo_factura'])->first()){
            $errores['error mesa'] = Validaciones::errorCantidad($body['mesa'], 1, 10);
            $errores['error restaurante'] = Validaciones::errorCantidad($body['restaurante'], 1, 10);
            $errores['error mozo'] = Validaciones::errorCantidad($body['mozo'], 1, 10);
            $errores['error cocinero'] = Validaciones::errorCantidad($body['cocinero'], 1, 10);
            if(isset($body['comentario'])){
              $errores['error comentario'] = Validaciones::errorLargoCadena($body['comentario'], 10, 66);
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
            $response->getBody()->write(json_encode(array('error' =>  'Error, debe existir la mesa y la factura')));
            $response = $response->withStatus(401);
          }
      }
      else{
        $response->getBody()->write(json_encode(array('error' =>  'Error, debe cargas los siguientes campos: codigo_factura, codigo_mesa, mesa, restaurante, mozo y cocinero')));
            $response = $response->withStatus(401);
      }
        return $response->withHeader('Content-Type', 'application/json');
      } catch (Throwable $e) {
        var_dump(array("Error" =>  "error, intente nuevamente más tarde. ($e)"));
      }
    }

    public function camposModificarEncuesta(Request $request, RequestHandler $handler): Response
    {
      $response = new Response();
      try {
        $body = $request->getParsedBody();
        $response = $response->withStatus(401);
        $errores = [];
        if(!(isset($body['codigo_factura']) || isset($body['codigo_mesa']) || isset($body['mesa']) || isset($body['restaurante']) 
        || isset($body['mozo']) || isset($body['cocinero']) || isset($body['comentario'])))
        {
            $response->getBody()->write(json_encode(array('error' => 'Debe modificar al menos un dato.')));
            $response = $response->withStatus(401);
            return $response->withHeader('Content-Type', 'application/json');
        }
        if(isset($body['codigo_factura'])){
          //$errores['error codigo_factura'] = Validaciones::errorLargoCadena($body['codigo_factura'], 5, 5);
          if(!Factura::where('codigo_factura', $body['codigo_factura'])->first()){
            $response->getBody()->write(json_encode(array('error' => 'Debe ingresar un código de factura existente.')));
            $response = $response->withStatus(401);
            return $response->withHeader('Content-Type', 'application/json');
          }
        }
        if(isset($body['codigo_mesa'])){
          //$errores['error dni'] = Validaciones::errorDni($body['dni']);
          if(!Mesa::where('codigo_mesa', $body['codigo_mesa'])->first()){
            $response->getBody()->write(json_encode(array('error' => 'Debe ingresar un código de mesa existente.')));
            $response = $response->withStatus(401);
            return $response->withHeader('Content-Type', 'application/json');
          }
        }
        if(isset($body['mesa'])){
          $errores['error mesa'] = Validaciones::errorCantidad($body['mesa'], 1, 10);
        }
        if(isset($body['restaurante'])){
          $errores['error restaurante'] = Validaciones::errorCantidad($body['restaurante'], 1, 10);
        }
        if(isset($body['mozo'])){
          $errores['error mozo'] = Validaciones::errorCantidad($body['mozo'], 1, 10);
        }
        if(isset($body['cocinero'])){
          $errores['error cocinero'] = Validaciones::errorCantidad($body['cocinero'], 1, 10);
        }
        if(isset($body['comentario'])){
          $errores['error comentario'] = Validaciones::errorLargoCadena($body['comentario'], 10, 66);
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

    public function camposCargarSuspension(Request $request, RequestHandler $handler): Response
    {
      $response = new Response();
      try {
        $body = $request->getParsedBody();
        $response = $response->withStatus(401);
        $errores = [];
        if(isset($body['usuario_id']) && isset($body['motivo'])){
          if(Usuario::find($body['usuario_id'])){
            $errores['error motivo'] = Validaciones::errorLargoCadena($body['motivo'], 3, 66);
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

    public function camposModificarSuspension(Request $request, RequestHandler $handler): Response
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
            $errores['error usuario_id'] = "El usuario no existe";
          }
        }
        if(isset($body['motivo'])){
          $errores['error motivo'] = Validaciones::errorLargoCadena($body['motivo'], 3, 66);
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