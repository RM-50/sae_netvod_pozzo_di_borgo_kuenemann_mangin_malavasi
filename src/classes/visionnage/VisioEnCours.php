<?php



namespace iutnc\netvod\visionnage;


use Exception;
use iutnc\netvod\application\User;
use iutnc\netvod\db\ConnectionFactory;
use iutnc\netvod\video\Episode;
use iutnc\netvod\video\Serie;



class VisioEnCours
{

    /**
     * @param int $idUser
     * @param int $idEpisode
     * @return bool
     */
    public static function addVideoEnCours(int $idUser,int $idEpisode):bool
    {
        if (!self::isVideoEnCours($idEpisode)) {
            $db = ConnectionFactory::makeConnection();
            $sql = <<<END
                insert into videoencours (id_episode,id_user) values (?,?)
                END;
            $st = $db->prepare($sql);
            $st->bindParam(1, $idEpisode);
            $st->bindParam(2, $idUser);
            $st->execute();
            return true;
        }
        return false;
    }


    /**
     * @param int $id
     * @return bool
     */
    public static function delVideoEnCours(int $id):bool
    {
        $db = ConnectionFactory::makeConnection();
        $sql = "delete from videoencours where id_episode = ?";
        $st = $db->prepare($sql);
        $st->bindParam(1, $id);
        return $st->execute();
    }



    /**
     * @param int $id
     * @return bool
     */
    public static function isVideoEnCours(int $id):bool
    {
        $db = ConnectionFactory::makeConnection();
        $sql = "select id_episode from videoencours where id_episode = ?";
        $st = $db->prepare($sql);
        $st->bindParam(1, $id);
        $st->execute();
        return $st->rowCount() > 0;
    }

    public static function getVideoEnCours(int $idUser):array
    {
        $db = ConnectionFactory::makeConnection();
        $sql = "select * from videoencours v inner join episode e on v.id_episode = e.id where v.id_user = ?";
        $st = $db->prepare($sql);
        $st->bindParam(1, $idUser);
        $st->execute();
        $episodes = [];
        while ($values = $st->fetch()) {
            $episode = new Episode($values["id_episode"], $values["titre"],$values["file"]);
            $episode->resume = $values["resume"];
            $episode->duree = $values["duree"];
            $episodes[] = $episode;
        }
        return $episodes;
    }

    public static function getVideoEnCoursLength(int $idUser):int
    {
        $db = ConnectionFactory::makeConnection();
        $sql = "select id_episode from videoencours where id_user = ?";
        $st = $db->prepare($sql);
        $st->bindParam(1, $idUser);
        $st->execute();
        $tab = [];
        while ($values = $st->fetch()) {
            $tab[] = $values["id_episode"];
        }
        return count($tab);
    }

    /**
     * @param string $name
     * @return mixed
     * @throws Exception
     */
    public function __get(string $name): mixed
    {
        if (!property_exists($this, $name)) throw new Exception("$name: invalid property");
        return $this->$name;
    }
}