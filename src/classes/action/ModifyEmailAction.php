<?php

namespace iutnc\netvod\action;

use iutnc\netvod\db\ConnectionFactory;

class ModifyEmailAction extends Action
{

    public function execute(): string
    {
        if (isset($_SESSION['user_connected']))
        {
            $user = unserialize($_SESSION['user_connected']);
            if ($user->active === 1) {
                if ($this->http_method === 'GET') {
                    $html = <<< END
                        <form id="modify-mail" method="POST" action="?action=modify-email">
                            <label for="email">Entrez votre nouvel email</label>
                            <input type="email" name="email">
                            <br /><br />
                            
                            <label for="confirm-email">Confirmez votre nouvel email</label>
                            <input type="email" name="confirm-email">
                            <br /><br />
                            
                            <button type="submit">Valider</button>
                        </form> 
                        END;
                } elseif ($this->http_method === 'POST') {
                    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
                    $confirm_email = filter_var($_POST['confirm-email'], FILTER_SANITIZE_EMAIL);
                    if ($email !== $confirm_email) {
                        $html = 'Les adresses emails sont différentes';
                    } else {
                        $html = $user->modifierEmail($email);
                    }
                }
            }
            else
            {
                $html = "Le compte doit être activé pour accéder à cette fonctionnalité";
            }

        }
        else
        {
            $html = 'Veuillez vous connecter avant d\'accéder au site';
        }
        return $html;
    }
}