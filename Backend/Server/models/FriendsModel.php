<?php

namespace models;

use tools\Utils;
use Exception;

class FriendsModel
{
    private $utils;

    /**
     * Construtor da classe. Instancia o objeto Utils para uso posterior.
     */
    public function __construct()
    {
        $this->utils = new Utils();  // Instancia a classe Utils para executar funções como consultas no banco de dados.
    }

    /**
     * Obtém a lista de amigos de um usuário com base no seu ID.
     * 
     * @param int $id ID do usuário cujos amigos serão recuperados
     * @return array Lista de amigos do usuário
     * @throws Exception Em caso de erro ao executar a consulta
     */
    public function getFriendsById($id)
    {
        // Apenas para depuração, imprime o tipo do ID fornecido
        echo gettype($id);

        try {
            // Consulta SQL para obter os amigos de um usuário, utilizando UNION para pegar ambas as direções da amizade
            $query = "
            SELECT u.* 
            FROM users u 
            JOIN friends f ON u.ID_U = f.ID_A 
            WHERE f.ID_U = :id 
            UNION 
            SELECT u.* 
            FROM users u 
            JOIN friends f ON u.ID_U = f.ID_U 
            WHERE f.ID_A = :id2;
        ";

            // Parâmetros da consulta
            $params = [
                ':id' => (string) $id,    // ID do usuário, convertido para inteiro
                ':id2' => (string) $id   // ID do usuário novamente, para a segunda parte da união
            ];

            // Executa a consulta usando a função executeQuery da classe Utils e retorna os resultados
            $result = $this->utils->executeQuery($query, $params, true);

            return $result ?? [];  // Retorna a lista de amigos ou um array vazio se não houver amigos
        } catch (Exception $e) {
            // Lança uma exceção com uma mensagem de erro personalizada caso a consulta falhe
            throw new Exception("Erro ao obter amigos do usuário: " . $e->getMessage());
        }
    }
}
