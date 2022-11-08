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
            case'note':
                $action = new \iutnc\netvod\action\NoteAction();
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

        }
        else
        {
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
