<?php



namespace iutnc\netvod\action;


use \iutnc\netvod\exceptions\AuthException;
use iutnc\netvod\auth\Auth;



class SigninAction extends Action
{



    /**
     * @return string
     */

    public function execute(): string
    {
        $html = "";
        if ($_SERVER['REQUEST_METHOD'] === 'GET')
        {
            $html = <<< END
                        <div class="form-group">
                            <div class="title">
                                <label for="signin">Se connecter</label>
                            </div>
                            <form id="signin-form" method="POST" action="?action=signin">
                                <div class="form-item">
                                    <span class="form-item-icon material-symbols-rounded">email</span>
                                    <input type="email" name="email" placeholder="Entrez un email">
                                </div>
                                <br>
                                <div class="form-item">
                                    <span class="form-item-icon material-symbols-rounded">lock</span>
                                    <input type="password" name="passwd" placeholder="Entrez mot de passe">
                                </div>
                                <br>
                                <div class="form-item-other">
                                    <button type="submit">Se connecter</button>
                                    <label for="forgotpasswd">Vous avez oublié votre mot de passe ?</label>
                                    <label><a href='?action=forgot-passwd'">Mot de passe oublié</a></label>
                                </div>
                            </form>
                        </div>
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
                    $user = unserialize($_SESSION['user_connected']);
                    if ($user->active === 1) {
                        $html = '<meta http-equiv="refresh" content="0.1; URL=index.php">';
                    }
                    else
                    {
                        $html = "Le compte doit être activé pour vous connecter";
                    }
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