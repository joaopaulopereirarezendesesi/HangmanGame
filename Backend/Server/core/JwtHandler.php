<?php

namespace core;

require_once __DIR__ . '/../config/config.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

/**
 * Classe responsável por gerar e validar tokens JWT.
 */
class JwtHandler
{
    /**
     * @var string A chave secreta usada para assinar os tokens JWT.
     */
    private static string $secret = JWT_SECRET;

    /**
     * @var string O algoritmo de assinatura utilizado, que é HS256 (HMAC com SHA-256).
     */
    private static string $alg = "HS256";

    /**
     * Gera um novo token JWT.
     *
     * @param array $payload Dados a serem incluídos no token (por exemplo, informações do usuário).
     * @param int $expTime Tempo de expiração do token em segundos. O padrão é 3600 segundos (1 hora).
     * @return string O token JWT gerado.
     */
    public static function generateToken(array $payload, int $expTime = 3600): string
    {
        $issuedAt = time();
        $payload['iat'] = $issuedAt;
        $payload['exp'] = $issuedAt + $expTime;

        return JWT::encode($payload, self::$secret, self::$alg);
    }

    /**
     * Valida e decodifica um token JWT.
     *
     * @param string $token O token JWT a ser validado.
     * @return array|null Retorna os dados decodificados do token se válido, ou null caso contrário.
     */
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
