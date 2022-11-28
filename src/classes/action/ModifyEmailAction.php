<?php



namespace iutnc\netvod\action;


use iutnc\netvod\auth\Auth;
use iutnc\netvod\db\ConnectionFactory;
use iutnc\netvod\exceptions\AuthException;



class ModifyEmailAction extends Action
{



    /**
     * @return string
     */

    public function execute(): string
    {
        if (isset($_SESSION['user_connected']))
        {
            $user = unserialize($_SESSION['user_connected']);
            if ($user->active === 1) {
                if ($this->http_method === 'GET') {
                    $html = <<< END
                        <div class="form-group">
                            <div class="title">
                                <label for="modify-mail">Modifier mon email</label>
                            </div>
                            <form id="modify-mail" method="POST" action="?action=modify-email">
                                <div class="double-form-item">
                                    <div class="double-form-sous-item">
                                        <span class="form-item-icon material-symbols-rounded">email</span>
                                        <input type="email" name="email" placeholder="Nouvel email">
                                    </div>
                                    <br />
                                    <div class="double-form-sous-item">
                                        <span class="form-item-icon material-symbols-rounded">email</span>   
                                        <input type="email" name="confirm-email" placeholder="Confirmez email">
                                    </div>
                                </div>
                                <br />
                                <div class="form-item">
                                    <span class="form-item-icon material-symbols-rounded">lock</span>
                                    <input type="password" name="passwd" placeholder="Entrez votre mot de passe">
                                </div>
                                <br />
                                <div class="form-item-other">
                                    <button type="submit">Valider</button>
                                </div>
                            </form>
                        </div>
                        END;
                } elseif ($this->http_method === 'POST') {
                    $passwd = filter_var($_POST['passwd'], FILTER_SANITIZE_STRING);
                    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
                    $confirm_email = filter_var($_POST['confirm-email'], FILTER_SANITIZE_EMAIL);
                    try {
                        Auth::authenticate($user->email, $passwd);
                        if ($email !== $confirm_email) {
                            $html = 'Les adresses emails sont différentes';
                        } else {
                            $html = $user->modifierEmail($email);
                        }
                    }catch (AuthException $e)
                    {
                        $html = $e->getMessage();
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