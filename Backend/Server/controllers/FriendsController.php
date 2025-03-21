<?php

namespace controllers;

require_once __DIR__ . '/../vendor/autoload.php';

use models\FriendsModel;
use tools\Utils;
use Exception;

class FriendsController
{
    private $friendsModel;

    /**
     * Construtor da classe. Instancia o modelo de amigos (FriendsModel) para ser usado nas operações.
     */
    public function __construct()
    {
        $this->friendsModel = new FriendsModel();  // Instancia o modelo FriendsModel, que manipula a lógica de dados dos amigos
    }

    /**
     * Método responsável por obter os amigos de um usuário com base no ID fornecido na requisição.
     */
    public function getFriendsById()
    {
        try {
            // Lê o corpo da requisição e decodifica o JSON em um array associativo
            $data = json_decode(file_get_contents('php://input'), true);

            // Verifica se o ID foi fornecido no corpo da requisição
            if (isset($data['id'])) {
                // Chama o método getFriendsById do modelo para obter os amigos do usuário
                $friends = $this->friendsModel->getFriendsById((string)$data['id']);  // Converte o ID para string antes de passar para o modelo
                // Retorna a resposta JSON com a lista de amigos
                Utils::jsonResponse([
                    'friends' => $friends
                ], 200);
            } else {
                // Se o ID não for fornecido, lança uma exceção
                throw new Exception("ID não fornecido.");
            }
        } catch (Exception $e) {
            // Caso ocorra algum erro, retorna a mensagem de erro em formato JSON
            Utils::jsonResponse([
                'error' => $e->getMessage()
            ], 500);  // Retorna o erro com o código de status 500 (erro interno do servidor)
        }
    }
}
