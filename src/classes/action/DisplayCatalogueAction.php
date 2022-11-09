<?php

namespace iutnc\netvod\action;

use iutnc\netvod\db\ConnectionFactory;
use iutnc\netvod\render\SerieRenderer;
use iutnc\netvod\video\Serie;

class  DisplayCatalogueAction extends Action
{
    public function execute(): string
    {
        $html = '';
        if (isset($_SESSION['user_connected']))
        {
            $html .= <<< END
                    <h1>Notre catalogue : </h1>
                    <a href= "?action=display-catalogue">catalogue</a>
                    END;

            $html .= "  <div><h1> <a> Notre catalogue : </a></h1></div>";
            $sqlSerie = "SELECT * FROM serie";
            $sqlLstEps = "SELECT titre, file FROM episode where serieid = ?";

            try{
                $db = ConnectionFactory::makeConnection();
                $stmt_serie = $db->query($sqlSerie);

                while ($row_serie = $stmt_serie->fetch(\PDO::FETCH_ASSOC)) {

                    $serie = new Serie();
                    $renderer = new SerieRenderer($serie);

                    $html .= '
                        <ul>
                        <p>
                        '.$content .= $renderer->render(Renderer::short).'  
                        </p>     
                        </ul>
                    </div>'
                    ;

                }

            }catch (PDOException $e){
                echo $e->getMessage();
            }

        }
        return $html;
    }
}