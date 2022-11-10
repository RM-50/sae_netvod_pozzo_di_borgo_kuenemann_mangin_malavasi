<?php

namespace iutnc\netvod\action;

use iutnc\netvod\db\ConnectionFactory;
use iutnc\netvod\render\EpisodeRenderer;
use iutnc\netvod\render\SerieRenderer;
use iutnc\netvod\video\Episode;
use iutnc\netvod\video\Serie;
use PDOException;

class DisplayEpisodeAction extends Action
{



    /**
     * @return string
     */

    public function execute(): string
    {
        $html = '';
        $renderer = '';
        if (isset($_SESSION['user_connected'])) {

            $sqlEpisode = "SELECT id, titre, resume, duree, file FROM episode where id = ?";

            try {
                $db = ConnectionFactory::makeConnection();
                $stmt_episode = $db->prepare($sqlEpisode);
                $stmt_episode->bindParam(1, $_GET['id']);
                $stmt_episode->execute();

                while ($row = $stmt_episode->fetch(\PDO::FETCH_ASSOC)) {
                    $episode = new Episode($row['id'],$row['titre'], $row['file']);
                    $renderer = new EpisodeRenderer($episode);
                }
                $html =  $renderer->render(2);
                $id= $_GET['id'];
            } catch (PDOException $exception) {
                echo $exception->getMessage();
            }
        }
        return $html;
    }

}