<?php
require_once './models/Validaciones.php';

use App\Models\Usuario;
use App\Models\Producto;
use App\Models\Comanda;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class PedidosMiddlelware
{
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
            }
          }
          else{
            $response->getBody()->write(json_encode(array('error' =>  'Comanda y/o producto no existen, corrobore los datos.')));
          }
        }
        else{
          $response->getBody()->write(json_encode(array('error' =>  'Debe ingresar: codigo_comanda, producto_id y cantidad.')));
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
        }
        return $response->withHeader('Content-Type', 'application/json');
      } catch (Throwable $e) {
        var_dump(array("Error" => "error, intente nuevamente más tarde. ($e)"));
      }
    }

    public function prepararPedido(Request $request, RequestHandler $handler): Response
    {
      $response = new Response();
      try {
        $body = $request->getParsedBody();
        $response = $response->withStatus(401);
        $error = Validaciones::errorCantidad($body['demora_estimada'], 5, 100);
        if(empty($error)){
          $response = $handler->handle($request);
        }
        else{
          $response->getBody()->write(json_encode("La demora estimada debe estar expresada en minutos. " . $error));
        }
        return $response->withHeader('Content-Type', 'application/json');
      } catch (Throwable $e) {
        var_dump(array("Error" => "error, intente nuevamente más tarde. ($e)"));
      }
    }
   
    public function VerTiempoRestantePedido(Request $request, RequestHandler $handler): Response
    {
      $response = new Response();
      try {
        $body = $request->getParsedBody();
        $response = $response->withStatus(401);
        $errores = [];
        if(isset($body['codigo_comanda']) && isset($body['codigo_mesa'])){
          $errores['error_codigo_comanda'] = Validaciones::errorLargoCadena($body['codigo_comanda'], 5, 5);
          $errores['error_codigo_mesa'] = Validaciones::errorLargoCadena($body['codigo_mesa'], 5, 5);
          $mensajesErr = array_filter($errores, fn($e) => $e != 0, ARRAY_FILTER_USE_BOTH);
          if(count($mensajesErr) == 0){
            $response = $handler->handle($request);
          }
          else{
            $response->getBody()->write(json_encode(array("error" => $mensajesErr)));
          }
        }
        else{
          $response->getBody()->write(json_encode("Debe ingresar el codigo_comanda y codigo_mesa entregado por el/la  mozo/a"));
        }
        return $response->withHeader('Content-Type', 'application/json');
      } catch (Throwable $e) {
        var_dump(array("Error" => "error, intente nuevamente más tarde. ($e)"));
      }
    }
}