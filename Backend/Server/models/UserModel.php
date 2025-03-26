<?php

namespace models;

use tools\Utils;
use Exception;

class UserModel
{
    private $utils;

    public function __construct()
    {
        $this->utils = new Utils();
    }

    public function getAllUsers(): ?array
    {
        try {
            $query = "SELECT * FROM users";
            $result = $this->utils->executeQuery($query, [], true);

            return $result[0] ?? null;
        } catch (Exception $e) {
            Utils::debug_log(
                [
                    "modelsErrorUser-getAllUsers" => $e->getMessage(),
                ],
                "error"
            );
            Utils::jsonResponse(["error" => "Internal server error"], 500);
            exit();
        }
    }

    public function getUserById(string $userId): ?array
    {
        try {
            $query = "SELECT * FROM users WHERE ID_U = :id";
            $params = [":id" => $userId];
            $result = $this->utils->executeQuery($query, $params, true);

            return $result[0] ?? null;
        } catch (Exception $e) {
            Utils::debug_log(
                [
                    "modelsErrorUser-getUserById" => $e->getMessage(),
                ],
                "error"
            );
            Utils::jsonResponse(["error" => "Internal server error"], 500);
            exit();
        }
    }

    public function createUser(
        string $nickname,
        string $email,
        string $password
    ): string {
        try {
            $query =
                "SELECT * FROM users WHERE email = :email OR NICKNAME = :nickname";
            $params = [":email" => $email, ":nickname" => $nickname];
            $existingUser = $this->utils->executeQuery($query, $params, true);

            if ($existingUser) {
                if ($existingUser[0]["email"] === $email) {
                    Utils::jsonResponse(
                        ["error" => "Email já utilizado!"],
                        400
                    );
                }
                if ($existingUser[0]["NICKNAME"] === $nickname) {
                    Utils::jsonResponse(
                        ["error" => "Nickname já utilizado!"],
                        400
                    );
                }
            }

            $query =
                "INSERT INTO users (ID_U, NICKNAME, EMAIL, PASSWORD, ONLINE) VALUES (UUID(), :nickname, :email, :password, 1)";
            $params = [
                ":nickname" => $nickname,
                ":email" => $email,
                ":password" => password_hash($password, PASSWORD_ARGON2ID),
            ];

            $this->utils->executeQuery($query, $params);

            return $this->utils->executeQuery(
                "SELECT LAST_INSERT_ID()",
                [],
                true
            )[0]["LAST_INSERT_ID()"];
        } catch (Exception $e) {
            Utils::debug_log(
                [
                    "modelsErrorUser-createUser" => $e->getMessage(),
                ],
                "error"
            );
            Utils::jsonResponse(["error" => "Internal server error"], 500);
            exit();
        }
    }

    public function getUserByEmail(string $email): ?array
    {
        try {
            $query = "SELECT * FROM users WHERE email = :email";
            $params = [":email" => $email];
            $result = $this->utils->executeQuery($query, $params, true);

            return $result[0] ?? null;
        } catch (Exception $e) {
            Utils::debug_log(
                [
                    "modelsErrorUser-getUserByEmail" => $e->getMessage(),
                ],
                "error"
            );
            Utils::jsonResponse(["error" => "Internal server error"], 500);
            exit();
        }
    }

    public function getRoomOrganizer(string $id_o): ?array
    {
        try {
            $query = "SELECT * FROM users WHERE ID_U = :id_o";
            $params = [":id_o" => $id_o];
            $result = $this->utils->executeQuery($query, $params, true);

            return $result[0] ?? null;
        } catch (Exception $e) {
            Utils::debug_log(
                [
                    "modelsErrorUser-getRoomOrganizer" => $e->getMessage(),
                ],
                "error"
            );
            Utils::jsonResponse(["error" => "Internal server error"], 500);
            exit();
        }
    }
}
