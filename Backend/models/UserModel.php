<?php
class UserModel {
    private $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    public function getAllUsers() {
        $stmt = $this->db->query("SELECT * FROM users");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserById($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE ID_U = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createUser($nickname, $email, $password) {
        $stmt = $this->db->prepare("INSERT INTO users (NICKNAME, EMAIL, PASSWORD) VALUES (:nickname, :email, :password)");
        $stmt->execute([
            ':nickname' => $nickname,
            ':email' => $email,
            ':password' => password_hash($password, PASSWORD_BCRYPT)
        ]);
        return $this->db->lastInsertId();
    }
}
