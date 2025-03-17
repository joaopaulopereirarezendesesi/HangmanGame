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
            throw new Exception("Error executing query: " . $e->getMessage());
        }
    }

    public static function displayMessage($message, $type = 'info')
    {
        $colors = [
            'info' => "\033[34m",         // Azul
            'success' => "\033[32m",      // Verde
            'error' => "\033[31m",        // Vermelho
            'warning' => "\033[33m",      // Amarelo
            'player_join' => "\033[36m",  // Ciano
            'player_leave' => "\033[35m", // Magenta
            'default' => "\033[37m",      // Branco
            'reset' => "\033[0m"          // Reset
        ];

        $color = $colors[$type] ?? $colors['default'];
        echo $color . $message . $colors['reset'] . "\n";
    }


    public static function jsonResponse($data, $status = 200)
    {
        header('Content-Type: application/json');
        http_response_code($status);
        echo json_encode($data);
        exit;
    }

    public static function errorResponse($message, $code = 400)
    {
        self::jsonResponse(['error' => $message], $code);
    }

    public static function debug_log($message) {
        $logFile = __DIR__ . '../debug.log';
        $date = date('Y-m-d H:i:s'); 
        $logMessage = "[$date] $message\n"; 
        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }
}
