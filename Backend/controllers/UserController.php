<?php
require_once '../models/UserModel.php';

class UserController {
    private $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    public function index() {
        $users = $this->userModel->getAllUsers();
        echo json_encode($users);
    }

    public function show($id) {
        $user = $this->userModel->getUserById($id);
        echo json_encode($user);
    }

    public function create() {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!empty($data['nickname']) && !empty($data['email']) && !empty($data['password'])) {
            $id = $this->userModel->createUser($data['nickname'], $data['email'], $data['password']);
            echo json_encode(['message' => 'Usuário criado', 'id' => $id]);
        } else {
            echo json_encode(['error' => 'Dados inválidos']);
        }
    }
}
