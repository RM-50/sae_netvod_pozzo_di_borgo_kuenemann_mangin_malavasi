<?php

namespace iutnc\netvod\action;

use iutnc\netvod\application\User;
use iutnc\netvod\auth\Auth;

class ForgotPasswordAction extends Action
{

    public function execute(): string
    {
        if ($this->http_method === 'GET')
        {
            if (!isset($_GET['token']))
            {
                $html = <<<END
                    <form id="renew-password" action="?action=forgot-password">
                            <label for="email">Entrez votre email</label>
                            <input type="email" name="email">
                            <br /><br />
                            
                            <button type="submit">Valider</button>
                    </form>
                    END;
            }
            else
            {
                $token = $_GET['token'];
                $html = <<< END
                        <form id="modify-passwd" method="POST" action="?action=modify-passwd&token=$token">
                            <label for="passwd">Entrez votre nouveau mot de passe</label>
                            <input type="password" name="passwd">
                            <br /><br />
                            
                            <label for="confirm-passwd">Confirmez votre nouveau mot de passe</label>
                            <input type="password" name="confirm-passwd">
                            <br /><br />
                            
                            <button type="submit">Valider</button>
                        </form> 
                        END;

            }

        }
        elseif ($this->http_method === 'POST')
        {
            if (!isset($_GET['token']))
            {
                $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
                if (User::verifierEmail($email))
                {
                    $token = Auth::creerToken('renew', )
                    $html = "Cliquer sur ce lien pour changer de mot de passe";
                }
                else
                {
                    $html = "Cet email n'existe pas";
                }
            }
        }


    }
}