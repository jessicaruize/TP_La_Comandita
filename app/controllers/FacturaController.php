<?php
require_once './models/Factura.php';
require_once './models/Cliente.php';
require_once './models/Usuario.php';
require_once './models/Mesa.php';
require_once './interfaces/IApiUsable.php';
date_default_timezone_set('America/Buenos_Aires');
use \App\Models\Factura as Factura;
use \App\Models\Cliente as Cliente;
use \App\Models\Usuario as Usuario;
use \App\Models\Mesa as Mesa;
use App\Models\Operacion;

class FacturaController implements IApiUsable
{
  public function TraerUno($request, $response, $args)
  {
    try{
      $id = $args['id'];
      $factura = Factura::find($id);
      if ($factura !== null) {
        $payload = json_encode($factura);
      } else {
        $payload = json_encode(array("mensaje" => "Factura no encontrado"));
      }
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Throwable $e){
      var_dump(array("error" => "Error al traer factura ($e)"));
    }
  }

  public function TraerTodos($request, $response, $args)
  {
    try{
      $lista = Factura::all();
      $payload = json_encode(array("listaFacturas" => $lista));
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Throwable $e){
      var_dump(array("error" => "Error al traer facturas ($e)"));
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
      $mesa =  Mesa::find($body['mesa_id']);
      $codigoFactura = 0;
      while($codigoFactura == 0 || Factura::where('codigo_factura', $codigoFactura)->first())
      {
        $codigoFactura = Mesa::obtener_codigo();
      }
      if(count($archivos) > 0){
        if ($archivos['foto']->getError() == UPLOAD_ERR_OK) {
          $destino="./fotosFacturas/";
          if (!file_exists($destino)) {
            mkdir($destino, 0777, true);
          }
          $foto = $archivos['foto']->getClientFilename();
          $extension = explode(".", $foto);
          $destino .= $codigoFactura . '_' . $cliente->id . '_' . $usuario->id . '_' . $mesa->id . '_' 
          . (new DateTime('now'))->format('Ymd-Him') . '.' . $extension[1];
          $archivos['foto']->moveTo($destino);
        }
      }
      $mesa->estado = EstadoMesa::EsperandoPedido->name;
      $mesa->save();
      $factura = new Factura();
      $factura->codigo_factura =  $codigoFactura;
      $factura->cliente_id = $cliente->id;
      $factura->mozo_id = $usuario->id;
      $factura->mesa_id = $mesa->id;
      $factura->foto = $destino;
      $factura->save();
      $operacion = new Operacion();
      $operacion->usuario_id = $usuario->id;
      $operacion->factura_id = Factura::where('codigo_factura', $codigoFactura)->first()->id;
      $operacion->save();
      $payload = json_encode(array("mensaje" => "Factura creado con exito", "codigo_factura" => $codigoFactura));
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Throwable $e){
      var_dump(array("error" => "Error al crear factura ($e)"));
    }
  }

  public function ModificarUno($request, $response, $args)
  {
    try{
      $body = $request->getParsedBody();
      // $archivos = $request->getUploadedFiles();
      $id = $args['id'];
      $factura = Factura::find($id);
      if ($factura !== null) {
        // $destino = "";
        // if(count($archivos) > 0){
          //   if ($archivos['foto']->getError() == UPLOAD_ERR_OK) {
            //     $destino="./fotosFacturas/";
            //     if (!file_exists($destino)) {
              //       mkdir($destino, 0777, true);
              //     }
              //     $foto = $archivos['foto']->getClientFilename();
              //     $extension = explode(".", $foto);
              
              //     unlink($factura->foto);
              //     $destino .= $factura->codigo_factura . '_' . $cliente->id . '_' . $usuario->id . '_' . $mesa->id . '_' 
              //     . (new DateTime('now'))->format('Y-m-d:H-i-m') . '.' . $extension[1];
              //     $archivos['foto']->moveTo($destino);
              //   }
              // }
              
        if(isset($body['cliente_dni'])){
          $cliente = Cliente::where('dni', $body['cliente_dni'])->first();
          var_dump($cliente);
          $factura->cliente_id = $cliente->id;
        }
        if(isset($body['mozo_id'])){
          $usuario = Usuario::find($body['mozo_id']);
          $factura->mozo_id = $usuario->id;
        }
        if(isset($body['mesa_id'])){
          $mesa =  Mesa::find($body['mesa_id']);
          $factura->mesa_id = $mesa->id;
        }
        // if($destino != ""){
        //   $factura->foto = $destino;
        // }
        $factura->save();
        $payload = json_encode(array("mensaje" => "Factura modificado con exito"));
      } else {
        $payload = json_encode(array("mensaje" => "Factura no encontrado"));
      }
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Throwable $e){
      var_dump(array("error" => "Error al modificar factura ($e)"));
    }
  }

  public function BorrarUno($request, $response, $args)
  {
    try{
      $id = $args['id'];
      $factura = Factura::find($id);
      if ($factura !== null) {
        $factura->delete();
        $payload = json_encode(array("mensaje" => "Factura borrado con exito"));
      } else {
        $payload = json_encode(array("mensaje" => "Factura no encontrado"));
      }
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Throwable $e){
      var_dump(array("error" => "Error al borrar factura ($e)"));
    }
  }
}
