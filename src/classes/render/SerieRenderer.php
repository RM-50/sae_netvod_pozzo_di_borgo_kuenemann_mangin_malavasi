<?php

namespace iutnc\netvod\render;

use iutnc\netvod\video\Episode;
use iutnc\netvod\video\Serie;

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
                    <p> <img src="../../rsrc/minSerie/carSerie.png"> {$this->serie->titreSerie}
                    </p>
                    EOF;

        return $html;
    }

    protected function long()
    {
        $html = <<<EOF
            <div>
                <p>Titre : {$this->serie->titreSerie} {$this->serie->genre}  {$this->serie->publicVise} {$this->serie->descriptif} {$this->serie->anneeSortie}
                 {$this->serie->dateAjout} {$this->serie->nbEpisodes}
                </p>
               
        EOF;
        foreach ($this->serie->listeEpisode as $value) {
            $render = new EpisodeRenderer($value);
            $html .= $render->render(2);
        }
        $html .= "</div>";
        return $html;
    }

    public function render(int $selector) : string
    {
        return $this->rendered = ($selector == Renderer::LONG) ? $this->long(): $this->short();
    }
}