<?php
require_once './models/Validaciones.php';

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class MesasMiddlelware
{
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
    
}