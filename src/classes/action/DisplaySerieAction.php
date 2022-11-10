<?php



namespace iutnc\netvod\action;


use iutnc\netvod\application\User;
use iutnc\netvod\db\ConnectionFactory;
use iutnc\netvod\render\Renderer;
use iutnc\netvod\render\SerieRenderer;
use iutnc\netvod\video\Episode;
use iutnc\netvod\video\Serie;
use iutnc\netvod\note\Note;
use PDOException;



class DisplaySerieAction extends Action
{



    /**
     * @return string
     */

    public function execute(): string
    {
        $html = '';
        if (isset($_SESSION['user_connected'])) {
            $user = unserialize($_SESSION['user_connected']);
            if ($user->active === 1) {
                $html .= "  <div><h1> <a> Notre catalogue : </a></h1></div>";
                $sqlSerie = "SELECT * FROM serie where id = ?";
                $sqlLstEps = "SELECT id, titre, file FROM episode where serie_id = ?";

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
                        $listeEpisode [] = new Episode($row['id'],$row['titre'], $row['file']);
                    }
                    /**
                    $sqlGenre = "select libelle from genre g, serie2genre s where g.id = s.id_genre and s.id_serie = {$_GET['id']}";
                    $stmt_serie = $db->prepare($sqlGenre);
                    $stmt_serie->execute();
                    $row_genre = $stmt_serie->fetch(\PDO::FETCH_ASSOC);

                    $sqlPublic = "select libelle from genre g, serie2genre s where g.id = s.id_genre and s.id_serie = {$_GET['id']}";
                    $stmt_serie = $db->prepare($sqlPublic);
                    $stmt_serie->execute();
                    $row_public = $stmt_serie->fetch(\PDO::FETCH_ASSOC);
                    */
                    $serie = new Serie($row_serie['titre'], $listeEpisode);
                    // Attribut en public car impossible d'acceder au variable si elles sont protected
                    /**
                    $serie->publicVisee = $row_public["libelle"];
                    $serie->descriptif = $row_serie["descriptif"];
                    $serie->genre = $row_genre["libelle"];
                    $serie->dateAjout = $row_serie["genre"];
                     * */
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
                                if ($pref->isPref($row_serie["id"])) {
                                    $pref->delPreference($_GET['id']);
                                }
                                $html .= "<br><a href='?action=display-serie&id={$_GET['id']}&favoris=1'>Ajoutez aux favoris</a>";
                            } else if ($_GET['favoris'] == 1) {
                                if (!$pref->isPref($row_serie["id"])) {
                                    $mail = $user->email;
                                    $idUser = User::getId($mail);
                                    $pref->addPreference($idUser, $_GET['id'], $serie);
                                }
                                $html .= "<a href='?action=display-serie&id={$_GET['id']}&favoris=3'>Retirez des favoris</a>";
                            } else if ($_GET['favoris'] == 2) {
                                $html .= "<a href='?action=display-serie&id={$_GET['id']}&favoris=3'>Retirez des favoris</a>";
                            }
                        }else {
                            $html .= "<a href='?action=display-serie&id={$_GET['id']}&favoris=1'>Ajoutez aux favoris</a>";
                        }
                    }else {
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

        return $html;
    }
}