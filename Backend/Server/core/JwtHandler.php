<?php

namespace core; // Define o namespace como "core", indicando que a classe JwtHandler faz parte do núcleo do sistema

// Inclui o arquivo de configuração, onde provavelmente a chave secreta do JWT (JWT_SECRET) está definida
require_once __DIR__ . '/../config/config.php';

// Importa as classes necessárias para trabalhar com JWT usando a biblioteca Firebase JWT
use Firebase\JWT\JWT; // Classe principal para criar e decodificar JWT
use Firebase\JWT\Key;  // Classe usada para definir a chave de assinatura do JWT

class JwtHandler
{
    // Define a chave secreta para a assinatura dos tokens JWT, que é carregada das configurações (JWT_SECRET)
    private static string $secret = JWT_SECRET;
    // Define o algoritmo de assinatura utilizado, que é o HS256 (HMAC com SHA-256)
    private static string $alg = "HS256";

    /**
     * Gera um novo token JWT.
     *
     * @param array $payload Dados a serem incluídos no token (por exemplo, informações do usuário)
     * @param int $expTime Tempo de expiração do token em segundos (padrão: 3600 segundos = 1 hora)
     * @return string O token JWT gerado
     */
    public static function generateToken(array $payload, int $expTime = 3600): string
    {
        $issuedAt = time(); // Registra o momento de emissão do token
        $payload['iat'] = $issuedAt; // Adiciona a data de emissão ao payload
        $payload['exp'] = $issuedAt + $expTime; // Adiciona a data de expiração ao payload

        // Codifica o payload em um token JWT usando a chave secreta e o algoritmo HS256
        return JWT::encode($payload, self::$secret, self::$alg);
    }

    /**
     * Valida e decodifica um token JWT.
     *
     * @param string $token O token JWT a ser validado
     * @return array|null Retorna os dados decodificados se o token for válido, ou null caso contrário
     */
    public static function validateToken(string $token): ?array
    {
        try {
            // Decodifica o token usando a chave secreta e o algoritmo
            $decoded = JWT::decode($token, new Key(self::$secret, self::$alg));
            return (array) $decoded; // Retorna o payload do token decodificado
        } catch (\Exception $e) {
            // Em caso de erro (token inválido ou expirado), retorna null
            return null;
        }
    }
}
