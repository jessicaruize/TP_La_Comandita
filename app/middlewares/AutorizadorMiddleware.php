<?php
require_once './middlewares/AutentificadorJWT.php';
require_once './models/RolUsuario.php';

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class AutorizacionMiddelware
{

  public function verificarBartender(Request $request, RequestHandler $handler): Response
  {
    $response = new Response();
    try {
      $header = $request->getHeaderLine('Authorization');
      if(is_null($header) || empty($header))
      {
        $response->getBody()->write(json_encode(array("mensaje" => 'Error, el token esta vacio.')));
        $response = $response->withStatus(401);
      }
      else
      {
          $token = trim(explode("Bearer", $header)[1]);
          if(is_null($token) || empty($token)){
            $response->getBody()->write(json_encode(array("mensaje" => 'Error, el token esta vacio.')));
            $response = $response->withStatus(401);
          }
          else{
            $payload = AutentificadorJWT::ObtenerPayLoad($token);
            if(isset($payload->rol))
            {
              if($payload->rol == RolUsuario::obtenerValor(0))
              {
                $response = $handler->handle($request);
              }
              else{
                $response->getBody()->write(json_encode(array("mensaje" => 'No esta autorizado.')));
                $response = $response->withStatus(400);
              }
            }
            else{
              $response->getBody()->write(json_encode(array("mensaje" => 'Error en los datos.')));
              $response = $response->withStatus(400);
            }
          }
      }
      
    } catch (Throwable $e) {
      echo "error $e";
    }
    finally{
      return $response->withHeader('Content-Type', 'application/json');
    }
  }
  public function verificarCervecero(Request $request, RequestHandler $handler): Response
  {
    $response = new Response();
    try {
      $header = $request->getHeaderLine('Authorization');
      if(is_null($header) || empty($header))
      {
        $response->getBody()->write(json_encode(array("mensaje" => 'Error, el token esta vacio.')));
        $response = $response->withStatus(401);
      }
      else
      {
          $token = trim(explode("Bearer", $header)[1]);
          if(is_null($token) || empty($token)){
            $response->getBody()->write(json_encode(array("mensaje" => 'Error, el token esta vacio.')));
            $response = $response->withStatus(401);
          }
          else{
            $payload = AutentificadorJWT::ObtenerPayLoad($token);
            if(isset($payload->rol))
            {
              if($payload->rol == RolUsuario::obtenerValor(1))
              {
                $response = $handler->handle($request);
              }
              else{
                $response->getBody()->write(json_encode(array("mensaje" => 'No esta autorizado.')));
                $response = $response->withStatus(400);
              }
            }
            else{
              $response->getBody()->write(json_encode(array("mensaje" => 'Error en los datos.')));
              $response = $response->withStatus(400);
            }
          }
      }
      
    } catch (Throwable $e) {
      echo "error $e";
    }
    finally{
      return $response->withHeader('Content-Type', 'application/json');
    }
  }
  public function verificarCocinero(Request $request, RequestHandler $handler): Response
  {
    $response = new Response();
    try {
      $header = $request->getHeaderLine('Authorization');
      if(is_null($header) || empty($header))
      {
        $response->getBody()->write(json_encode(array("mensaje" => 'Error, el token esta vacio.')));
        $response = $response->withStatus(401);
      }
      else
      {
          $token = trim(explode("Bearer", $header)[1]);
          if(is_null($token) || empty($token)){
            $response->getBody()->write(json_encode(array("mensaje" => 'Error, el token esta vacio.')));
            $response = $response->withStatus(401);
          }
          else{
            $payload = AutentificadorJWT::ObtenerPayLoad($token);
            if(isset($payload->rol))
            { 
              if($payload->rol == RolUsuario::obtenerValor(2) && $payload->sector == Sector::obtenerValor(2))
              {
                $response = $handler->handle($request);
              }
              else{
                $response->getBody()->write(json_encode(array("mensaje" => 'No esta autorizado.')));
                $response = $response->withStatus(400);
              }
            }
            else{
              $response->getBody()->write(json_encode(array("mensaje" => 'Error en los datos.')));
              $response = $response->withStatus(400);
            }
          }
      }
      
    } catch (Throwable $e) {
      echo "error $e";
    }
    finally{
      return $response->withHeader('Content-Type', 'application/json');
    }
  }
  public function verificarPastelero(Request $request, RequestHandler $handler): Response
  {
    $response = new Response();
    try {
      $header = $request->getHeaderLine('Authorization');
      if(is_null($header) || empty($header))
      {
        $response->getBody()->write(json_encode(array("mensaje" => 'Error, el token esta vacio.')));
        $response = $response->withStatus(401);
      }
      else
      {
          $token = trim(explode("Bearer", $header)[1]);
          if(is_null($token) || empty($token)){
            $response->getBody()->write(json_encode(array("mensaje" => 'Error, el token esta vacio.')));
            $response = $response->withStatus(401);
          }
          else{
            $payload = AutentificadorJWT::ObtenerPayLoad($token);
            if(isset($payload->rol))
            {
              if($payload->rol == RolUsuario::obtenerValor(2) && $payload->sector == Sector::obtenerValor(3))
              {
                $response = $handler->handle($request);
              }
              else{
                $response->getBody()->write(json_encode(array("mensaje" => 'No esta autorizado.')));
                $response = $response->withStatus(400);
              }
            }
            else{
              $response->getBody()->write(json_encode(array("mensaje" => 'Error en los datos.')));
              $response = $response->withStatus(400);
            }
          }
      }
      
    } catch (Throwable $e) {
      echo "error $e";
    }
    finally{
      return $response->withHeader('Content-Type', 'application/json');
    }
  }

  public function verificarMozo(Request $request, RequestHandler $handler): Response
  {
    $response = new Response();
    try {
      $header = $request->getHeaderLine('Authorization');
      if(is_null($header) || empty($header))
      {
        $response->getBody()->write(json_encode(array("mensaje" => 'Error, el token esta vacio.')));
        $response = $response->withStatus(401);
      }
      else
      {
          $token = trim(explode("Bearer", $header)[1]);
          if(is_null($token) || empty($token)){
            $response->getBody()->write(json_encode(array("mensaje" => 'Error, el token esta vacio.')));
            $response = $response->withStatus(401);
          }
          else{
            $payload = AutentificadorJWT::ObtenerPayLoad($token);
            if(isset($payload->rol))
            {
              if($payload->rol == RolUsuario::obtenerValor(3))
              {
                $response = $handler->handle($request);
              }
              else{
                $response->getBody()->write(json_encode(array("mensaje" => 'No esta autorizado.')));
                $response = $response->withStatus(400);
              }
            }
            else{
              $response->getBody()->write(json_encode(array("mensaje" => 'Error en los datos.')));
              $response = $response->withStatus(400);
            }
          }
      }
      
    } catch (Throwable $e) {
      echo "error $e";
    }
    finally{
      return $response->withHeader('Content-Type', 'application/json');
    }
  }
    
  public function verificarSocio(Request $request, RequestHandler $handler): Response
  {
    $response = new Response();
    try {
      $header = $request->getHeaderLine('Authorization');
      if(is_null($header) || empty($header))
      {
        $response->getBody()->write(json_encode(array("mensaje" => 'Error, el token esta vacio.')));
        $response = $response->withStatus(401);
      }
      else
      {
          $token = trim(explode("Bearer", $header)[1]);
          if(is_null($token) || empty($token)){
            $response->getBody()->write(json_encode(array("mensaje" => 'Error, el token esta vacio.')));
            $response = $response->withStatus(401);
          }
          else{
            $payload = AutentificadorJWT::ObtenerPayLoad($token);
            if(isset($payload->rol))
            {
              if($payload->rol == RolUsuario::obtenerValor(4))
              {
                $response = $handler->handle($request);
              }
              else{
                $response->getBody()->write(json_encode(array("mensaje" => 'No esta autorizado.')));
                $response = $response->withStatus(400);
              }
            }
            else{
              $response->getBody()->write(json_encode(array("mensaje" => 'Error en los datos.')));
              $response = $response->withStatus(400);
            }
          }
      }
      
    } catch (Throwable $e) {
      echo "error $e";
    }
    finally{
      return $response->withHeader('Content-Type', 'application/json');
    }
  }


    
}