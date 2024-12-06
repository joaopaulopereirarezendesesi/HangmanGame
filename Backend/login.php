<?php
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'hangmangame';

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

        $email = $_POST['email'];
        $password = $_POST['password'];

        $sql = "SELECT * FROM users WHERE EMAIL = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            die("E-mail ou senha inválidos.");
        }

        $user = $result->fetch_assoc();
        $hashedPassword = $user['PASSWORD'];

        if (password_verify($password, $hashedPassword)) {
            echo "Login realizado com sucesso!";
        } else {
            echo "E-mail ou senha inválidos.";
        }

        $stmt->close();
    
?>