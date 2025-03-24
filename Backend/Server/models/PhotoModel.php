<?php

namespace models;

use tools\Utils;
use Exception;

class PhotoModel
{
    private $utils;

    /**
     * Construtor da classe. Instancia o objeto Utils para realizar operações de banco de dados.
     */
    public function __construct()
    {
        $this->utils = new Utils();  // Instancia a classe Utils para poder executar funções como consultas no banco de dados.
    }

    /**
     * Obtém as fotos relacionadas a uma matéria específica.
     * 
     * @param string $matter A matéria que será utilizada como filtro para as fotos
     * @return array Lista de fotos relacionadas à matéria
     * @throws Exception Se ocorrer um erro ao executar a consulta no banco de dados
     */
    public function takePhotoWithMatter($matter)
    {
        try {
            $query = "SELECT * FROM photos WHERE MATTER = :matter";

            $params = [':matter' => $matter];

            $result = $this->utils->executeQuery($query, $params, true);

            return $result[0]['ADDRESS'] ?? [];
        } catch (Exception $e) {
            throw new Exception("Erro ao buscar fotos por matéria: " . $e->getMessage());
        }
    }
}
