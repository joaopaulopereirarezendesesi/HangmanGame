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
        $data = json_decode(file_get_contents('php://input'), true);

        if (!empty($data['nickname']) && !empty($data['email']) && !empty($data['password']) && !empty($data['confirm_password'])) {

            if ($data['password'] === $data['confirm_password']) {

                $id = $this->userModel->createUser($data['nickname'], $data['email'], $data['password']);
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
        $data = json_decode(file_get_contents('php://input'), true);

        if (!empty($data['email']) && !empty($data['password'])) {
            $user = $this->userModel->getUserByEmail($data['email']);

            if ($user && password_verify($data['password'], $user['password'])) {
                session_start();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['nickname'] = $user['nickname'];

                //PROFESSOR caso esteja vendo o backend, adicione uma caixa de relembre-me!
                if (isset($data['remember']) && $data['remember'] == true) {
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
}
