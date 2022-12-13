<?php
require_once './models/Validaciones.php';

use App\Models\Cliente;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class ClientesMiddlelware
{

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
}