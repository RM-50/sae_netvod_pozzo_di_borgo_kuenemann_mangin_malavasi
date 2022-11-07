<?php

namespace iutnc\netvod\action;

class AccueilAction extends Action
{

    public function execute(): string
    {
        $html = '';
        if (isset($_SESSION['user_connected']))
        {
            $user = unserialize($_SESSION['user_connected']);
            $mail = $user->email;
            $html = <<< END
                    <h1>Bienvenue $mail</h1>
                    <h2>Veuillez choisir une action dans la liste ci dessous</h2>
                    <div class="accueil-action">
                        <ul>
                            <li><a href="?action=modify-passwd">Changer de mot de passe</a></li>
                            <li><a href="?action=modify-email">Changer d'adresse mail</a></li>
                        </ul>
                    </div>
                    END;

        }
        else
        {
            $html = "<h1>Bienvenue !</h1>";
        }
        return $html;
    }
}