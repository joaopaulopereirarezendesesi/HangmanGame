<?php

namespace controllers;

require_once __DIR__ . '/../vendor/autoload.php';

use models\UserModel;
use tools\Utils;
use core\JwtHandler;
use Exception;

class UserController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $users = $this->userModel->getAllUsers();
        Utils::jsonResponse($users, 200);
    }

    public function show($id)
    {
        $user = $this->userModel->getUserById($id);
        if ($user) {
            Utils::jsonResponse($user, 200);
        } else {
            Utils::errorResponse("Usuário não encontrado", 404);
        }
    }

    public function create()
    {
        $json = file_get_contents("php://input");
        if (empty($json)) {
            Utils::debug_log("Nenhum dado JSON recebido. Tentando $_POST...");
            $data = $_POST;
        } else {
            $data = json_decode($json, true);
        }

        if (!$data) {
            Utils::debug_log("F, estou no !data");
            Utils::errorResponse("Erro ao processar os dados. Envie como JSON.", 400);
            return;
        }

        $requiredParams = ['nickname', 'email', 'password', 'confirm_password'];
        $data = Utils::validateParams($data, $requiredParams);

        if (!$this->validateEmail($data['email'])) {
            Utils::debug_log("F, estou no validaemail");
            Utils::errorResponse("Formato de e-mail inválido", 400);
            return;
        }

        if (!Utils::validatePassword($data['password'])) {
            Utils::debug_log("F, estou no password");
            Utils::errorResponse("A senha deve ter pelo menos 8 caracteres, conter uma letra maiúscula, uma minúscula, um número e um caractere especial.", 400);
            return;
        }

        if ($data['password'] !== $data['confirm_password']) {
            Utils::debug_log("F, estou no conecidencia de senhas");
            Utils::errorResponse("As senhas não coincidem", 400);
            return;
        }

        $this->userModel->createUser($data['nickname'], $data['email'], $data['password']);
        Utils::jsonResponse(['message' => 'Usuário criado com sucesso!'], 201);
        $this->login($data['email'], $data['password']);
    }

    public function recoverPassword()
    {
        $requiredParams = ['id', 'oldPassword', 'newPassword', 'c_newPassword'];
        $data = Utils::validateParams($_POST, $requiredParams);

        $password = $this->userModel->getPasswordbyId($data['id']);

        if ($password !== $data['oldPassword']) {
            Utils::errorResponse("Sua senha antiga não corresponde à senha inputada", 400);
            return;
        }
    }

    public function login($email = null, $password = null)
    {
        if ($email === null || $password === null) {
            $requiredParams = ['email', 'password'];
            $data = Utils::validateParams($_POST, $requiredParams);
            $email = strtolower(trim($data['email']));
            $password = $data['password'];
        } else {
            $email = strtolower(trim($email));
        }

        $user = $this->userModel->getUserByEmail($email);

        if ($user && password_verify($password, $user['PASSWORD'])) {

            $token = JwtHandler::generateToken([
                'user_id' => $user['ID_U'],
                'email' => $user['EMAIL'],
                'nickname' => $user['NICKNAME'],
                'password' => $user['PASSWORD'],
            ]);

            session_start();
            $_SESSION['user_id'] = $user['ID_U'];
            $_SESSION['nickname'] = $user['NICKNAME'];
            setcookie('token', $token, time() + 3600, '/', '', true, true);

            setcookie('user_id', $user['ID_U'], time() + (86400 * 30), '/', '', true, false);
            setcookie('nickname', $user['NICKNAME'], time() + (86400 * 30), '/', '', true, false);

            Utils::jsonResponse([
                'message' => 'Login bem-sucedido',
                'user_id' => $user['ID_U']
            ], 200);
        } else {
            Utils::errorResponse("Credenciais inválidas", 401);
        }
    }

    public function logout()
    {
        session_start();
        session_unset();
        session_destroy();

        setcookie('user_id', '', time() - 3600, '/');
        setcookie('nickname', '', time() - 3600, '/');
        setcookie('token', '', time() - 3600, '/');

        Utils::jsonResponse(['message' => 'Logout bem-sucedido'], 200);
    }

    public function isLoggedIn()
    {
        session_start();

        if (isset($_SESSION['user_id'])) {
            return true;
        }

        if (isset($_COOKIE['user_id']) && isset($_COOKIE['nickname'])) {
            $user = $this->userModel->getUserById($_COOKIE['user_id']);

            if ($user && $user['NICKNAME'] === $_COOKIE['nickname']) {
                $_SESSION['user_id'] = $user['ID_U'];
                $_SESSION['nickname'] = $user['NICKNAME'];
                return true;
            }
        }

        return false;
    }

    function validateEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public function getRoomOrganizer()
    {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!isset($input['id_o'])) {
            throw new Exception('ID do organizador não fornecido.');
        }

        $id_o = $input['id_o'];

        Utils::jsonResponse([
            'rooms' => $this->userModel->getRoomOrganizer($id_o)
        ]);
    }
}
