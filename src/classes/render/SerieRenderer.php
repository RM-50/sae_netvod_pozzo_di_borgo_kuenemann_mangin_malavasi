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
        return '<table class="table">
                       <thead class="thead-dark">
                           <tr>
                             <th scope="col">Playlist: '.$this->serie->titreSerie.'</th>
                             <th scope="col">Titre</th>
                             <th scope="col">Genre</th>
                              <th scope="col">Artiste | Album ou Auteur</th>
                              <th scope="col">Annee</th>
                              <th scope="col">Duree</th>
                           </tr>
                       </thead>
                    <tbody>  
        ';
    }

    public function render(int $selector) : string
    {
        return $this->rendered = ($selector == Renderer::LONG) ? $this->long(): $this->short();
    }

}