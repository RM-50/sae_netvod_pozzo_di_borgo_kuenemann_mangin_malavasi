<?php

namespace iutnc\netvod\action;

class ModifyPasswordAction extends Action
{

    public function execute(): string
    {
        if (isset($_SESSION['user_connected'])) {
            $user = unserialize($_SESSION['user_connected']);
            if ($user->active === 1) {
                if ($this->http_method === 'GET') {
                    $html = <<< END
                        <form id="modify-passwd" method="POST" action="?action=modify-passwd">
                            <label for="passwd">Entrez votre nouveau mot de passe</label>
                            <input type="password" name="passwd">
                            <br /><br />
                            
                            <label for="confirm-passwd">Confirmez votre nouveau mot de passe</label>
                            <input type="password" name="confirm-passwd">
                            <br /><br />
                            
                            <button type="submit">Valider</button>
                        </form> 
                        END;
                } elseif ($this->http_method === 'POST') {
                    $passwd = filter_var($_POST['passwd'], FILTER_SANITIZE_STRING);
                    $confirm_passwd = filter_var($_POST['confirm-passwd'], FILTER_SANITIZE_STRING);
                    if ($passwd !== $confirm_passwd) {
                        $html = 'Les mots de passes sont différents';
                    } else {
                        $html = $user->modifierMotDePasse($passwd);
                    }
                }
            }
        }
        else
        {
            $html = 'Veuillez vous connecter avant d\'accéder au site';
        }
        return $html;
    }
}