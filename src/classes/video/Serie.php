<?php

namespace iutnc\netvod\video;

use Exception;
use iutnc\netvod\db\ConnectionFactory;
use iutnc\netvod\exceptions\InvalidPropertyNameException;
use iutnc\netvod\note\Note;

class Serie
{
    protected string $titreSerie;
    protected string $genre;
    protected string $descriptif;
    protected int $anneeSortie;
    protected string $dateAjout;
    protected int $nbEpisodes;
    protected array $listeEpisode;
    protected mixed $publicVise;

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
     * @param int $id
     * @return string
     */
    public static function getTitre(int $id): string
    {
        $sql = "SELECT titre FROM serie WHERE id = ?";
        $db = ConnectionFactory::makeConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row['titre'];
    }

    /**
     * @return array
     */
    public static function getAllIdSerie(): array
    {
        $sql = "SELECT id FROM serie";
        $db = ConnectionFactory::makeConnection();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $listeId = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $listeId [] = $row['id'];
        }
        return $listeId;
    }


    /**
     * @param string $titre
     * @return int
     * @throws Exception
     */
    public static function getIdSerie(string $titre):int
    {
        $titreR = str_replace("<p>","",$titre);
        $titreR = str_replace("</p>","",$titreR);
        $sql = "select id from serie where titre like ?";
        $db = ConnectionFactory::makeConnection();
        $stmt_serie = $db->prepare($sql);
        $stmt_serie->bindParam(1, $titreR);
        $stmt_serie->execute();
        $row_serie = $stmt_serie->fetch(\PDO::FETCH_ASSOC);
        if ($row_serie === false) {
            throw new Exception("Serie $titreR not found");
        }
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