<?php

namespace iutnc\netvod\action;

class ListePreferencesAction extends Action
{

    public function execute(): string
    {
        $html = "";
        if (isset($_SESSION["userConnected"])) {
            $user = $_SESSION["userConnected"];
            $preferences = $user->preferences;
            $seriesPref = $preferences->series;
            $html = <<<END
                <div>
                     <table id="champ">
                         <tr>
            END;
            foreach ($seriesPref as $value) {
                $renderer = ""; //new SerieRenderer($value);
                $contenu=$renderer->render();
                $titre = $value->titre;
                $html .= <<<END
                    <td>
                        <nav>
                            <ul>
                                <li class="menu-deroulant">
                                    <a href="#">$titre</a>
                                    <ul class="sous-menu">
                                        <p>$contenu</p>
                                    </ul>
                                </li>
                            </ul>
                        </nav>
                    </td>
                END;
            }
            $html .= <<<END
                        </tr>
                    </table>                    
                </div>
                END;
        }else {

        }
        return $html;
    }
}