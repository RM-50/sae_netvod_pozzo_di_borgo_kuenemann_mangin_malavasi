<?php

namespace iutnc\netvod\dispatch;

use iutnc\netvod\action\AddPreferencesAction;

use iutnc\netvod\action\AccueilAction;
use iutnc\netvod\action\ListePreferencesAction;
use iutnc\netvod\action\RegisterAction;
use iutnc\netvod\action\SigninAction;
use iutnc\netvod\action\Signout;

class Dispatcher
{

    private ?string $action;

    /**
     * Constructeur prenant en parametre une action a executer
     * @param string|null $action action a executer
     */
    public function __construct(?string $action)
    {
        $this->action = $action;
    }

    public function run() : void
    {
        switch ($this->action)
        {
            case "add-preferences":
                $html = new AddPreferencesAction();
                $html->execute();
                break;
            case 'register':
                $action = new RegisterAction();
                $html = $action->execute();
                break;
            case 'signin':
                $action = new SigninAction();
                $html = $action->execute();
                break;
            case 'signout':
                $action = new Signout();
                $html = $action->execute();
                break;
            default:
                $action = new AccueilAction();
                $html = $action->execute();
        }
        $this->renderPage($html);
    }

    private function renderPage(string $html) : void
    {
        if (isset($_SESSION['user_connected']))
        {
            $inscription = '';
            $connection = '<li id="element"><a href="?action=signout">Se Deconnecter</a></li>';
        }
        else
        {
            $inscription = '<li id="element"><a href="?action=register">S\'inscrire</a></li>';
            $connection = '<li id="element"><a href="?action=signin">Se Connecter</a></li>';
        }
        echo <<<END
            <!DOCTYPE html>
            <html lang="fr">
                <head>
                    <title>NetVOD</title>
                    <meta charset="UTF-8"> 
                    <link rel="stylesheet" href="netvod.css">  
                </head>
                <body>
                    <nav id="menu">
                        <ul>
                            <li id="element"><a href="index.php">Accueil</a></li>
                            $inscription
                            $connection
                        </ul>
                    </nav>
                    <div class="content">
                        $html
                    </div>
                    
                </body>
            
            </html>
            
             
            END;

    }

}
