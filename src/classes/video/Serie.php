<?php

namespace iutnc\netvod\video;

use Exception;
use iutnc\netvod\db\ConnectionFactory;
use iutnc\netvod\exceptions\InvalidPropertyNameException;

class Serie
{
    protected string $titreSerie;
    protected string $genre;
    protected string $publicVise;
    protected string $descriptif;
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

    public static function find(mixed $titre)
    {
    }

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
     * @throws Exception
     */
    public function __get(string $name): mixed
    {
        if (!property_exists($this, $name)) throw new Exception("$name: invalid property");
        return $this->$name;
    }

}