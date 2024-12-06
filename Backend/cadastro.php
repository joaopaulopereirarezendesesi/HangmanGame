<?php
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'hangmangame';

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}



if (isset($_POST['nickname'], $_POST['email'], $_POST['password'])) {
    $nickname = $_POST['nickname'];
    $email = $_POST['email'];
    $userPassword = $_POST['password'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Email inválido.");
    }

    $hashedPassword = password_hash($userPassword, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (NICKNAME, EMAIL, PASSWORD) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $nickname, $email, $hashedPassword);

    if ($stmt->execute()) {
        echo "Usuário cadastrado com sucesso!";
    } else {
        echo "Erro ao criar o usuário: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Preencha todos os campos obrigatórios.";
}

$conn->close();
?>
