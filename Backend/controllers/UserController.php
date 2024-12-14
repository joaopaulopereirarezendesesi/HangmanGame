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
            $nickname = trim($_POST['nickname']);
            $email = strtolower(trim($_POST['email']));
            $password = $_POST['password'];
            $confirmPassword = $_POST['confirm_password'];

            if (!$this->validateEmail($email)) {
                echo json_encode(['error' => 'Formato de e-mail invalido']);
                return;
            }

            $passwordValidation = $this->validatePassword($password);
            if (!$passwordValidation) {
                echo json_encode(['error' => 'A senha deve ter pelo menos 8 caracteres, conter uma letra maiuscula, uma minuscula, um numero e um caractere especial']);
                return;
            }

            if ($password !== $confirmPassword) {
                echo json_encode(['error' => 'As senhas nao coincidem']);
                return;
            }

            $this->userModel->createUser($nickname, $email, $password);
        } else {
            echo json_encode(['error' => 'Dados invalidos']);
        }
    }

    public function login()
    {
        if (!empty($_POST['email']) && !empty($_POST['password'])) {
            $email = strtolower(trim($_POST['email']));
            $password = $_POST['password'];
            print_r('aoba');
            $user = $this->userModel->getUserByEmail($email);

            if ($user && password_verify($password, $user['PASSWORD'])) {
                session_start();
                $_SESSION['user_id'] = $user['ID_U'];
                $_SESSION['nickname'] = $user['NICKNAME'];

                if (isset($_POST['remember']) && $_POST['remember'] == 'true') {
                    setcookie('user_id', $user['ID_U'], time() + (86400 * 30), '/', '', true, false);
                    setcookie('nickname', $user['NICKNAME'], time() + (86400 * 30), '/', '', true, false);
                }

                echo json_encode([
                    'message' => 'Login bem-sucedido',
                    'user_id' => $user['ID_U'],
                    'cookies' => $_COOKIE,
                ]);
            } else {
                echo json_encode(['error' => 'Credenciais invalidas']);
            }
        } else {
            echo json_encode(['error' => 'Email e senha sao obrigatorios']);
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
            $user = $this->userModel->getUserById($_COOKIE['user_id']);

            if ($user && $user['NICKNAME'] === $_COOKIE['nickname']) {
                $_SESSION['user_id'] = $user['ID_U'];
                $_SESSION['nickname'] = $user['NICKNAME'];
                return true;
            }
        }

        return false;
    }

    public function msgFriends() 
    {
        $id_p = $_POST['id'];
        $id_f = $_POST['id_f'];
    }

    public function addFriends() 
    {
    }


    private function validateEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    private function validatePassword($password)
    {
        return preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password);
    }
}
