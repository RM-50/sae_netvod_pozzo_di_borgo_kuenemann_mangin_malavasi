<?php

namespace iutnc\netvod\action;

use iutnc\netvod\db\ConnectionFactory;
use iutnc\netvod\render\Renderer;
use iutnc\netvod\render\SerieRenderer;
use iutnc\netvod\video\Episode;
use iutnc\netvod\video\Serie;
use PDOException;

class DisplaySerieAction extends Action
{

    public function execute(): string
    {
        $html = '';
        if (isset($_SESSION['user_connected'])) {
            $user = unserialize($_SESSION['user_connected']);
            if ($user->active === 1) {
                $html .= "  <div><h1> <a> Notre catalogue : </a></h1></div>";
                $sqlSerie = "SELECT titre FROM serie where id = ?";
                $sqlLstEps = "SELECT titre, file FROM episode where serie_id = ?";

                try {
                    $db = ConnectionFactory::makeConnection();
                    $stmt_serie = $db->prepare($sqlSerie);
                    $stmt_serie->bindParam(1, $_GET['id']);
                    $stmt_serie->execute();
                    $row_serie = $stmt_serie->fetch(\PDO::FETCH_ASSOC);

                    $stmt = $db->prepare($sqlLstEps);
                    $stmt->bindParam(1, $_GET['id']);
                    $stmt->execute();
                    $listeEpisode = [];
                    while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                        $listeEpisode [] = new Episode($row['titre'], $row['file']);
                    }
                    $serie = new Serie($row_serie['titre'], $listeEpisode);
                    $renderer = new SerieRenderer($serie);

                    $html = $renderer->render(2);

                } catch (PDOException $e) {
                    echo $e->getMessage();
                }
            }
            else
            {
                $html = "Le compte doit être activé pour accéder à cette fonctionnalité";
            }
        }
        else
        {
            $html = "Vous devez vous connecter pour accéder à cette fonctionnalité";
        }
        return $html;
    }
}