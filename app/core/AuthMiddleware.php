<?php

namespace App\Core;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthMiddleware
{
    private static function abort(string $message, int $code): never
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode(['error' => $message]);
        exit;
    }

    public static function handle(): array
    {
        $headers    = getallheaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? null;

        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            self::abort('Token nao fornecido', 401);
        }

        try {
            $secret  = $_ENV['JWT_SECRET'] ?? 'secret';
            $decoded = JWT::decode($matches[1], new Key($secret, 'HS256'));

            return [
                'sub' => (int) $decoded->sub
            ];
        } catch (\Exception $e) {
            self::abort('Token invalido ou expirado', 401);
        }
    }
}