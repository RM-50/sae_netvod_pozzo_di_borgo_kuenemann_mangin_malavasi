<?php

namespace iutnc\netvod\action;

use iutnc\netvod\db\ConnectionFactory;
use iutnc\netvod\render\Renderer;
use iutnc\netvod\render\SerieRenderer;
use iutnc\netvod\tri\RechMotCle;
use iutnc\netvod\video\Episode;
use iutnc\netvod\video\Serie;
use PDOException;

class  DisplayCatalogueAction extends Action
{
    public function execute(): string
    {
        $html = <<<END
                <form id='rechercheMotCle' method='POST' action='?action=display-catalogue'>
                    <label for="cle">Mot clé de recherche :</label>
                    <input type="text" name="cle">
                    <button type="submit">Rechercher !</button>
                </form>
                END;
        if ($this->http_method === "POST") {
            if (isset($_POST["cle"])) {
                $idSeries=RechMotCle::triParMotCle($_POST["cle"]);
                $html .= $this->afficherCatalogue($idSeries);
            }
        }else {
            $html .= $this->afficherCatalogue();
        }

        return $html;
    }

    public function afficherCatalogue(array $idSeries=[]):string
    {
        $html = "<div><h1> <a> Notre catalogue : </a></h1></div> <br>";
        if ($idSeries == []) {
            if (isset($_SESSION['user_connected']))
            {
                try{
                    $stmt_serie = $this->requestSerie();
                    while ($row_serie = $stmt_serie->fetch(\PDO::FETCH_ASSOC)) {
                        $id = 0;
                        $html .= $this->getHtml($row_serie, $html,$id);
                    }
                }catch (PDOException $e){
                    echo $e->getMessage();
                }

            }

        }else {
            $stmt_serie = $this->requestSerie();
            while ($row_serie = $stmt_serie->fetch(\PDO::FETCH_ASSOC)) {
                $id = $row_serie['id'];
                if (isset($idSeries[$id])) {
                    $html .= $this->getHtml($row_serie, $html,$id);
                }
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
                $stmt_serie->bindParam(1,$row_serie);
                $stmt_serie->execute();

            }catch (PDOException $e){
                echo $e->getMessage();
            }
            return $stmt_serie;
        }

    /**
     * @param int $id
     * @param mixed $row_serie
     * @param string $html
     * @return string
     */
    private function getHtml(mixed $row_serie, string $html,int $id=-1): string
    {
        if (!$id == -1) {
            $stmt = $this->requestEpisode($id);
        }else {
            $stmt = $this->requestEpisode($row_serie['id']);
        }
        $listeEpisode = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $listeEpisode [] = new Episode($row['titre'], $row['file']);
        }
        $serie = new Serie($row_serie['titre'], $listeEpisode);
        $renderer = new SerieRenderer($serie);

        $user = unserialize($_SESSION["user_connected"]);
        $pref = $user->pref;
        $favoris = "";
        if ($pref->isPref($row_serie["id"])) {
            $favoris = "&favoris=2";
        }
        $html = "             
            <a href='?action=display-serie&id={$row_serie['id']}$favoris'>
            {$renderer->render(Renderer::COMPACT)} 
            </a> <br>
            ";
        return $html;
    }
}