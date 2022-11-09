<?php

namespace iutnc\netvod\action;

use iutnc\netvod\db\ConnectionFactory;

class ActivateAccountAction extends Action
{

    public function execute(): string
    {
        if (isset($_SESSION['user_connected']))
        {
            if (isset($_GET['token']))
            {
                $db = ConnectionFactory::makeConnection();
                $stmt = $db->prepare("");
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
    }
}