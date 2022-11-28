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
require_once './controllers/FacturaController.php';
require_once './controllers/PedidoController.php';
require_once './controllers/SuspensionController.php';

require_once './middlewares/AutentificadorMiddleware.php';
require_once './middlewares/AutorizadorMiddleware.php';
require_once './middlewares/CamposMiddleware.php';

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
    $response->getBody()->write("Rutas disponibles:<br>");
    $response->getBody()->write("/usuarios<br>");
    $response->getBody()->write("/productos<br>");
    $response->getBody()->write("/mesas<br>");
    $response->getBody()->write("/pedidos<br>");
    $response->getBody()->write("/login");
    return $response;
    return $response;

});

$app->post('/login[/]', \UsuarioController::class . ':Login')->add(\CamposMiddlelware::class . ':camposLogin'); 



// Routes

//Region ABM
$app->group('/clientes', function (RouteCollectorProxy $group) {
    $group->get('[/]', \ClienteController::class . ':TraerTodos');
    $group->get('/{id}', \ClienteController::class . ':TraerUno');
    $group->post('[/]', \ClienteController::class . ':CargarUno')->add(\CamposMiddlelware::class . ':camposCargarCliente');
    $group->put('/{id}', \ClienteController::class . ':ModificarUno')->add(\CamposMiddlelware::class . ':camposModificarCliente');
    $group->delete('/{id}', \ClienteController::class . ':BorrarUno');
})->add(\AutorizacionMiddelware::class . ':verificarSocio')->add(\AutenticacionMiddelware::class . ':verificarToken');

$app->group('/mesas', function (RouteCollectorProxy $group) {
    $group->get('[/]', \MesaController::class . ':TraerTodos');
    $group->get('/{id}', \MesaController::class . ':TraerUno');
    $group->post('[/]', \MesaController::class . ':CargarUno')->add(\CamposMiddlelware::class . ':camposCargarMesa');
    $group->put('/{id}', \MesaController::class . ':ModificarUno')->add(\CamposMiddlelware::class . ':camposModificarMesa');
    $group->delete('/{id}', \MesaController::class . ':BorrarUno');
})->add(\AutorizacionMiddelware::class . ':verificarSocio')->add(\AutenticacionMiddelware::class . ':verificarToken');

$app->group('/productos', function (RouteCollectorProxy $group) {
    $group->get('[/]', \ProductoController::class . ':TraerTodos');
    $group->get('/{id}', \ProductoController::class . ':TraerUno');
    $group->post('[/]', \ProductoController::class . ':CargarUno')->add(\CamposMiddlelware::class . ':camposCargarProducto');
    $group->put('/{id}', \ProductoController::class . ':ModificarUno')->add(\CamposMiddlelware::class . ':camposModificarProducto');
    $group->delete('/{id}', \ProductoController::class . ':BorrarUno');
})->add(\AutorizacionMiddelware::class . ':verificarSocio')->add(\AutenticacionMiddelware::class . ':verificarToken');

$app->group('/usuarios', function (RouteCollectorProxy $group) {
    $group->get('[/]', \UsuarioController::class . ':TraerTodos');
    $group->get('/{id}', \UsuarioController::class . ':TraerUno');
    $group->post('[/]', \UsuarioController::class . ':CargarUno')->add(\CamposMiddlelware::class . ':camposCargarUsuario');
    $group->put('/{id}', \UsuarioController::class . ':ModificarUno')->add(\CamposMiddlelware::class . ':camposModificarUsuario');
    $group->delete('/{id}', \UsuarioController::class . ':BorrarUno');
})->add(\AutorizacionMiddelware::class . ':verificarSocio')->add(\AutenticacionMiddelware::class . ':verificarToken');

$app->group('/facturas', function (RouteCollectorProxy $group) {
    $group->get('[/]', \FacturaController::class . ':TraerTodos')->add(\AutorizacionMiddelware::class . ':verificarSocio');
    $group->get('/{id}', \FacturaController::class . ':TraerUno')->add(\AutorizacionMiddelware::class . ':verificarSocio');
    $group->post('[/]', \FacturaController::class . ':CargarUno')->add(\CamposMiddlelware::class . ':camposCargarFactura')->add(\AutorizacionMiddelware::class . ':verificarMozo');
    $group->put('/{id}', \FacturaController::class . ':ModificarUno')->add(\CamposMiddlelware::class . ':camposModificarFactura')->add(\AutorizacionMiddelware::class . ':verificarMozo');
    $group->delete('/{id}', \FacturaController::class . ':BorrarUno')->add(\AutorizacionMiddelware::class . ':verificarMozo');
  })->add(\AutenticacionMiddelware::class . ':verificarToken');


