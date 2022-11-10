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
        $this->series = [];
        while ($values = $st->fetch()) {
            $stmt2 = $db->prepare("SELECT titre, file FROM episode WHERE serie_id = ?");
            $stmt2->bindParam(1, $values['s.id']);
            $stmt2->execute();
            $liste_episodes = [];
            while ($row = $stmt2->fetch(\PDO::FETCH_ASSOC))
            {
                $liste_episodes[] = new Episode($row['id'],$row['titre'], $row['file']);
            }
            $this->series[] = new Serie($values["titre"], $liste_episodes);
        }
    }



    /**
     * @param int $idUser
     * @param int $idSerie
     * @param Serie $serie
     * @return bool
     */

    public function addPreference(int $idUser,int $idSerie,Serie $serie):bool
    {
        if (!isset($this->series[$idSerie])) {
            $this->series[$idSerie] = $serie;
            $db = ConnectionFactory::makeConnection();
            $sql = <<<END
            insert into preferences (id_user,id_serie) values (?,?)
            END;
            $st = $db->prepare($sql);
            $st->bindParam(1, $idUser);
            $st->bindParam(2, $idSerie);
            $st->execute();
            return true;
        }
        return false;
    }



    /**
     * @param int $id
     * @return bool
     */

    public function delPreference(int $id):bool
    {
        if (!isset($this->series[$id])) {
            unset($this->series[$id]);
            $db = ConnectionFactory::makeConnection();
            $sql = "delete from preferences where id_serie = ?";
            $st = $db->prepare($sql);
            $st->bindParam(1, $id);
            $st->execute();
            return true;
        }
        return false;
    }



    /**
     * @param int $id
     * @return bool
     */

    public function isPref(int $id):bool
    {
        $db = ConnectionFactory::makeConnection();
        $sql = "select id_serie from preferences";
        $st = $db->prepare($sql);
        $st->execute();
        $this->series = [];
        while ($values = $st->fetch()) {
            if ($values["id_serie"] === $id) {
                return true;
            }
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