<?php
require_once './models/Validaciones.php';

use App\Models\Mesa;
use App\Models\Comanda;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class EncuestasMiddlelware
{
    public function cargarEncuesta(Request $request, RequestHandler $handler): Response
    {
      $response = new Response();
      try {
        $body = $request->getParsedBody();
        $response = $response->withStatus(401);
        $errores = [];
        if(isset($body['codigo_comanda']) && isset($body['codigo_mesa']) && isset($body['mesa']) 
        && isset($body['restaurante']) && isset($body['mozo']) && isset($body['cocinero'])){
          // $errores['error_codigo_mesa'] = Validaciones::errorLargoCadena($body['codigo_mesa'], 5, 5);
          // $errores['error_codigo_comanda'] = Validaciones::errorLargoCadena($body['codigo_comanda'], 5, 5);
          //con el codigo de comanda podemos obtener de todas formas el codigo de mesa correspondiente
          if(Mesa::where('codigo_mesa', $body['codigo_mesa'])->first() && Comanda::where('codigo_comanda', $body['codigo_comanda'])->first()){
            $errores['error_mesa'] = Validaciones::errorCantidad($body['mesa'], 1, 10);
            $errores['error_restaurante'] = Validaciones::errorCantidad($body['restaurante'], 1, 10);
            $errores['error_mozo'] = Validaciones::errorCantidad($body['mozo'], 1, 10);
            $errores['error_cocinero'] = Validaciones::errorCantidad($body['cocinero'], 1, 10);
            if(isset($body['comentario'])){
              $errores['error_comentario'] = Validaciones::errorLargoCadena($body['comentario'], 10, 66);
            }
            $mensajesErr = array_filter($errores, fn($e) => $e != 0, ARRAY_FILTER_USE_BOTH);
            if(count($mensajesErr) == 0){
              $response = $handler->handle($request);
            }
            else{
              $response->getBody()->write(json_encode($mensajesErr));
              $response = $response->withStatus(401);
            }
          }
          else{
            $response->getBody()->write(json_encode(array('error' =>  'Error, debe existir la mesa y la comanda')));
            $response = $response->withStatus(401);
          }
      }
      else{
        $response->getBody()->write(json_encode(array('error' =>  'Error, debe cargas los siguientes campos: codigo_comanda, codigo_mesa, mesa, restaurante, mozo y cocinero')));
            $response = $response->withStatus(401);
      }
        return $response->withHeader('Content-Type', 'application/json');
      } catch (Throwable $e) {
        var_dump(array("Error" =>  "error, intente nuevamente más tarde. ($e)"));
      }
    }

    public function modificarEncuesta(Request $request, RequestHandler $handler): Response
    {
      $response = new Response();
      try {
        $body = $request->getParsedBody();
        $response = $response->withStatus(401);
        $errores = [];
        if(!(isset($body['codigo_comanda']) || isset($body['codigo_mesa']) || isset($body['mesa']) || isset($body['restaurante']) 
        || isset($body['mozo']) || isset($body['cocinero']) || isset($body['comentario'])))
        {
            $response->getBody()->write(json_encode(array('error' => 'Debe modificar al menos un dato.')));
            $response = $response->withStatus(401);
            return $response->withHeader('Content-Type', 'application/json');
        }
        if(isset($body['codigo_comanda'])){
          //$errores['error_codigo_comanda'] = Validaciones::errorLargoCadena($body['codigo_comanda'], 5, 5);
          if(!Comanda::where('codigo_comanda', $body['codigo_comanda'])->first()){
            $response->getBody()->write(json_encode(array('error' => 'Debe ingresar un código de comanda existente.')));
            $response = $response->withStatus(401);
            return $response->withHeader('Content-Type', 'application/json');
          }
        }
        if(isset($body['codigo_mesa'])){
          //$errores['error_dni'] = Validaciones::errorDni($body['dni']);
          if(!Mesa::where('codigo_mesa', $body['codigo_mesa'])->first()){
            $response->getBody()->write(json_encode(array('error' => 'Debe ingresar un código de mesa existente.')));
            $response = $response->withStatus(401);
            return $response->withHeader('Content-Type', 'application/json');
          }
        }
        if(isset($body['mesa'])){
          $errores['error_mesa'] = Validaciones::errorCantidad($body['mesa'], 1, 10);
        }
        if(isset($body['restaurante'])){
          $errores['error_restaurante'] = Validaciones::errorCantidad($body['restaurante'], 1, 10);
        }
        if(isset($body['mozo'])){
          $errores['error_mozo'] = Validaciones::errorCantidad($body['mozo'], 1, 10);
        }
        if(isset($body['cocinero'])){
          $errores['error_cocinero'] = Validaciones::errorCantidad($body['cocinero'], 1, 10);
        }
        if(isset($body['comentario'])){
          $errores['error_comentario'] = Validaciones::errorLargoCadena($body['comentario'], 10, 66);
        }
        $mensajesErr = array_filter($errores, fn($e) => $e != 0, ARRAY_FILTER_USE_BOTH);
        if(count($mensajesErr) == 0){
          $response = $handler->handle($request);
        }
        else{
          $response->getBody()->write(json_encode($mensajesErr));
          $response = $response->withStatus(401);
        }
        return $response->withHeader('Content-Type', 'application/json');
      } catch (Throwable $e) {
        var_dump(array("Error" => "error, intente nuevamente más tarde. ($e)"));
      }
    }
   
}