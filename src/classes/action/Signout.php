<?php



namespace iutnc\netvod\action;



class Signout extends Action
{



    /**
     * @return string
     */

    public function execute(): string
    {
        unset($_SESSION['user_connected']);
        $html = 'Deconnexion réussi';
        return $html;
    }
}