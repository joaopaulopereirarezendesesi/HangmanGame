<?php

namespace handlers;

require_once __DIR__ . "/../config/config.php";

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use models\UserModel;
use tools\Utils;
use Exception;

/**
 * Classe responsável por gerar e validar tokens JWT.
 */
class HandlerJwt
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
    public static function generateToken(
        array $payload,
        int $expTime = 3600
    ): ?string {
        try {
            $issuedAt = time();
            $payload["iat"] = $issuedAt;
            $payload["exp"] = $issuedAt + $expTime;

            return JWT::encode($payload, self::$secret, self::$alg);
        } catch (Exception $e) {
            Utils::debug_log(
                [
                    "coreErrorJwtHandler-generateToken" => $e->getMessage(),
                ],
                "error"
            );
            Utils::jsonResponse(["error" => "Internal server error"], 500);

            return null;
        }
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
            $decoded = JWT::decode(Utils::decrypt($token), new Key(self::$secret, self::$alg));

            $userModel = new UserModel();
            $data =  $userModel->getUserById($decoded->user_id);

            if (!$data) {
                Utils::jsonResponse(["error" => "unrecognized token"], 403);
            }

            if ($data['EMAIL'] !== $decoded->email) {
                Utils::jsonResponse(["error" => "unrecognized token"], 403);
            }

            if ($data['NICKNAME'] !== $decoded->nickname) {
                Utils::jsonResponse(["error" => "unrecognized token"], 403);
            }

            if ("hangman-game" !== $decoded->iss) {
                Utils::jsonResponse(["error" => "unrecognized token"], 403);
            }

            return (array) $decoded;
        } catch (Exception $e) {
            return null;
        }
    }
}
