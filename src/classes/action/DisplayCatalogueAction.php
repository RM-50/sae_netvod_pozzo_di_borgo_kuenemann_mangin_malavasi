<?php

namespace iutnc\netvod\action;

use iutnc\netvod\db\ConnectionFactory;
use iutnc\netvod\render\Renderer;
use iutnc\netvod\render\SerieRenderer;
use iutnc\netvod\video\Episode;
use iutnc\netvod\video\Serie;
use PDOException;

class  DisplayCatalogueAction extends Action
{
    public function execute(): string
    {
        $html = '';
        if (isset($_SESSION['user_connected']))
        {
            $html .= "  <div><h1> <a> Notre catalogue : </a></h1></div>";
            $sqlSerie = "SELECT * FROM serie";
            $sqlLstEps = "SELECT titre, file FROM episode where serie_id = ?";

            try{
                $db = ConnectionFactory::makeConnection();
                $stmt_serie = $db->prepare($sqlSerie);
                $stmt_serie->execute();

                while ($row_serie = $stmt_serie->fetch(\PDO::FETCH_ASSOC)) {
                    $stmt = $db->prepare($sqlLstEps);
                    $stmt->bindParam(1,$row_serie['id']);
                    $stmt->execute();
                    $listeEpisode = [];
                    while($row = $stmt->fetch(\PDO::FETCH_ASSOC)){
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
                    $html .= " 
                        
                        <a href='?action=display-serie&id={$row_serie['id']}$favoris'>
                         {$renderer->render(Renderer::COMPACT)} 
                        </a>     
                      "
                    ;

                }

            }catch (PDOException $e){
                echo $e->getMessage();
            }

        }
        return $html;
    }
}