<?php

namespace tools;

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../config/config.php";

use PDO;
use PDOException;
use Exception;
use core\Database;
use PHPMailer\PHPMailer\PHPMailer;
use handlers\HandlerJwt;

class Utils
{
    private PDO $dbConnection;

    public function __construct()
    {
        $this->dbConnection = Database::connect();
    }

    /**
     * Obtém o ID do usuário a partir do token JWT enviado na requisição.
     *
     * @return string|null Retorna o ID do usuário decodificado ou null caso o token seja inválido ou não encontrado.
     */
    public static function getUserIdFromToken(): ?string
    {
        try {
            $token = self::getToken();

            if (!$token) {
                self::jsonResponse("Token não encontrado.", 401);
                return null;
            }

            $decodedToken = HandlerJwt::validateToken($token);
            if (!$decodedToken) {
                return null;
            }

            return $decodedToken["user_id"];
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

    /**
     * Criptografa dados usando AES-256-CBC.
     *
     * @param string $data Dados a serem criptografados.
     * @return string Dados criptografados codificados em base64.
     */
    public static function encrypt(string $data): string
    {
        $cipherMethod = "AES-256-CBC";
        $initializationVector = random_bytes(openssl_cipher_iv_length($cipherMethod));
        $encryptedData = openssl_encrypt($data, $cipherMethod, ENCIPITATE_KEY, 0, $initializationVector);

        if ($encryptedData === false) {
            self::debug_log(
                [
                    "toolsErrorUtils-executeQuery" => "encryption error",
                ],
                "error"
            );
            self::jsonResponse(["error" => "Internal server error"], 500);
        }

        return base64_encode($initializationVector . $encryptedData);
    }

    /**
     * Descriptografa dados criptografados com AES-256-CBC.
     *
     * @param string $encryptedData Dados criptografados codificados em base64.
     * @return string|null Dados descriptografados ou null em caso de falha.
     */
    public static function decrypt(string $encryptedData): ?string
    {
        $cipherMethod = "AES-256-CBC";
        $decodedData = base64_decode($encryptedData, true);

        if ($decodedData === false) {
            self::debug_log(
                [
                    "toolsErrorUtils-executeQuery" => "deencryption error",
                ],
                "error"
            );
            self::jsonResponse(["error" => "Internal server error"], 500);
        }

        $initializationVectorLength = openssl_cipher_iv_length($cipherMethod);
        $initializationVector = substr($decodedData, 0, $initializationVectorLength);
        $encryptedValue = substr($decodedData, $initializationVectorLength);

        $decryptedValue = openssl_decrypt($encryptedValue, $cipherMethod, ENCIPITATE_KEY, 0, $initializationVector);

        return $decryptedValue !== false ? $decryptedValue : null;
    }

    /**
     * Executa uma consulta SQL parametrizada.
     *
     * @param string $query Consulta SQL.
     * @param array $parameters Parâmetros a serem vinculados à consulta.
     * @param bool $fetch Se verdadeiro, retorna os resultados da consulta.
     * @return mixed Retorna os resultados da consulta ou o statement.
     */
    public function executeQuery(
        string $query,
        array $parameters = [],
        bool $fetch = false
    ): mixed {
        try {
            $statement = $this->dbConnection->prepare($query);
            $statement->execute($parameters);

            if ($fetch) {
                return $statement->fetchAll(PDO::FETCH_ASSOC);
            }

            return $statement;
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

    // /**
    //  * Envia um e-mail com uma imagem embutida no corpo da mensagem.
    //  *
    //  * @param string $to Destinatário.
    //  * @param string $subject Assunto do e-mail.
    //  * @param string $body Corpo do e-mail.
    //  * @param string $imagePath Caminho da imagem a ser embutida.
    //  * @param string $from Remetente (padrão: hangmangame.com@gmail.com).
    //  * @param string $fromName Nome do remetente (padrão: HangmanGame.com).
    //  * @return bool Retorna true se o e-mail for enviado com sucesso.
    //  */
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
    //         throw new Exception("Error sending email: " . $mail->ErrorInfo);
    //     }
    // }

    /**
     * Valida a senha de uma sala em relação ao seu hash.
     *
     * @param string $hashedPassword Senha criptografada para comparação.
     * @param string|null $password Senha em texto plano para validar.
     * @return bool|null Retorna true se a senha for válida, false se inválida, ou null em caso de erro.
     */
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

            return null;
        }
    }

    /**
     * Retorna uma resposta JSON e encerra a execução do script.
     *
     * @param array|string $data Dados a serem enviados no JSON.
     * @param int $status Código de status HTTP (padrão: 200).
     * @param string $header Cabeçalho Content-Type (padrão: application/json).
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
     * @param array $request Dados da requisição.
     * @param array $requiredParameters Lista de parâmetros obrigatórios.
     * @return array|null Retorna os parâmetros validados ou null se estiverem faltando.
     */
    public static function validateParams(
        array $request,
        array $requiredParameters
    ): ?array {
        try {
            $missingParameters = [];
            foreach ($requiredParameters as $parameter) {
                if (!isset($request[$parameter])) {
                    $missingParameters[] = $parameter;
                }
            }

            if (!empty($missingParameters)) {
                self::jsonResponse(
                    "Parâmetros ausentes: " . implode(", ", $missingParameters),
                    400
                );
            }

            return array_intersect_key($request, array_flip($requiredParameters));
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
     * @param array|string $message Mensagem a ser registrada.
     * @param string $path Caminho do arquivo de log (padrão: "debug").
     */
    public static function debug_log(
        array|string $message,
        string $path = "debug"
    ): void {
        try {
            $logFilePath = __DIR__ . "/../logs/" . $path . ".log";
            $currentTime = date("Y-m-d H:i:s");

            if (!is_dir(__DIR__ . "/../logs/")) {
                mkdir(__DIR__ . "/../logs/", 0777, true);
            }

            if (is_array($message) || is_object($message)) {
                $message = json_encode($message, JSON_PRETTY_PRINT);
            }

            $logEntry = "[$currentTime] $message\n";

            if (file_put_contents($logFilePath, $logEntry, FILE_APPEND) === false) {
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
     * - Mínimo 8 caracteres
     * - Pelo menos uma letra maiúscula, uma minúscula, um número e um caractere especial
     *
     * @param string $password Senha a ser validada.
     * @return bool Retorna true se a senha for válida.
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
     * @return string|null Retorna o token ou null se não encontrado.
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
