<?php

namespace core;

require_once __DIR__ . '/../config/config.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtHandler
{
    private static string $secret = JWT_SECRET;
    private static string $alg = "HS256";

    public static function generateToken(array $payload, int $expTime = 3600): string
    {
        $issuedAt = time();
        $payload['iat'] = $issuedAt;
        $payload['exp'] = $issuedAt + $expTime;

        return JWT::encode($payload, self::$secret, self::$alg);
    }

    public static function validateToken(string $token): ?array
    {
        try {
            $decoded = JWT::decode($token, new Key(self::$secret, self::$alg));
            return (array) $decoded;
        } catch (\Exception $e) {
            return null;
        }
    }
}
