<?php
require_once './models/Pedido.php';
require_once './models/Factura.php';
require_once './models/Producto.php';
require_once './models/RolUsuario.php';
require_once './models/Sector.php';
require_once './models/EstadoPedido.php';
require_once './interfaces/IApiUsable.php';
date_default_timezone_set('America/Buenos_Aires');

use \App\Models\Pedido as Pedido;
use \App\Models\Factura as Factura;
use \App\Models\Producto as Producto;
use Illuminate\Database\DBAL\TimestampType;

class PedidoController implements IApiUsable
{
  public function TraerUno($request, $response, $args)
  {
    try{
      $id = $args['id'];
      $pedido = Pedido::find($id);
      if ($pedido !== null) {
        $payload = json_encode($pedido);
      } else {
        $payload = json_encode(array("mensaje" => "Pedido no encontrado"));
      }
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Throwable $e){
      var_dump(array("error" => "Error al traer pedido ($e)"));
    }
  }
  public function TraerPedientesTragosVinos($request, $response, $args)
  {
    try{
      $pedidos = Pedido::where('sector', 'BarraTragosVinos')->where('estado', 'Pendiente')->get();
      if ($pedidos !== null) {
        $payload = json_encode($pedidos);
      } else {
        $payload = json_encode(array("mensaje" => "No hay pedidos pendientes para barra de tragos y vinos"));
      }
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Throwable $e){
      var_dump(array("error" => "Error al traer pedido ($e)"));
    }
  }

  public function TraerTodos($request, $response, $args)
  {
    try{
      $lista = Pedido::all();
      $payload = json_encode(array("listaPedidos" => $lista));
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Throwable $e){
      var_dump(array("error" => "Error al traer pedidos ($e)"));
    }
  }

  public function CargarUno($request, $response, $args)
  {
    try{
      $body = $request->getParsedBody();
      $pedido = new Pedido();
      $pedido->factura_id =  Factura::where('codigo_factura', $body['codigo_factura'])->first()->id;
      $pedido->producto_id = $body['producto_id'];
      if(isset($body['detalle'])){
        $pedido->detalle = ucfirst($body['detalle']);
      }
      $pedido->cantidad = $body['cantidad'];
      $pedido->estado = EstadoPedido::obtenerValor(0);
      $pedido->save();
      $payload = json_encode(array("mensaje" => "Pedido creado con exito"));

      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Throwable $e){
      var_dump(array("error" => "Error al crear pedido ($e)"));
    }
  }

  public function ModificarUno($request, $response, $args)
  {
    try{
      $body = $request->getParsedBody();
      $id = $args['id'];
      $pedido = Pedido::find($id);
      if ($pedido !== null) {
        if(isset($body['preparador_id'])){
          $pedido->preparador_id = $body['preparador_id'];
        }
        if(isset($body['codigo_factura'])){
          $pedido->factura_id = Factura::where('codigo_factura', $body['codigo_factura'])->first()->id;
        }
        if(isset($body['producto_id'])){
          $pedido->producto_id = $body['producto_id'];
        }
        if(isset($body['sector'])){
          $pedido->sector = Sector::obtenerValor($body['sector']);
        }
        if(isset($body['cantidad'])){
          $pedido->cantidad = ucfirst($body['cantidad']);
        }
        if(isset($body['estado'])){
          $pedido->estado = EstadoPedido::obtenerValor($body['estado']);
        }
        if(isset($body['demora_estimada'])){
          $pedido->demora_estimada = $body['demora_estimada'];
        }
        if(isset($body['entregado'])){
          $pedido->entregado = new DateTime(date("Y-m-d H:i:s"));
        }
        $pedido->save();
        $payload = json_encode(array("mensaje" => "Pedido modificado con exito"));
      } else {
        $payload = json_encode(array("mensaje" => "Pedido no encontrado"));
      }
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Throwable $e){
      var_dump(array("error" => "Error al modificar pedido ($e)"));
    }
  }

  public function BorrarUno($request, $response, $args)
  {
    try{
      $id = $args['id'];
      $pedido = Pedido::find($id);
      if ($pedido !== null) {
        $pedido->delete();
        $payload = json_encode(array("mensaje" => "Pedido borrado con exito"));
      } else {
        $payload = json_encode(array("mensaje" => "Pedido no encontrado"));
      }
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Throwable $e){
      var_dump(array("error" => "Error al borrar pedido ($e)"));
    }
  }
}
