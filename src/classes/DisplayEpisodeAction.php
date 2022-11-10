<?php

namespace iutnc\netvod;

use iutnc\netvod\db\ConnectionFactory;
use iutnc\netvod\render\EpisodeRenderer;
use iutnc\netvod\render\Renderer;
use iutnc\netvod\render\SerieRenderer;
use iutnc\netvod\video\Episode;
use iutnc\netvod\video\Serie;
use PDOException;

class DisplayEpisodeAction extends action\Action
{

    public function execute(): string
    {
        $html = '';
        if (isset($_SESSION['user_connected'])) {
            $html .= " ";
            $sqlEpisode = "SELECT id, titre, resume, duree, file FROM episode where id = ?";

            try {
                $db = ConnectionFactory::makeConnection();
                $stmt_episode = $db->prepare($sqlEpisode);
                $stmt_episode->bindParam(1, $_GET['id']);
                $stmt_episode->execute();
                $row_episode = $stmt_episode->fetch(\PDO::FETCH_ASSOC);

                $episodeSelect = new Episode($row_episode['id'],$row_episode['titre'], $row_episode['file']);

                $renderer = new EpisodeRenderer($episodeSelect);

                $html .= " 
                        <p>
                         {$renderer->render(Renderer::LONG)} 
                        </p>     
                      "
                ;

            } catch (PDOException $e) {
                echo $e->getMessage();
            }
        }
        return $html;
    }
}