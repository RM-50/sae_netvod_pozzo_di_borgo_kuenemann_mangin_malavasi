<?php

namespace iutnc\netvod\action;

use iutnc\netvod\commentaire\Commentaire;

class CommentaireAction
{



    /**
     * @return string
     */

    public function execute(): string
    {
        if (!isset($_SESSION['user_connected'])) {
            $html = " Vous n'êtes pas connecté";

        } else {
            $user = unserialize($_SESSION['user_connected']);
            if ($user->active === 1)
            {
                if (!isset($_GET['id'])) {
                    $html = "Vous n'avez pas sélectionné de série";
                } else
                {
                    $id = $_GET['id'];
                    $commentaires = Commentaire::GetCommentaire($id);
                    $html="<p>Commentaires :</p>";
                    foreach ($commentaires as $commentaire) {
                        $html .= <<<END
                        <p>$commentaire</p>
                        END;
                    }
                    $html .=  "<button onclick=\"window.location.href='index.php?action=display-serie&id=$id'\">Retourner à la série</button>";
                }
            } else
            {
                $html = "Le compte doit être activé pour pouvoir commenter";
            }
        }
        return $html;
    }
}

