<?php
require_once './models/Pedido.php';
require_once './models/Comanda.php';
require_once './models/Producto.php';
require_once './models/RolUsuario.php';
require_once './models/Sector.php';
require_once './models/Operacion.php';
require_once './models/EstadoPedido.php';
require_once './interfaces/IApiUsable.php';
require_once './models/Mesa.php';
date_default_timezone_set('America/Buenos_Aires');
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Capsule\Manager as Capsule;

use \App\Models\Mesa as Mesa;
use \App\Models\Pedido as Pedido;
use \App\Models\Comanda as Comanda;
use \App\Models\Producto as Producto;
use \App\Models\Usuario as Usuario;
use App\Models\Operacion as Operacion;
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
  
  public function TraerTragosVinosPendientes($request, $response, $args)
  {
    try{
      $resultado = PedidoController::TraerPorSectorYEstado(Sector::BarraTragosVinos->name, EstadoPedido::Pendiente->name, "No hay pedidos pendientes para Barra Tragos y Vinos");
      $response->getBody()->write(json_encode($resultado));
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Throwable $e){
      var_dump(array("error" => "Error al traer pedido ($e)"));
    }
  }
  public function TraerBarraChoperasPendientes($request, $response, $args)
  {
    try{
      $resultado = PedidoController::TraerPorSectorYEstado(Sector::BarraChoperas->name, EstadoPedido::Pendiente->name, "No hay pedidos pendientes para Barra de Choperas");
      $response->getBody()->write(json_encode($resultado));
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Throwable $e){
      var_dump(array("error" => "Error al traer pedido ($e)"));
    }
  }
  public function TraerCocinaPendientes($request, $response, $args)
  {
    try{
      $resultado = PedidoController::TraerPorSectorYEstado(Sector::Cocina->name, EstadoPedido::Pendiente->name, "No hay pedidos pendientes para Cocina");
      $response->getBody()->write(json_encode($resultado));
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Throwable $e){
      var_dump(array("error" => "Error al traer pedido ($e)"));
    }
  }


  public function TraerCandyBarPendientes($request, $response, $args)
  {
    try{
      $resultado = PedidoController::TraerPorSectorYEstado(Sector::CandyBar->name, EstadoPedido::Pendiente->name, "No hay pedidos pendientes para Candy Bar");
      $response->getBody()->write(json_encode($resultado));
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Throwable $e){
      var_dump(array("error" => "Error al traer pedido ($e)"));
    }
  }

  public function TraerTragosVinosPreparacion($request, $response, $args)
  {
    try{
      $resultado = PedidoController::TraerPorSectorYEstado(Sector::BarraTragosVinos->name, EstadoPedido::Preparacion->name, "No hay pedidos en preparacion para Barra Tragos y Vinos");
      $response->getBody()->write(json_encode($resultado));
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Throwable $e){
      var_dump(array("error" => "Error al traer pedido ($e)"));
    }
  }

  public function TraerBarraChoperasPreparacion($request, $response, $args)
  {
    try{
      $resultado = PedidoController::TraerPorSectorYEstado(Sector::BarraChoperas->name, EstadoPedido::Preparacion->name, "No hay pedidos en preparacion para Barra Choperas");
      $response->getBody()->write(json_encode($resultado));
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Throwable $e){
      var_dump(array("error" => "Error al traer pedido ($e)"));
    }
  }
  public function TraerCocinaPreparacion($request, $response, $args)
  {
    try{
      $resultado = PedidoController::TraerPorSectorYEstado(Sector::Cocina->name, EstadoPedido::Preparacion->name, "No hay pedidos en preparacion para Cocina");
      $response->getBody()->write(json_encode($resultado));
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Throwable $e){
      var_dump(array("error" => "Error al traer pedido ($e)"));
    }
  }
  public function TraerCandyBarPreparacion($request, $response, $args)
  {
    try{
      $resultado = PedidoController::TraerPorSectorYEstado(Sector::CandyBar->name, EstadoPedido::Preparacion->name, "No hay pedidos en preparacion para CandyBar");
      $response->getBody()->write(json_encode($resultado));
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Throwable $e){
      var_dump(array("error" => "Error al traer pedido ($e)"));
    }
  }

  private static function TraerPorSectorYEstado($sector, $estado, $mensajeErr)
  {
      $pedidos = Capsule::table('pedidos')
      ->select('pedidos.*')
      ->join('productos', 'productos.id', '=', 'pedidos.producto_id')
      ->where('pedidos.estado', '=', $estado)
      ->where('productos.sector', '=', $sector)
      ->get();
      if ($pedidos !== null) {
        return $pedidos;
      } 
      return array("mensaje" => $mensajeErr);
  }

  public function VerTiempoRestantePedido($request, $response, $args)
  {
    try{
      $body = $request->getParsedBody();
      $pedido = Capsule::table('pedidos')
      ->select('pedidos.*')
      ->join('comandas', 'comandas.id', '=', 'pedidos.comanda_id')
      ->where('comandas.codigo_comanda', '=', $body['codigo_comanda'])
      ->orderBy('demora_estimada', 'DESC')
      ->get()->first();
      if ($pedido !== null) {
        $ahora = date("H:i:s");
        $pedidoTomado = explode(' ', $pedido->tomado);

        $tiempoRestante =  strtotime($pedidoTomado[1]) - strtotime($ahora) + ($pedido->demora_estimada * 60);
        $payload = json_encode(array("Tiempo_Restante(minutos)" => $tiempoRestante/60));
      } 
      else{
        $response->getBody()->write(json_encode(array("error" => "No se encontro el pedido solicitado")));
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
      $pedido->comanda_id =  Comanda::where('codigo_comanda', $body['codigo_comanda'])->first()->id;
      $pedido->producto_id = $body['producto_id'];
      if(isset($body['detalle'])){
        $pedido->detalle = ucfirst($body['detalle']);
      }
      $pedido->cantidad = $body['cantidad'];
      $pedido->estado = EstadoPedido::Pendiente->name;
      $pedido->save();
      $payload = json_encode(array("mensaje" => "Pedido creado con exito", "Id_pedido" => $pedido->id));

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
        if(isset($body['preparador_dni'])){
          $pedido->preparador_id = Usuario::where('dni', $body['preparador_dni'])->first()->id;
        }
        if(isset($body['codigo_comanda'])){
          $pedido->comanda_id = Comanda::where('codigo_comanda', $body['codigo_comanda'])->first()->id;
        }
        if(isset($body['producto_id'])){
          $pedido->producto_id = $body['producto_id'];
        }
        if(isset($body['detalle'])){
          $pedido->detalle = Sector::obtenerValor($body['detalle']);
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
        // if(isset($body['entregado'])){
        //   if(strtolower($body['entregado']) == "si"){
        //     $pedido->entregado = new DateTime(date("Y-m-d H:i:s"));
        //   }
        //   else{
        //     $pedido->entregado = null;
        //   }
        // }
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

//preparar
  public function PrepararTragosVinos($request, $response, $args)
  {
    try{
      $body = $request->getParsedBody();
      $header = $request->getHeaderLine('Authorization');
      $token = trim(explode("Bearer", $header)[1]);
      $dataToken = AutentificadorJWT::ObtenerPayLoad($token);
      $idPedido = $args['id'];
      $resultado = PedidoController::prepararPedido(Sector::BarraTragosVinos->name, $idPedido, $dataToken->idUsuario, $body['demora_estimada']);
      $response->getBody()->write(json_encode($resultado));
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Throwable $e){
      var_dump(array("error" => "Error al modificar pedido ($e)"));
    }
  }

  public function PrepararBarraChoperas($request, $response, $args)
  {
    try{
      $body = $request->getParsedBody();
      $header = $request->getHeaderLine('Authorization');
      $token = trim(explode("Bearer", $header)[1]);
      $dataToken = AutentificadorJWT::ObtenerPayLoad($token);
      $idPedido = $args['id'];
      $resultado = PedidoController::prepararPedido(Sector::BarraChoperas->name, $idPedido, $dataToken->idUsuario, $body['demora_estimada']);
      $response->getBody()->write(json_encode($resultado));
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Throwable $e){
      var_dump(array("error" => "Error al modificar pedido ($e)"));
    }
  }
  public function PrepararCocina($request, $response, $args)
  {
    try{
      $body = $request->getParsedBody();
      $header = $request->getHeaderLine('Authorization');
      $token = trim(explode("Bearer", $header)[1]);
      $dataToken = AutentificadorJWT::ObtenerPayLoad($token);
      $idPedido = $args['id'];
      $resultado = PedidoController::prepararPedido(Sector::Cocina->name, $idPedido, $dataToken->idUsuario, $body['demora_estimada']);
      $response->getBody()->write(json_encode($resultado));
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Throwable $e){
      var_dump(array("error" => "Error al modificar pedido ($e)"));
    }
  }
  public function PrepararCandyBar($request, $response, $args)
  {
    try{
      $body = $request->getParsedBody();
      $header = $request->getHeaderLine('Authorization');
      $token = trim(explode("Bearer", $header)[1]);
      $dataToken = AutentificadorJWT::ObtenerPayLoad($token);
      $idPedido = $args['id'];
      $resultado = PedidoController::prepararPedido(Sector::CandyBar->name, $idPedido, $dataToken->idUsuario, $body['demora_estimada']);
      $response->getBody()->write(json_encode($resultado));
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Throwable $e){
      var_dump(array("error" => "Error al modificar pedido ($e)"));
    }
  }

  private static function prepararPedido($sector, $idPedido, $idUsuario, $demoraEstimada){
    $pedido = Capsule::table('pedidos')
    ->select('pedidos.*')
    ->join('productos', 'productos.id', '=', 'pedidos.producto_id')
    ->where('pedidos.id', '=', $idPedido)
    ->where('productos.sector', '=', $sector)
    ->where('pedidos.estado', '=', EstadoPedido::Pendiente->name)
    ->get()->first();
    
    
    if ($pedido !== null) {
      $pedido = Pedido::find($idPedido);
      $pedido->preparador_id = $idUsuario;
      $pedido->demora_estimada = $demoraEstimada;
      $pedido->tomado = new DateTime(date("H:i:s"));
      $pedido->estado = EstadoPedido::Preparacion->name;
      $pedido->save();
      return array("mensaje" => "Pedido($idPedido) tomado con exito");
    } 
    return array("mensaje" => "Verifique que el pedido exista, corresponda a su sector y que además este en estado pendiente.");
  }

  //terminar
  public function TerminarTragosVinos($request, $response, $args)
  {
    try{
      $header = $request->getHeaderLine('Authorization');
      $token = trim(explode("Bearer", $header)[1]);
      $dataToken = AutentificadorJWT::ObtenerPayLoad($token);
      $idPedido = $args['id'];
      $resultado = PedidoController::terminarPedido(Sector::BarraTragosVinos->name, $idPedido, $dataToken->idUsuario);
      $response->getBody()->write(json_encode($resultado));
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Throwable $e){
      var_dump(array("error" => "Error al modificar pedido ($e)"));
    }
  }

  public function TerminarBarraChoperas($request, $response, $args)
  {
    try{
      $header = $request->getHeaderLine('Authorization');
      $token = trim(explode("Bearer", $header)[1]);
      $dataToken = AutentificadorJWT::ObtenerPayLoad($token);
      $idPedido = $args['id'];
      $resultado = PedidoController::terminarPedido(Sector::BarraChoperas->name, $idPedido, $dataToken->idUsuario);
      $response->getBody()->write(json_encode($resultado));
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Throwable $e){
      var_dump(array("error" => "Error al modificar pedido ($e)"));
    }
  }
  public function TerminarCocina($request, $response, $args)
  {
    try{
      $header = $request->getHeaderLine('Authorization');
      $token = trim(explode("Bearer", $header)[1]);
      $dataToken = AutentificadorJWT::ObtenerPayLoad($token);
      $idPedido = $args['id'];
      $resultado = PedidoController::terminarPedido(Sector::Cocina->name, $idPedido, $dataToken->idUsuario);
      $response->getBody()->write(json_encode($resultado));
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Throwable $e){
      var_dump(array("error" => "Error al modificar pedido ($e)"));
    }
  }
  public function TerminarCandyBar($request, $response, $args)
  {
    try{
      $header = $request->getHeaderLine('Authorization');
      $token = trim(explode("Bearer", $header)[1]);
      $dataToken = AutentificadorJWT::ObtenerPayLoad($token);
      $idPedido = $args['id'];
      $resultado = PedidoController::terminarPedido(Sector::CandyBar->name, $idPedido, $dataToken->idUsuario);
      $response->getBody()->write(json_encode($resultado));
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Throwable $e){
      var_dump(array("error" => "Error al modificar pedido ($e)"));
    }
  }

  private static function terminarPedido($sector, $idPedido, $idUsuario){
    $pedido = Capsule::table('pedidos')
    ->select('pedidos.*')
    ->join('productos', 'productos.id', '=', 'pedidos.producto_id')
    ->where('pedidos.id', '=', $idPedido)
    ->where('productos.sector', '=', $sector)
    ->where('pedidos.estado', '=', EstadoPedido::Preparacion->name)
    ->get()->first();
    
    
    if ($pedido !== null) {
      $pedido = Pedido::find($idPedido);
      $pedido->estado = EstadoPedido::Listo->name;
      $pedido->save();
      $operacion = new Operacion();
      $operacion->usuario_id = $idUsuario;
      $operacion->comanda_id = $pedido->comanda_id;
      $operacion->save();
      return array("mensaje" => "Pedido($idPedido) listo con exito");
    } 
    return array("mensaje" => "Verifique que el pedido exista, corresponda a su sector y que además este en estado de preparación.");
  }

  public function ServirPedido($request, $response, $args){
    try{
      $idPedido = $args['id'];
      $pedido = Capsule::table('pedidos')
      ->select('pedidos.*')
      ->where('pedidos.id', '=', $idPedido)
      ->where('pedidos.estado', '=', EstadoPedido::Listo->name)
      ->get()->first();
      
      if ($pedido !== null) {
        $pedido = Pedido::find($idPedido);
        $pedido->entregado = new DateTime(date("H:i:s"));
        $pedido->estado = EstadoPedido::Entregado->name;
        $pedido->save();
        $comanda = Comanda::find($pedido->comanda_id);
        $mesa = Mesa::find($comanda->mesa_id);
        $mesa->estado = EstadoMesa::Comiendo->name;
        $mesa->save();

        $payload =  array("mensaje" => "Pedido($idPedido) entregado con exito");
      }
      else{
        $payload = array("mensaje" => "Verifique que el pedido exista y que además este en estado de Listo.");
      }
      $response->getBody()->write(json_encode($payload));
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
