<?php



namespace iutnc\netvod\action;


use iutnc\netvod\activeRecord\Episode;
use iutnc\netvod\application\User;
use iutnc\netvod\render\EpisodeRenderer;
use iutnc\netvod\visionnage\VisioEnCours;
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
            try {
                $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

                $row = Episode::getEpisode($id);

                $episode = new Episode($row['id'],$row['titre'], $row['file']);
                $renderer = new EpisodeRenderer($episode);
                $html =  $renderer->render(1);

                VisioEnCours::addVideoEnCours(User::getId($user->email),$row['id']);
            } catch (PDOException $exception) {
                echo $exception->getMessage();
            }
        }
        return $html;
    }

}