$app->group('/pedidos', function (RouteCollectorProxy $group) {
    $group->get('[/]', \PedidoController::class . ':TraerTodos')->add(\AutorizacionMiddelware::class . ':verificarSocio');
    $group->get('/pendientesTragosVinos[/]', \PedidoController::class . ':TraerPedientesTragosVinos')->add(\AutorizacionMiddelware::class . ':verificarBartender');
    $group->get('/pendientesChoperas[/]', \PedidoController::class . ':TraerPedientesChoperas')->add(\AutorizacionMiddelware::class . ':verificarCervecero');
    $group->get('/pendientesCocina[/]', \PedidoController::class . ':TraerPedientesCocina')->add(\AutorizacionMiddelware::class . ':verificarCocinero');
    $group->get('/pendientesCandyBar[/]', \PedidoController::class . ':TraerPedientesCandyBar')->add(\AutorizacionMiddelware::class . ':verificarPastelero');
    $group->get('/{id}', \PedidoController::class . ':TraerUno')->add(\AutorizacionMiddelware::class . ':verificarSocio');
    $group->post('[/]', \PedidoController::class . ':CargarUno')->add(\CamposMiddlelware::class . ':camposCargarPedido')->add(\AutorizacionMiddelware::class . ':verificarMozo');
    $group->put('/{id}', \PedidoController::class . ':ModificarUno')->add(\CamposMiddlelware::class . ':camposModificarPedido')->add(\AutorizacionMiddelware::class . ':verificarMozo');
    $group->delete('/{id}', \PedidoController::class . ':BorrarUno')->add(\AutorizacionMiddelware::class . ':verificarMozo')->add(\AutorizacionMiddelware::class . ':verificarMozo');;
  })->add(\AutenticacionMiddelware::class . ':verificarToken');

$app->group('/encuestas', function (RouteCollectorProxy $group) {
    $group->get('[/]', \EncuestaController::class . ':TraerTodos')->add(\AutorizacionMiddelware::class . ':verificarSocio');
    $group->get('/{id}', \EncuestaController::class . ':TraerUno')->add(\AutorizacionMiddelware::class . ':verificarSocio');
    $group->post('[/]', \EncuestaController::class . ':CargarUno')->add(\CamposMiddlelware::class . ':camposCargarEncuesta');
    $group->put('/{id}', \EncuestaController::class . ':ModificarUno')->add(\CamposMiddlelware::class . ':camposModificarEncuesta')->add(\AutorizacionMiddelware::class . ':verificarSocio');
    $group->delete('/{id}', \EncuestaController::class . ':BorrarUno')->add(\AutorizacionMiddelware::class . ':verificarSocio');
  })->add(\AutenticacionMiddelware::class . ':verificarToken');  

$app->group('/suspensiones', function (RouteCollectorProxy $group) {
      $group->get('[/]', \SuspensionController::class . ':TraerTodos');
      $group->get('/{id}', \SuspensionController::class . ':TraerUno');
      $group->post('[/]', \SuspensionController::class . ':CargarUno')->add(\CamposMiddlelware::class . ':camposCargarSuspension');
      $group->put('/{id}', \SuspensionController::class . ':ModificarUno')->add(\CamposMiddlelware::class . ':camposModificarSuspension');
      $group->delete('/{id}', \SuspensionController::class . ':BorrarUno');
})->add(\AutorizacionMiddelware::class . ':verificarSocio')->add(\AutenticacionMiddelware::class . ':verificarToken');






$app->group('/estadisticas', function (RouteCollectorProxy $group) {
    $group->get('[/]', \SuspensionController::class . ':TraerTodos');
    $group->get('/{id}', \SuspensionController::class . ':TraerUno');
    $group->post('[/]', \SuspensionController::class . ':CargarUno')->add(\CamposMiddlelware::class . ':camposCargarSuspension');
    $group->put('/{id}', \SuspensionController::class . ':ModificarUno')->add(\CamposMiddlelware::class . ':camposModificarSuspension');
    $group->delete('/{id}', \SuspensionController::class . ':BorrarUno');
})->add(\AutorizacionMiddelware::class . ':verificarSocio')->add(\AutenticacionMiddelware::class . ':verificarToken');




  

$app->group('/operaciones', function (RouteCollectorProxy $group) {
    $group->get('[/]', \UsuarioController::class . ':TraerTodos');
    $group->get('/{id}', \UsuarioController::class . ':TraerUno');
    $group->post('[/]', \UsuarioController::class . ':CargarUno')->add(\CamposMiddlelware::class . ':camposCargarUsuario');
    $group->put('/{id}', \UsuarioController::class . ':ModificarUno')->add(\CamposMiddlelware::class . ':camposModificarUsuario');
    $group->delete('/{id}', \UsuarioController::class . ':BorrarUno');
  });

$app->group('/registros', function (RouteCollectorProxy $group) {
    $group->get('[/]', \UsuarioController::class . ':TraerTodos');
    $group->get('/{id}', \UsuarioController::class . ':TraerUno');
    $group->post('[/]', \UsuarioController::class . ':CargarUno')->add(\CamposMiddlelware::class . ':camposCargarUsuario');
    $group->put('/{id}', \UsuarioController::class . ':ModificarUno')->add(\CamposMiddlelware::class . ':camposModificarUsuario');
    $group->delete('/{id}', \UsuarioController::class . ':BorrarUno');
  });


  //End Region ABM

$app->run();
