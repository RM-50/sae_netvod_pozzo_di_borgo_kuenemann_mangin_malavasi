<?php



namespace iutnc\netvod\note;


use iutnc\netvod\db\ConnectionFactory;
use iutnc\netvod\exceptions\InvalidPropertyNameException;
use PDO;



class Note
{

    private int $note, $id_serie, $id_user;
    private string $commentaire;



    /**
     *
     */
    public function __construct()
    {
        if (isset($_GET['id'])) {
            $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
            $sql = "SELECT note,commentaire FROM avis WHERE id_serie = ? AND id_user = ?";
            $db = ConnectionFactory::makeConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam(1, $id);
            $user = unserialize($_SESSION['user_connected']);
            $userId = $user->id;
            $stmt->bindParam(2, $userId);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$result) {
                $this->note = 0;
                $this->commentaire = "";
            } else {
                $this->note = $result['note'];
                $this->commentaire = $result['commentaire'];
            }
            $this->id_serie = $id;
            $this->id_user = $userId;
        }
        else{
            $this->note=0;
        }
    }



    /**
     * @param string $commentaire
     * @param int $note
     * @param int $id_serie
     * @param int $id_user
     * @return void
     */
    public static function insertionCommentaire(string $commentaire, int $note, int $id_serie, int $id_user): void
    {
        $db = ConnectionFactory::makeConnection();
        $sql = "INSERT INTO avis (id_serie, id_user, note, commentaire) VALUES (?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(1, $id_serie);
        $stmt->bindParam(2, $id_user);
        $stmt->bindParam(3, $note);
        $stmt->bindParam(4, $commentaire);
        $stmt->execute();

    }



    /**
     * @return bool
     */

    public function avisVide():bool
    {
        if ($this->note == 0 && $this->commentaire == null)
        {
            return true;
        }
        return false;
    }


    /**
     * @param string $attribut
     * @return mixed
     * @throws InvalidPropertyNameException
     */
    public function __get(string $attribut) : mixed
    {
        if (property_exists($this, $attribut))
            return $this->$attribut;
        else
            throw new InvalidPropertyNameException("La classe note ne possede pas d'attribut : $attribut");
    }
}