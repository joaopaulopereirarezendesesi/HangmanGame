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
    $userPassword = $_POST['password'];
?>