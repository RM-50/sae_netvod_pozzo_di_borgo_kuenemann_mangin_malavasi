<?php

namespace iutnc\netvod\visionnage;

use Exception;
use iutnc\netvod\application\User;
use iutnc\netvod\db\ConnectionFactory;
use iutnc\netvod\video\Episode;
use iutnc\netvod\video\Serie;



class ClassVisio
{
    protected array $visiocours;



    public function __construct(int $id)
    {
        $db = ConnectionFactory::makeConnection();
        $sql = <<<END
            select s.id, titre from videoencours v, serie s, user u
            where v.id_serie = s.id and v.id_user = u.id and u.id = ?
            END;
        $st = $db->prepare($sql);
        $st->bindParam(1, $id);
        $st->execute();
        $this->visiocours = [];
        while ($values = $st->fetch()) {
            $stmt2 = $db->prepare("SELECT titre, file FROM episode WHERE serie_id = ?");
            $stmt2->bindParam(1, $values['s.id']);
            $stmt2->execute();
            $liste_episodes = [];
            while ($row = $stmt2->fetch(\PDO::FETCH_ASSOC))
            {
                $liste_episodes[] = new Episode($row['titre'], $row['file']);
            }
            $this->visiocours[] = new Serie($values["titre"], $liste_episodes);
        }
    }





    public function addPreference($serie):bool
    {
        if (!isset($this->series[$serie])) {
            $this->visiocours[] = $serie;
            $db = ConnectionFactory::makeConnection();
            $sql = <<<END
            insert into videoencours (nomSerie) values (?)
            END;
            $st = $db->prepare($sql);
            $st->bindParam(1, $serie);
            $st->execute();
            return true;
        }
        return false;
    }



    public function delPreference($serie):bool
    {
        if (!isset($this->visiocours[$serie])) {
            unset($this->visiocours[$serie]);
            $db = ConnectionFactory::makeConnection();
            $sql = "delete from videoencours where nomSerie = ?";
            $st = $db->prepare($sql);
            $st->bindParam(1, $serie);
            $st->execute();
            return true;
        }
        return false;
    }


    public function __get(string $name): mixed
    {
        if (!property_exists($this, $name)) throw new Exception("$name: invalid property");
        return $this->$name;
    }


}