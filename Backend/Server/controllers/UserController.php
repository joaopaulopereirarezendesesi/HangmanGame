<?php

namespace controllers;

// Carrega as dependências necessárias
require_once __DIR__ . '/../vendor/autoload.php';

use models\UserModel;  // Importa o modelo de usuários
use tools\Utils;       // Importa as funções auxiliares da classe Utils
use core\JwtHandler;   // Importa o manipulador de tokens JWT
use Exception;         // Importa a classe Exception para capturar erros

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
     * Lista todos os usuários.
     */
    public function index()
    {
        $users = $this->userModel->getAllUsers();
        Utils::jsonResponse($users, 200);
    }

    /**
     * Exibe um usuário específico com base no ID.
     *
     * @param int $id ID do usuário.
     */
    public function show($id)
    {
        $user = $this->userModel->getUserById($id);
        
        if ($user) {
            Utils::jsonResponse($user, 200);
        } else {
            Utils::errorResponse("Usuário não encontrado", 404);
        }
    }

    /**
     * Cria um novo usuário.
     */
    public function create()
    {
        $data = $_POST;

        if (empty($data)) {
            Utils::jsonResponse(['error' => "Nenhum dado recebido."], 400);
            return;
        }

        $requiredParams = ['nickname', 'email', 'password', 'confirm_password'];
        $data = Utils::validateParams($data, $requiredParams);

        if (!$this->validateEmail($data['email'])) {
            Utils::jsonResponse(['error' => "Formato de e-mail inválido"], 400);
            return;
        }

        if (!Utils::validatePassword($data['password'])) {
            Utils::jsonResponse(['error' => "A senha deve ter pelo menos 8 caracteres, conter uma letra maiúscula, uma minúscula, um número e um caractere especial."], 400);
            return;
        }

        if ($data['password'] !== $data['confirm_password']) {
            Utils::jsonResponse(['error' => "As senhas não coincidem"], 400);
            return;
        }

        $this->userModel->createUser($data['nickname'], $data['email'], $data['password']);
        Utils::jsonResponse(['message' => 'Usuário criado com sucesso!'], 201);

        $this->login($data['email'], $data['password']);
    }

    /**
     * Recupera a senha do usuário.
     */
    public function recoverPassword()
    {
        $data = $_POST;
        $data = Utils::validateParams($_POST, $data);
        
        $password = $this->userModel->getPasswordbyId($data['id']);
        
        if ($password !== $data['oldPassword']) {
            Utils::errorResponse("Sua senha antiga não corresponde à senha inputada", 400);
            return;
        }
    }

    /**
     * Realiza o login do usuário.
     *
     * @param string|null $email Email do usuário.
     * @param string|null $password Senha do usuário.
     */
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
            Utils::jsonResponse(['message' => 'Credenciais inválidas'], 400);
        }
    }

    /**
     * Realiza o logout do usuário.
     */
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

    /**
     * Valida o formato do e-mail.
     *
     * @param string $email Email a ser validado.
     * @return bool Retorna true se o e-mail for válido.
     */
    public function validateEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Obtém as salas que um usuário organiza.
     */
    public function getRoomOrganizer()
    {
        $id = Utils::getUserIdFromToken();
        if (!$id)
            return;

        Utils::debug_log($id);

        if (!$id) {
            throw new Exception('Token não fornecido.');
        }

        $id_o = $id;

        Utils::jsonResponse([
            'rooms' => $this->userModel->getRoomOrganizer($id_o)
        ]);
    }
}
