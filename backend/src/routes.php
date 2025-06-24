<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$app = AppFactory::create();

require __DIR__ . '/middleware.php';

// Register
$app->post('/api/auth/register', function (Request $request, Response $response) use ($app) {
    $data = $request->getParsedBody();
    if (!isset($data['name']) || !isset($data['email']) || !isset($data['password']) || !isset($data['department'])) {
        $response->getBody()->write(json_encode(['error' => 'Missing required fields']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }

    $name = filter_var($data['name'], FILTER_SANITIZE_STRING);
    $email = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
    $password = password_hash($data['password'], PASSWORD_BCRYPT);
    $department = filter_var($data['department'], FILTER_SANITIZE_STRING);
    $role = in_array($data['role'], ['staff', 'manager', 'admin']) ? $data['role'] : 'staff';

    $db = $app->getContainer()->get('db');
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $response->getBody()->write(json_encode(['error' => 'Email already exists']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(409);
    }

    $stmt = $db->prepare("INSERT INTO users (name, email, password, role, department) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$name, $email, $password, $role, $department]);

    $response->getBody()->write(json_encode(['message' => 'User registered successfully', 'user_id' => $db->lastInsertId()]));
    return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
});

// Login
$app->post('/api/auth/login', function (Request $request, Response $response) use ($app) {
    $data = $request->getParsedBody();
    if (!isset($data['email']) || !isset($data['password'])) {
        $response->getBody()->write(json_encode(['error' => 'Email and password are required']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }

    $email = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
    $password = $data['password'];

    $db = $app->getContainer()->get('db');
    $stmt = $db->prepare("SELECT id, email, password, role FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $permissions = [];
        switch ($user['role']) {
            case 'staff':
                $permissions = ['view_products', 'view_stock'];
                break;
            case 'manager':
                $permissions = ['view_products', 'add_products', 'update_products', 'manage_stock'];
                break;
            case 'admin':
                $permissions = ['all_permissions', 'delete_products', 'manage_users', 'view_reports'];
                break;
        }

        $payload = [
            'user_id' => $user['id'],
            'email' => $user['email'],
            'role' => $user['role'],
            'permissions' => $permissions,
            'iat' => time(),
            'exp' => time() + 3600
        ];
        $token = JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');
        $response->getBody()->write(json_encode(['token' => $token]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    $response->getBody()->write(json_encode(['error' => 'Invalid credentials']));
    return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
});

// Get Products
$app->get('/api/products', function (Request $request, Response $response) use ($app) {
    $db = $app->getContainer()->get('db');
    $query = "SELECT * FROM products";
    $params = [];
    $category = $request->getQueryParam('category');
    $lowStock = $request->getQueryParam('low_stock');

    if ($category) {
        $query .= " WHERE category = ?";
        $params[] = $category;
    }
    if ($lowStock && $lowStock === 'true') {
        $query .= $category ? " AND" : " WHERE";
        $query .= " quantity < minimum_stock";
    }

    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $products = $stmt->fetchAll();
    $response->getBody()->write(json_encode(['products' => $products]));
    return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
});

// Add Product
$app->post('/api/products', function (Request $request, Response $response) use ($app) {
    $data = $request->getParsedBody();
    $db = $app->getContainer()->get('db');
    $stmt = $db->prepare("INSERT INTO products (name, sku, category, price, quantity, minimum_stock, supplier_id, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$data['name'], $data['sku'], $data['category'], $data['price'], $data['quantity'], $data['minimum_stock'], $data['supplier_id'], $data['description']]);
    $response->getBody()->write(json_encode(['message' => 'Product added successfully', 'product_id' => $db->lastInsertId()]));
    return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
});

// Update Product
$app->put('/api/products/{id}', function (Request $request, Response $response, $args) use ($app) {
    $id = $args['id'];
    $data = $request->getParsedBody();
    $db = $app->getContainer()->get('db');
    $stmt = $db->prepare("UPDATE products SET name = ?, quantity = ?, price = ?, minimum_stock = ? WHERE id = ?");
    $stmt->execute([$data['name'], $data['quantity'], $data['price'], $data['minimum_stock'], $id]);
    $response->getBody()->write(json_encode(['message' => 'Product updated successfully']));
    return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
});

// Delete Product
$app->delete('/api/products/{id}', function (Request $request, Response $response, $args) use ($app) {
    $id = $args['id'];
    $db = $app->getContainer()->get('db');
    $stmt = $db->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $response->getBody()->write(json_encode(['message' => 'Product deleted successfully']));
    return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
});
?>