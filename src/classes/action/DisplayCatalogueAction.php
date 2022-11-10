<?php

namespace iutnc\netvod\action;

use iutnc\netvod\db\ConnectionFactory;
use iutnc\netvod\render\Renderer;
use iutnc\netvod\render\SerieRenderer;
use iutnc\netvod\tri\TriCriteres;
use iutnc\netvod\tri\TriMotCle;
use iutnc\netvod\video\Episode;
use iutnc\netvod\video\Serie;
use PDOException;

class  DisplayCatalogueAction extends Action
{
    public function execute(): string
    {
        if (isset($_SESSION['user_connected'])) {
            $user = unserialize($_SESSION['user_connected']);
            if ($user->active === 1) {
                $html = <<<END
                        <form id='rechercheMotCle' method='POST' action='?action=display-catalogue'>
                            <label for="cle">Mot clé de recherche :</label>
                            <input type="text" name="cle">
                            <button type="submit">Rechercher !</button>
                        </form>
                        END;
                if ($this->http_method === "POST") {
                    if (isset($_POST["cle"])) {
                        $idSeries = TriMotCle::tri($_POST["cle"]);
                        $html .= $this->afficherCatalogue($idSeries);
                    }
                } else {
                    $html .= $this->afficherCatalogue();
                }
            } else {
                $html = "Le compte doit être activé pour utiliser cette fonctionnalité";
            }
            $html = <<<END
                <form id='rechercheMotCle' method='POST' action='?action=display-catalogue'>
                    <label for="cle">Mot clé de recherche :</label>
                    <input type="text" name="cle">
                    <button type="submit">Rechercher !</button>
                </form>
                END;
            $html .= <<<END
                    <nav id="deroule">
                        <ul id="serie">
                            <li class="menu-deroulant">
                                <a id="amenu" href="">Trier</a>
                                    <ul class="sous-menu">
                                        <li><a id="amenu" href="?action=display-catalogue&sort=titre">Titre</a></li>
                                        <li><a id="amenu" href="?action=display-catalogue&sort=date">Date</a></li>
                                        <li><a id="amenu" href="?action=display-catalogue&sort=annee">Annee de creation</a></li>
                                        <li><a id="amenu" href="?action=display-catalogue&sort=descriptif">Descriptif</a></li>
                                    </ul>
                            </li>
                        </ul>
                    </nav>
            END;

            if ($this->http_method === "POST") {
                if (isset($_POST["cle"])) {
                    $idSeries = TriMotCle::tri($_POST["cle"]);
                    $html .= $this->afficherCatalogue($idSeries);
                }
            } else {
                if (isset($_GET["sort"])) {
                    $idSeries = -1;
                    if ($_GET["sort"] === "titre") {
                        $idSeries = TriCriteres::tri("titre");
                    } else if ($_GET["sort"] === "date") {
                        $idSeries = TriCriteres::tri("date_ajout");
                    } else if ($_GET["sort"] === "annee") {
                        $idSeries = TriCriteres::tri("annee");
                    } else if ($_GET["sort"] === "descriptif") {
                        $idSeries = TriCriteres::tri("descriptif");

                    }
                    $html .= $this->afficherCatalogue($idSeries);
                } else {
                    $html .= $this->afficherCatalogue();
                }
            }
        }
        else
        {
            $html = "Il faut se connecter avant d'accéder au site";
        }
        return $html;
    }

    public function afficherCatalogue(array $idSeries=[]):string
    {
        $html = "<div><h1> <a> Notre catalogue : </a></h1></div> <br>";
        if ($idSeries == []) {
                try{
                    $stmt_serie = $this->requestSerie();
                    while ($row_serie = $stmt_serie->fetch(\PDO::FETCH_ASSOC)) {
                        $id = $row_serie["id"];
                        $titre = $row_serie["titre"];
                        $html .= $this->getHtml($titre, $html,$id);
                    }
                }catch (PDOException $e){
                    echo $e->getMessage();
                }

            } else {
            foreach ($idSeries as $id) {
                $sqlSerie = "SELECT titre FROM serie where id = ?";

                $db = ConnectionFactory::makeConnection();
                $stmt_serie = $db->prepare($sqlSerie);
                $stmt_serie->bindParam(1, $id);
                $stmt_serie->execute();
                $row = $stmt_serie->fetch(\PDO::FETCH_ASSOC);
                $html .= $this->getHtml($row["titre"], $html,$id-1);
            }
            }
        return $html;
        }

        public function requestSerie():\PDOStatement
        {
            $stmt_serie = null;
            try {

            $sqlSerie = "SELECT * FROM serie";

            $db = ConnectionFactory::makeConnection();
            $stmt_serie = $db->prepare($sqlSerie);
            $stmt_serie->execute();

            }catch (PDOException $e){
                echo $e->getMessage();
            }
            return $stmt_serie;
        }

        public function requestEpisode(int $row_serie):\PDOStatement
        {
            $stmt_serie = null;
            try {

                $sqlLstEps = "SELECT titre, file FROM episode where serie_id = ?";

                $db = ConnectionFactory::makeConnection();
                $stmt_serie = $db->prepare($sqlLstEps);
                $stmt_serie->bindParam(1, $row_serie);
                $stmt_serie->execute();
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
            return $stmt_serie;
        }

    /**
     * @param string $titre
     * @param string $html
     * @param int $id
     * @return string
     */
    private function getHtml(string $titre, string $html,int $id=-1): string
    {
        $stmt = $this->requestEpisode($id);
        $listeEpisode = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $listeEpisode [] = new Episode($row['titre'], $row['file']);
        }
        $serie = new Serie($titre, $listeEpisode);
        $renderer = new SerieRenderer($serie);

        $user = unserialize($_SESSION["user_connected"]);
        $pref = $user->pref;
        $favoris = "";
        if ($pref->isPref($id)) {
            $favoris = "&favoris=2";
        }
        return "             
            <a href='?action=display-serie&id={$id}$favoris'>
            {$renderer->render(Renderer::COMPACT)} 
            </a> <br>
            ";
    }
}