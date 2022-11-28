<?php
require_once './middlewares/AutentificadorJWT.php';

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class AutenticacionMiddelware
{
    public function verificarToken(Request $request, RequestHandler $handler): Response
    {
      try {
        $response = new Response();
        $header = $request->getHeaderLine('Authorization');
        if(is_null($header) || empty($header))
        {
            $response->getBody()->write('Error, el token esta vacio.');
            $response = $response->withStatus(400);
        }
        else
        {
            $token = trim(explode("Bearer", $header)[1]);
            if(is_null($token) || empty($token)){
              $response->getBody()->write('Error, el token esta vacio.');
              $response = $response->withStatus(400);
            }
            else{
              AutentificadorJWT::verificarToken($token);
              $response = $handler->handle($request);
            }
        }
      } catch (Throwable $e) {
        echo "error $e";
      }
      finally{
        return $response->withHeader('Content-Type', 'application/json');
      }
    }


    
}