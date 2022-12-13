<?php
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Illuminate\Database\Capsule\Manager as Capsule;


require __DIR__ . '/../vendor/autoload.php';

require_once './controllers/ClienteController.php';
require_once './controllers/UsuarioController.php';
require_once './controllers/MesaController.php';
require_once './controllers/ProductoController.php';
require_once './controllers/EncuestaController.php';
require_once './controllers/ComandaController.php';
require_once './controllers/PedidoController.php';
require_once './controllers/SuspensionController.php';

require_once './middlewares/AutentificadorMiddleware.php';
require_once './middlewares/AutorizadorMiddleware.php';
require_once './middlewares/ClientesMiddleware.php';
require_once './middlewares/ComandasMiddleware.php';
require_once './middlewares/EncuestasMiddleware.php';
require_once './middlewares/MesasMiddleware.php';
require_once './middlewares/PedidosMiddleware.php';
require_once './middlewares/ProductosMiddleware.php';
require_once './middlewares/SuspensionesMiddleware.php';
require_once './middlewares/UsuariosMiddleware.php';

// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);
$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();

// Eloquent
$container=$app->getContainer();

$capsule = new Capsule;
$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => $_ENV['MYSQL_HOST'],
    'database'  => $_ENV['MYSQL_DB'],
    'username'  => $_ENV['MYSQL_USER'],
    'password'  => $_ENV['MYSQL_PASS'],
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

$app->get('[/]', function (Request $request, Response $response) {    
    $response->getBody()->write("La comanda - Jessica Ruiz");
    return $response;
});

$app->post('/login[/]', \UsuarioController::class . ':Login')->add(\UsuariosMiddlelware::class . ':login'); 
$app->get('/estadoPedido[/]', \PedidoController::class . ':VerTiempoRestantePedido')->add(\PedidosMiddlelware::class . ':VerTiempoRestantePedido'); 


// ABM
$app->group('/admin', function (RouteCollectorProxy $app) {

  $app->group('/clientes', function (RouteCollectorProxy $group) {
      $group->post('[/]', \ClienteController::class . ':CargarUno')
      ->add(\ClientesMiddlelware::class . ':cargarCliente');
      $group->put('/{id}', \ClienteController::class . ':ModificarUno')
      ->add(\ClientesMiddlelware::class . ':modificarCliente');
      $group->delete('/{id}', \ClienteController::class . ':BorrarUno');
  });

  $app->group('/suspensiones', function (RouteCollectorProxy $group) {
    $group->post('[/]', \SuspensionController::class . ':CargarUno')
    ->add(\SuspensionesMiddlelware::class . ':cargarSuspension');
    $group->put('/{id}', \SuspensionController::class . ':ModificarUno')
    ->add(\SuspensionesMiddlelware::class . ':modificarSuspension');
    $group->delete('/{id}', \SuspensionController::class . ':BorrarUno');
  });

  $app->group('/estadisticas', function (RouteCollectorProxy $group) {
  $group->post('[/]', \SuspensionController::class . ':CargarUno')
  ->add(\EstadisticasMiddlelware::class . ':cargarSuspension');
  $group->put('/{id}', \SuspensionController::class . ':ModificarUno')
  ->add(\EstadisticasMiddlelware::class . ':modificarSuspension');
  $group->delete('/{id}', \SuspensionController::class . ':BorrarUno');
  });

  $app->group('/usuarios', function (RouteCollectorProxy $group) {
    $group->post('[/]', \UsuarioController::class . ':CargarUno')
    ->add(\UsuariosMiddlelware::class . ':cargarUsuario');
    $group->put('/{id}', \UsuarioController::class . ':ModificarUno')
    ->add(\UsuariosMiddlelware::class . ':modificarUsuario');
    $group->delete('/{id}', \UsuarioController::class . ':BorrarUno');
  });
  
  $app->group('/productos', function (RouteCollectorProxy $group) {
    $group->post('[/]', \ProductoController::class . ':CargarUno')
    ->add(\ProductosMiddlelware::class . ':cargarProducto');
    $group->put('/{id}', \ProductoController::class . ':ModificarUno')
    ->add(\ProductosMiddlelware::class . ':modificarProducto');
    $group->delete('/{id}', \ProductoController::class . ':BorrarUno');
  });

  $app->group('/mesas', function (RouteCollectorProxy $group) {
    $group->post('[/]', \MesaController::class . ':CargarUno')
    ->add(\MesasMiddlelware::class . ':cargarMesa');
    $group->put('/{id}', \MesaController::class . ':ModificarUno')
    ->add(\MesasMiddlelware::class . ':modificarMesa');
    $group->put('/cerrar/{codigo_mesa}', \MesaController::class . ':CerrarMesa');
    $group->delete('/{id}', \MesaController::class . ':BorrarUno');
  });

})
->add(\AutorizacionMiddelware::class . ':verificarSocio')
->add(\AutenticacionMiddelware::class . ':verificarToken');

