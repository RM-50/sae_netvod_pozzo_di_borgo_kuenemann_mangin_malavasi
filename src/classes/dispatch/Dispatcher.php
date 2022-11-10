<?php

namespace iutnc\netvod\dispatch;

use iutnc\netvod\action\ActivateAccountAction;
use iutnc\netvod\action\AddPreferencesAction;

use iutnc\netvod\action\AccueilAction;
use iutnc\netvod\action\DisplayCatalogueAction;
use iutnc\netvod\action\DisplaySerieAction;
use iutnc\netvod\action\ListePreferencesAction;
use iutnc\netvod\action\ModifyEmailAction;
use iutnc\netvod\action\ModifyPasswordAction;
use iutnc\netvod\action\RegisterAction;
use iutnc\netvod\action\SigninAction;
use iutnc\netvod\action\Signout;
use iutnc\netvod\DisplayEpisodeAction;

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
            case 'modify-email':
                $action = new ModifyEmailAction();
                $html = $action->execute();
                break;
            case 'modify-passwd':
                $action = new ModifyPasswordAction();
                $html = $action->execute();
                break;
            case 'display-catalogue':
                $action = new DisplayCatalogueAction();
                $html = $action->execute();
                break;
            case 'display-serie':
                $action = new DisplaySerieAction();
                $html = $action->execute();
                break;
            case 'activate-account':
                $action = new ActivateAccountAction();
                $html = $action->execute();
                break;
            case 'display-episode':
                $action = new DisplayEpisodeAction();
                $html = $action->execute();
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
            $catalogue = '<li id="element"><a href="?action=display-catalogue">Notre catalogue</a></li>';
        }
        else
        {
            $inscription = '<li id="element"><a href="?action=register">S\'inscrire</a></li>';
            $connection = '<li id="element"><a href="?action=signin">Se Connecter</a></li>';
            $catalogue = '';
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
                            $catalogue   
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
