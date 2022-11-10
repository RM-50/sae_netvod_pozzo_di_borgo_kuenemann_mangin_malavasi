<?php

namespace iutnc\netvod\action;

use iutnc\netvod\db\ConnectionFactory;
use iutnc\netvod\render\Renderer;
use iutnc\netvod\render\SerieRenderer;
use iutnc\netvod\video\Episode;
use iutnc\netvod\video\Serie;
use iutnc\netvod\note\Note;
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
                    $visio = $user->visio;
                    $html .= "<br>";

                    $favoris = "";
                    $Visionnage="";

                    // Ajout/Suppression des favoris
                    if (filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT)
                        || filter_var($_GET['favoris'],FILTER_SANITIZE_STRING)) {
                        if (isset($_GET['favoris'])) {
                            if ($_GET['favoris'] == 3) {
                                $favoris = "favoris=3";
                                if ($pref->isPref($row_serie["id"])) {
                                    $pref->delPreference($_GET['id']);
                                }
                                $html .= "<br><a href='?action=display-serie&id={$_GET['id']}&favoris=1&$Visionnage'>Ajoutez aux favoris</a>";
                            } else if ($_GET['favoris'] == 1) {
                                $favoris="favoris=1";
                                if (!$pref->isPref($row_serie["id"])) {
                                    $mail = $user->email;
                                    $idUser = Serie::getIdUser($mail);
                                    $pref->addPreference($idUser, $_GET['id'], $serie);
                                }
                                $html .= "<a href='?action=display-serie&id={$_GET['id']}&favoris=3&$Visionnage'>Retirez des favoris</a>";
                            } else if ($_GET['favoris'] == 2) {
                                $favoris="favoris=2";
                                $html .= "<a href='?action=display-serie&id={$_GET['id']}&favoris=3&$Visionnage'>Retirez des favoris</a>";
                            }
                        }else {
                            $html .= "<a href='?action=display-serie&id={$_GET['id']}&favoris=1&$Visionnage'>Ajoutez aux favoris</a>";
                        }
                        $html .= "<br>";
                        $html .= "<br>";
                        //Ajout / Suppression des vidéos en cours de lecture
                        if (isset($_GET['EnCours'])) {
                            if ($_GET['EnCours'] == 3) {
                                $Visionnage="Visionnage=3";
                                if (!$visio->isVideoEnCours($row_serie["id"])) {
                                    $visio->delVideoEnCours($_GET['id']);
                                }
                                $html .= "<br><a href='?action=display-serie&id={$_GET['id']}&$favoris&VIsionnage=1'>Ajoutez aux Visionnages</a>";
                            }

                            else if ($_GET['EnCours'] == 1) {
                                $Visionnage="Visionnage=1";
                                if (!$visio->isVideoEnCours($row_serie["id"])) {
                                    $mail = $user->email;
                                    $idUser = Serie::getIdUser($mail);
                                    $visio->addVideoEnCours($idUser, $_GET['id'], $serie);
                                }
                                $html .= "<a href='?action=display-serie&id={$_GET['id']}&$favoris&Visionnages=3'>Retirez des Visionnages</a>";
                            } else if ($_GET['EnCours'] == 2) {
                                $Visionnage="Visionnage=2";
                                $html .= "<a href='?action=display-serie&id={$_GET['id']}&$favoris&Visionnages=3'>Retirez des Visionnages</a>";
                            }

                        }else {
                            $html .= "<a href='?action=display-serie&id={$_GET['id']}&$favoris&Visionnages=1'>Ajoutez aux Visionnages</a>";
                        }

                    }




                    else {
                        $html = "<h3>Vous ne pouvez pas modifier directement l'id de la serie</h3>";
                    }

                }


                catch (PDOException $e) {
                    echo $e->getMessage();
                }
            }else
            {
                $html = "Le compte doit être activé pour accéder à cette fonctionnalité";
            }
        }
        else
        {
            $html = "Vous devez vous connecter pour accéder à cette fonctionnalité";
        }



        /*$id= $_GET['id'];
        $html .=<<<END
        </br>
        <br>
        <br>
        <button onclick="window.location.href='index.php?action=note&id=$id'">noter</button>
        END;
        */

        return $html;
    }
}