<?php

namespace iutnc\netvod\preference;

use Exception;
use iutnc\netvod\db\ConnectionFactory;

class Preferences
{
    protected array $series;

    /**
     * Constructor
     */
    public function __construct()
    {
        $db = ConnectionFactory::makeConnection();
        $sql = <<<END
            select nomSerie from preferences
        END;
        $st = $db->prepare($sql);
        $st->execute();
        while ($values = $st->fetch()) {
            $this->series[] = $values["nom"];
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