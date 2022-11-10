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
        $filename = str_replace("mp4", "png", $this->episode->filename);
        $content =  '<p class="ep">'.$this->episode->titre . '</p>';
        $content .= "<img alt='img' src='./rsrc/minEpisode/$filename'> ";
        return $content;
    }

    protected function long() : string
    {
        $filename = str_replace("mp4", "png", $this->episode->filename);
        $content = '<p class="ep">'.$this->episode->titre. " | ";
        $content .= " | resume: " . $this->episode->resume;
        $content .= '</p>';
        $content .= "<video controls width='1000' alt='img' src='./rsrc/episode/{$this->episode->filename}'> ";
        return $content;
    }

    public function render(int $selector) : string
    {
        return $this->rendered = ($selector == Renderer::LONG) ? $this->long(): $this->short();
    }
}

