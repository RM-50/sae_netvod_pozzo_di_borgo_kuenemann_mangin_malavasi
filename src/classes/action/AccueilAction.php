<?php



namespace iutnc\netvod\action;


use Exception;
use iutnc\netvod\application\User;
use iutnc\netvod\preference\Preferences;
use iutnc\netvod\render\EpisodeRenderer;
use iutnc\netvod\render\SerieRenderer;
use iutnc\netvod\video\Episode;
use iutnc\netvod\video\Serie;
use iutnc\netvod\visionnage\VisioEnCours;


class AccueilAction extends Action
{

    /**
     * @return string
     * @throws Exception
     */
    public function execute(): string
    {
        if (isset($_SESSION['user_connected']))
        {
            $user = unserialize($_SESSION['user_connected']);
            if ($user->active === 1) {
                $mail = $user->email;
                $mail = explode('@', $mail)[0];
                $html = <<< END
                <div class='accueil-action-group'>
                    <div class='accueil-action-title'>
                        <h1>Bienvenue $mail</h1>
                    </div>
                    <div class="accueil-action-item">
                        <label for="profil"><a href="?action=profile">Voir mon profil</a></label>
                    </div>
                    <div class="accueil-action-item">
                        {$this->doPreferences($user)}
                    </div>
                    <div class="accueil-action-item">
                        {$this->doVisionnageEnCours($user)}
                    </div>
                </div>
                END;
            }
            else
            {
                $html = "Le compte doit être activé";
            }
        }
        else
        {
            $html = <<<END
                    <div class="accueil-action-group">
                        <h1 id='welcome'>Bienvenue !</h1>
                    </div>
                  END;
        }
        return $html;
    }

    /**
     * @throws Exception
     */
    private function doPreferences(User $user):string
    {
        $html = "";
        $idUser = User::getID($user->email);
        if (Preferences::getPrefLength($idUser) != 0) {
            $html = '
                        <h2>Favoris</h2>
                        <table id="champ">
                        <tr>
                        <td>
                            <nav id="deroule">
                                <ul id="serie">';
            foreach (Preferences::getPref($idUser) as $serie) {
                $renderer = new SerieRenderer($serie);
                $titre=$renderer->render(1);
                $titre = str_replace("<p>","",$titre);
                $titre = str_replace("</p>","",$titre);
                $id = Serie::getIdSerie($titre);
                $html .= <<<END
                            <li class="menu-deroulant">
                                <a id="amenu" href="">$titre</a>
                                <ul class="sous-menu">
                                    <li><a id="amenu" href="index.php?action=display-serie&id={$id}">Episodes</a></li>
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
        return $html;
    }

    private function doVisionnageEnCours(User $user):string
    {
        $html = "";
        $idUser = User::getID($user->email);
        if (VisioEnCours::getVideoEnCoursLength($idUser) != 0) {
            $html = '
                        <h2>Visionnage en cours</h2>
                            <div class="accueil-action-group-liste">';
            foreach (VisioEnCours::getVideoEnCours($idUser) as $episode) {
                $titreSerie = Episode::getSerieEpisode($episode->id);
                $id = $episode->id;
                $html .= <<<END
                        <div class="accueil-action-item-list">
                            <nav id="deroule">
                                <ul id="serie">
                                    <li class="menu-deroulant">
                                        <a id="amenu" href="">$titreSerie</a>
                                        <ul class="sous-menu">
                                            <li><a id="amenu" href="index.php?action=display-episode&id=$id">Episodes $id</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                        END;
            }
            $html .= <<<END
                    </div>
                    END;
        }
        return $html;
    }
}