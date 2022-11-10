<?php

namespace iutnc\netvod\action;

use iutnc\netvod\auth\Auth;

class RegisterAction extends Action
{

    public function execute(): string
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET'){
            $html = <<<END
                        <form id="creer_user" method="POST" action="?action=register">
                            <label for="email">Email</label>
                            <input type="email" name="email">
                            <br /><br />
                            
                            <label for="passwd">Mot de passe</label>
                            <input type="password" name="passwd">
                            <br /><br />
                           
                            <label for="passwd_confirm">Confirmer mot de passe</label>
                            <input type="password" name="passwd_confirm">
                            <br /><br />
                            
                            <button type="submit">S'inscire</button>
                        </form>
                        END;
        }
        elseif ($_SERVER['REQUEST_METHOD'] === 'POST'){
            $passwd = filter_var($_POST['passwd'], FILTER_SANITIZE_STRING);
            $passwd_confirm = filter_var($_POST['passwd_confirm'], FILTER_SANITIZE_STRING);
            if (! filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
            {
                $html = "adresse email invalide";
            }
            elseif ($passwd === $passwd_confirm)
            {
                $html = Auth::register($_POST['email'], $passwd);
                if ($html === 'Inscription réussie')
                    {
                        Auth::authenticate($_POST['email'], $passwd);
                        $user = unserialize($_SESSION['user_connected']);
                        $token = Auth::creerToken('activation', $user->id);
                        $html .= "<br /> Veuillez maintenant activer votre compte <br /><br />";
                        $html .= "<button onclick=\"window.location.href='?action=activate-account&token=$token'\">Activer Compte</button>";
                    }
            }
            else
            {
                $html = "Les mots de passe ne correspondent pas";
            }
        }
        return $html;
    }
}