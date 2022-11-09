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
        return '<p class="ep">'.$this->serie->titreSerie;
    }

    protected function long()
    {
        $html = <<<EOF
            <div>
                <p>Titre : {$this->serie->titreSerie}</p>
                <p>Genre : {$this->serie->genre}</p>
                <p>Description : {$this->serie->publicVise}</p>
                <p>Description : {$this->serie->descriptif}</p>
                <p>Année de sortie : {$this->serie->anneeSortie}</p>
                <p>Date ajout : {$this->serie->dateAjout}</p>
                <p>Nombre d'episode : {$this->serie->nbEpisodes}</p>
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