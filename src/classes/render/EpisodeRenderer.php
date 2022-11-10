<?php

namespace iutnc\netvod\render;

use iutnc\netvod\video\Episode;

class EpisodeRenderer implements Renderer
{
    protected Episode $episode;
    protected string $rendered;


    public function __construct(Episode $eps)
    {
        $this->episode = $eps;
    }

    protected function short() : string
    {
        return '<p class="ep">'.$this->episode->titre;
    }

    protected function long() : string
    {
        $content = '<p class="ep">'. $this->episode->filename. " | ";
        $content .= " | Duree: " . $this->episode->titre;
        $content .= " | resume: " . $this->episode->resume;
        $content .= '</p>';
        return $content;
    }

    public function render(int $selector) : string
    {
        return $this->rendered = ($selector == Renderer::LONG) ? $this->long(): $this->short();
    }
}

