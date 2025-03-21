<?php

namespace controllers;

// Carrega as dependências necessárias
require_once __DIR__ . '/../vendor/autoload.php';

use models\UserModel;  // Importa o modelo de usuários
use tools\Utils;       // Importa as funções auxiliares da classe Utils
use core\JwtHandler;   // Importa o manipulador de tokens JWT
use Exception;         // Importa a classe Exception para capturar erros

class UserController
{
    private $userModel; // Declara a variável de modelo de usuário

    // Construtor da classe, inicializa o modelo de usuários
    public function __construct()
    {
        $this->userModel = new UserModel(); // Instancia o modelo de usuários
    }

    // Método para listar todos os usuários
    public function index()
    {
        $users = $this->userModel->getAllUsers();  // Obtém todos os usuários
        Utils::jsonResponse($users, 200);  // Retorna a lista de usuários como resposta JSON
    }

    // Método para exibir um usuário específico com base no ID
    public function show($id)
    {
        $user = $this->userModel->getUserById($id);  // Obtém o usuário pelo ID
        if ($user) {
            Utils::jsonResponse($user, 200);  // Retorna o usuário como resposta JSON
        } else {
            Utils::errorResponse("Usuário não encontrado", 404);  // Responde com erro caso o usuário não seja encontrado
        }
    }

    // Método para criar um novo usuário
    public function create()
    {
        // Obtém os dados da requisição, seja em formato JSON ou via $_POST
        $json = file_get_contents("php://input");
        if (empty($json)) {
            Utils::debug_log("Nenhum dado JSON recebido. Tentando $_POST...");
            $data = $_POST;  // Caso os dados não sejam em JSON, tenta obter via $_POST
        } else {
            $data = json_decode($json, true);  // Converte os dados JSON em um array associativo
        }

        // Verifica se os dados foram corretamente recebidos
        if (!$data) {
            Utils::errorResponse("Erro ao processar os dados. Envie como JSON.", 400);
            return;
        }

        // Valida os parâmetros obrigatórios
        $requiredParams = ['nickname', 'email', 'password', 'confirm_password'];
        $data = Utils::validateParams($data, $requiredParams);

        // Valida o formato do e-mail
        if (!$this->validateEmail($data['email'])) {
            Utils::errorResponse("Formato de e-mail inválido", 400);
            return;
        }

        // Valida a senha
        if (!Utils::validatePassword($data['password'])) {
            Utils::errorResponse("A senha deve ter pelo menos 8 caracteres, conter uma letra maiúscula, uma minúscula, um número e um caractere especial.", 400);
            return;
        }

        // Verifica se as senhas são iguais
        if ($data['password'] !== $data['confirm_password']) {
            Utils::errorResponse("As senhas não coincidem", 400);
            return;
        }

        // Cria o usuário no banco de dados
        $this->userModel->createUser($data['nickname'], $data['email'], $data['password']);
        Utils::jsonResponse(['message' => 'Usuário criado com sucesso!'], 201);  // Responde com sucesso

        // Realiza o login automaticamente após a criação do usuário
        $this->login($data['email'], $data['password']);
    }

    // Método para recuperar a senha
    public function recoverPassword()
    {
        $json = file_get_contents("php://input");
        if (empty($json)) {
            Utils::debug_log("Nenhum dado JSON recebido. Tentando $_POST...");
            $data = $_POST;  // Tenta obter os dados via $_POST caso não seja JSON
        } else {
            $data = json_decode($json, true);  // Converte os dados JSON em um array associativo
        }

        // Valida os parâmetros recebidos
        $data = Utils::validateParams($_POST, $data);

        // Obtém a senha atual do usuário a partir do banco de dados
        $password = $this->userModel->getPasswordbyId($data['id']);

        // Verifica se a senha antiga fornecida é correta
        if ($password !== $data['oldPassword']) {
            Utils::errorResponse("Sua senha antiga não corresponde à senha inputada", 400);
            return;
        }
    }

    // Método para realizar o login
    public function login($email = null, $password = null)
    {
        if ($email === null || $password === null) {
            $requiredParams = ['email', 'password'];
            $data = Utils::validateParams($_POST, $requiredParams);  // Valida os parâmetros recebidos via POST
            $email = strtolower(trim($data['email']));  // Converte o e-mail para minúsculo
            $password = $data['password'];
        } else {
            $email = strtolower(trim($email));  // Converte o e-mail para minúsculo
        }

        // Verifica as credenciais do usuário no banco de dados
        $user = $this->userModel->getUserByEmail($email);

        if ($user && password_verify($password, $user['PASSWORD'])) {
            // Gera um token JWT válido para o usuário
            $token = JwtHandler::generateToken([
                'user_id' => $user['ID_U'],
                'email' => $user['EMAIL'],
                'nickname' => $user['NICKNAME'],
                'password' => $user['PASSWORD'],
            ]);

            session_start();  // Inicia uma nova sessão
            $_SESSION['user_id'] = $user['ID_U'];  // Armazena o ID do usuário na sessão
            $_SESSION['nickname'] = $user['NICKNAME'];  // Armazena o nickname na sessão
            setcookie('token', $token, time() + 3600, '/', '', true, true);  // Define o cookie de token

            // Define os cookies de ID e nickname para persistência
            setcookie('user_id', $user['ID_U'], time() + (86400 * 30), '/', '', true, false);
            setcookie('nickname', $user['NICKNAME'], time() + (86400 * 30), '/', '', true, false);

            // Responde com sucesso e o ID do usuário
            Utils::jsonResponse([
                'message' => 'Login bem-sucedido',
                'user_id' => $user['ID_U']
            ], 200);
        } else {
            // Responde com erro se as credenciais forem inválidas
            Utils::errorResponse("Credenciais inválidas", 401);
        }
    }

    // Método para realizar o logout
    public function logout()
    {
        session_start();
        session_unset();  // Limpa os dados da sessão
        session_destroy();  // Destrói a sessão

        // Remove os cookies de sessão e token
        setcookie('user_id', '', time() - 3600, '/');
        setcookie('nickname', '', time() - 3600, '/');
        setcookie('token', '', time() - 3600, '/');

        // Responde com sucesso no logout
        Utils::jsonResponse(['message' => 'Logout bem-sucedido'], 200);
    }

    // Função para validar o formato do e-mail
    function validateEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);  // Retorna verdadeiro se o e-mail for válido
    }

    // Método para obter as salas que um usuário organiza
    public function getRoomOrganizer()
    {
        $input = json_decode(file_get_contents('php://input'), true);  // Obtém os dados de entrada JSON

        if (!isset($input['id_o'])) {
            throw new Exception('ID do organizador não fornecido.');  // Lança um erro caso o ID não seja fornecido
        }

        $id_o = $input['id_o'];

        // Retorna as salas organizadas pelo usuário
        Utils::jsonResponse([
            'rooms' => $this->userModel->getRoomOrganizer($id_o)
        ]);
    }
}
