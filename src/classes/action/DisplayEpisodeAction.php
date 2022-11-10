<?php



namespace iutnc\netvod\action;


use iutnc\netvod\application\User;
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
        if (isset($_SESSION['user_connected'])) {
            $user = unserialize($_SESSION['user_connected']);
            $enCours = $user->getVisio();
            $sqlEpisode = "SELECT * FROM episode where id = ?";

            try {
                $db = ConnectionFactory::makeConnection();
                $stmt_episode = $db->prepare($sqlEpisode);
                $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
                $stmt_episode->bindParam(1, $id);
                $stmt_episode->execute();
                $row = $stmt_episode->fetch(\PDO::FETCH_ASSOC);

                $episode = new Episode($row['id'],$row['titre'], $row['file']);
                $renderer = new EpisodeRenderer($episode);

                $enCours->addVideoEnCours($episode,User::getId($user->email),$row["serie_id"]);

                $html =  $renderer->render(2);

            } catch (PDOException $exception) {
                echo $exception->getMessage();
            }
        }
        return $html;
    }

}