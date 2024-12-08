<?php

class UserModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function getAllUsers()
    {
        $stmt = $this->db->query("SELECT * FROM users");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE ID_U = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createUser($nickname, $email, $password)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($existingUser) {
            echo json_encode(['message' => 'Email ja cadastrado!']);
            return;
        }

        $stmt = $this->db->prepare("SELECT * FROM users WHERE NICKNAME = :nickname");
        $stmt->bindParam(':nickname', $nickname);
        $stmt->execute();
        $existingNickname = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($existingNickname) {
            echo json_encode(['message' => 'Nickname ja utilizado!']);
            return;
        }

        try {
            $stmt = $this->db->prepare("INSERT INTO users (NICKNAME, EMAIL, PASSWORD) VALUES (:nickname, :email, :password)");
            $stmt->execute([
                ':nickname' => $nickname,
                ':email' => $email,
                ':password' => password_hash($password, PASSWORD_ARGON2ID)
            ]);
            $id = $this->db->lastInsertId();
            echo json_encode(['message' => 'ID do usuario incerido' . $id]);
            return $id;
        } catch (Exception $e) {
            error_log("Erro ao cadastrar usuario: " . $e->getMessage());
            return 'Erro ao cadastrar usuario';
        }
    }

    public function getUserByEmail($email)
    {
        $query = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user) {
            error_log("Usuário não encontrado para o e-mail: " . $email);
        }
        return $user;
    }
}
