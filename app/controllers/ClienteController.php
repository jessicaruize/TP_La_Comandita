<?php
require_once './models/Cliente.php';
require_once './interfaces/IApiUsable.php';
date_default_timezone_set('America/Buenos_Aires');
use \App\Models\Cliente as Cliente;

class ClienteController implements IApiUsable
{

  public function TraerUno($request, $response, $args)
  {
    try{
      $id = $args['id'];
      $cliente = Cliente::find($id);
      if ($cliente !== null) {
        $payload = json_encode($cliente);
      }
      else {
        $payload = json_encode(array("mensaje" => "Cliente no encontrado"));
      }
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Throwable $e){
      var_dump(array("error" => "Error al traer cliente ($e)"));
    }
  }

  public function TraerTodos($request, $response, $args)
  {
    try{
      $lista = Cliente::all();
      $payload = json_encode(array("listaClientes" => $lista));
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Throwable $e){
      var_dump(array("error" => "Error al traer clientes ($e)"));
    }
  }

  public function CargarUno($request, $response, $args)
  {
    try{
      $body = $request->getParsedBody();
      $cliente = new Cliente();
      $cliente->nombre = ucfirst($body['nombre']);
      $cliente->apellido = ucfirst($body['apellido']);
      $cliente->dni = $body['dni'];
      $cliente->telefono = $body['telefono'];
      $cliente->save();
      $payload = json_encode(array("mensaje" => "Cliente creado con exito"));
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Throwable $e){
      var_dump(array("error" => "Error al crear cliente ($e)"));
    }
  }

  public function ModificarUno($request, $response, $args)
  {
    try{
      $body = $request->getParsedBody();
      $id = $args['id'];
      $cliente = Cliente::find($id);
      if ($cliente !== null) {
        if(isset($body['dni'])){
          $cliente->dni = $body['dni'];
        }
        if(isset($body['nombre'])){
          $cliente->nombre = ucfirst($body['nombre']);
        }
        if(isset($body['apellido'])){
          $cliente->apellido = ucfirst($body['apellido']);
        }
        if(isset($body['telefono'])){
          $cliente->telefono = $body['telefono'];
        }
        $cliente->save();
        $payload = json_encode(array("mensaje" => "Cliente modificado con exito"));
      } else {
        $payload = json_encode(array("mensaje" => "Cliente no encontrado"));
      }
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Throwable $e){
      var_dump(array("error" => "Error al modificar cliente ($e)"));
    }
  }

  public function BorrarUno($request, $response, $args)
  {
    try{
      $id = $args['id'];
      $cliente = Cliente::find($id);
      if ($cliente !== null) {
      $cliente->delete();
      $payload = json_encode(array("mensaje" => "Cliente borrado con exito"));
    } else {
      $payload = json_encode(array("mensaje" => "Cliente no encontrado"));
    }
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Throwable $e){
      var_dump(array("error" => "Error al borrar cliente ($e)"));
    }
  }
}
