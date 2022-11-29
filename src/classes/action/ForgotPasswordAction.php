<?php



namespace iutnc\netvod\action;


use Exception;
use iutnc\netvod\application\User;
use iutnc\netvod\auth\Auth;
use iutnc\netvod\db\ConnectionFactory;
use PDO;



class ForgotPasswordAction extends Action
{



    /**
     * @return string
     * @throws Exception
     */

    public function execute(): string
    {
        if ($this->http_method === 'GET')
        {
            if (!isset($_GET['token']))
            {
                $html = <<<END
                    <div class="form-group">
                        <form id="renew-password" method="POST" action="?action=forgot-passwd">
                            <div class="title">
                                <label for="renew-password">Réinitialiser mon mot de passe</label>
                            </div>
                            <div class="form-item">
                                <span class="form-item-icon material-symbols-rounded">email</span>
                                <input type="email" name="email" placeholder="Entrez votre email">
                            </div>
                            <div class="form-item-other">        
                                <button type="submit">Valider</button>
                            </div>
                        </form>
                    </div>
                    END;
            }
            else
            {
                $token = $_GET['token'];
                if (isset($_GET['mail']))
                {
                    $email = filter_var($_GET['mail'], FILTER_SANITIZE_EMAIL);
                }
                else{
                    $email = null;
                }
                $html = <<< END
                        <div class="form-group">
                            <form id="renew-passwd" method="POST" action="?action=forgot-passwd&token=$token&mail=$email">
                                <div class="title">
                                    <label for="renew-passwd">Réinitialiser mon mot de passe</label>
                                </div>
                                <div class="double-form-item">
                                    <div class="double-form-sous-item">
                                        <span class="form-item-icon material-symbols-rounded">lock</span>
                                        <input type="password" name="passwd" placeholder="Nouveau mot de passe">
                                    </div>
                                    <div class="double-form-sous-item">
                                        <span class="form-item-icon material-symbols-rounded">lock</span>
                                        <input type="password" name="confirm-passwd" placeholder="Confirmez mot de passe">
                                    </div>
                                </div>
                                <div class="form-item-other">
                                    <button type="submit">Valider</button> 
                                </div>                                    
                            </form>
                        </div>
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
                    $id = User::getID($email);
                    $token = Auth::creerToken('renew', $id);
                    $html = <<<END
                            <meta http-equiv="refresh" content="0.1; URL=index.php?action=forgot-passwd&token=$token&mail=$email">
                         END;
                }
                else
                {
                    $html = "Cet email n'existe pas";
                }
            }
            else{
                if (isset($_GET['mail'])) {
                    $passwd = filter_var($_POST['passwd'], FILTER_SANITIZE_STRING);
                    $confirm_passwd = filter_var($_POST['confirm-passwd'], FILTER_SANITIZE_STRING);
                    $token = filter_var($_GET['token'], FILTER_SANITIZE_STRING);
                    $email = filter_var($_GET['mail'], FILTER_SANITIZE_EMAIL);
                    $id = User::getID($email);

                    if ($passwd === $confirm_passwd) {
                        $db = ConnectionFactory::makeConnection();
                        $date = date('Y-m-d H:i:s', time());
                        $stmt = $db->prepare("SELECT * FROM user WHERE renew_token = '$token'
                                            AND renew_expires > str_to_date('$date', '%Y-%m-%d %H:%i:%s')
                                            AND id = '$id'");
                        $stmt->execute();
                        $row = $stmt->fetch(PDO::FETCH_ASSOC);
                        if (!$row) {
                            $html = "Le token a expiré, veuillez réessayer : <br /><br />";
                            $html .= "<button onclick=\"window.location.href='?action=forgot-passwd&token=$token'\">Changer de mot de passe</button>";
                        } else {
                            $stmt = $db->prepare("update user set active = 1, renew_token=null, renew_expires=null
                                        where renew_token = '$token'");
                            $stmt->execute();
                            $user = new User($id, $email, $passwd, 1);
                            $user->active = 1;
                            $_SESSION['user_connected'] = serialize($user);
                            $html = "<div class='form-group'><div class='title'>Changement effectué !</div></div>";
                        }
                    }
                }
                else
                {
                    $html = "mail invalide";
                }
            }
        }

        return $html;
    }
}