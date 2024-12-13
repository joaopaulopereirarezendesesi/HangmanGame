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
        // Verificar se os dados estão completos
        if (empty($_POST['nickname']) || empty($_POST['email']) || empty($_POST['password'])) {
            echo json_encode(['error' => 'Todos os campos são obrigatórios']);
            return;
        }

        $nickname = trim($_POST['nickname']);
        $email = strtolower(trim($_POST['email']));
        $password = $_POST['password'];

        // Validar o formato do e-mail
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['error' => 'Formato de e-mail inválido']);
            return;
        }

        // Inicializar o array de erros
        $errors = [];

        // Verificar se a senha atende aos critérios
        if (strlen($password) < 8) {
            $errors['errorQuantidade'] = 'A senha deve ter pelo menos 8 caracteres.';
        }
        if (!preg_match('/[A-Z]/', $password)) {
            $errors['errorLetraMaiuscula'] = 'A senha deve conter uma letra maiúscula.';
        }
        if (!preg_match('/[a-z]/', $password)) {
            $errors['errorLetraMinuscula'] = 'A senha deve conter uma letra minúscula.';
        }
        if (!preg_match('/[0-9]/', $password)) {
            $errors['errorNumero'] = 'A senha deve conter um número.';
        }
        if (!preg_match('/[\W_]/', $password)) {
            $errors['errorCaracter'] = 'A senha deve conter um caracter especial.';
        }

        // Se houver erros de validação de senha, retornar os erros
        if (!empty($errors)) {
            echo json_encode(['errors' => $errors]);
            return;
        }

        // Verificar se o e-mail já está cadastrado
        if ($this->userModel->emailExists($email)) {
            echo json_encode(['error' => 'E-mail já cadastrado']);
            return;
        }

        // Criar o usuário
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        if ($this->userModel->createUser($nickname, $email, $hashedPassword)) {
            echo json_encode(['success' => 'Usuário criado com sucesso']);
        } else {
            echo json_encode(['error' => 'Erro ao criar usuário']);
        }
    }


    // public function login()
    // {

    //     if (!empty($_POST['email']) && !empty($_POST['password'])) {
    //         $email = strtolower(trim($_POST['email']));
    //         $password = $_POST['password'];
    //         $hashedPassword = password_verify($password, $user['PASSWORD']);

    //         $user = $this->userModel->getUserByEmail($email);
    //         error_log("PasswordBanco: " . $user['PASSWORD']);
    //         error_log("Email: " . $_POST['email']);
    //         error_log("Password: " . $_POST['password']);
    //         error_log("hashedPassword: " . $hashedPassword);
            
    //         if ($user && password_verify($password, $user['PASSWORD'])) {
    //             session_start();
                
    //             $_SESSION['user_id'] = $user['ID_U'];
    //             $_SESSION['nickname'] = $user['NICKNAME'];

    //             if (isset($_POST['remember']) && $_POST['remember'] == 'true') {
    //                 setcookie('user_id', $user['ID_U'], time() + (86400 * 30), '/', '', true, false);
    //                 setcookie('nickname', $user['NICKNAME'], time() + (86400 * 30), '/', '', true, false);
    //             }

    //             echo json_encode([
    //                 'message' => 'Login bem-sucedido',
    //                 'user_id' => $user['ID_U'],
    //                 'cookies' => $_COOKIE,
    //             ]);
    //         } else {
    //             echo json_encode(['error' => 'Credenciais invalidas']);
    //         }
    //     } else {
    //         echo json_encode(['error' => 'Email e senha sao obrigatorios']);
    //     }
    // }

    public function login()
    {
        if (!empty($_POST['email']) && !empty($_POST['password'])) {
            $email = strtolower(trim($_POST['email']));
            $password = $_POST['password'];

            $user = $this->userModel->getUserByEmail($email);

            error_log("Email: " . $_POST['email']);
            error_log("Password: " . $_POST['password']);

            if ($user) {
                $hashedPassword = password_verify($password, $user['PASSWORD']);
                error_log("PasswordBanco: " . $user['PASSWORD']);
                error_log("hashedPassword: " . ($hashedPassword ? 'true' : 'false'));
                error_log("Senha fornecida: " . $password);
                error_log("Hash armazenado: " . $user['PASSWORD']);

                if ($hashedPassword) {
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
