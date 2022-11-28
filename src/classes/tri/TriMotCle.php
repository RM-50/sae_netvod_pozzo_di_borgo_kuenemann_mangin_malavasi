<?php



namespace iutnc\netvod\tri;


use iutnc\netvod\db\ConnectionFactory;



class TriMotCle
{



    /**
     * @param string $mot
     * @return array
     */

    public static function tri(string $mot):array
    {
        if ($mot === '' || $mot === " ") return [];
        $sql = "select titre,descriptif,id from serie";
        $db = ConnectionFactory::makeConnection();
        $stmt_serie = $db->prepare($sql);
        $stmt_serie->execute();
        $serieWithMot = [];
        $mot = strtolower($mot);
        while ($value = $stmt_serie->fetch(\PDO::FETCH_ASSOC)) {
            $titre = strtolower($value['titre']);
            $descriptif = strtolower($value['descriptif']);
            if (str_contains($titre, $mot) || str_contains($descriptif, $mot)) {
                $serieWithMot[] = $value["id"];
            }
        }
        return $serieWithMot;
    }
}