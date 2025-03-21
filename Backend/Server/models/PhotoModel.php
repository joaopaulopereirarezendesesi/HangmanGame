<?php

namespace models;

use tools\Utils;
use Exception;

class PhotoModel
{
    private $utils;

    public function __construct()
    {
        $this->utils = new Utils();
    }

    public function takePhotoWhithByMatter($matter)
    {
        try {
            $query = "SELECT * FROM photos WHERE MATTER = :matter";
            $params = [':matter' => $matter];
            $result = $this->utils->executeQuery($query, $params, true);

            return $result ?? [];
        } catch (Exception $e) {
            throw new Exception("Erro ao buscar fotos por matéria: " . $e->getMessage());
        }
    }
}
