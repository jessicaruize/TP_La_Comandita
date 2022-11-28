<?php
require_once './models/Usuario.php';
require_once './models/Registro.php';
require_once './models/RolUsuario.php';
require_once './models/Sector.php';
require_once './interfaces/IApiUsable.php';
date_default_timezone_set('America/Buenos_Aires');
use \App\Models\Usuario as Usuario;
use \App\Models\Registro as Registro;

class UsuarioController implements IApiUsable
{
  
  public static function Login($request, $response, $args) 
  {
    try{
      $body = $request->getParsedBody();
      $response = $response->withStatus(401);
      $usuario = Usuario::where('mail', $body['mail'])->first();
      if(!is_null($usuario)){
        if(password_verify($body['clave'], $usuario->clave)){
          $token = AutentificadorJWT::CrearToken($usuario->id, $usuario->rol, $usuario->sector);
          $registro = new Registro();
          $registro->usuario_id = $usuario->id;
          $registro->save();
          $payload = json_encode(array("mensaje"=>"Usuario Válido.","token"=>$token));
          $response = $response->withStatus(200);
        }
        else{
          $payload = json_encode(array("mensaje" => "Corrobore los datos."));
        }
      }
      else{
        $payload = json_encode(array("mensaje" => "El usuario no es valido."));
      }
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Throwable $e){
      var_dump(array("error" => "Error al corroborar credenciales ($e)"));
    }
  }

  public function TraerUno($request, $response, $args)
  {
    try{
      $id = $args['id'];
      $usuario = Usuario::find($id);
      if ($usuario !== null) {
        $payload = json_encode($usuario);
      } else {
        $payload = json_encode(array("mensaje" => "Usuario no encontrado"));
      }
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Throwable $e){
      var_dump(array("error" => "Error al traer usuario ($e)"));
    }
  }

  public function TraerTodos($request, $response, $args)
  {
    try{
      $lista = Usuario::all();
      $payload = json_encode(array("listaUsuarios" => $lista));
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Throwable $e){
      var_dump(array("error" => "Error al traer usuarios ($e)"));
    }
  }

  public function CargarUno($request, $response, $args)
  {
    try{
      $body = $request->getParsedBody();
      $usr = new Usuario();
      $usr->mail =  $body['mail'];
      $usr->clave = password_hash($body['clave'], PASSWORD_DEFAULT);
      $usr->nombre = ucfirst($body['nombre']);
      $usr->apellido = ucfirst($body['apellido']);
      $usr->dni = $body['dni'];
      $usr->telefono = $body['telefono'];
      $usr->rol = RolUsuario::obtenerValor($body['rol']);
      $usr->sector = Sector::obtenerValor($body['sector']);
      $usr->save();

      $payload = json_encode(array("mensaje" => "Usuario creado con exito"));

      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Throwable $e){
      var_dump(array("error" => "Error al crear usuario ($e)"));
    }
  }

  public function ModificarUno($request, $response, $args)
  {
    try{
      $body = $request->getParsedBody();
      $id = $args['id'];
      $usr = Usuario::find($id);
      if ($usr !== null) {
        if(isset($body['mail'])){
          $usr->mail = $body['mail'];
        }
        if(isset($body['dni'])){
          $usr->dni = $body['dni'];
        }
        if(isset($body['clave'])){
          $usr->clave = $body['clave'];
        }
        if(isset($body['nombre'])){
          $usr->nombre = ucfirst($body['nombre']);
        }
        if(isset($body['apellido'])){
          $usr->apellido = ucfirst($body['apellido']);
        }
        if(isset($body['telefono'])){
          $usr->telefono = $body['telefono'];
        }
        if(isset($body['rol'])){
          $usr->rol = RolUsuario::obtenerValor($body['rol']);
        }
        if(isset($body['sector'])){
          $usr->sector = Sector::obtenerValor($body['sector']);
        }
        $usr->save();
        $payload = json_encode(array("mensaje" => "Usuario modificado con exito"));
      } else {
        $payload = json_encode(array("mensaje" => "Usuario no encontrado"));
      }
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Throwable $e){
      var_dump(array("error" => "Error al modificar usuario ($e)"));
    }
  }

  public function BorrarUno($request, $response, $args)
  {
    try{
      $id = $args['id'];
      $usuario = Usuario::find($id);
      if ($usuario !== null) {
        $usuario->delete();
        $payload = json_encode(array("mensaje" => "Usuario borrado con exito"));
      } else {
        $payload = json_encode(array("mensaje" => "Usuario no encontrado"));
      }
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    catch(Throwable $e){
      var_dump(array("error" => "Error al borrar usuario ($e)"));
    }
  }
}
