<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../tools/helpers.php';

class UserController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new \models\UserModel();
    }

    public function index()
    {
        $users = $this->userModel->getAllUsers();
        jsonResponse($users, 200);
    }

    public function show($id)
    {
        $user = $this->userModel->getUserById($id);
        if ($user) {
            jsonResponse($user, 200);
        } else {
            errorResponse("Usuário não encontrado", 404);
        }
    }

    public function create()
    {
        $requiredParams = ['nickname', 'email', 'password', 'confirm_password'];
        $data = validateParams($_POST, $requiredParams);

        if (!validateEmail($data['email'])) {
            errorResponse("Formato de e-mail inválido", 400);
        }

        if (!validatePassword($data['password'])) {
            errorResponse("A senha deve ter pelo menos 8 caracteres, conter uma letra maiúscula, uma minúscula, um número e um caractere especial.", 400);
        }

        if ($data['password'] !== $data['confirm_password']) {
            errorResponse("As senhas não coincidem", 400);
        }

        $this->userModel->createUser($data['nickname'], $data['email'], $data['password']);
        jsonResponse(['message' => 'Usuário criado com sucesso!'], 201);
    }

    public function login()
    {
        $requiredParams = ['email', 'password'];
        $data = validateParams($_POST, $requiredParams);

        $email = strtolower(trim($data['email']));
        $password = $data['password'];

        $user = $this->userModel->getUserByEmail($email);

        if ($user && password_verify($password, $user['PASSWORD'])) {
            session_start();
            $_SESSION['user_id'] = $user['ID_U'];
            $_SESSION['nickname'] = $user['NICKNAME'];

            if (isset($_POST['remember']) && $_POST['remember'] == 'true') {
                setcookie('user_id', $user['ID_U'], time() + (86400 * 30), '/', '', true, false);
                setcookie('nickname', $user['NICKNAME'], time() + (86400 * 30), '/', '', true, false);
            }

            jsonResponse([
                'message' => 'Login bem-sucedido',
                'user_id' => $user['ID_U']
            ], 200);
        } else {
            errorResponse("Credenciais inválidas", 401);
        }
    }

    public function logout()
    {
        session_start();
        session_unset();
        session_destroy();

        setcookie('user_id', '', time() - 3600, '/');
        setcookie('nickname', '', time() - 3600, '/');

        jsonResponse(['message' => 'Logout bem-sucedido'], 200);
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
}
