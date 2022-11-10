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
            $sqlEpisode = "SELECT titre, file FROM episode where serie_id = ?";

            try {
                $db = ConnectionFactory::makeConnection();
                $stmt_episode = $db->prepare($sqlEpisode);
                $stmt_episode->bindParam(1, $_GET['id']);
                $stmt_episode->execute();
                $row_episode = $stmt_episode->fetch(\PDO::FETCH_ASSOC);

                $episodeSelect = '';
                while ($row = $stmt_episode->fetch(\PDO::FETCH_ASSOC)) {
                    $episodeSelect = new Episode($row['titre'], $row['file']);
                }
                $episode = new Episode($row_episode['titre'], $episodeSelect);
                $renderer = new EpisodeRenderer($episode);

                $html .= " 
                        
                        <a href='?action=display-serie&id={$row_episode['id']}'>
                         {$renderer->render(Renderer::COMPACT)} 
                        </a>     
                      "
                ;

            } catch (PDOException $e) {
                echo $e->getMessage();
            }
        }
        return $html;
    }
}