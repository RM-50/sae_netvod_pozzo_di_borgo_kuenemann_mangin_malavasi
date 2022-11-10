<?php

namespace iutnc\netvod\action;

use iutnc\netvod\render\SerieRenderer;

class AccueilAction extends Action
{



    /**
     * @return string
     */

    public function execute(): string
    {

        if (isset($_SESSION['user_connected']))
        {
            $html = '';
            $user = unserialize($_SESSION['user_connected']);
            if ($user->active === 1) {
                $mail = $user->email;
                $prefs = $user->getPrefs();
                $seriesPref = $prefs->series;
                $visios = $user->visio;
                $VisionnageEnC = $visios->visiocours;

                $html = <<< END
                    <h1>Bienvenue $mail</h1>
                    <h2>Veuillez choisir une action dans la liste ci dessous</h2>
                    
                    <div class="accueil-action">
                        <ul>
                            <li><a href="?action=modify-passwd">Changer de mot de passe</a></li>
                            <li><a href="?action=modify-email">Changer d'adresse mail</a></li>
                        </ul>
                    </div> <br>
                
            END;
                if (!sizeof($seriesPref) == 0) {
                    $html .= '<table id="champ">
                        <tr>
                        <td>
                            <nav id="deroule">
                                <ul id="serie">';
                foreach ($seriesPref as $value) {
                    $renderer = new SerieRenderer($value);
                    $titre=$renderer->render(1);
                    $html .= <<<END
                        <li class="menu-deroulant">
                            <a id="amenu" href="">$titre</a>
                            <ul class="sous-menu">
                                <li><a id="amenu" href="">Episodes</a></li>
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
                
                END;
                }

                if (!sizeof($VisionnageEnC) == 0) {
                    $html .= '<table id="champ">
                        <tr>
                        <td>
                            <nav id="deroule">
                                <ul id="serie">';
                    foreach ($VisionnageEnC as $value) {
                        $renderer = new SerieRenderer($value);
                        $titre=$renderer->render(1);
                        $html .= <<<END
                        <li class="menu-deroulant">
                            <a id="amenu" href="">$titre</a>
                            <ul class="sous-menu">
                                <li><a id="amenu" href="">Episodes</a></li>
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