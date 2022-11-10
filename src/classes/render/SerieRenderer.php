<?php

namespace iutnc\netvod\render;

use iutnc\netvod\video\Episode;
use iutnc\netvod\video\Serie;
use iutnc\netvod\note\Note;
use iutnc\netvod\action\DisplaySerieActionAction;

class SerieRenderer implements Renderer
{
    protected Serie $serie;

    public function __construct(Serie $serie)
    {
        $this->serie = $serie;
    }

    protected function short(): string
    {
        $html = <<<EOF
                    <p> 
                        {$this->serie->titreSerie}
                    </p>
                    EOF;

        return $html;
    }

    protected function long()
    {
        $id= $_GET['id'];
        $html = <<<EOF
            <div>
                <p> Titre : {$this->serie->titreSerie} {$this->serie->genre}  {$this->serie->publicVise} {$this->serie->descriptif} {$this->serie->anneeSortie}
                 {$this->serie->dateAjout} {$this->serie->nbEpisodes}
                </p>
                <form id="serie" method="post">
               
        EOF;
        foreach ($this->serie->listeEpisode as $value) {
            $render = new EpisodeRenderer($value);
            $current = $render->render(1);
            $html .= "<button id='serie' style='width: 300px' formaction='index.php?action=display-episode&id={$value->id}'> $current </button> </br>";
        }
        $html .= " </form> </div>";
        return $html;
    }

    public function render(int $selector) : string
    {
        return $this->rendered = ($selector == Renderer::LONG) ? $this->long(): $this->short();
    }
}