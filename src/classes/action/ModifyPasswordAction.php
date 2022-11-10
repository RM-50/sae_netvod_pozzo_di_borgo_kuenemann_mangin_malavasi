<?php



namespace iutnc\netvod\action;


use iutnc\netvod\application\User;
use iutnc\netvod\auth\Auth;
use iutnc\netvod\exceptions\AuthException;



class ModifyPasswordAction extends Action
{



    /**
     * @return string
     */

    public function execute(): string
    {
        if (isset($_SESSION['user_connected'])) {
            $user = unserialize($_SESSION['user_connected']);
            if ($user->active === 1) {
                if ($this->http_method === 'GET') {
                    $html = <<< END
                        <form id="modify-passwd" method="POST" action="?action=modify-passwd">
                            <label for="old-passwd">Entrez votre ancien mot de passe</label>
                            <input type="password" name="old-passwd">
                            <br /><br />
                            
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
                    $old_passwd = filter_var($_POST['old-passwd'], FILTER_SANITIZE_STRING);
                    try{
                        Auth::authenticate($user->email, $old_passwd);
                        $passwd = filter_var($_POST['passwd'], FILTER_SANITIZE_STRING);
                        $confirm_passwd = filter_var($_POST['confirm-passwd'], FILTER_SANITIZE_STRING);
                        if ($passwd !== $confirm_passwd) {
                            $html = 'Les mots de passes sont différents';
                        } else {
                            $html = User::modifierMotDePasse($passwd, $user->email);
                        }
                    }catch (AuthException $e)
                    {
                        $html = $e->getMessage();
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