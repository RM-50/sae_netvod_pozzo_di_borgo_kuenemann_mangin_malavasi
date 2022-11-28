<?php



namespace iutnc\netvod\action;


use iutnc\netvod\db\ConnectionFactory;
use iutnc\netvod\preference\Preferences;
use iutnc\netvod\render\Renderer;
use iutnc\netvod\render\SerieRenderer;
use iutnc\netvod\tri\TriCriteres;
use iutnc\netvod\tri\TriMotCle;
use iutnc\netvod\video\Episode;
use iutnc\netvod\video\Serie;
use PDOException;



class  DisplayCatalogueAction extends Action
{
    /**
     * @return string
     */
    public function execute(): string
    {
        if (isset($_SESSION['user_connected'])) {
            $user = unserialize($_SESSION['user_connected']);
            if ($user->active === 1) {
                $html = DisplayTriAction::execute($this->http_method);
                $idSeries = [];
                foreach ($_POST['idSeries'] as $idSerie) {
                    if (filter_var($idSerie,FILTER_SANITIZE_NUMBER_INT)) $idSeries[] = $idSerie;
                    else {
                        $idSeries = [];
                        break;
                    }
                }
                $html .= $this->afficherCatalogue($idSeries);
            } else {
                $html = "Le compte doit être activé pour utiliser cette fonctionnalité";
            }
        }
        else
        {
            $html = "Il faut se connecter avant d'accéder au site";
        }
        return $html;
    }



    /**
     * @param array $idSeries
     * @return string
     */

    public function afficherCatalogue(array $idSeries=[]):string
    {
        $html = "<div><h1> Notre catalogue : </h1></div> <br>";
        if ($idSeries == []) {
                try{
                    $stmt_serie = $this->requestSerie();
                    while ($row_serie = $stmt_serie->fetch(\PDO::FETCH_ASSOC)) {
                        $id = $row_serie["id"];
                        $titre = $row_serie["titre"];
                        $html .= $this->getHtml($titre, $id);
                    }
                }catch (PDOException $e){
                    echo $e->getMessage();
                }
            }
        else
        {
            foreach ($idSeries as $id) {
                $sqlSerie = "SELECT titre FROM serie where id = ?";

                $db = ConnectionFactory::makeConnection();
                $stmt_serie = $db->prepare($sqlSerie);
                $stmt_serie->bindParam(1, $id);
                $stmt_serie->execute();
                $row = $stmt_serie->fetch(\PDO::FETCH_ASSOC);
                $html .= $this->getHtml($row["titre"], $id);
            }
        }
        return $html;
    }



    /**
     * @return \PDOStatement
     */

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



    /**
     * @param int $row_serie
     * @return array
     */

    public function requestEpisode(int $row_serie): array
    {
            try {

                $sqlLstEps = "SELECT id,titre, file FROM episode where serie_id = ?";

                $db = ConnectionFactory::makeConnection();
                $stmt_serie = $db->prepare($sqlLstEps);
                $stmt_serie->bindParam(1, $row_serie);
                $stmt_serie->execute();
                $listeEpisode = [];
                while ($row = $stmt_serie->fetch(\PDO::FETCH_ASSOC)) {
                    $listeEpisode [] = new Episode($row['id'],$row['titre'], $row['file']);
                }
                return $listeEpisode;
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
            return [];
        }


    /**
     * @param string $titre
     * @param int $id
     * @return string
     */
    private function getHtml(string $titre,int $id): string
    {
        $listeEpisode = $this->requestEpisode($id);
        $serie = new Serie($titre, $listeEpisode);
        $renderer = new SerieRenderer($serie);

        $favoris = "";
        return "<a href='?action=display-serie&id={$id}$favoris'>{$renderer->render(Renderer::COMPACTWITHIMGFORCATALOGUE)} </a><br>";
    }
}