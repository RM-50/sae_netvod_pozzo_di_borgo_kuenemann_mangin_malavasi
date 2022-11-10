<?php



namespace iutnc\netvod\tri;


use iutnc\netvod\db\ConnectionFactory;



class TriCriteres implements Tri
{



    /**
     * @param string $mot
     * @return array
     */

    public static function tri(string $mot): array
    {
        $sql = "select id from serie order by $mot asc";
        $db = ConnectionFactory::makeConnection();
        $stmt_serie = $db->prepare($sql);

        $stmt_serie->execute();
        $serieWithMot = [];
        while ($value = $stmt_serie->fetch(\PDO::FETCH_ASSOC)) {
            $serieWithMot[] = $value["id"];
        }
        return $serieWithMot;
    }

}