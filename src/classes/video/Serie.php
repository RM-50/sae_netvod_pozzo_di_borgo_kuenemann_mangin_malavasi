<?php

namespace iutnc\netvod\video;

use Exception;
use iutnc\netvod\db\ConnectionFactory;

class Serie
{
    protected string $titreSerie,$genre,$publicVise,$descriptif;
    protected int $anneeSortie;
    protected string $dateAjout;
    protected int $nbEpisodes;
    protected array $listeEpisode;

    /**
     * @param string $titre titre de la serie
     * @param array $listeEps
     */
    public function __construct(string $titre, array $listeEps)
    {
        $this->titreSerie = $titre;
        $this->listeEpisode = $listeEps;
        $this->genre = "";
        $this->publicVise = "";
        $this->descriptif = "";
        $this->anneeSortie = 0;
        $this->dateAjout = "";
        $this->nbEpisodes = 0;
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



    /**
     * @param string $mail
     * @return int
     */

    public static function getIdUser(string $mail):int
    {
        $sql = "select id from User where email = ?";
        $db = ConnectionFactory::makeConnection();
        $stmt_serie = $db->prepare($sql);
        $stmt_serie->bindParam(1, $mail);
        $stmt_serie->execute();
        $row_serie = $stmt_serie->fetch(\PDO::FETCH_ASSOC);
        return $row_serie["id"];
    }



    /**
     * @param int $id
     * @return float
     */

    public static function getNote(int $id):float
    {
        $sql = "SELECT round(avg(note),1) as note FROM avis where id_serie = ?";
        $db = ConnectionFactory::makeConnection();
        $stmt_serie = $db->prepare($sql);
        $stmt_serie->bindParam(1, $id);
        $stmt_serie->execute();
        $row_serie = $stmt_serie->fetch(\PDO::FETCH_ASSOC);
        if (!$row_serie)
        {
            $note = 0;
        }
        else
        {
            $note = floatval($row_serie["note"]);
        }
        return $note;
    }

}