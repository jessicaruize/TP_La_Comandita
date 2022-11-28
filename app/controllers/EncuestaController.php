<?php
require_once './models/Encuesta.php';
require_once './models/RolUsuario.php';
require_once './models/Sector.php';
require_once './interfaces/IApiUsable.php';
date_default_timezone_set('America/Buenos_Aires');
use \App\Models\Encuesta as Encuesta;

class EncuestaController implements IApiUsable
{
  

  public function TraerUno($request, $response, $args)
  {
    try{
      $id = $args['id'];
      $encuesta = Encuesta::find($id);
      if ($encuesta !== null) {
        $payload = json_encode($encuesta);
      } else {
        $payload = json_encode(array("mensaje" => "Encuesta no encontrado"));
      }
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Throwable $e){
      var_dump(array("error" => "Error al traer encuesta ($e)"));
    }
  }

  public function TraerTodos($request, $response, $args)
  {
    try{
      $lista = Encuesta::all();
      $payload = json_encode(array("listaEncuestas" => $lista));
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Throwable $e){
      var_dump(array("error" => "Error al traer encuestas ($e)"));
    }
  }

  public function CargarUno($request, $response, $args)
  {
    try{
      $body = $request->getParsedBody();
      $encuesta = new Encuesta();
      $encuesta->codigo_factura =  $body['codigo_factura'];
      $encuesta->codigo_mesa = $body['codigo_mesa'];
      $encuesta->mesa = ucfirst($body['mesa']);
      $encuesta->restaurante = ucfirst($body['restaurante']);
      $encuesta->mozo = $body['mozo'];
      $encuesta->cocinero = $body['cocinero'];
      $encuesta->comentario = $body['comentario'];
      $encuesta->save();

      $payload = json_encode(array("mensaje" => "Encuesta creado con exito"));

      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Throwable $e){
      var_dump(array("error" => "Error al crear encuesta ($e)"));
    }
  }

  public function ModificarUno($request, $response, $args)
  {
    try{
      $body = $request->getParsedBody();
      $id = $args['id'];
      $encuesta = Encuesta::find($id);
      if ($encuesta !== null) {
        if(isset($body['codigo_factura'])){
          $encuesta->codigo_factura = $body['codigo_factura'];
        }
        if(isset($body['codigo_mesa'])){
          $encuesta->codigo_mesa = $body['codigo_mesa'];
        }
        if(isset($body['mesa'])){
          $encuesta->mesa = $body['mesa'];
        }
        if(isset($body['restaurante'])){
          $encuesta->restaurante = ucfirst($body['restaurante']);
        }
        if(isset($body['mozo'])){
          $encuesta->mozo = ucfirst($body['mozo']);
        }
        if(isset($body['cocinero'])){
          $encuesta->cocinero = $body['cocinero'];
        }
        if(isset($body['comentario'])){
          $encuesta->comentario = $body['comentario'];
        }
        $encuesta->save();
        $payload = json_encode(array("mensaje" => "Encuesta modificado con exito."));
      } else {
        $payload = json_encode(array("mensaje" => "Encuesta no encontrada."));
      }
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Throwable $e){
      var_dump(array("error" => "Error al modificar encuesta ($e)."));
    }
  }

  public function BorrarUno($request, $response, $args)
  {
    try{
      $id = $args['id'];
      $encuesta = Encuesta::find($id);
      if ($encuesta !== null) {
        $encuesta->delete();
        $payload = json_encode(array("mensaje" => "Encuesta borrado con exito."));
      } else {
        $payload = json_encode(array("mensaje" => "Encuesta no encontrada."));
      }
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Throwable $e){
      var_dump(array("error" => "Error al borrar encuesta ($e)."));
    }
  }
}
