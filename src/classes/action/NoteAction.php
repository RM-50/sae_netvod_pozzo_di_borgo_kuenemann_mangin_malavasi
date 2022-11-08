<?php

namespace iutnc\netvod\action;

class NoteAction extends Action
{

    public function execute(): string
    {
        if(!isset($_SESSION['user_connected']))
        {
            $html= " Vous n'êtes pas connecté";

        }
        else{
            if(!isset($_GET['id']))
            {
                $html = "Vous n'avez pas sélectionné de série";
            }
            else{
                $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
                $sql = "SELECT note FROM `serie` WHERE id_serie = ? AND id_user = ?";
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(1, $id);
                $user = unserialize($_SESSION['user_connected']);
                $stmt->bindParam(2, $user->id);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                if(!$result)
                {
                    $html = "Vous n'avez pas encore noté cette série";
                    if ($this->http_method === 'GET')
                    {
                        $html = <<<END
                        <form method="post" action="index.php?action=note">
                            <label>Note : <input type="number" min="1" max="5" name="note"></label>
                            <label>Commentaire : </label><br/>
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
                        <p>Vous avez donné la note de $note/5</p>
                        <p>Commentaire : $commentaire</p>
                        END;
                    }
                }
                else{
                    $html =<<<END
                    <p>Votre note pour cette série est de {$result['note']} et votre commentaire est : {$result['commentaire']}</p>
                    <button onclick="window.location.href='index.php?action='">Retourner à la série</button>
                    <button onclick="window.location.href='index.php?action=note'">Modifier la note et le commentaire</button>
                    END;
                }

            }
        }
        return $html;
    }
}