<?php
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'hangmangame';

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
    $nickname = $_POST['nickname'];
    $photo = $_FILES['photo'];
    $email = $_POST['email'];
    $userPassword = $_POST['password'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Email inválido.");
    }

    $hashedPassword = password_hash($userPassword, PASSWORD_DEFAULT);

    $uploadDir = 'uploads/photos/';
    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0777, true)) {
        die("Erro ao criar diretório para uploads.");
    }

    $photoName = uniqid() . '-' . basename($photo['name']);
    $photoPath = $uploadDir . $photoName;

    if (getimagesize($photo['tmp_name']) === false) {
        die("O arquivo enviado não é uma imagem válida.");
    }

    if (move_uploaded_file($photo['tmp_name'], $photoPath)) {
        $sql = "INSERT INTO users (NICKNAME, PHOTO, EMAIL, PASSWORD) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $nickname, $photoPath, $email, $hashedPassword);

        if ($stmt->execute()) {
            echo "Usuário cadastrado com sucesso!";
        } else {
            echo "Erro ao criar o usuário: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Erro ao fazer upload da imagem: " . $photo['error'];
    }
} else {
    echo "Você não enviou uma imagem ou houve um erro no envio.";
}

$conn->close();
