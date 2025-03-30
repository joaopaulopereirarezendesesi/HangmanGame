<?php

namespace tools;

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../config/config.php";

use PDO;
use PDOException;
use Exception;
use core\Database;
use PHPMailer\PHPMailer\PHPMailer;
use core\JwtHandler;

class Utils
{
    private PDO $db;

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
    public static function getUserIdFromToken(): ?string
    {
        try {
            $token = self::getToken();

            if (!$token) {
                self::jsonResponse("Token não encontrado.", 401);
                return null;
            }

            $decoded = JwtHandler::validateToken($token);
            if (!$decoded) {
                return null;
            }

            return $decoded["user_id"];
        } catch (PDOException $e) {
            self::debug_log(
                [
                    "toolsErrorUtils-getUserIdFromToken" => $e->getMessage(),
                ],
                "error"
            );
            self::jsonResponse(["error" => "Internal server error"], 500);

            return null;
        }
    }

    public static function encrypt(string $data): string
    {
        $cipher = "AES-256-CBC";
        $iv = random_bytes(openssl_cipher_iv_length($cipher));
        $encrypted = openssl_encrypt($data, $cipher, ENCIPITATE_KEY, 0, $iv);

        if ($encrypted === false) {
            self::debug_log(
                [
                    "toolsErrorUtils-executeQuery" => "encryption error",
                ],
                "error"
            );
            self::jsonResponse(["error" => "Internal server error"], 500);
        }

        return base64_encode($iv . $encrypted);
    }

