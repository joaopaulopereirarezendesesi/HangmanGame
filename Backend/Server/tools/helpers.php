<?php

namespace tools;

require_once __DIR__ . '/../vendor/autoload.php';

use PDO;
use PDOException;
use Exception;
use core\Database;
use PHPMailer\PHPMailer\PHPMailer;

class Utils
{
    private $db;

    public function __construct()
    {
        $this->db = Database::connect(); 
    }

    public function executeQuery($query, $params = [], $fetch = false)
    {
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);

            if ($fetch) {
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }

            return $stmt;
        } catch (PDOException $e) {
            throw new Exception("Erro ao executar a consulta: " . $e->getMessage());
        }
    }

    public function sendEmailWithInlineImage($to, $subject, $body, $imagePath, $from = 'hangmangame.com@gmail.com', $fromName = 'HangmanGame.com') {
        $mail = new PHPMailer(true);
    
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'hangmangame.com@gmail.com'; 
            $mail->Password = 'HangMangame123'; 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
    
            $mail->setFrom($from, $fromName);
            $mail->addAddress($to);
    
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;
    
            $mail->addEmbeddedImage($imagePath, 'image1');
    
            $mail->send();
            return true;
        } catch (Exception $e) {
            throw new Exception("Erro ao enviar email: " . $mail->ErrorInfo);
        }
    }

    public static function isPortInUse($port) {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $output = shell_exec("netstat -aon | findstr :{$port}");
            if ($output) {
                return true;
            }
        } else {
            $output = shell_exec("lsof -ti :{$port}");
            if (!empty($output)) {
                return true;
            }
        }

        return false;
    }

    public static function displayMessage($message, $type = 'info') {
        $colors = [
            'info' => "\033[34m",   
            'success' => "\033[32m",
            'error' => "\033[31m",   
            'reset' => "\033[0m"   
        ];

        echo $colors[$type] . $message . $colors['reset'] . "\n";
    }

    public static function jsonResponse($data, $status = 200) {
        header('Content-Type: application/json');
        http_response_code($status);
        echo json_encode($data);
        exit;
    }

    public static function errorResponse($message, $code = 400) {
        self::jsonResponse(['error' => $message], $code);
    }

    public static function validateParams($request, $requiredParams) {
        $missing = [];
        foreach ($requiredParams as $param) {
            if (!isset($request[$param])) {
                $missing[] = $param;
            }
        }
    
        if (!empty($missing)) {
            self::errorResponse("Parâmetros ausentes: " . implode(', ', $missing), 400);
            exit; 
        }
    
        return array_intersect_key($request, array_flip($requiredParams));
    }
    

    public static function validatePassword($password) {
        return preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password);
    }

    public static function getToken() {
        return $_COOKIE['token'] ?? $_SERVER['HTTP_AUTHORIZATION'] ?? null;
    }
}
