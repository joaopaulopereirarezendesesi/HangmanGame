<?php

namespace models;

use tools\Utils;
use Exception;

class UserModel
{
    private Utils $utils;

    public function __construct()
    {
        $this->utils = new Utils();
    }

    public function getAllUsers(): ?array
    {
        try {
            $usersQuery = "SELECT * FROM users";
            $usersResult = $this->utils->executeQuery($usersQuery, [], true);

            return $usersResult[0] ?? null;
        } catch (Exception $e) {
            Utils::debug_log(
                [
                    "modelsErrorUser-getAllUsers" => $e->getMessage(),
                ],
                "error"
            );
            Utils::jsonResponse(["error" => "Internal server error"], 500);

            return null;
        }
    }

    public function getUserById(string $userId): ?array
    {
        try {
            $userQuery = "SELECT * FROM users WHERE ID_U = :userId";
            $queryParameters = [":userId" => $userId];
            $userResult = $this->utils->executeQuery($userQuery, $queryParameters, true);

            return $userResult[0] ?? null;
        } catch (Exception $e) {
            Utils::debug_log(
                [
                    "modelsErrorUser-getUserById" => $e->getMessage(),
                ],
                "error"
            );
            Utils::jsonResponse(["error" => "Internal server error"], 500);

            return null;
        }
    }

    public function createUser(
        string $nickname,
        string $email,
        string $password,
        ?string $userPhoto = null
    ): ?string {
        try {
            $existingUserQuery =
                "SELECT * FROM users WHERE email = :email OR NICKNAME = :nickname";
            $queryParameters = [":email" => $email, ":nickname" => $nickname];
            $existingUserData = $this->utils->executeQuery($existingUserQuery, $queryParameters, true);

            if ($existingUserData) {
                if ($existingUserData[0]["email"] === $email) {
                    Utils::jsonResponse(
                        ["error" => "Email já utilizado!"],
                        400
                    );
                }
                if ($existingUserData[0]["NICKNAME"] === $nickname) {
                    Utils::jsonResponse(
                        ["error" => "Nickname já utilizado!"],
                        400
                    );
                }
            }

            $createUserQuery =
                "INSERT INTO users (ID_U, NICKNAME, EMAIL, PASSWORD, ONLINE, PHOTO, TFA) VALUES (UUID(), :nickname, :email, :password, 2, :userPhoto, 0)";
            $createUserParameters = [
                ":nickname" => $nickname,
                ":email" => $email,
                ":password" => password_hash($password, PASSWORD_ARGON2ID),
                ":userPhoto" => $userPhoto,
            ];

            $this->utils->executeQuery($createUserQuery, $createUserParameters);

            $lastInsertIdQuery = "SELECT LAST_INSERT_ID()";
            $lastInsertIdResult = $this->utils->executeQuery($lastInsertIdQuery, [], true);

            return $lastInsertIdResult[0]["LAST_INSERT_ID()"];
        } catch (Exception $e) {
            Utils::debug_log(
                [
                    "modelsErrorUser-createUser" => $e->getMessage(),
                ],
                "error"
            );
            Utils::jsonResponse(["error" => "Internal server error"], 500);

            return null;
        }
    }

    public function getUserByEmail(string $email): ?array
    {
        try {
            $userEmailQuery = "SELECT * FROM users WHERE email = :email";
            $queryParameters = [":email" => $email];
            $userEmailResult = $this->utils->executeQuery($userEmailQuery, $queryParameters, true);

            return $userEmailResult[0] ?? null;
        } catch (Exception $e) {
            Utils::debug_log(
                [
                    "modelsErrorUser-getUserByEmail" => $e->getMessage(),
                ],
                "error"
            );
            Utils::jsonResponse(["error" => "Internal server error"], 500);

            return null;
        }
    }

    public function getRoomOrganizer(string $organizerId): ?array
    {
        try {
            $organizerQuery = "SELECT * FROM users WHERE ID_U = :organizerId";
            $queryParameters = [":organizerId" => $organizerId];
            $organizerResult = $this->utils->executeQuery($organizerQuery, $queryParameters, true);

            return $organizerResult[0] ?? null;
        } catch (Exception $e) {
            Utils::debug_log(
                [
                    "modelsErrorUser-getRoomOrganizer" => $e->getMessage(),
                ],
                "error"
            );
            Utils::jsonResponse(["error" => "Internal server error"], 500);

            return null;
        }
    }
}
