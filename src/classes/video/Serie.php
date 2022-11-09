<?php

namespace iutnc\netvod\video;

use iutnc\netvod\exception\InvalidPropertyNameException;

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
     * @param array $tabEps liste des episodes de la serie
     */
    public function __construct(string $titre, array $listeEps)
    {
        $this->titreSerie = $titre;
        $this->listeEpisode = $listeEps;
    }

    /**
     * @throws InvalidPropertyNameException
     */
    public function __get(string $name): mixed {
        return (isset($this->$name)) ? $this->$name : throw new InvalidPropertyNameException("Proprietée invalide");
    }

}