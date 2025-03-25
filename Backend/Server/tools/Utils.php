<?php

namespace tools;

require_once __DIR__ . '/../vendor/autoload.php';

use PDO;
use PDOException;
use Exception;
use core\Database;
use PHPMailer\PHPMailer\PHPMailer;
use core\JwtHandler;

class Utils
{
    private $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
     * Obtém o ID do usuário a partir do token JWT enviado na requisição.
     *
     * Este método valida e decodifica o token JWT, e retorna o ID do usuário
     * embutido dentro do token. Caso o token não seja encontrado ou
     * não seja válido, o método retorna null.
     *
     * @return mixed Retorna o ID do usuário decodificado ou null caso o token
     *               não seja encontrado ou seja inválido.
     * @throws Exception Lança uma exceção caso o token não seja válido ou
     *                   ocorra algum erro ao decodificá-lo.
     */
    public static function getUserIdFromToken()
    {
        $token = self::getToken();  
        self::debug_log($token);

        if (!$token) {
            self::errorResponse('Token não encontrado.', 401);  
            return null;
        }

        $decoded = JwtHandler::validateToken($token);
        if (!$decoded) {
            return null;
        }

        return $decoded['user_id'];  
    }

    /**
     * Executa uma consulta SQL parametrizada.
     *
     * @param string $query  Consulta SQL
     * @param array  $params Parâmetros a serem vinculados à consulta
     * @param bool   $fetch  Se verdadeiro, retorna os resultados da consulta
     * @return mixed Retorna os resultados da consulta (se fetch for true) ou o statement
     * @throws Exception Em caso de erro na execução da consulta
     */
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

    /**
     * Envia um e-mail com uma imagem embutida no corpo da mensagem.
     *
     * @param string $to        Destinatário
     * @param string $subject   Assunto do e-mail
     * @param string $body      Corpo do e-mail
     * @param string $imagePath Caminho da imagem a ser embutida
     * @param string $from      Remetente (padrão: hangmangame.com@gmail.com)
     * @param string $fromName  Nome do remetente (padrão: HangmanGame.com)
     * @return bool Retorna true se o e-mail for enviado com sucesso
     * @throws Exception Se houver erro no envio do e-mail
     */
    public function sendEmailWithInlineImage($to, $subject, $body, $imagePath, $from = 'hangmangame.com@gmail.com', $fromName = 'HangmanGame.com')
    {
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

    public static function validateRoomPassword(string $hashedPassword, ?string $password): bool
    {
        return !empty($password) && password_verify($password, $hashedPassword);
    }

    /**
     * Exibe mensagens no console, formatando-as com cores.
     *
     * @param string $message Mensagem a ser exibida
     * @param string $type    Tipo da mensagem (info, success, error)
     */
    public static function displayMessage($message, $type = 'info')
    {
        $colors = [
            'info' => "\033[34m",
            'success' => "\033[32m",
            'error' => "\033[31m",
            'reset' => "\033[0m"
        ];

        echo $colors[$type] . $message . $colors['reset'] . "\n";
    }

    /**
     * Retorna uma resposta JSON e encerra a execução do script.
     *
     * @param mixed $data   Dados a serem enviados no JSON
     * @param int   $status Código de status HTTP (padrão: 200)
     */
    public static function jsonResponse($data, $status = 200)
    {
        header('Content-Type: application/json');
        http_response_code($status);
        echo json_encode($data);
        exit;
    }

    /**
     * Valida se todos os parâmetros obrigatórios estão presentes na requisição.
     *
     * @param array $request        Dados da requisição
     * @param array $requiredParams Lista de parâmetros obrigatórios
     * @return array Retorna os parâmetros validados
     */
    public static function validateParams($request, $requiredParams)
    {
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

    /**
     * Registra mensagens de debug em um arquivo de log.
     *
     * @param string $message Mensagem a ser registrada
     */
    public static function debug_log($message, $path = "debug")
    {
        $logFile = __DIR__ . '/../logs/'. $path . '.log';
        $date = date('Y-m-d H:i:s');
        
        if (is_array($message) || is_object($message)) {
            $message = print_r($message, true);
        }
        
        $logMessage = "[$date] $message\n";
        
        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }
    

    /**
     * Valida se uma senha atende aos critérios de segurança.
     * - Mínimo de 8 caracteres
     * - Pelo menos uma letra maiúscula, uma minúscula, um número e um caractere especial
     *
     * @param string $password Senha a ser validada
     * @return bool Retorna true se a senha for válida
     */
    public static function validatePassword($password)
    {
        return preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password);
    }

    /**
     * Obtém o token de autenticação da requisição (via cookie ou cabeçalho HTTP).
     *
     * @return string|null Retorna o token ou null se não encontrado
     */
    public static function getToken()
    {
        return $_COOKIE['token'] ?? $_SERVER['HTTP_AUTHORIZATION'] ?? null;
    }
}
