<?php
require_once './models/Suspension.php';
require_once './interfaces/IApiUsable.php';
date_default_timezone_set('America/Buenos_Aires');
use \App\Models\Suspension as Suspension;

class SuspensionController implements IApiUsable
{
  public function TraerUno($request, $response, $args)
  {
    try{
      $id = $args['id'];
      $suspension = Suspension::find($id);
      if ($suspension !== null) {
        $payload = json_encode($suspension);
      } else {
        $payload = json_encode(array("mensaje" => "Suspensión no encontrado"));
      }
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Throwable $e){
      var_dump(array("error" => "Error al traer suspensión ($e)"));
    }
  }

  public function TraerTodos($request, $response, $args)
  {
    try{
      $lista = Suspension::all();
      $payload = json_encode(array("listaSuspensions" => $lista));
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Throwable $e){
      var_dump(array("error" => "Error al traer suspensionss ($e)"));
    }
  }

  public function CargarUno($request, $response, $args)
  {
    try{
      $body = $request->getParsedBody();
      $suspension = new Suspension();
      $suspension->usuario_id =  $body['usuario_id'];
      $suspension->motivo = $body['motivo'];
      $suspension->save();
      $payload = json_encode(array("mensaje" => "Suspensión creado con éxito"));
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Throwable $e){
      var_dump(array("error" => "Error al crear suspensión ($e)"));
    }
  }

  public function ModificarUno($request, $response, $args)
  {
    try{
      $body = $request->getParsedBody();
      $id = $args['id'];
      $suspension = Suspension::find($id);
      if ($suspension !== null) {
        if(isset($body['usuario_id'])){
          $suspension->usuario_id = $body['usuario_id'];
        }
        if(isset($body['motivo'])){
          $suspension->motivo = $body['motivo'];
        }
        $suspension->save();
        $payload = json_encode(array("mensaje" => "Suspensión modificado con exito"));
      } else {
        $payload = json_encode(array("mensaje" => "Suspensión no encontrado"));
      }
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Throwable $e){
      var_dump(array("error" => "Error al modificar suspensión ($e)"));
    }
  }

  public function BorrarUno($request, $response, $args)
  {
    try{
      $id = $args['id'];
      $suspension = Suspension::find($id);
      if ($suspension !== null) {
        $suspension->delete();
        $payload = json_encode(array("mensaje" => "Suspension borrado con exito"));
      } else {
        $payload = json_encode(array("mensaje" => "Suspension no encontrado"));
      }
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Throwable $e){
      var_dump(array("error" => "Error al borrar suspension ($e)"));
    }
  }
}
