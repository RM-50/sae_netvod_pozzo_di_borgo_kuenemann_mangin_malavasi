<?php

namespace iutnc\netvod\preference;

use Exception;
use iutnc\netvod\application\User;
use iutnc\netvod\db\ConnectionFactory;
use iutnc\netvod\video\Episode;
use iutnc\netvod\video\Serie;

class Preferences
{
    protected array $series;

    /**
     * Constructor
     */
    public function __construct(int $id)
    {
        $db = ConnectionFactory::makeConnection();
        $sql = <<<END
            select s.id, titre from preferences p, serie s, user u
            where p.id_serie = s.id and p.id_user = u.id and u.id = ?
            END;
        $st = $db->prepare($sql);
        $st->bindParam(1, $id);
        $st->execute();
        while ($values = $st->fetch()) {
            if (!$values) {
                $this->series = [];
                break;
            }
            $stmt2 = $db->prepare("SELECT titre, file FROM episode WHERE serie_id = ?");
            $stmt2->bindParam(1, $values['s.id']);
            $stmt2->execute();
            $liste_episodes = [];
            while ($row = $stmt2->fetch(\PDO::FETCH_ASSOC))
            {
                $liste_episodes[] = new Episode($row['titre'], $row['file']);
            }
            $this->series[] = new Serie($values["titre"], $liste_episodes);
        }
    }

    /**
     * @param $serie
     * @return bool
     */
    public function addPreference($serie):bool
    {
        if (!isset($this->series[$serie])) {
            $this->series[] = $serie;
            $db = ConnectionFactory::makeConnection();
            $sql = <<<END
            insert into preferences (nomSerie) values (?)
            END;
            $st = $db->prepare($sql);
            $st->bindParam(1, $serie);
            $st->execute();
            return true;
        }
        return false;
    }

    /**
     * @param $serie
     * @return bool
     */
    public function delPreference($serie):bool
    {
        if (!isset($this->series[$serie])) {
            unset($this->series[$serie]);
            $db = ConnectionFactory::makeConnection();
            $sql = "delete from preferences where nomSerie = ?";
            $st = $db->prepare($sql);
            $st->bindParam(1, $serie);
            $st->execute();
            return true;
        }
        return false;
    }

    /**
     * @throws Exception
     */
    public function __get(string $name): mixed
    {
        if (!property_exists($this, $name)) throw new Exception("$name: invalid property");
        return $this->$name;
    }
}