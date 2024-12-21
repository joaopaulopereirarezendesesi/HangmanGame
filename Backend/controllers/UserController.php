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
        \tools\Utils::jsonResponse($users, 200);
    }

    public function show($id)
    {
        $user = $this->userModel->getUserById($id);
        if ($user) {
            \tools\Utils::jsonResponse($user, 200);
        } else {
            \tools\Utils::errorResponse("Usuário não encontrado", 404);
        }
    }

    public function create()
    {
        $requiredParams = ['nickname', 'email', 'password', 'confirm_password'];
        $data = \tools\Utils::validateParams($_POST, $requiredParams);

        if (!$this->validateEmail($data['email'])) {
            \tools\Utils::errorResponse("Formato de e-mail inválido", 400);
            return;
        }

        if (!\tools\Utils::validatePassword($data['password'])) {
            \tools\Utils::errorResponse("A senha deve ter pelo menos 8 caracteres, conter uma letra maiúscula, uma minúscula, um número e um caractere especial.", 400);
            return;
        }

        if ($data['password'] !== $data['confirm_password']) {
            \tools\Utils::errorResponse("As senhas não coincidem", 400);
            return;
        }

        $this->userModel->createUser($data['nickname'], $data['email'], $data['password']);
        \tools\Utils::jsonResponse(['message' => 'Usuário criado com sucesso!'], 201);
    }

    
    public function recoverPassword()
    {
        $requiredParams = ['id' ,'oldPassword', 'newPassword', 'c_newPassword'];
        $data = \tools\Utils::validateParams($_POST, $requiredParams);

        $password = $this->userModel->getPasswordbyId($data['id']);

        if($password !== $data['oldPassword']) {
            \tools\Utils::errorResponse("Sua senha antiga não corresponde a senha inputada", 400);
            return;
        }


    }

    public function login()
    {
        $requiredParams = ['email', 'password'];
        $data = \tools\Utils::validateParams($_POST, $requiredParams);

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

            \tools\Utils::jsonResponse([
                'message' => 'Login bem-sucedido',
                'user_id' => $user['ID_U']
            ], 200);
        } else {
            \tools\Utils::errorResponse("Credenciais inválidas", 401);
        }
    }

    public function logout()
    {
        session_start();
        session_unset();
        session_destroy();

        setcookie('user_id', '', time() - 3600, '/');
        setcookie('nickname', '', time() - 3600, '/');

        \tools\Utils::jsonResponse(['message' => 'Logout bem-sucedido'], 200);
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
}
