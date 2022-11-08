<?php

namespace iutnc\netvod\dispatch;

use iutnc\netvod\action;

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
        $html ="";
        switch ($this->action)
        {
            case'note':
                $action = new \iutnc\netvod\action\NoteAction();
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
