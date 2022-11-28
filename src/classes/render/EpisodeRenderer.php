<?php



namespace iutnc\netvod\render;


use iutnc\netvod\video\Episode;



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
        $filename = "./rsrc/minEpisode/".$filename;
        $content = "
                    <div class='contentEpisode'>
                        <img src='{$filename}' alt='image episode' class='imgEpisode'>
                        <div class='alignContentOver'>
                            <p class='ep'>{$this->episode->titre}</p>
                            <button class='btnEpisode' onclick='window.location.href=\"index.php?action=display-episode&id={$this->episode->id}\"'>
                                <img src='./rsrc/play.png' alt='play' class='play'>
                                <p>Regarder</p>
                            </button>
                        </div>
                    </div>";
        $content .= "<style>
            .contentEpisode {
                width: 40em;
                height: 20em;
            }
            .contentEpisode img {
                display: block;
                width: 40em;
                height: 20em;
                margin: auto;
            }
            .contentEpisode .alignContentOver {
                position: fixed;
                align-items: center;
                display: flex; 
                left: 10%;
                top: 75%;
                width: 80%;
                color: white;
                text-align: left;
            }
            .contentEpisode .ep {
                font-size: 1.5em;
                color: #444444;
                text-shadow: white 1px 1px;
            }
            .content .play {
                width: 15px;
                height: 15px; 
                padding-right: 0.2em;
            }
            .contentEpisode .btnEpisode {                
                height: 3em;
                align-items: center;
                appearance: none;
                border: 0;
                border-radius: 4px;
                cursor: pointer;
                display: flex;
                -webkit-box-pack: center;
                opacity: 1;
                padding: 0.8em;
                user-select: none;
                word-break: break-word;
                white-space: nowrap;
                position: absolute; 
                left: 85%;
            }
            </style>";
        return $content;

    }



    /**
     * @return string
     */

    protected function long() : string
    {
        $id = $_GET['id'];
        $content = '<p class="ep">'.$this->episode->titre. " | ";
        $content .= " | resume: " . $this->episode->resume;
        $content .= '</p>';
        $content .= "<video controls width='1000' src='./rsrc/episode/{$this->episode->filename}'></video>";
        $content .=  "</br><button onclick=\"window.location.href='index.php?action=note&id=$id'\">noter</button>";
        return $content;
    }

    protected function shortImage(): string
    {
        $content = "<h3>{$this->episode->titre}</h3>";
        $filename = str_replace("mp4", "png", $this->episode->filename);
        $content .= "<img src='./rsrc/minEpisode/$filename' alt='image episode' class='imgEpisode'>";
        return $content;
    }

    protected function shortImageCatalogue(): string
    {
        $filename = str_replace("mp4", "png", $this->episode->filename);
        return "<img src='./rsrc/minEpisode/$filename' alt='image episode' class='imgEpisode'>";
    }



    /**
     * @param int $selector
     * @return string
     */

    public function render(int $selector) : string
    {
        return match ($selector) {
            1 => $this->short(),
            2 => $this->long(),
            3 => $this->shortImage(),
            4 => $this->shortImageCatalogue(),
            default => "Erreur de rendu",
        };
    }
}

