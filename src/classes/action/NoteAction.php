<?php

namespace iutnc\netvod\action;

use iutnc\netvod\note\Note;



class NoteAction extends Action
{

    public function execute(): string
    {
        $notation = new Note();
        if(!isset($_SESSION['user_connected']))
        {
            $html= " Vous n'êtes pas connecté";

        }
        else
        {
            if(!isset($_GET['id']))
            {
                $html = "Vous n'avez pas sélectionné de série";
            }
            else{

                if($notation->avisVide())
                {
                    $html = "Vous n'avez pas encore noté cette série";
                    $id= $_GET['id'];
                    $user = unserialize($_SESSION['user_connected']);
                    $user_id = $user->id;
                    if ($this->http_method === 'GET')
                    {
                        $html = <<<END
                        <form method="post" action="index.php?action=note&id=$id">
                            <label for="note" >Note : </label><input type="number" min="1" max="5" name="note"></br>
                            <label for="commentaire" >Commentaire : </label><br/>
                            <textarea name="commentaire" rows="7" cols="30"></textarea><br/>
                            <button type="submit">Valider</button>
                        </form>    
                        END;
                    }
                    elseif ($this->http_method === 'POST')
                    {
                        $note = filter_var($_POST['note'], FILTER_SANITIZE_NUMBER_INT);
                        $commentaire = filter_var($_POST['commentaire'], FILTER_SANITIZE_STRING);
                        $html = <<<END
                        <p>Vous avez donné la note de $note/5</p></br>
                        <p>Commentaire : $commentaire</p>
                        <button onclick="window.location.href='index.php'">Retourner à la série</button>
                        END;
                        Note::insertionCommentaire($commentaire, $note, $id, $user_id);
                    }
                }
                else
                {
                    if(isset($_GET['modifier']))
                    {
                        $id= $_GET['id'];
                        $html = <<<END
                        <form method="post" action="index.php?action=note&id=$id">
                            <label for="note" >Note : </label><input type="number" min="1" max="5" name="note"></br>
                            <label for="commentaire" >Commentaire : </label><br/>
                            <textarea name="commentaire" rows="7" cols="30"></textarea><br/>
                            <button type="submit">Valider</button>
                        </form>    
                        END;
                        $notation->modifierAvis();
                    }
                    else
                    {
                        $id= $_GET['id'];
                        $html = <<<END
                        <p xmlns="http://www.w3.org/1999/html">Votre note pour cette série est de {$notation->note} et votre commentaire est : </br> {$notation->commentaire}</p>
                        <button onclick="window.location.href='index.php">Retourner à la série</button>
                        <button onclick="window.location.href='index.php?action=note&id=$id&modifier=true'">Modifier la note et le commentaire</button>
                        END;
                    }

                }

            }
        }
        return $html;
    }
}