/*
$app->group('/operaciones', function (RouteCollectorProxy $group) {
$group->post('[/]', \UsuarioController::class . ':CargarUno')->add(\CamposMiddlelware::class . ':cargarUsuario');
$group->put('/{id}', \UsuarioController::class . ':ModificarUno')->add(\CamposMiddlelware::class . ':modificarUsuario');
$group->delete('/{id}', \UsuarioController::class . ':BorrarUno');
});

$app->group('/registros', function (RouteCollectorProxy $group) {
$group->post('[/]', \UsuarioController::class . ':CargarUno')->add(\CamposMiddlelware::class . ':cargarUsuario');
$group->put('/{id}', \UsuarioController::class . ':ModificarUno')->add(\CamposMiddlelware::class . ':modificarUsuario');
$group->delete('/{id}', \UsuarioController::class . ':BorrarUno');
});*/

$app->group('/usuario', function (RouteCollectorProxy $app) {

  $app->group('/mesas', function (RouteCollectorProxy $group) {
      //$group->put('/{id}', \MesaController::class . ':cambiarEstado')->add(\CamposMiddlelware::class . ':modificarEstadoMesa');
  })->add(\AutorizacionMiddelware::class . ':verificarUsuario');


  $app->group('/comandas', function (RouteCollectorProxy $group) {
      
      $group->post('[/]', \ComandaController::class . ':CargarUno')
      ->add(\ComandasMiddlelware::class . ':cargarComanda');
      
      $group->put('/cobrarComanda/{codigo_comanda}', \ComandaController::class . ':CobrarComanda');
      
      $group->put('/{id}', \ComandaController::class . ':ModificarUno')
      ->add(\ComandasMiddlelware::class . ':modificarComanda');
      
      $group->delete('/{id}', \ComandaController::class . ':BorrarUno');

  })->add(\AutorizacionMiddelware::class . ':verificarMozo');

  $app->group('/pedidos', function (RouteCollectorProxy $app) {
    $app->group('/mozo', function (RouteCollectorProxy $group) {

      $group->post('[/]', \PedidoController::class . ':CargarUno')
        ->add(\PedidosMiddlelware::class . ':cargarPedido');

      $group->put('/{id}', \PedidoController::class . ':ModificarUno')
        ->add(\PedidosMiddlelware::class . ':modificarPedido');

      $group->put('/servir/{id}', \PedidoController::class . ':ServirPedido');


      $group->delete('/{id}', \PedidoController::class . ':BorrarUno');
      
    })->add(\AutorizacionMiddelware::class . ':verificarMozo');

    $app->group('/preparar', function (RouteCollectorProxy $group) {
      $group->put('/tragosVinos/{id}[/]', \PedidoController::class . ':PrepararTragosVinos')
      ->add(\PedidosMiddlelware::class . ':prepararPedido')
      ->add(\AutorizacionMiddelware::class . ':verificarBartender');

      $group->put('/barraChoperas/{id}[/]', \PedidoController::class . ':PrepararBarraChoperas')
      ->add(\PedidosMiddlelware::class . ':prepararPedido')
      ->add(\AutorizacionMiddelware::class . ':verificarCervecero');

      $group->put('/cocina/{id}[/]', \PedidoController::class . ':PrepararCocina')
      ->add(\PedidosMiddlelware::class . ':prepararPedido')
      ->add(\AutorizacionMiddelware::class . ':verificarCocinero');

      $group->put('/candyBar/{id}[/]', \PedidoController::class . ':PrepararCandyBar')
      ->add(\PedidosMiddlelware::class . ':prepararPedido')
      ->add(\AutorizacionMiddelware::class . ':verificarPastelero');
    });

    $app->group('/terminar', function (RouteCollectorProxy $group) {
      $group->put('/tragosVinos/{id}[/]', \PedidoController::class . ':TerminarTragosVinos')
      ->add(\AutorizacionMiddelware::class . ':verificarBartender');

      $group->put('/barraChoperas/{id}[/]', \PedidoController::class . ':TerminarBarraChoperas')
      ->add(\AutorizacionMiddelware::class . ':verificarCervecero');

      $group->put('/cocina/{id}[/]', \PedidoController::class . ':TerminarCocina')
      ->add(\AutorizacionMiddelware::class . ':verificarCocinero');

      $group->put('/candyBar/{id}[/]', \PedidoController::class . ':TerminarCandyBar')
      ->add(\AutorizacionMiddelware::class . ':verificarPastelero');
    });
  });


})->add(\AutenticacionMiddelware::class . ':verificarToken');
  


  $app->group('/encuestas', function (RouteCollectorProxy $group) {

      $group->post('[/]', \EncuestaController::class . ':CargarUno')
      ->add(\EncuestasMiddlelware::class . ':cargarEncuesta');

      $group->put('/{id}', \EncuestaController::class . ':ModificarUno')
      ->add(\EncuestasMiddlelware::class . ':modificarEncuesta')
      ->add(\AutorizacionMiddelware::class . ':verificarSocio');

      $group->delete('/{id}', \EncuestaController::class . ':BorrarUno')
      ->add(\AutorizacionMiddelware::class . ':verificarSocio');

    })->add(\AutenticacionMiddelware::class . ':verificarToken');  
  
  //End Region ABM





