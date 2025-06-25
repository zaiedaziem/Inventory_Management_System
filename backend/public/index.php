<?php
//php -S localhost:8000 -t public
//npm run serve

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Routing\RouteCollectorProxy;

use App\db;
use App\Middleware\JwtMiddleware;
use App\Controllers\AuthController;
use App\Controllers\ProductController;
use App\Services\ProductService;

// Initialize Slim app
$app = AppFactory::create();
$secretKey = "my-secret-key"; // Use env variable in production
$unprotectedRoutes = ['/api/auth/register', '/api/auth/login'];
$jwtMiddleware = new JwtMiddleware($secretKey, $unprotectedRoutes);
$app->addRoutingMiddleware();
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

// --- Unprotected Routes (Register & Login) ---
$app->post('/api/auth/register', function (Request $request, Response $response) use ($secretKey) {
    try {
        $registrationData = json_decode($request->getBody()->getContents(), true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($registrationData)) {
            $errorBody = json_encode(['error' => 'Invalid JSON data provided.']);
            $response->getBody()->write($errorBody);
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
        $database = new db();
        $authController = new AuthController($database, $secretKey);
        $result = $authController->register($registrationData);
        $response->getBody()->write(json_encode($result));
        return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
    } catch (\Throwable $e) {
        $errorBody = json_encode(['error' => 'Server error: ' . $e->getMessage()]);
        $response->getBody()->write($errorBody);
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    }
});

$app->post('/api/auth/login', function (Request $request, Response $response) use ($secretKey) {
    try {
        $credentials = json_decode($request->getBody()->getContents(), true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($credentials)) {
            $errorBody = json_encode(['error' => 'Invalid JSON data provided for login.']);
            $response->getBody()->write($errorBody);
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
        $database = new db();
        $authController = new AuthController($database, $secretKey);
        $result = $authController->login($credentials);
        $response->getBody()->write(json_encode($result));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    } catch (\Throwable $e) {
        $errorBody = json_encode(['error' => 'Server error: ' . $e->getMessage()]);
        $response->getBody()->write($errorBody);
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    }
});

// --- Protected Routes (JWT required) ---
$app->group('/api', function (RouteCollectorProxy $group) {
    // GET /api/products
    $group->get('/products', function (Request $request, Response $response) {
        $database = new db();
        $controller = new ProductController($database);
        $products = $controller->index();
        $response->getBody()->write(json_encode($products));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    });

    // POST /api/products
    $group->post('/products', function (Request $request, Response $response) {
        $database = new db();
        $controller = new ProductController($database);
        $data = json_decode($request->getBody()->getContents(), true);
        $result = $controller->store($data);
        $response->getBody()->write(json_encode($result));
        return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
    });

    // PUT /api/products/{id}
    $group->put('/products/{id}', function (Request $request, Response $response, array $args) {
        $database = new db();
        $controller = new ProductController($database);
        $id = (int)$args['id'];
        $data = json_decode($request->getBody()->getContents(), true);
        $result = $controller->update($id, $data);
        $response->getBody()->write(json_encode($result));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    });

    // DELETE /api/products/{id}
    $group->delete('/products/{id}', function (Request $request, Response $response, array $args) {
        $database = new db();
        $controller = new ProductController($database);
        $id = (int)$args['id'];
        $result = $controller->destroy($id);
        $response->getBody()->write(json_encode($result));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    });

    // Add more routes as needed (e.g., inventory, suppliers, etc.)
})->add($jwtMiddleware);

// --- Global CORS Middleware ---
$app->add(function (Request $request, RequestHandler $handler): Response {
    $response = $handler->handle($request);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
});

$app->run();