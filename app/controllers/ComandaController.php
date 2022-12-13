<?php
require_once './models/Comanda.php';
require_once './models/Cliente.php';
require_once './models/Usuario.php';
require_once './models/Mesa.php';
require_once './models/Operacion.php';
require_once './interfaces/IApiUsable.php';
date_default_timezone_set('America/Buenos_Aires');
use \App\Models\Comanda as Comanda;
use \App\Models\Cliente as Cliente;
use \App\Models\Usuario as Usuario;
use \App\Models\Mesa as Mesa;
use App\Models\Operacion as Operacion;

use \App\Models\Pedido as Pedido;
use Illuminate\Database\Capsule\Manager as Capsule;

class ComandaController implements IApiUsable
{
  public function TraerUno($request, $response, $args)
  {
    try{
      $codigoComanda = $args['codigo_comanda'];
      // $comanda = Comanda::find($id);
      $comanda = Comanda::where('codigo_comanda', $codigoComanda)->first();
      if ($comanda !== null) {
        $payload = json_encode($comanda);
      } else {
        $payload = json_encode(array("mensaje" => "Comanda no encontrado"));
      }
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Throwable $e){
      var_dump(array("error" => "Error al traer comanda ($e)"));
    }
  }

  public function TraerTodos($request, $response, $args)
  {
    try{
      $lista = Comanda::all();
      $payload = json_encode(array("listaComandas" => $lista));
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Throwable $e){
      var_dump(array("error" => "Error al traer comandas ($e)"));
    }
  }

  public function CargarUno($request, $response, $args)
  {
    try{
      $body = $request->getParsedBody();
      $header = $request->getHeaderLine('Authorization');
      $token = trim(explode("Bearer", $header)[1]);
      $dataToken = AutentificadorJWT::ObtenerPayLoad($token);
      $archivos = $request->getUploadedFiles();
      $destino = "";
      $cliente = Cliente::where('dni', $body['cliente_dni'])->first();
      $usuario = Usuario::find($dataToken->idUsuario);
      $mesa =  Mesa::where('codigo_mesa', $body['codigo_mesa'])->first();
      $codigoComanda = 0;
      while($codigoComanda == 0 || Comanda::where('codigo_comanda', $codigoComanda)->first())
      {
        $codigoComanda = Comanda::obtener_codigo();
      }
      if(count($archivos) > 0){
        if ($archivos['foto']->getError() == UPLOAD_ERR_OK) {
          $destino="./fotosComandas/";
          if (!file_exists($destino)) {
            mkdir($destino, 0777, true);
          }
          $foto = $archivos['foto']->getClientFilename();
          $extension = explode(".", $foto);
          $destino .= $codigoComanda . '_' . $cliente->id . '_' . $usuario->id . '_' . $mesa->id . '_' 
          . (new DateTime('now'))->format('Ymd-Him') . '.' . $extension[1];
          $archivos['foto']->moveTo($destino);
        }
      }
      $comanda = new Comanda();
      $comanda->codigo_comanda =  $codigoComanda;
      $comanda->cliente_id = $cliente->id;
      $comanda->mozo_id = $usuario->id;
      $comanda->mesa_id = $mesa->id;
      $comanda->foto = $destino;
      $comanda->save();
      $mesa->estado = EstadoMesa::EsperandoPedido->name;
      $mesa->save();
      $operacion = new Operacion();
      $operacion->usuario_id = $usuario->id;
      $operacion->comanda_id = Comanda::where('codigo_comanda', $codigoComanda)->first()->id;
      $operacion->save();
      $payload = json_encode(array("mensaje" => "Comanda creado con exito", "codigo_comanda" => $codigoComanda));
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Throwable $e){
      var_dump(array("error" => "Error al crear comanda ($e)"));
    }
  }

  public function ModificarUno($request, $response, $args)
  {
    try{
      $body = $request->getParsedBody();
      $comanda = Comanda::where('codigo_comanda', $args['codigo_comanda'])->first();
      // $archivos = $request->getUploadedFiles();
      if ($comanda !== null) {
        // $destino = "";
        // if(count($archivos) > 0){
          //   if ($archivos['foto']->getError() == UPLOAD_ERR_OK) {
            //     $destino="./fotosComandas/";
            //     if (!file_exists($destino)) {
              //       mkdir($destino, 0777, true);
              //     }
              //     $foto = $archivos['foto']->getClientFilename();
              //     $extension = explode(".", $foto);
              
              //     unlink($comanda->foto);
              //     $destino .= $comanda->codigo_comanda . '_' . $cliente->id . '_' . $usuario->id . '_' . $mesa->id . '_' 
              //     . (new DateTime('now'))->format('Y-m-d:H-i-m') . '.' . $extension[1];
              //     $archivos['foto']->moveTo($destino);
              //   }
              // }
              
        if(isset($body['cliente_dni'])){
          $cliente = Cliente::where('dni', $body['cliente_dni'])->first();
          $comanda->cliente_id = $cliente->id;
        }
        if(isset($body['mozo_id'])){
          $usuario = Usuario::where('dni', $body['mozo_dni'])->first();
          $comanda->mozo_id = $usuario->id;
        }
        if(isset($body['codigo_mesa'])){
          $mesa =  Mesa::where('codigo_mesa', $body['codigo_mesa'])->first();
          $comanda->mesa_id = $mesa->id;
        }
        // if($destino != ""){
        //   $comanda->foto = $destino;
        // }
        $comanda->save();
        $payload = json_encode(array("mensaje" => "Comanda modificado con exito"));
      } else {
        $payload = json_encode(array("mensaje" => "Comanda no encontrado"));
      }
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Throwable $e){
      var_dump(array("error" => "Error al modificar comanda ($e)"));
    }
  }

  public function CobrarComanda($request, $response, $args){
    try{
      $codigo_comanda = $args['codigo_comanda'];      
      $comanda = Comanda::where('codigo_comanda', $codigo_comanda)->get()->first();
      if ($comanda !== null) {
        $total = Capsule::table('pedidos')
        ->select(Capsule::raw('SUM(productos.precio * pedidos.cantidad) as total'))
        ->join('productos', 'productos.id', '=', 'pedidos.producto_id')
        ->where('pedidos.comanda_id', '=', $comanda->id)
        ->get()->first();
        $mesa = Mesa::find($comanda->mesa_id);
        $mesa->estado = EstadoMesa::Pagando->name;
        $mesa->save();

        $payload =  array("Total" =>  $total);
      }
      else{
        $payload = array("mensaje" => "Verifique que el pedido exista y que además este en estado de Listo.");
      }
      $response->getBody()->write(json_encode($payload));
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Throwable $e){
      var_dump(array("error" => "Error al cobrar ($e)"));
    }
  }

  public function BorrarUno($request, $response, $args)
  {
    try{
      $comanda = Comanda::where('codigo_comanda', $args['codigo_comanda'])->first();
      if ($comanda !== null) {
        $comanda->delete();
        $payload = json_encode(array("mensaje" => "Comanda borrado con exito"));
      } else {
        $payload = json_encode(array("mensaje" => "Comanda no encontrado"));
      }
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Throwable $e){
      var_dump(array("error" => "Error al borrar comanda ($e)"));
    }
  }
}