$app->group('/datos', function (RouteCollectorProxy $group) {
    
  $group->group('/clientes', function (RouteCollectorProxy $group) {
    $group->get('[/]', \ClienteController::class . ':TraerTodos');
    $group->get('/{id}', \ClienteController::class . ':TraerUno');
  })->add(\AutorizacionMiddelware::class . ':verificarSocio');
  
  $group->group('/usuarios', function (RouteCollectorProxy $group) {
    $group->get('[/]', \UsuarioController::class . ':TraerTodos');
    $group->get('/{id}[/]', \UsuarioController::class . ':TraerUno');
  })->add(\AutorizacionMiddelware::class . ':verificarSocio');
  
  $group->group('/suspensiones', function (RouteCollectorProxy $group) {
    $group->get('[/]', \SuspensionController::class . ':TraerTodos');
    $group->get('/{id}[/]', \SuspensionController::class . ':TraerUno');
  })->add(\AutorizacionMiddelware::class . ':verificarSocio');
  
  $group->group('/encuestas', function (RouteCollectorProxy $group) {
    $group->get('[/]', \EncuestaController::class . ':TraerTodos')->add(\AutorizacionMiddelware::class . ':verificarSocio');
    $group->get('/{id}[/]', \EncuestaController::class . ':TraerUno')->add(\AutorizacionMiddelware::class . ':verificarSocio');
  });  

  $group->group('/estadisticas', function (RouteCollectorProxy $group) {
    $group->get('[/]', \SuspensionController::class . ':TraerTodos');
    $group->get('/{id}', \SuspensionController::class . ':TraerUno');
  })->add(\AutorizacionMiddelware::class . ':verificarSocio');


  $group->group('/operaciones', function (RouteCollectorProxy $group) {
    $group->get('[/]', \UsuarioController::class . ':TraerTodos');
    $group->get('/{id}', \UsuarioController::class . ':TraerUno');
  });

  $group->group('/registros', function (RouteCollectorProxy $group) {
    $group->get('[/]', \UsuarioController::class . ':TraerTodos');
    $group->get('/{id}', \UsuarioController::class . ':TraerUno');
  });



  $group->group('/productos', function (RouteCollectorProxy $group) {
    $group->get('[/]', \ProductoController::class . ':TraerTodos');
    $group->get('/{id}[/]', \ProductoController::class . ':TraerUno');
  });
  
  $group->group('/mesas', function (RouteCollectorProxy $group) {
    $group->get('[/]', \MesaController::class . ':TraerTodos');
    $group->get('/{id}[/]', \MesaController::class . ':TraerUno');
  });
  
  

  $group->group('/comandas', function (RouteCollectorProxy $group) {
    $group->get('[/]', \ComandaController::class . ':TraerTodos')->add(\AutorizacionMiddelware::class . ':verificarSocio');
    $group->get('/{id}[/]', \ComandaController::class . ':TraerUno')->add(\AutorizacionMiddelware::class . ':verificarSocio');
   });


  $group->group('/pedidos', function (RouteCollectorProxy $group) {
    $group->get('[/]', \PedidoController::class . ':TraerTodos')->add(\AutorizacionMiddelware::class . ':verificarSocio');
    $group->get('/{id}[/]', \PedidoController::class . ':TraerUno')->add(\AutorizacionMiddelware::class . ':verificarSocio');
    
    $group->group('/pendientes', function (RouteCollectorProxy $group) {
      $group->get('/tragosVinos[/]', \PedidoController::class . ':TraerTragosVinosPendientes')->add(\AutorizacionMiddelware::class . ':verificarBartender');
      $group->get('/barraChoperas[/]', \PedidoController::class . ':TraerBarraChoperasPendientes')->add(\AutorizacionMiddelware::class . ':verificarCervecero');
      $group->get('/cocina[/]', \PedidoController::class . ':TraerCocinaPendientes')->add(\AutorizacionMiddelware::class . ':verificarCocinero');
      $group->get('/candyBar[/]', \PedidoController::class . ':TraerCandyBarPendientes')->add(\AutorizacionMiddelware::class . ':verificarPastelero');
    });

    $group->group('/preparacion', function (RouteCollectorProxy $group) {
      $group->get('/tragosVinos[/]', \PedidoController::class . ':TraerTragosVinosPreparacion')->add(\AutorizacionMiddelware::class . ':verificarBartender');
      $group->get('/barraChoperas[/]', \PedidoController::class . ':TraerBarraChoperasPreparacion')->add(\AutorizacionMiddelware::class . ':verificarCervecero');
      $group->get('/cocina[/]', \PedidoController::class . ':TraerCocinaPreparacion')->add(\AutorizacionMiddelware::class . ':verificarCocinero');
      $group->get('/candyBar[/]', \PedidoController::class . ':TraerCandyBarPreparacion')->add(\AutorizacionMiddelware::class . ':verificarPastelero');
    });

  });

  

})->add(\AutenticacionMiddelware::class . ':verificarToken');


$app->run();
