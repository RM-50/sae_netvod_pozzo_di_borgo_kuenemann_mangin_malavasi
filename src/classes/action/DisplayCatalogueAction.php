<?php

namespace iutnc\netvod\action;

use iutnc\netvod\db\ConnectionFactory;

use iutnc\netvod\render\SerieRenderer;

class  DisplayCatalogueAction extends Action
{

    public function execute(): string
    {
        $html = '<h1>Notre catalogue</h1>';
        if (isset($_SESSION['user_connected']))
        {
            $sql = "SELECT titre FROM serie";

            try{
                $db = ConnectionFactory::makeConnection();
                $stmt_serie = $db->query($sql);

                while ($row_serie = $stmt_serie->fetch(\PDO::FETCH_ASSOC)) {
                    $serie = Serie::find($_GET['titre']);
                    $renderer = new SerieRenderer($serie);

                    $html .= ' 
                    <div class="catalogue-action">
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