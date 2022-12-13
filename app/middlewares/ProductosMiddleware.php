<?php
require_once './models/Validaciones.php';

use App\Models\Producto;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class ProductosMiddlelware
{
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
          $response->getBody()->write(json_encode(array('error' => 'El producto ya existe (nombre existente).')));
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
}