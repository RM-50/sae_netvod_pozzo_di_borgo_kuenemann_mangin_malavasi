<?php



namespace iutnc\netvod\render;


use iutnc\netvod\video\Episode;
use iutnc\netvod\video\Serie;
use iutnc\netvod\note\Note;
use iutnc\netvod\action\DisplaySerieActionAction;



class SerieRenderer implements Renderer
{
    protected Serie $serie;



    /**
     * @param Serie $serie
     */

    public function __construct(Serie $serie)
    {
        $this->serie = $serie;
    }



    /**
     * @return string
     */

    protected function short(): string
    {
        $html = <<<EOF
                    <p> 
                        {$this->serie->titreSerie}
                    </p>
                    EOF;

        return $html;
    }



    /**
     * @return string
     */

    protected function long()
    {
        $id= $_GET['id'];
        $html = <<<EOF
            <div xmlns="http://www.w3.org/1999/html">
                <p> Titre : {$this->serie->titreSerie} </br> {$this->serie->getNote($_GET['id'])} </br> <button onclick="window.location.href='index.php?action=display-commentaire&id=$id'">Afficher les commentaires</button> </br> {$this->serie->genre}  {$this->serie->publicVise} {$this->serie->descriptif} {$this->serie->anneeSortie}
                 {$this->serie->dateAjout} {$this->serie->nbEpisodes}
                </p>
                <form id="serieF" method="post">
               
        EOF;
        foreach ($this->serie->listeEpisode as $value) {
            $render = new EpisodeRenderer($value);
            $current = $render->render(1);
            $html .= "<button class='serie'' formaction='index.php?action=display-episode&id={$value->id}'> $current </button> </br>";
        }
        $html .= " </form> </div>";
        return $html;
    }



    /**
     * @param int $selector
     * @return string
     */

    public function render(int $selector) : string
    {
        return $this->rendered = ($selector == Renderer::LONG) ? $this->long(): $this->short();
    }
}