<?php



namespace iutnc\netvod\dispatch;


use iutnc\netvod\action\AccueilAction;
use iutnc\netvod\action\ActivateAccountAction;
use iutnc\netvod\action\CommentaireAction;
use iutnc\netvod\action\DisplayCatalogueAction;
use iutnc\netvod\action\DisplayEpisodeAction;
use iutnc\netvod\action\DisplaySerieAction;
use iutnc\netvod\action\ForgotPasswordAction;
use iutnc\netvod\action\ModifyEmailAction;
use iutnc\netvod\action\ModifyPasswordAction;
use iutnc\netvod\action\NoteAction;
use iutnc\netvod\action\ProfileAction;
use iutnc\netvod\action\RegisterAction;
use iutnc\netvod\action\SigninAction;
use iutnc\netvod\action\Signout;
use iutnc\netvod\exceptions\AuthException;


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



    /**
     * @return void
     * @throws AuthException
     */

    public function run() : void
    {
        switch ($this->action)
        {
            case 'register':
                $action = new RegisterAction();
                break;
            case 'signin':
                $action = new SigninAction();
                break;
            case'note':
                $action = new NoteAction();
                break;
            case 'signout':
                $action = new Signout();
                break;
            case 'modify-email':
                $action = new ModifyEmailAction();
                break;
            case 'modify-passwd':
                $action = new ModifyPasswordAction();
                break;
            case 'display-catalogue':
                $action = new DisplayCatalogueAction();
                break;
            case 'display-serie':
                $action = new DisplaySerieAction();
                break;
            case 'activate-account':
                $action = new ActivateAccountAction();
                break;
            case 'display-episode':
                $action = new DisplayEpisodeAction();
                break;
            case 'forgot-passwd':
                $action = new ForgotPasswordAction();
                break;
            case 'display-commentaire':
                $action = new CommentaireAction();
                break;
            case 'profile':
                $action = new ProfileAction();
                break;
            default:
                $action = new AccueilAction();
        }
        $html = $action->execute();
        $this->renderPage($html);
    }



    /**
     * @param string $html
     * @return void
     */

    private function renderPage(string $html) : void
    {
        if (isset($_SESSION['user_connected']))
        {
            $inscription = '';
            $connection = '<li class="element"><a href="?action=signout">Se Deconnecter</a></li>';
            $catalogue = '<li class="element"><a href="?action=display-catalogue">Notre catalogue</a></li>';
        }
        else
        {
            $inscription = '<li class="element"><a href="?action=register">S\'inscrire</a></li>';
            $connection = '<li class="element"><a href="?action=signin">Se Connecter</a></li>';
            $catalogue = '';
        }
        echo <<<END
            <!DOCTYPE html>
            <html lang="fr">
                <head>
                    <title>NetVOD</title>
                    <meta charset="UTF-8"> 
                    <link rel="stylesheet" href="netvod.css"> 
                    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@48,400,0,0"/>
                </head>
                <header>
                    <div class="menuPrincipal">
                        <nav class="menu">
                                <div> <a href="index.php"> <img src="rsrc/logo.png" alt="NETVOD"> </a> </div>
                                <ul class="navList">
                                    <li class="element"><a href="index.php">Accueil</a></li>
                                    $inscription
                                    $connection
                                    $catalogue   
                                </ul>
                        </nav>
                    </div>
                </header>
                <body>
                    <div class="content">
                        $html
                    </div>
                </body>
            
            </html>
            
             
            END;

    }

}
