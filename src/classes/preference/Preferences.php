<?php

namespace iutnc\netvod\preference;

use Exception;
use iutnc\netvod\activeRecord\Serie;
use iutnc\netvod\db\ConnectionFactory;


class Preferences
{
    /**
     * @param int $idUser
     * @param int $idSerie
     * @return bool
     */
    public static function addPreference(int $idUser, int $idSerie):bool
    {
        $db = ConnectionFactory::makeConnection();
        $sql = <<<END
            insert into preferences (id_user,id_serie) values (?,?)
            END;
        $st = $db->prepare($sql);
        $st->bindParam(1, $idUser);
        $st->bindParam(2, $idSerie);
        return $st->execute();
    }



    /**
     * @param int $id
     * @return bool
     */
    public static function delPreference(int $id):bool
    {
        $db = ConnectionFactory::makeConnection();
        $sql = "delete from preferences where id_serie = ?";
        $st = $db->prepare($sql);
        $st->bindParam(1, $id);
        return $st->execute();
    }



    /**
     * @param int $id
     * @return bool
     */
    public static function isPref(int $id):bool
    {
        $db = ConnectionFactory::makeConnection();
        $sql = "select id_serie from preferences";
        $st = $db->prepare($sql);
        $st->execute();
        while ($values = $st->fetch()) {
            if ($values["id_serie"] === $id) {
                return true;
            }
        }
        return false;
    }

    public static function getPref(int $id):array
    {
        $db = ConnectionFactory::makeConnection();
        $sql = "select * from serie s inner join preferences p on p.id_serie = s.id where id_user = ?";
        $st = $db->prepare($sql);
        $st->bindParam(1, $id);
        $st->execute();
        $res = [];
        while ($values = $st->fetch()) {
            $serie = new Serie($values["titre"], []);
            $res[] = $serie;
        }
        return $res;
    }

    public static function getPrefLength(int $id):int
    {
        $db = ConnectionFactory::makeConnection();
        $sql = "select id_serie from preferences where id_user = ?";
        $st = $db->prepare($sql);
        $st->bindParam(1, $id);
        $st->execute();
        $res = 0;
        while ($values = $st->fetch()) {
            $res++;
        }
        return $res;
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