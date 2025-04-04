<?php

namespace handlers;

require_once __DIR__ . "/../config/config.php";

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use models\UserModel;
use tools\Utils;
use Exception;

/**
 * Class responsible for generating and validating JWT tokens.
 */
class HandlerJwt
{
    /**
     * @var string The secret key used to sign the JWT tokens.
     */
    private static string $secretKey = JWT_SECRET;

    /**
     * @var string The signing algorithm used, which is HS256 (HMAC with SHA-256).
     */
    private static string $algorithm = "HS256";

    /**
     * Generates a new JWT token.
     *
     * @param array $payload Data to be included in the token (e.g., user information).
     * @param int $expirationTime Token expiration time in seconds. The default is 3600 seconds (1 hour).
     * @return ?string The generated JWT token.
     */
    public static function generateToken(
        array $payload,
        int $expirationTime = 3600
    ): ?string {
        try {
            $issuedAt = time();
            $payload["iat"] = $issuedAt;
            $payload["exp"] = $issuedAt + $expirationTime;

            return JWT::encode($payload, self::$secretKey, self::$algorithm);
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
     * Validates and decodes a JWT token.
     *
     * @param string $token The JWT token to be validated.
     * @return ?array Returns the decoded data from the token if valid, or null otherwise.
     */
    public static function validateToken(string $token): ?array
    {
        try {
            $decodedToken = JWT::decode(Utils::decrypt($token), new Key(self::$secretKey, self::$algorithm));

            $userModel = new UserModel();
            $userData =  $userModel->getUserById($decodedToken->user_id);

            if (!$userData) {
                Utils::jsonResponse(["error" => "unrecognized token"], 403);
            }

            if ($userData['EMAIL'] !== $decodedToken->email) {
                Utils::jsonResponse(["error" => "unrecognized token"], 403);
            }

            if ($userData['NICKNAME'] !== $decodedToken->nickname) {
                Utils::jsonResponse(["error" => "unrecognized token"], 403);
            }

            if ("hangman-game" !== $decodedToken->iss) {
                Utils::jsonResponse(["error" => "unrecognized token"], 403);
            }

            return get_object_vars($decodedToken);
        } catch (Exception $e) {
            Utils::debug_log(
                [
                    "coreErrorJwtHandler-validateToken" => $e->getMessage(),
                ],
                "error"
            );
            Utils::jsonResponse(["error" => "Internal server error"], 500);

            return null;
        }
    }
}