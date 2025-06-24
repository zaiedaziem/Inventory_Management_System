<?php
use Slim\Factory\AppFactory;
use DI\Container;
use Dotenv\Dotenv;

require __DIR__ . '/../../vendor/autoload.php';

$container = new Container();
AppFactory::setContainer($container);
$app = AppFactory::create();

$dotenv = Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();

require __DIR__ . '/../src/dependencies.php';
require __DIR__ . '/../src/routes.php';

$app->setBasePath('/jwt-demo/backend/public');
$app->run();
?>