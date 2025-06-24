<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as SlimResponse;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$app->add(function (Request $request, RequestHandler $handler) {
    $route = $request->getUri()->getPath();
    $token = $request->getHeaderLine('Authorization');

    if (preg_match('/^\/api\/auth\/(register|login)$/', $route) || !$token) {
        return $handler->handle($request);
    }

    if (!$token) {
        $response = new SlimResponse();
        $response->getBody()->write(json_encode(['error' => 'Token required']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
    }

    try {
        $decoded = JWT::decode(substr($token, 7), new Key($_ENV['JWT_SECRET'], 'HS256'));
        $request = $request->withAttribute('token', $decoded);
    } catch (Exception $e) {
        $response = new SlimResponse();
        $response->getBody()->write(json_encode(['error' => 'Invalid token']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
    }

    // Role-based access control
    $permissions = [
        '/api/products' => ['view_products'],
        '/api/products' => ['add_products'], // POST
        '/api/products/{id}' => ['update_products'], // PUT
        '/api/products/{id}' => ['delete_products'] // DELETE
    ];

    $method = $request->getMethod();
    $path = $route;
    $requiredPerm = $permissions[$path] ?? [];

    if ($requiredPerm && !in_array($requiredPerm[0], $decoded->permissions)) {
        $response = new SlimResponse();
        $response->getBody()->write(json_encode(['error' => 'Insufficient permissions']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
    }

    return $handler->handle($request);
});
?>