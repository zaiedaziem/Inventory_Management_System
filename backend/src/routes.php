<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$app->post('/login', function ($request, $response) {
    $data = json_decode((string)$request->getBody(), true);
    $username = $data['username'];
    $password = $data['password'];

    $stmt = $this->get('db')->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && ($password == $user['password'])) {
        $payload = [
            'sub' => $user['id'],
            'iat' => time(),
            'exp' => time() + 3600
        ];
        $token = JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');
        $response->getBody()->write(json_encode(['token' => $token]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    return $response->withStatus(401);
});
