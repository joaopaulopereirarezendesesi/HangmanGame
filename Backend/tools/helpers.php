<?php

namespace tools;

use PDO;
use PDOException;
use Exception;
use core\Database;

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

    public function sendEmailWithInlineImage($to, $subject, $body, $imagePath, $from = 'colocar email depois', $fromName = 'Seu Nome') {
        $boundary = md5(uniqid(time()));

        $headers = "From: $fromName <$from>\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: multipart/related; boundary=\"$boundary\"\r\n";

        $message = "--$boundary\r\n";
        $message .= "Content-Type: text/html; charset=UTF-8\r\n";
        $message .= "Content-Transfer-Encoding: 7bit\r\n";
        $message .= "$body\r\n";  

        $fileContent = chunk_split(base64_encode(file_get_contents($imagePath)));
        $fileName = basename($imagePath);

        $message .= "--$boundary\r\n";
        $message .= "Content-Type: image/jpeg; name=\"$fileName\"\r\n"; 
        $message .= "Content-Disposition: inline; filename=\"$fileName\"\r\n";
        $message .= "Content-ID: <image1>\r\n";  
        $message .= "Content-Transfer-Encoding: base64\r\n";
        $message .= "$fileContent\r\n";

        $message .= "--$boundary--";

        if (mail($to, $subject, $message, $headers)) {
            return true;
        } else {
            return false;
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
        }

        return array_intersect_key($request, array_flip($requiredParams));
    }

    public static function validatePassword($password) {
        return preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password);
    }
}
