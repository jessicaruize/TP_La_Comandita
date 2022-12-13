<?php
require_once './models/Validaciones.php';

use App\Models\Usuario;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class SuspensionesMiddlelware
{
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