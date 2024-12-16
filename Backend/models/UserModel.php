<?php

namespace models;

use core\Database;
use PDO;
use PDOException;
use Exception;

class UserModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function getAllUsers()
    {
        try {
            $stmt = $this->db->query("SELECT * FROM users");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erro ao obter usuários: " . $e->getMessage());
        }
    }

    public function getUserById($id)
    {
        try {
            $query = "SELECT * FROM users WHERE ID_U = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erro ao obter usuário: " . $e->getMessage());
        }
    }

    public function createUser($nickname, $email, $password)
    {
        try {
            $query = "SELECT * FROM users WHERE email = :email OR NICKNAME = :nickname";
            $stmt = $this->db->prepare($query);
            $stmt->execute([':email' => $email, ':nickname' => $nickname]);
            $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existingUser) {
                if ($existingUser['email'] === $email) {
                    throw new Exception('Email já cadastrado!');
                }
                if ($existingUser['NICKNAME'] === $nickname) {
                    throw new Exception('Nickname já utilizado!');
                }
            }

            $query = "INSERT INTO users (NICKNAME, EMAIL, PASSWORD) VALUES (:nickname, :email, :password)";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':nickname' => $nickname,
                ':email' => $email,
                ':password' => password_hash($password, PASSWORD_ARGON2ID),
            ]);

            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception("Erro ao criar usuário: " . $e->getMessage());
        }
    }

    public function getUserByEmail($email)
    {
        try {
            $query = "SELECT * FROM users WHERE email = :email";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erro ao obter usuário por email: " . $e->getMessage());
        }
    }

    public function emailExists($email)
    {
        try {
            $query = "SELECT COUNT(*) FROM users WHERE email = :email";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            // Verifica se o e-mail já está registrado
            $count = $stmt->fetchColumn();
            return $count > 0;  // Retorna true se o e-mail já existe
        } catch (PDOException $e) {
            throw new Exception("Erro ao verificar e-mail: " . $e->getMessage());
        }
}

}
