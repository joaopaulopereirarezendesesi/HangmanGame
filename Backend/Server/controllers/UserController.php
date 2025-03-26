<?php

namespace controllers;

require_once __DIR__ . "/../vendor/autoload.php";

use models\UserModel;
use tools\Utils;
use core\JwtHandler;
use Exception;

/**
 * Class UserController
 *
 * Responsible for user management, including creation,
 * authentication, password recovery, and session management.
 */
class UserController
{
    /** @var UserModel User model instance */
    private UserModel $userModel;

    /**
     * UserController constructor.
     *
     * Initializes the user model.
     */
    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * Lists all registered users in the system.
     */
    public function index(): void
    {
        try {
            $users = $this->userModel->getAllUsers();
            Utils::jsonResponse($users);
        } catch (Exception $e) {
            Utils::debug_log(
                [
                    "controllerErrorUser-show" => $e->getMessage(),
                ],
                "error"
            );
            Utils::jsonResponse(["error" => "Internal server error"], 500);
        }
    }

    /**
     * Displays a specific user based on the provided ID.
     *
     * @param int $id User ID.
     */
    public function show($userId): void
    {
        try {
            $user = $this->userModel->getUserById($userId);

            if ($user) {
                Utils::jsonResponse($user);
            } else {
                Utils::jsonResponse(["error" => "User not found"], 404);
            }
        } catch (Exception $e) {
            Utils::debug_log(
                [
                    "controllerErrorUser-show" => $e->getMessage(),
                ],
                "error"
            );
            Utils::jsonResponse(["error" => "Internal server error"], 500);
        }
    }

    /**
     * Creates a new user with the provided data.
     */
    public function create(): void
    {
        try {
            $data = $_POST;

            if (empty($data)) {
                Utils::jsonResponse(["error" => "No data received."], 400);
            }

            $requiredParams = [
                "nickname",
                "email",
                "password",
                "confirm_password",
            ];
            $data = Utils::validateParams($data, $requiredParams);

            if (!filter_var($data["email"], FILTER_VALIDATE_EMAIL)) {
                Utils::jsonResponse(["error" => "Invalid email format"], 400);
            }

            if (!Utils::validatePassword($data["password"])) {
                Utils::jsonResponse(
                    [
                        "error" =>
                        "The password must be at least 8 characters long, contain an uppercase letter, a lowercase letter, a number, and a special character.",
                    ],
                    400
                );
            }

            if ($data["password"] !== $data["confirm_password"]) {
                Utils::jsonResponse(["error" => "Passwords do not match"], 400);
            }

            $this->userModel->createUser(
                filter_var($data["nickname"], FILTER_SANITIZE_FULL_SPECIAL_CHARS),
                strval($data["email"]),
                strval($data["password"])
            );
            Utils::jsonResponse(
                ["message" => "User successfully created!"],
                201
            );
        } catch (Exception $e) {
            Utils::debug_log(
                [
                    "controllerErrorUser-create" => $e->getMessage(),
                ],
                "error"
            );
            Utils::jsonResponse(["error" => "Internal server error"], 500);
        }
    }

    /**
     * Logs in the user based on the provided credentials.
     *
     * @param string|null $email User email.
     * @param string|null $password User password.
     */
    public function login(): void
    {
        try {
            $requiredParams = ["email", "password"];
            $data = Utils::validateParams($_POST, $requiredParams);
            $email = strtolower(trim($data["email"]));
            $password = $data["password"];

            $user = $this->userModel->getUserByEmail($email);

            if ($user && password_verify($password, $user["PASSWORD"])) {
                $token = JwtHandler::generateToken([
                    "user_id" => $user["ID_U"],
                    "email" => $user["EMAIL"],
                    "nickname" => $user["NICKNAME"],
                    "iss" => "hangman-game",
                    "aud" => "user",
                    "sub" => "login",
                    "exp" => time() + 3600,
                    "iat" => time(),
                ]);

                session_start();
                $_SESSION["user_id"] = $user["ID_U"];
                $_SESSION["nickname"] = $user["NICKNAME"];
                header(
                    "Set-Cookie: jwt=" .
                        Utils::encrypt($token) .
                        "; Path=/; Secure; HttpOnly; SameSite=None; Partitioned; Expires=" .
                        gmdate("D, d M Y H:i:s T", time() + 3600)
                );
                setcookie("nickname", $user["NICKNAME"], [
                    "expires" => time() + 86400 * 30,
                    "path" => "/",
                    "domain" => "",
                    "secure" => true,
                    "httponly" => false,
                    "samesite" => "Strict"
                ]);

                Utils::jsonResponse([
                    "message" => "Login successful",
                ]);
            } else {
                Utils::jsonResponse(["error" => "Invalid credentials"], 400);
            }
        } catch (Exception $e) {
            Utils::debug_log(
                [
                    "controllerErrorUser-login" => $e->getMessage(),
                ],
                "error"
            );
            Utils::jsonResponse(["error" => "Internal server error"], 500);
        }
    }

    /**
     * Retrieves the rooms that a user organizes based on their ID.
     */
    public function getRoomOrganizer(): void
    {
        try {
            $userId = Utils::getUserIdFromToken();
            if (!$userId) {
                Utils::jsonResponse(["error" => "Token not provided"], 403);
            }

            $id_o = $userId;

            Utils::jsonResponse([
                "rooms" => $this->userModel->getRoomOrganizer($id_o),
            ]);
        } catch (Exception $e) {
            Utils::debug_log(
                [
                    "controllerErrorUser-getRoomOrganizer" => $e->getMessage(),
                ],
                "error"
            );
            Utils::jsonResponse(["error" => "Internal server error"], 500);
        }
    }
}
