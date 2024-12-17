<?php

function isServerRunning($port) {
    $output = shell_exec("netstat -an | grep $port");
    return !empty($output);
}

//Vi isso no stack overflow e achei mto foda

function displayMessage($message, $type = 'info') {
    $colors = [
        'info' => "\033[34m",    // Azul
        'success' => "\033[32m", // Verde
        'error' => "\033[31m",   // Vermelho
        'reset' => "\033[0m"     // Reset
    ];

    echo $colors[$type] . $message . $colors['reset'] . "\n";
}

function jsonResponse($data, $status = 200) {
    header('Content-Type: application/json');
    http_response_code($status);
    echo json_encode($data);
    exit;
}

function errorResponse($message, $code = 400) {
    jsonResponse(['error' => $message], $code);
}

function validateParams($request, $requiredParams) {
    $missing = [];
    foreach ($requiredParams as $param) {
        if (!isset($request[$param])) {
            $missing[] = $param;
        }
    }

    if (!empty($missing)) {
        errorResponse("Parâmetros ausentes: " . implode(', ', $missing), 400);
    }

    return array_intersect_key($request, array_flip($requiredParams));
}

function validateEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validatePassword($password)
{
    return preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password);
}

function validateRoomPassword($hashedPassword, $password)
{
    return !empty($password) && password_verify($password, $hashedPassword);
}