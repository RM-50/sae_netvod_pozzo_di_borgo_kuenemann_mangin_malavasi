<?php

namespace iutnc\netvod\action;

use iutnc\netvod\db\ConnectionFactory;
use PDO;

class ActivateAccountAction extends Action
{

    public function execute(): string
    {
        if (isset($_SESSION['user_connected']))
        {
            if (isset($_GET['token']))
            {
                $user = unserialize($_SESSION['user_connected']);
                $db = ConnectionFactory::makeConnection();
                $date = date('Y-m-d H:i:s', time());
                $stmt = $db->prepare("SELECT * FROM user WHERE activation_token = '{$_GET['token']}'
                                            AND activation_expires > str_to_date('$date', '%Y-%m-%d %H:%i:%s')
                                            AND id = '$user->id'");
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$row)
                {
                    $html = "Le token a expiré, veuillez réessayer : <br /><br />";
                    $html .= "<button onclick=\"window.location.href='?action=activate-account&token={$_GET['token']}'\">Activer Compte</button>";
                }
                else
                {
                    $stmt = $db->prepare("update user set active = 1, activation_token=null
                                        where activation_token = '{$_GET['token']}'");
                    $stmt->execute();
                    $user->active = 1;
                    $_SESSION['user_connected'] = serialize($user);
                    $html = "Activation réussie !";
                }
            }
            else
            {
                $html = "Le token est invalide";
            }
        }
        else
        {
            $html = 'Veuillez vous connecter avant d\'accéder au site';
        }
        return $html;
    }
}