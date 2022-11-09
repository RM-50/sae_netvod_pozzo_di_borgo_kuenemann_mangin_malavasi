<?php

namespace iutnc\netvod\action;

use iutnc\netvod\render\SerieRenderer;

class AccueilAction extends Action
{

    public function execute(): string
    {
        $html = '';
        if (isset($_SESSION['user_connected']))
        {
            $user = unserialize($_SESSION['user_connected']);
            if ($user->active === 1) {
                $mail = $user->email;
                $prefs = $user->pref;
                $seriesPref = $prefs->series;
                $html = <<< END
                    <h1>Bienvenue $mail</h1>
                    <h2>Veuillez choisir une action dans la liste ci dessous</h2>
                    
                    <div class="accueil-action">
                        <ul>
                            <li><a href="?action=modify-passwd">Changer de mot de passe</a></li>
                            <li><a href="?action=modify-email">Changer d'adresse mail</a></li>
                        </ul>
                    </div> <br>
                <div>
            END;
                if (!sizeof($seriesPref) == 0) {
                    $html .= '<table id="champ">
                        <tr>
                        <td>
                            <nav id="deroule">
                                <ul id="serie">';
                    foreach ($seriesPref as $value) {
                        $renderer = new SerieRenderer($value);
                        $contenu = $renderer->render(2);
                        $titre = $value->titreSerie;
                        $html .= <<<END
                        <li class="menu-deroulant">
                            <a href="">$titre</a>
                            <ul class="sous-menu">
                                <li><a href="">$contenu</a></li>
                            </ul>
                        </li>
                        END;
                    }
                    $html .= <<<END
                                </ul>
                            </nav>
                            </td>
                        </tr>
                     </table>          
                </div>
                END;
                }
            }
            else
            {
                $html = "Le compte doit être activé";
            }

        }
        else
        {
            $html = "<h1>Bienvenue !</h1>";
        }
        return $html;
    }
}