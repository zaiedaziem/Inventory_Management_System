<?php
use Psr\Container\ContainerInterface;

$container->set('db', function (ContainerInterface $c) {
    $host = $_ENV['DB_HOST'];
    $db = $_ENV['DB_NAME'];
    $user = $_ENV['DB_USER'];
    $pass = $_ENV['DB_PASS'];

    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
});
?>