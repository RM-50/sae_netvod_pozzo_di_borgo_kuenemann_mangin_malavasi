<?php

namespace iutnc\netvod\action;

class AccueilAction extends Action
{

    public function execute(): string
    {
        if (isset($_SESSION['user_connected']))
        {
            $user = unserialize($_SESSION['user_connected']);
            $mail = $user->email;
            $html = <<< END
                    <h1>Bienvenue $mail</h1>
                    <h2>Veuillez choisir une action dans la liste ci dessous</h2>
                    <ul>
                        <li><a href</li>
                    </ul>
                        
                    END;

        }
        else
        {

        }
    }
}