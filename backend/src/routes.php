<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$app->add(function (Request $request, Response $response, callable $next) {
    $authHeader = $request->getHeaderLine('Authorization');
    if (strpos($request->getUri()->getPath(), '/api/auth') !== false) {
        return $next($request, $response);
    }
    
    if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        $response->getBody()->write(json_encode(['error' => 'Token not provided']));
        return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
    }
    
    try {
        $jwt = $matches[1];
        $decoded = JWT::decode($jwt, new Key($this->get('jwt_secret'), 'HS256'));
        $request = $request->withAttribute('user', $decoded->data);
        return $next($request, $response);
    } catch (Exception $e) {
        $response->getBody()->write(json_encode(['error' => 'Invalid token']));
        return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
    }
});

// POST /api/auth/register
$app->post('/api/auth/register', function (Request $request, Response $response, array $args) {
    $data = $request->getParsedBody();
    
    try {
        $pdo = $this->get('db');
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, department) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['name'],
            $data['email'],
            $hashedPassword,
            $data['role'] ?? 'staff',
            $data['department'] ?? null
        ]);
        
        $response->getBody()->write(json_encode(['message' => 'User registered successfully']));
        return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
    } catch (Exception $e) {
        $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }
});

// POST /api/auth/login
$app->post('/api/auth/login', function (Request $request, Response $response, array $args) {
    $data = $request->getParsedBody();
    
    try {
        $pdo = $this->get('db');
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$data['email']]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($data['password'], $user['password'])) {
            $payload = [
                'iat' => time(),
                'exp' => time() + (60 * 60 * 24), // 24 hours
                'data' => [
                    'id' => $user['id'],
                    'email' => $user['email'],
                    'role' => $user['role']
                ]
            ];
            
            $jwt = JWT::encode($payload, $this->get('jwt_secret'), 'HS256');
            
            $response->getBody()->write(json_encode(['token' => $jwt]));
            return $response->withHeader('Content-Type', 'application/json');
        }
        
        $response->getBody()->write(json_encode(['error' => 'Invalid credentials']));
        return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
    } catch (Exception $e) {
        $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }
});

// GET /api/products
$app->get('/api/products', function (Request $request, Response $response, array $args) {
    try {
        $pdo = $this->get('db');
        $stmt = $pdo->query("SELECT p.*, s.name as supplier_name FROM products p LEFT JOIN suppliers s ON p.supplier_id = s.id");
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $response->getBody()->write(json_encode($products));
        return $response->withHeader('Content-Type', 'application/json');
    } catch (Exception $e) {
        $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }
});

// POST /api/products
$app->post('/api/products', function (Request $request, Response $response, array $args) {
    $data = $request->getParsedBody();
    
    try {
        $pdo = $this->get('db');
        $stmt = $pdo->prepare("INSERT INTO products (name, sku, category, price, quantity, minimum_stock, supplier_id, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['name'],
            $data['sku'],
            $data['category'],
            $data['price'],
            $data['quantity'],
            $data['minimum_stock'],
            $data['supplier_id'],
            $data['description']
        ]);
        
        $response->getBody()->write(json_encode(['message' => 'Product added successfully']));
        return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
    } catch (Exception $e) {
        $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }
});

// PUT /api/products/{id}
$app->put('/api/products/{id}', function (Request $request, Response $response, array $args) {
    $data = $request->getParsedBody();
    
    try {
        $pdo = $this->get('db');
        $stmt = $pdo->prepare("UPDATE products SET name = ?, sku = ?, category = ?, price = ?, quantity = ?, minimum_stock = ?, supplier_id = ?, description = ? WHERE id = ?");
        $stmt->execute([
            $data['name'],
            $data['sku'],
            $data['category'],
            $data['price'],
            $data['quantity'],
            $data['minimum_stock'],
            $data['supplier_id'],
            $data['description'],
            $args['id']
        ]);
        
        $response->getBody()->write(json_encode(['message' => 'Product updated successfully']));
        return $response->withHeader('Content-Type', 'application/json');
    } catch (Exception $e) {
        $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }
});

// DELETE /api/products/{id}
$app->delete('/api/products/{id}', function (Request $request, Response $response, array $args) {
    try {
        $pdo = $this->get('db');
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$args['id']]);
        
        $response->getBody()->write(json_encode(['message' => 'Product deleted successfully']));
        return $response->withHeader('Content-Type', 'application/json');
    } catch (Exception $e) {
        $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }
});