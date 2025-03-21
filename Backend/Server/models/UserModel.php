<?php

namespace models;

use tools\Utils;
use Exception;

class UserModel
{
    // Instância de utilitário para executar queries no banco de dados
    private $utils;

    // Construtor da classe, inicializa a instância de Utils
    public function __construct()
    {
        $this->utils = new Utils();
    }

    // Método para obter todos os usuários
    public function getAllUsers()
    {
        try {
            // Query para buscar todos os usuários da tabela "users"
            $query = "SELECT * FROM users";
            // Executa a query e retorna todos os usuários
            $result = $this->utils->executeQuery($query, [], true);
            return $result;
        } catch (Exception $e) {
            // Caso ocorra um erro, lança uma exceção com a mensagem de erro
            throw new Exception("Erro ao obter usuários: " . $e->getMessage());
        }
    }

    // Método para obter um usuário pelo seu ID
    public function getUserById($id)
    {
        try {
            // Query para buscar todos os dados do usuário pelo ID
            $query = "SELECT * FROM users WHERE ID_U = :id";
            $params = [':id' => $id];
            // Executa a query e retorna os dados do usuário
            $result = $this->utils->executeQuery($query, $params, true);
            return $result[0] ?? null; // Retorna os dados do usuário ou null se não encontrado
        } catch (Exception $e) {
            // Caso ocorra um erro, lança uma exceção com a mensagem de erro
            throw new Exception("Erro ao obter usuário: " . $e->getMessage());
        }
    }

    // Método para criar um novo usuário
    public function createUser($nickname, $email, $password)
    {
        try {
            // Verifica se já existe um usuário com o mesmo email ou nickname
            $query = "SELECT * FROM users WHERE email = :email OR NICKNAME = :nickname";
            $params = [':email' => $email, ':nickname' => $nickname];
            $existingUser = $this->utils->executeQuery($query, $params, true);

            if ($existingUser) {
                // Se o email já estiver cadastrado, lança um erro
                if ($existingUser[0]['email'] === $email) {
                    throw new Exception('Email já cadastrado!');
                }
                // Se o nickname já estiver em uso, lança um erro
                if ($existingUser[0]['NICKNAME'] === $nickname) {
                    throw new Exception('Nickname já utilizado!');
                }
            }

            // Log de depuração com os dados do novo usuário
            Utils::debug_log(json_encode([
                "nick" => $nickname,
                "email" => $email,
                "password" => $password
            ]));

            // Query para inserir um novo usuário na tabela "users"
            $query = "INSERT INTO users (ID_U, NICKNAME, EMAIL, PASSWORD, ONLINE) VALUES (UUID(), :nickname, :email, :password, 1)";
            // Parâmetros da query, incluindo a senha hash
            $params = [
                ':nickname' => $nickname,
                ':email' => $email,
                ':password' => password_hash($password, PASSWORD_ARGON2ID),
            ];

            // Executa a query para inserir o novo usuário
            $this->utils->executeQuery($query, $params);

            // Retorna o ID do último usuário inserido
            return $this->utils->executeQuery("SELECT LAST_INSERT_ID()", [], true)[0]['LAST_INSERT_ID()'];
        } catch (Exception $e) {
            // Caso ocorra um erro, lança uma exceção com a mensagem de erro
            throw new Exception("Erro ao criar usuário: " . $e->getMessage());
        }
    }

    // Método para obter um usuário pelo seu email
    public function getUserByEmail($email)
    {
        try {
            // Query para buscar o usuário pelo email
            $query = "SELECT * FROM users WHERE email = :email";
            $params = [':email' => $email];
            // Executa a query e retorna os dados do usuário
            $result = $this->utils->executeQuery($query, $params, true);
            return $result[0] ?? null; // Retorna os dados do usuário ou null se não encontrado
        } catch (Exception $e) {
            // Caso ocorra um erro, lança uma exceção com a mensagem de erro
            throw new Exception("Erro ao obter usuário por email: " . $e->getMessage());
        }
    }

    // Método para verificar se um email já está registrado
    public function emailExists($email)
    {
        try {
            // Query para contar quantas vezes o email aparece na tabela "users"
            $query = "SELECT COUNT(*) FROM users WHERE email = :email";
            $params = [':email' => $email];
            // Executa a query e obtém o resultado
            $result = $this->utils->executeQuery($query, $params, true);
            // Retorna true se o email já existir, false caso contrário
            return $result[0]['COUNT(*)'] > 0;
        } catch (Exception $e) {
            // Caso ocorra um erro, lança uma exceção com a mensagem de erro
            throw new Exception("Erro ao verificar e-mail: " . $e->getMessage());
        }
    }

    // Método para obter a senha de um usuário pelo seu ID
    public function getPasswordbyId($id)
    {
        try {
            // Query para buscar a senha do usuário pelo ID
            $query = "SELECT `PASSWORD` FROM `users` WHERE `ID_U` = :id;";
            $params = [':id' => $id];
            // Executa a query e retorna a senha
            $result = $this->utils->executeQuery($query, $params, true);
            return $result; // Retorna a senha ou null se não encontrado
        } catch (Exception $e) {
            // Caso ocorra um erro, lança uma exceção com a mensagem de erro
            throw new Exception("Erro ao obter a senha: " . $e->getMessage());
        }
    }

    // Método para obter o organizador da sala pelo ID do organizador
    public function getRoomOrganizer($id_o)
    {
        try {
            // Query para buscar o organizador da sala pelo ID
            $query = "SELECT * FROM users WHERE ID_U = :id_o";
            $params = [':id_o' => $id_o];
            // Executa a query e retorna os dados do organizador
            $result = $this->utils->executeQuery($query, $params, true);
            return $result[0] ?? null; // Retorna os dados do organizador ou null se não encontrado
        } catch (Exception $e) {
            // Caso ocorra um erro, lança uma exceção com a mensagem de erro
            throw new Exception("Erro ao obter sala: " . $e->getMessage());
        }
    }
}