    public static function decrypt(string $encryptedData): ?string
    {
        $cipher = "AES-256-CBC";
        $data = base64_decode($encryptedData, true);

        if ($data === false) {
            self::debug_log(
                [
                    "toolsErrorUtils-executeQuery" => "deencryption error",
                ],
                "error"
            );
            self::jsonResponse(["error" => "Internal server error"], 500);
        }

        $ivLength = openssl_cipher_iv_length($cipher);
        $iv = substr($data, 0, $ivLength);
        $encrypted = substr($data, $ivLength);

        $decrypted = openssl_decrypt($encrypted, $cipher, ENCIPITATE_KEY, 0, $iv);

        return $decrypted !== false ? $decrypted : null;
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
    public function executeQuery(
        string $query,
        array $params = [],
        bool $fetch = false
    ): mixed {
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);

            if ($fetch) {
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }

            return $stmt;
        } catch (PDOException $e) {
            self::debug_log(
                [
                    "toolsErrorUtils-executeQuery" => $e->getMessage(),
                ],
                "error"
            );
            self::jsonResponse(["error" => "Internal server error"], 500);

            return null;
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
    // public function sendEmailWithInlineImage($to, $subject, $body, $imagePath, $from = 'hangmangame.com@gmail.com', $fromName = 'HangmanGame.com')
    // {
    //     $mail = new PHPMailer(true);

    //     try {
    //         $mail->isSMTP();
    //         $mail->Host = 'smtp.gmail.com';
    //         $mail->SMTPAuth = true;
    //         $mail->Username = 'hangmangame.com@gmail.com';
    //         $mail->Password = 'HangMangame123';
    //         $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    //         $mail->Port = 587;

    //         $mail->setFrom($from, $fromName);
    //         $mail->addAddress($to);

    //         $mail->isHTML(true);
    //         $mail->Subject = $subject;
    //         $mail->Body = $body;

    //         $mail->addEmbeddedImage($imagePath, 'image1');

    //         $mail->send();
    //         return true;
    //     } catch (Exception $e) {
    //         throw new Exception("Erro ao enviar email: " . $mail->ErrorInfo);
    //     }
    // }

    public static function validateRoomPassword(
        string $hashedPassword,
        ?string $password
    ): ?bool {
        try {
            return !empty($password) &&
                password_verify($password, $hashedPassword);
        } catch (PDOException $e) {
            self::debug_log(
                [
                    "toolsErrorUtils-validateRoomPassword" => $e->getMessage(),
                ],
                "error"
            );
            self::jsonResponse(["error" => "Internal server error"], 500);
            exit();
        } catch (PDOException $e) {
            self::debug_log(
                [
                    "toolsErrorUtils-validateRoomPassword" => $e->getMessage(),
                ],
                "error"
            );
            self::jsonResponse(["error" => "Internal server error"], 500);

            return false;
        }
    }

    /**
     * Retorna uma resposta JSON e encerra a execução do script.
     *
     * @param mixed $data   Dados a serem enviados no JSON
     * @param int   $status Código de status HTTP (padrão: 200)
     */
    public static function jsonResponse(
        array|string $data,
        int $status = 200,
        string $header = "application/json"
    ): void {
        try {
            header("Content-Type:" . $header . "; charset=utf-8");
            http_response_code($status);

            if (ob_get_length()) {
                ob_clean();
            }

            echo json_encode($data, JSON_UNESCAPED_UNICODE);
            exit();
        } catch (PDOException $e) {
            self::debug_log(
                [
                    "toolsErrorUtils-jsonResponse" => $e->getMessage(),
                ],
                "error"
            );
            self::jsonResponse(["error" => "Internal server error"], 500);
        }
    }

    /**
     * Valida se todos os parâmetros obrigatórios estão presentes na requisição.
     *
     * @param array $request        Dados da requisição
     * @param array $requiredParams Lista de parâmetros obrigatórios
     * @return array Retorna os parâmetros validados
     */
    public static function validateParams(
        array $request,
        array $requiredParams
    ): ?array {
        try {
            $missing = [];
            foreach ($requiredParams as $param) {
                if (!isset($request[$param])) {
                    $missing[] = $param;
                }
            }

            if (!empty($missing)) {
                self::jsonResponse(
                    "Parâmetros ausentes: " . implode(", ", $missing),
                    400
                );
            }

            return array_intersect_key($request, array_flip($requiredParams));
        } catch (PDOException $e) {
            self::debug_log(
                [
                    "toolsErrorUtils-jsonResponse" => $e->getMessage(),
                ],
                "error"
            );
            self::jsonResponse(["error" => "Internal server error"], 500);

            return null;
        }
    }

    /**
     * Registra mensagens de debug em um arquivo de log.
     *
     * @param string $message Mensagem a ser registrada
     */
    public static function debug_log(
        array|string $message,
        string $path = "debug"
    ): void {
        try {
            $logFile = __DIR__ . "/../logs/" . $path . ".log";
            $date = date("Y-m-d H:i:s");
    
            if (!is_dir(__DIR__ . "/../logs/")) {
                mkdir(__DIR__ . "/../logs/", 0777, true);
            }
    
            if (is_array($message) || is_object($message)) {
                $message = json_encode($message, JSON_PRETTY_PRINT);
            }
    
            $logMessage = "[$date] $message\n";
    
            if (file_put_contents($logFile, $logMessage, FILE_APPEND) === false) {
                throw new Exception("Erro ao escrever no arquivo de log.");
            }
        } catch (Exception $e) {
            self::debug_log(
                [
                    "toolsErrorUtils-debug_log" => $e->getMessage(),
                ],
                "error"
            );
            self::jsonResponse(["error" => "Internal server error"], 500);
        }
    }
    
    /**
     * Valida se uma senha atende aos critérios de segurança.
     * - Mínimo de 8 caracteres
     * - Pelo menos uma letra maiúscula, uma minúscula, um número e um caractere especial
     *
     * @param string $password Senha a ser validada
     * @return bool Retorna true se a senha for válida
     */
    public static function validatePassword(string $password): ?bool
    {
        try {
            return preg_match(
                '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
                $password
            );
        } catch (Exception $e) {
            self::debug_log(
                [
                    "toolsErrorUtils-debug_log" => $e->getMessage(),
                ],
                "error"
            );
            self::jsonResponse(["error" => "Internal server error"], 500);

            return null;
        }
    }

    /**
     * Obtém o token de autenticação da requisição (via cookie ou cabeçalho HTTP).
     *
     * @return string|null Retorna o token ou null se não encontrado
     */
    public static function getToken(): ?string
    {
        try {
            return $_COOKIE["jwt"] ??
                ($_SERVER["HTTP_AUTHORIZATION"] ?? null);
        } catch (Exception $e) {
            self::debug_log(
                [
                    "toolsErrorUtils-debug_log" => $e->getMessage(),
                ],
                "error"
            );
            self::jsonResponse(["error" => "Internal server error"], 500);

            return null;
        }
    }
}
