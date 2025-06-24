<?php
use DI\Container;
use Firebase\JWT\JWT;
use Dotenv\Dotenv;

$container = new Container();

$container->set('db', function() {
    $dotenv = Dotenv::createImmutable(dirname(__DIR__));
    $dotenv->load();
    
    $host = $_ENV['DB_HOST'];
    $dbname = $_ENV['DB_NAME'];
    $user = $_ENV['DB_USER'];
    $pass = $_ENV['DB_PASS'];
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
});

$container->set('jwt_secret', function() {
    return $_ENV['JWT_SECRET'];
});

return $container;