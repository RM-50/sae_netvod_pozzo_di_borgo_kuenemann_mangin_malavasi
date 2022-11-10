<?php

namespace iutnc\netvod\commentaire;

use iutnc\netvod\db\ConnectionFactory;
use PDO;

class Commentaire
{

    public static function getCommentaire(int $id):array
    {
        $sql = "SELECT commentaire FROM avis where id_serie = ?";
        $db = ConnectionFactory::makeConnection();
        $stmt_serie = $db->prepare($sql);
        $stmt_serie->bindParam(1, $id);
        $stmt_serie->execute();
        $listeCommentaire = [];
        while($row_serie = $stmt_serie->fetch(PDO::FETCH_ASSOC))
        {
            $listeCommentaire[] = $row_serie["commentaire"];
        }
        return $listeCommentaire;
    }

}