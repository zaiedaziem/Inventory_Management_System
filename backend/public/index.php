<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__) . '/src/dependencies.php';
require_once dirname(__DIR__) . '/src/routes.php';

$app = AppFactory::create();
require_once dirname(__DIR__) . '/src/routes.php';

$app->run();