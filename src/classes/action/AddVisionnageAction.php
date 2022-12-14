<?php



namespace iutnc\netvod\action;



class AddVisionnageAction extends Action
{



    /**
     * @return string
     */

    public function execute(): string
    {
        if (isset($_SESSION['*user*'])) {
            $user = unserialize($_SESSION["*user*"]);
            if ($user->active === 1) {
                $user = $_SESSION["*user*"];
                if (!isset($user->videoencours["*nomserie*"])) {
                    $html = "<p>Visionnage en cours</p>";
                } else {
                    $html = "<p>Série en cours</p>";
                }
            }
            else
            {
                $html = "Le compte doit être activé avant d'utiliser cette fonctionnalite";
            }
        }
        else
        {
            $html = "Vous devez vous connecter avant d'accéder au site";
        }
        return $html;

    }


}