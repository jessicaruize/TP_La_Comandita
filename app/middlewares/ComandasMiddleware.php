<?php
require_once './models/Validaciones.php';

use App\Models\Usuario;
use App\Models\Cliente;
use App\Models\Mesa;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class ComandasMiddlelware
{
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
}