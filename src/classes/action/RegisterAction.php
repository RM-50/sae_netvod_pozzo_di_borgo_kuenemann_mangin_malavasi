<?php



namespace iutnc\netvod\action;


use iutnc\netvod\auth\Auth;
use iutnc\netvod\exceptions\AuthException;



class RegisterAction extends Action
{



    /**
     * @return string
     * @throws AuthException
     */

    public function execute(): string
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET'){
            $html = <<<END
                        <div class="form-group">
                            <div class="title">
                                <label for="register">S'inscrire</label>
                            </div>
                            <form id="creer_user" method="POST" action="?action=register">
                                <div class="form-item">
                                    <span class="form-item-icon material-symbols-rounded">email</span>
                                    <input type="email" name="email" placeholder="Entrez un email">
                                </div>
                                <br>
                                <div class="double-form-item">
                                    <div class="double-form-sous-item">
                                        <span class="form-item-icon material-symbols-rounded">lock</span>
                                        <input type="password" name="passwd" placeholder="Entrez un mot de passe">
                                    </div>
                                    <div class="double-form-sous-item">
                                        <span class="form-item-icon material-symbols-rounded">lock</span>
                                        <input type="password" name="passwd_confirm" placeholder="Confirmez votre mot de passe">
                                    </div>
                                </div>
                                <br>
                                <div class="form-item-other">
                                    <label for="signin"> <a href="SigninAction.php">Vous avez déjà un compte ?</a></label>
                                    <button type="submit">S'inscire</button>
                                </div>
                            </form>
                        </div>
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