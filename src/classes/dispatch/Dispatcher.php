<?php

namespace iutnc\netvod\dispatch;

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
            default:
                $html = 'Bienvenue';
        }
    }

    private function renderPage(string $html) : void
    {
        echo <<<END
            <!DOCTYPE html>
            <html lang="fr">
                <head>
                    <title>DeefyApp</title>
                    <meta charset="UTF-8">   
                    <link rel="stylesheet" href="deefy.css"> 
                </head>
                <body>
                    <nav id="menu">
                        <ul>
                            <li id="element"><a href="index.php">Accueil</li>
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