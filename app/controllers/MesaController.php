<?php
require_once './models/Mesa.php';
require_once './models/EstadoMesa.php';
require_once './interfaces/IApiUsable.php';

use \App\Models\Mesa as Mesa;

class MesaController implements IApiUsable
{
  public function TraerUno($request, $response, $args)
  {
    try{
      $id = $args['id'];
      $mesa = Mesa::find($id);
      if ($mesa !== null) {
        $payload = json_encode($mesa);
      } else {
        $payload = json_encode(array("mensaje" => "Mesa no encontrado"));
      }
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Throwable $e){
      var_dump(array("error" => "Error al traer mesa ($e)"));
    }
  }

  public function TraerTodos($request, $response, $args)
  {
    try{
      $lista = Mesa::all();
      $payload = json_encode(array("listaMesas" => $lista));
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Throwable $e){
      var_dump(array("error" => "Error al traer mesas ($e)"));
    }
  }

  public function CargarUno($request, $response, $args)
  {
    try{
      $body = $request->getParsedBody();
      $mesa = new Mesa();
      $codigo = 0;
      while($codigo == 0 || Mesa::where('codigo_mesa', $codigo)->first())
      {
        $codigo = Mesa::obtener_codigo();
      }
      $mesa->codigo_mesa = $codigo;
      $mesa->estado = EstadoMesa::obtenerValor(3)->name; //cerrada
      $mesa->capacidad = $body['capacidad'];
      $mesa->save();
      $payload = json_encode(array("mensaje" => "Mesa creado con exito"));
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Throwable $e){
      var_dump(array("error" => "Error al crear mesa ($e)"));
    }
  }

  public function ModificarUno($request, $response, $args)
  {
    try{
      $body = $request->getParsedBody();
      $id = $args['id'];
      $mesa = Mesa::find($id);
      if ($mesa !== null) {
        if(isset($body['capacidad'])){
          $mesa->capacidad = $body['capacidad'];
        }
        if(isset($body['estado'])){
          $mesa->estado = EstadoMesa::obtenerValor($body['estado'])->name;
        }
        $mesa->save();
        $payload = json_encode(array("mensaje" => "Mesa modificado con exito"));
      } else {
        $payload = json_encode(array("mensaje" => "Mesa no encontrado"));
      }
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Throwable $e){
      var_dump(array("error" => "Error al modificar mesa ($e)"));
    }
  }

  public function BorrarUno($request, $response, $args)
  {
    try{
      $id = $args['id'];
      $mesa = Mesa::find($id);
      if ($mesa !== null) {
        $mesa->delete();
        $payload = json_encode(array("mensaje" => "Mesa borrado con exito"));
      } else {
        $payload = json_encode(array("mensaje" => "Mesa no encontrado"));
      }
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Throwable $e){
      var_dump(array("error" => "Error al borrar mesa ($e)"));
    }
  }
}
