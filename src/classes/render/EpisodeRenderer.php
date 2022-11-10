<?php



namespace iutnc\netvod\render;


use iutnc\netvod\video\Episode;
use iutnc\netvod\video\Serie;


class EpisodeRenderer implements Renderer
{
    protected Episode $episode;
    protected string $rendered;



    /**
     * @param Episode $eps
     */

    public function __construct(Episode $eps)
    {
        $this->episode = $eps;
    }



    /**
     * @return string
     */

    protected function short() : string
    {
        $filename = str_replace("mp4", "png", $this->episode->filename);
        $content =  '<p class="ep">'.$this->episode->titre . '</p>';
        $content .= "<img alt='img' src='./rsrc/minEpisode/$filename'> ";
        return $content;

    }



    /**
     * @return string
     */

    protected function long() : string
    {
        $id = Serie::getIdSerie($id);
        $filename = str_replace("mp4", "png", $this->episode->filename);
        $content = '<p class="ep">'.$this->episode->titre. " | ";
        $content .= " | resume: " . $this->episode->resume;
        $content .= '</p>';
        $content .= "<video controls width='1000' alt='img' src='./rsrc/episode/{$this->episode->filename}'></video>";
        $content .=  "</br><button onclick=\"window.location.href='index.php?action=note&id=$id';'\">noter</button>";
        return $content;
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

