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
        return "<p>{$this->serie->titreSerie}</p>";
    }


    protected function shortWithImage():string
    {
        $content = "<div class='serieRenderer'>";
        $content .= "<h2>{$this->serie->titreSerie}</h2>";
        foreach ($this->serie->listeEpisode as $value) {
            $render = new EpisodeRenderer($value);
            $current = $render->render(3);
            $content .= "<a href='index.php?action=display-episode&id={$value->id}'>{$current}</a>";
        }
        return $content."</div>";
    }

    protected function shortWithImageForCatalogue(): string
    {
        $content = "<div class='serieRender'><p>{$this->serie->titreSerie}</p>";
        for ($i = 0; $i < 1; $i++) {
            $render = new EpisodeRenderer($this->serie->listeEpisode[$i]);
            $current = $render->render(4);
            $content .= "$current";
        }
        return $content."</div>";
    }

    /**
     * @return string
     */
    protected function long(): string
    {
        $id= $_GET['id'];
        $note = $this->serie->getNote($_GET['id']);
        $strNote = $note ? 0 : "Serie non notée";
        if ($note != 0) {
            $strNote = "La serie est notée à " . $strNote;
        }
        $html = <<<EOF
            <div>
                <h2>{$this->serie->titreSerie}</h2>
                <p> $strNote </p> </br> <button onclick="window.location.href='index.php?action=display-commentaire&id=$id'">Afficher les commentaires</button> </br> <p> {$this->serie->genre}  {$this->serie->publicVise} {$this->serie->descriptif} {$this->serie->anneeSortie}
                 {$this->serie->dateAjout} {$this->serie->nbEpisodes}
                </p>
                <form id="serieF" method="post">
               
        EOF;
        foreach ($this->serie->listeEpisode as $value) {
            $render = new EpisodeRenderer($value);
            $current = $render->render(2);
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
        return match ($selector) {
            self::COMPACT => $this->short(),
            self::LONG => $this->long(),
            self::COMPACTWITHIMG => $this->shortWithImage(),
            self::COMPACTWITHIMGFORCATALOGUE, => $this->shortWithImageForCatalogue(),
            default => "Erreur de rendu",
        };
    }
}