<?php
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'hangmangame';

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("F, deu erro na conexão :(: " . $conn->connect_error);
}

if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
    $nickname = $_POST['nickname'];
    $photo = $_FILES['photo'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $uploadDir = 'C:\Users\joaop\Documents\GITHUB\-HangmanGame\Banco_de_dados\photos';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $photoName = uniqid() . '-' . basename($photo['name']);
    $photoPath = $uploadDir . $photoName;

    if (move_uploaded_file($photo['tmp_name'], $photoPath)) {
        $sql = "INSERT INTO users (NICKNAME, PHOTO, EMAIL, PASSWORD)
                VALUES (?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $nickname, $photoPath, $email, $password);

        if ($stmt->execute()) {
            echo "Tá cadastrado meu rei";
        } else {
            echo "F, deu erro ao criar o usuário :(: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "F, deu erro ao fazer upload da imagem :(: " . $photo['error'];
    }
} else {
    echo "F, você não enviou uma imagem ou houve um erro no envio.";
}

$conn->close();
