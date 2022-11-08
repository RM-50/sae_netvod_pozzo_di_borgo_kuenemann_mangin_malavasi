<?php

namespace iutnc\netvod\action;

use \iutnc\netvod\exceptions\AuthException;
use iutnc\netvod\auth\Auth;

class SigninAction extends Action
{

    public function execute(): string
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET')
        {
            $html = <<< END
                        <form id="signin" method="POST" action="?action=signin">
                            <label for="email">Email</label>
                            <input type="email" name="email">
                            <br /><br />
                            
                            <label for="passwd">Mot de passe</label>
                            <input type="password" name="passwd">
                            <br /><br />
                            
                            <button type="submit">Se connecter</button>
                        </form>
                        END;
        }
        elseif ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $passwd = filter_var($_POST['passwd'], FILTER_SANITIZE_STRING);

            try
            {
                if (Auth::authenticate($email, $passwd))
                {
                    $html = "Connexion réussie !";
                }
            }
            catch (AuthException $e1)
            {
                $html = $e1->getMessage();
            }

        }
        return $html;
    }
}