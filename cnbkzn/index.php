<?php

//{'nickname': input, 'email': input, 'password': input}

print_r($_POST);

$nickname = $_POST['nickname'];
$email = $_POST['email'];
$password = $_POST['password'];

//[''] -> acessar um valor de um json
