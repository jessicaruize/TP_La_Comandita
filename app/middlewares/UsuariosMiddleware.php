<?php
require_once './models/Validaciones.php';

use App\Models\Usuario;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class UsuariosMiddlelware
{

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
}