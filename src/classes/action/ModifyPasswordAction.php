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
                        <div class="form-group">
                            <div class="title">
                                <label for="modify-mail">Modifier mon mot de passe</label>
                            </div>
                            <form id="modify-mail" method="POST" action="?action=modify-email">
                                <div class="form-item">
                                    <span class="form-item-icon material-symbols-rounded">email</span>
                                    <input type="password" name="passwd" placeholder="Entrez votre adresse mail">
                                </div>
                                <br />
                                <div class="double-form-item">
                                    <div class="double-form-sous-item">
                                        <span class="form-item-icon material-symbols-rounded">lock</span>
                                        <input type="email" name="email" placeholder="Nouveau mot de passe">
                                    </div>
                                    <br />
                                    <div class="double-form-sous-item">
                                        <span class="form-item-icon material-symbols-rounded">lock</span>   
                                        <input type="email" name="confirm-email" placeholder="Confirmez mot de passe">
                                    </div>
                                </div>
                                <br />
                                <div class="form-item-other">
                                    <button type="submit">Valider</button>
                                </div>
                            </form>
                        </div>
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