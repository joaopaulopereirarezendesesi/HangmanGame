<?php
require_once __DIR__ . '/../models/UserModel.php';

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
        echo json_encode($users);
    }

    public function show($id)
    {
        $user = $this->userModel->getUserById($id);
        echo json_encode($user);
    }

    public function create()
    {
        if (!empty($_POST['nickname']) && !empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['confirm_password'])) {

            $nickname = $_POST['nickname'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $confirmPassword = $_POST['confirm_password'];

            if ($password === $confirmPassword) {
                $id = $this->userModel->createUser($nickname, $email, $password);
                echo json_encode(['message' => 'Usuário criado', 'id' => $id]);
            } else {
                echo json_encode(['error' => 'As senhas não coincidem']);
            }
        } else {
            echo json_encode(['error' => 'Dados inválidos']);
        }
    }

    public function login()
    {
        if (!empty($_POST['email']) && !empty($_POST['password'])) {
            $email = $_POST['email'];
            $password = $_POST['password'];

            $user = $this->userModel->getUserByEmail($email);

            if ($user && password_verify($password, $user['password'])) {
                session_start();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['nickname'] = $user['nickname'];

                if (isset($_POST['remember']) && $_POST['remember'] == 'true') {
                    setcookie('user_id', $user['id'], time() + (86400 * 30), '/', '', true, true, ['samesite' => 'Strict']);
                    setcookie('nickname', $user['nickname'], time() + (86400 * 30), '/', '', true, true, ['samesite' => 'Strict']);
                }

                echo json_encode(['message' => 'Login bem-sucedido', 'user_id' => $user['id']]);
            } else {
                echo json_encode(['error' => 'Credenciais inválidas']);
            }
        } else {
            echo json_encode(['error' => 'Email e senha são obrigatórios']);
        }
    }

    public function logout()
    {
        session_start();
        session_unset();

        if (isset($_COOKIE['user_id'])) {
            setcookie('user_id', '', time() - 3600, '/');
        }
        if (isset($_COOKIE['nickname'])) {
            setcookie('nickname', '', time() - 3600, '/');
        }

        session_destroy();
        echo json_encode(['message' => 'Logout bem-sucedido']);
    }

    public function isLoggedIn()
    {
        session_start();

        if (isset($_SESSION['user_id'])) {
            return true;
        }

        if (isset($_COOKIE['user_id']) && isset($_COOKIE['nickname'])) {
            $_SESSION['user_id'] = $_COOKIE['user_id'];
            $_SESSION['nickname'] = $_COOKIE['nickname'];

            return true; 
        }

        return false;
    }
}
