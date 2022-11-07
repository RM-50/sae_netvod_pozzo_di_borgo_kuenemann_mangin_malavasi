<?php

namespace iutnc\netvod\dispatch;

use iutnc\netvod\action\Signin;

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
            case 'signin':
                $action = new Signin();
                $html = $action->execute();
                break;
            default:
                $html = 'Bienvenue';
        }
        $this->renderPage($html);
    }

    private function renderPage(string $html) : void
    {
        echo <<<END
            <!DOCTYPE html>
            <html lang="fr">
                <head>
                    <title>NetVOD</title>
                    <meta charset="UTF-8">   
                </head>
                <body>
                    <nav id="menu">
                        <ul>
                            <li id="element"><a href="index.php">Accueil</a></li>
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
