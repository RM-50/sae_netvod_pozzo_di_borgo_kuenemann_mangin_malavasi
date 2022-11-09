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
            $html .= "  <div><h1> <a> Notre catalogue : </a></h1></div>";
            $sqlSerie = "SELECT titre,id FROM serie where id = ?";
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


                $user = unserialize($_SESSION["user_connected"]);
                $pref = $user->pref;
                $html .= "<br>";
                if (isset($_GET['favoris'])) {
                    if($_GET['favoris'] == 3){
                        if (!$pref->isPref($row_serie["id"])) {
                            $pref->delPreference($_GET['id']);
                        }
                        $html .= "<br><a href='?action=display-serie&id={$_GET['id']}&favoris=1'>Ajoutez aux favoris</a>";
                    }else if ($_GET['favoris'] == 1){
                        if (!$pref->isPref($row_serie["id"])) {
                            $mail = $user->email;
                            $idUser = Serie::getIdUser($mail);
                            $pref->addPreference($idUser,$_GET['id'],$serie);
                        }
                        $html .= "<a href='?action=display-serie&id={$_GET['id']}&favoris=3'>Retirez des favoris</a>";
                    } else if ($_GET['favoris'] == 2) {
                        $html .= "<a href='?action=display-serie&id={$_GET['id']}&favoris=3'>Retirez des favoris</a>";
                    }
                } else {
                    $html .= "<a href='?action=display-serie&id={$_GET['id']}&favoris=1'>Ajoutez aux favoris</a>";
                }

            } catch (PDOException $e) {
                echo $e->getMessage();
            }
        }
        return $html;
    }
}