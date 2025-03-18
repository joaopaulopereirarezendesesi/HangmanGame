<?php

namespace models;

use tools\Utils;
use Exception;

class UserModel
{
    private $utils;

    public function __construct()
    {
        $this->utils = new Utils();
    }

    public function getAllUsers()
    {
        try {
            $query = "SELECT * FROM users";
            $result = $this->utils->executeQuery($query, [], true);
            return $result;
        } catch (Exception $e) {
            throw new Exception("Erro ao obter usuários: " . $e->getMessage());
        }
    }

    public function getUserById($id)
    {
        try {
            $query = "SELECT * FROM users WHERE ID_U = :id";
            $params = [':id' => $id];
            $result = $this->utils->executeQuery($query, $params, true);
            return $result[0] ?? null;
        } catch (Exception $e) {
            throw new Exception("Erro ao obter usuário: " . $e->getMessage());
        }
    }

    public function createUser($nickname, $email, $password)
    {
        try {
            $query = "SELECT * FROM users WHERE email = :email OR NICKNAME = :nickname";
            $params = [':email' => $email, ':nickname' => $nickname];
            $existingUser = $this->utils->executeQuery($query, $params, true);

            if ($existingUser) {
                if ($existingUser[0]['email'] === $email) {
                    throw new Exception('Email já cadastrado!');
                }
                if ($existingUser[0]['NICKNAME'] === $nickname) {
                    throw new Exception('Nickname já utilizado!');
                }
            }

            $query = "INSERT INTO users (NICKNAME, EMAIL, PASSWORD) VALUES (:nickname, :email, :password)";
            $params = [
                ':nickname' => $nickname,
                ':email' => $email,
                ':password' => password_hash($password, PASSWORD_ARGON2ID),
            ];

            $this->utils->executeQuery($query, $params);

            return $this->utils->executeQuery("SELECT LAST_INSERT_ID()", [], true)[0]['LAST_INSERT_ID()'];
        } catch (Exception $e) {
            throw new Exception("Erro ao criar usuário: " . $e->getMessage());
        }
    }

    public function getUserByEmail($email)
    {
        try {
            $query = "SELECT * FROM users WHERE email = :email";
            $params = [':email' => $email];
            $result = $this->utils->executeQuery($query, $params, true);
            return $result[0] ?? null;
        } catch (Exception $e) {
            throw new Exception("Erro ao obter usuário por email: " . $e->getMessage());
        }
    }

    public function emailExists($email)
    {
        try {
            $query = "SELECT COUNT(*) FROM users WHERE email = :email";
            $params = [':email' => $email];
            $result = $this->utils->executeQuery($query, $params, true);
            return $result[0]['COUNT(*)'] > 0;
        } catch (Exception $e) {
            throw new Exception("Erro ao verificar e-mail: " . $e->getMessage());
        }
    }

    public function getPasswordbyId($id)
    {
        try {
            $query = "SELECT `PASSWORD` FROM `users` WHERE `ID_U` = :id;";
            $params = [':id' => $id];
            $result = $this->utils->executeQuery($query, $params, true);
            return $result;
        } catch (Exception $e) {
            throw new Exception("Erro ao obter a senha: " . $e->getMessage());
        }
    }

    public function getRoomOrganizer($id_o)
    {
        try {
            $query = "SELECT * FROM users WHERE ID_U = :id_o";
            $params = [':id_o' => $id_o];

            $result = $this->utils->executeQuery($query, $params, true);

            return $result[0] ?? null;
        } catch (Exception $e) {
            throw new Exception("Erro ao obter sala: " . $e->getMessage());
        }
    }
}
