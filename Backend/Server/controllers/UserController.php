<?php

namespace controllers;

require_once __DIR__ . "/../vendor/autoload.php";

use models\UserModel;
use tools\Utils;
use core\JwtHandler;
use Exception;

/**
 * Classe UserController
 *
 * Responsável pelo controle de usuários, incluindo operações de criação,
 * autenticação, recuperação de senha e gerenciamento de sessões.
 */
class UserController
{
    /** @var UserModel Instância do modelo de usuário */
    private $userModel;

    /**
     * Construtor da classe UserController.
     *
     * Inicializa o modelo de usuários.
     */
    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * Lista todos os usuários registrados no sistema.
     */
    public function index(): void
    {
        $users = $this->userModel->getAllUsers();
        Utils::jsonResponse($users, 200);
    }

    /**
     * Exibe um usuário específico com base no ID fornecido.
     *
     * @param int $id ID do usuário.
     */
    public function show($id): void
    {
        try {
            $user = $this->userModel->getUserById($id);

            if ($user) {
                Utils::jsonResponse($user, 200);
            } else {
                Utils::errorResponse("Usuário não encontrado", 404);
            }
        } catch (Exception $e) {
            Utils::jsonResponse(["error" => $e->getMessage()], 500);
        }
    }

    /**
     * Cria um novo usuário com os dados fornecidos.
     */
    public function create(): void
    {
        try {
            $data = $_POST;

            if (empty($data)) {
                Utils::jsonResponse(["error" => "Nenhum dado recebido."], 400);
                return;
            }

            $requiredParams = [
                "nickname",
                "email",
                "password",
                "confirm_password",
            ];
            $data = Utils::validateParams($data, $requiredParams);

            if (!filter_var($data["email"], FILTER_VALIDATE_EMAIL)) {
                Utils::jsonResponse(
                    ["error" => "Formato de e-mail inválido"],
                    400
                );
                return;
            }

            if (!Utils::validatePassword($data["password"])) {
                Utils::jsonResponse(
                    [
                        "error" =>
                            "A senha deve ter pelo menos 8 caracteres, conter uma letra maiúscula, uma minúscula, um número e um caractere especial.",
                    ],
                    400
                );
                return;
            }

            if ($data["password"] !== $data["confirm_password"]) {
                Utils::jsonResponse(
                    ["error" => "As senhas não coincidem"],
                    400
                );
                return;
            }

            $this->userModel->createUser(
                $data["nickname"],
                $data["email"],
                $data["password"]
            );
            Utils::jsonResponse(
                ["message" => "Usuário criado com sucesso!"],
                201
            );
        } catch (Exception $e) {
            Utils::jsonResponse(["error" => $e->getMessage()], 500);
        }
    }

    /**
     * Realiza o login do usuário com base nas credenciais fornecidas.
     *
     * @param string|null $email Email do usuário.
     * @param string|null $password Senha do usuário.
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
                ]);

                session_start();
                $_SESSION["user_id"] = $user["ID_U"];
                $_SESSION["nickname"] = $user["NICKNAME"];
                setcookie("token", $token, time() + 3600, "/", "", true, true);
                setcookie(
                    "user_id",
                    $user["ID_U"],
                    time() + 86400 * 30,
                    "/",
                    "",
                    true,
                    false
                );
                setcookie(
                    "nickname",
                    $user["NICKNAME"],
                    time() + 86400 * 30,
                    "/",
                    "",
                    true,
                    false
                );

                Utils::jsonResponse(
                    [
                        "message" => "Login bem-sucedido",
                        "user_id" => $user["ID_U"],
                    ],
                    200
                );
            } else {
                Utils::jsonResponse(["error" => "Credenciais inválidas"], 400);
            }
        } catch (Exception $e) {
            Utils::jsonResponse(["error" => $e->getMessage()], 500);
        }
    }

    /**
     * Obtém as salas que um usuário organiza com base no seu ID.
     */
    public function getRoomOrganizer(): void
    {
        try {
            $id = Utils::getUserIdFromToken();
            if (!$id) {
                throw new Exception("Token não fornecido.");
            }

            $id_o = $id;

            Utils::jsonResponse([
                "rooms" => $this->userModel->getRoomOrganizer($id_o),
            ]);
        } catch (Exception $e) {
            Utils::jsonResponse(["error" => $e->getMessage()], 500);
        }
    }
}
