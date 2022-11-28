<?php

namespace iutnc\netvod\action;

use iutnc\netvod\tri\TriCriteres;
use iutnc\netvod\tri\TriMotCle;

class DisplayTriAction
{

    public static function execute(string $http_method): string
    {
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
        $idSeries = [];
        if ($http_method === "POST") {
            if (isset($_POST["cle"])) {
                $cle = filter_var($_POST["cle"], FILTER_SANITIZE_STRING);
                $idSeries = TriMotCle::tri($cle);
            }
        }
        if (isset($_GET["sort"])) {
            filter_var($_GET["sort"], FILTER_SANITIZE_STRING) ? $sort = $_GET["sort"] : $sort = "";
            if ($sort === "titre") {
                $idSeries = TriCriteres::tri("titre");
            } else if ($sort === "date") {
                $idSeries = TriCriteres::tri("date_ajout");
            } else if ($sort === "annee") {
                $idSeries = TriCriteres::tri("annee");
            } else if ($sort === "descriptif") {
                $idSeries = TriCriteres::tri("descriptif");
            }
        }
        $_POST["idSeries"] = $idSeries;
        return $html;

    }
}