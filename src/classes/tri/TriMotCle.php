<?php

namespace iutnc\netvod\tri;

use iutnc\netvod\db\ConnectionFactory;

class TriMotCle
{
    public static function tri(string $mot):array
    {
        $sql = "select titre,descriptif,id from serie";
        $db = ConnectionFactory::makeConnection();
        $stmt_serie = $db->prepare($sql);
        $stmt_serie->execute();
        $serieWithMot = [];
        $i = 0;
        while ($value = $stmt_serie->fetch(\PDO::FETCH_ASSOC)) {
            if (str_contains($value["titre"], $mot) || str_contains($value["descriptif"], $mot)) {
                $serieWithMot[$i] = $value["id"];
            }
            $i++;
        }
        return $serieWithMot;
    }
}