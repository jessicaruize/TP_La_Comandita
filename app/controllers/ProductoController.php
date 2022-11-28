<?php
require_once './models/Producto.php';
require_once './models/Sector.php';
require_once './interfaces/IApiUsable.php';
date_default_timezone_set('America/Buenos_Aires');
use \App\Models\Producto as Producto;

class ProductoController implements IApiUsable
{
  public function TraerUno($request, $response, $args)
  {
    try{
      $id = $args['id'];
      $producto = Producto::find($id);
      if ($producto !== null) {
        $payload = json_encode($producto);
      } else {
        $payload = json_encode(array("mensaje" => "Producto no encontrado"));
      }
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Throwable $e){
      var_dump(array("error" => "Error al traer producto ($e)"));
    }
  }

  public function TraerTodos($request, $response, $args)
  {
    try{
      $lista = Producto::all();
      $payload = json_encode(array("listaProductos" => $lista));
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Throwable $e){
      var_dump(array("error" => "Error al traer productos ($e)"));
    }
  }

  public function CargarUno($request, $response, $args)
  {
    try{
      $body = $request->getParsedBody();
      $producto = new Producto();
      $producto->nombre = ucfirst($body['nombre']);
      $producto->cantidad = $body['cantidad'];
      $producto->sector = Sector::obtenerValor($body['sector']);
      $producto->precio =  $body['precio'];
      $producto->save();

      $payload = json_encode(array("mensaje" => "Producto creado con exito"));

      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Throwable $e){
      var_dump(array("error" => "Error al crear producto ($e)"));
    }
  }

  public function ModificarUno($request, $response, $args)
  {
    try{
      $body = $request->getParsedBody();
      $id = $args['id'];
      $producto = Producto::find($id);
      if ($producto !== null) {
        if(isset($body['nombre'])){
          $producto->nombre = $body['nombre'];
        }
        if(isset($body['cantidad'])){
          $producto->cantidad += (int)$body['cantidad'];
        }
        if(isset($body['sector'])){
          $producto->sector = Sector::obtenerValor($body['sector']);
        }
        if(isset($body['precio'])){
          $producto->precio = (double)$body['precio'];
        }
        $producto->save();
        $payload = json_encode(array("mensaje" => "Producto modificado con exito"));
      } else {
        $payload = json_encode(array("mensaje" => "Producto no encontrado"));
      }
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Throwable $e){
      var_dump(array("error" => "Error al modificar producto ($e)"));
    }
  }

  public function BorrarUno($request, $response, $args)
  {
    try{
      $id = $args['id'];
      $producto = Producto::find($id);
      if ($producto !== null) {
        $producto->delete();
        $payload = json_encode(array("mensaje" => "Producto borrado con exito"));
      } else {
        $payload = json_encode(array("mensaje" => "Producto no encontrado"));
      }
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Throwable $e){
      var_dump(array("error" => "Error al borrar producto ($e)"));
    }
  }
}
