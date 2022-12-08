<?php

namespace iutnc\netvod\activeRecord;

use Exception;
use iutnc\netvod\db\ConnectionFactory;
use iutnc\netvod\exceptions\InvalidPropertyNameException;

class Avis
{
    private int $id_serie;
    private int $id_user;
    private int $note;
    private string $commentaire;

    public function __construct(int $note, string $commentaire,int $id_serie = -1, int $id_user = -1)
    {
        $this->id_serie = $id_serie;
        $this->id_user = $id_user;
        $this->note = $note;
        $this->commentaire = $commentaire;
    }

    /**
     * Méthode findAll qui permet de récupérer tous les avis
     * @return array tableau d'objets Avis
     */
    public static function findAll() : array
    {
        // Récupération de la connexion à la base de données
        $db = ConnectionFactory::makeConnection();
        // Ecriture de la requête SQL
        $sql = "SELECT * FROM avis";
        // Préparation de la requête
        $stmt = $db->prepare($sql);
        // Exécution de la requête
        $stmt->execute();
        // Création du tableau d'avis
        $avis = [];
        // Récupération des résultats
        while ($row = $stmt->fetch()) {
            $avis[] = new Avis($row['note'], $row['commentaire'], $row['id_serie'], $row['id_user']);
        }
        // Retour du tableau d'avis
        return $avis;
    }

    /**
     * Méthode findBySerieUser qui permet de récupérer un avis en fonction de l'id de la série et de l'id de l'utilisateur
     * @param int $id_serie id de la série
     * @param int $id_user id de l'utilisateur
     * @return Avis|null retourne un objet Avis si il existe, null sinon
     */
    public static function findBySerieUser(int $id_serie, int $id_user) : ?Avis
    {
        // Connexion à la base de données
        $db = ConnectionFactory::makeConnection();
        // Ecriture de la requête SQL
        $sql = "SELECT * FROM avis WHERE id_serie = :id_serie AND id_user = :id_user";
        // Préparation de la requête
        $stmt = $db->prepare($sql);
        // Remplacement des paramètres
        $stmt->bindValue(":id_serie", $id_serie);
        $stmt->bindValue(":id_user", $id_user);
        // Exécution de la requête
        $stmt->execute();
        // Récupération du résultat
        $result = $stmt->fetch();
        // S'il y a un avis pour cette série et cet utilisateur
        if ($result) {
            // Alors on retourne l'objet Avis correspondant
            return new Avis($result["note"], $result["commentaire"], $result["id_serie"], $result["id_user"]);
        }
        // Sinon on retourne null
        return null;
    }

    /**
     * Méthode findBySerie qui permet de récupérer tous les avis d'une série
     * @param int $id_serie id de la série
     * @return array tableau d'objets Avis
     */
    public static function findBySerie(int $id_serie) : array
    {
        // Connexion à la base de données
        $db = ConnectionFactory::makeConnection();
        // Ecriture de la requête SQL
        $sql = "SELECT * FROM avis WHERE id_serie = :id_serie";
        // Préparation de la requête
        $stmt = $db->prepare($sql);
        // Remplacement des paramètres
        $stmt->bindValue(":id_serie", $id_serie);
        // Exécution de la requête
        $stmt->execute();
        // Création du tableau d'avis
        $avis = [];
        // Récupération du résultat
        $result = $stmt->fetchAll();
        // Pour chaque avis de la série
        foreach ($result as $row) {
            // On ajoute l'objet Avis correspondant au tableau
            $avis[] = new Avis($row["note"], $row["commentaire"], $row["id_serie"], $row["id_user"]);
        }
        // On retourne le tableau d'avis
        return $avis;
    }

    /**
     * Méthode findByUser qui permet de récupérer tous les avis d'un utilisateur
     * @param int $id_user id de l'utilisateur
     * @return array tableau d'objets Avis
     */
    public static function findByUser(int $id_user) : array
    {
        // Connexion à la base de données
        $db = ConnectionFactory::makeConnection();
        // Ecriture de la requête SQL
        $sql = "SELECT * FROM avis WHERE id_user = :id_user";
        // Préparation de la requête
        $stmt = $db->prepare($sql);
        // Remplacement des paramètres
        $stmt->bindValue(":id_user", $id_user);
        // Exécution de la requête
        $stmt->execute();
        // Création du tableau d'avis
        $avis = [];
        // Récupération du résultat
        $result = $stmt->fetchAll();
        // Pour chaque avis de l'utilisateur
        foreach ($result as $row) {
            // On ajoute l'objet Avis correspondant au tableau
            $avis[] = new Avis($row["note"], $row["commentaire"], $row["id_serie"], $row["id_user"]);
        }
        // On retourne le tableau d'avis
        return $avis;
    }

    /**
     * Méthode save qui permet de sauvegarder ou de mettre à jour un avis
     * @return void
     * @throws Exception lorsque l'avis est invalide
     */
    public function save() : void
    {
        // Si la serie ou l'utilisateur n'existent pas, on lance une exception
        if ($this->id_serie === -1 || $this->id_user === -1) {
            throw new Exception("Impossible de sauvegarder un avis pour une serie ou un utilisateur inexistant(e)");
        }
        // Si l'avis n'existe pas, on l'ajoute
        $prefs = Avis::findBySerieUser($this->id_serie, $this->id_user);
        if ($prefs === null) {
            $this->insert();
        }
        // Sinon on la met a jour
        else {
            $this->update();
        }
    }

    /**
     * Méthode insert qui permet d'ajouter un avis dans la base de données
     * @return void
     */
    private function insert() : void
    {
        // Connexion à la base de données
        $db = ConnectionFactory::makeConnection();
        // Ecriture de la requête SQL
        $sql = "INSERT INTO avis (note, commentaire, id_serie, id_user) VALUES (:note, :commentaire, :id_serie, :id_user)";
        // Préparation de la requête
        $stmt = $db->prepare($sql);
        // Remplacement des paramètres
        $stmt->bindValue(":note", $this->note);
        $stmt->bindValue(":commentaire", $this->commentaire);
        $stmt->bindValue(":id_serie", $this->id_serie);
        $stmt->bindValue(":id_user", $this->id_user);
        // Exécution de la requête
        $stmt->execute();
    }

    /**
     * Méthode update qui permet de mettre à jour un avis dans la base de données
     * @return void
     */
    private function update() : void
    {
        // Connexion à la base de données
        $db = ConnectionFactory::makeConnection();
        // Ecriture de la requête SQL
        $sql = "UPDATE avis SET note = :note, commentaire = :commentaire WHERE id_serie = :id_serie AND id_user = :id_user";
        // Préparation de la requête
        $stmt = $db->prepare($sql);
        // Remplacement des paramètres
        $stmt->bindValue(":note", $this->note);
        $stmt->bindValue(":commentaire", $this->commentaire);
        $stmt->bindValue(":id_serie", $this->id_serie);
        $stmt->bindValue(":id_user", $this->id_user);
        // Exécution de la requête
        $stmt->execute();
    }

    /**
     * Méthode delete qui permet de supprimer un avis dans la base de données
     * @return void
     */
    public function delete() : void
    {
        // Connexion à la base de données
        $db = ConnectionFactory::makeConnection();
        // Ecriture de la requête SQL
        $sql = "DELETE FROM avis WHERE id_serie = :id_serie AND id_user = :id_user";
        // Préparation de la requête
        $stmt = $db->prepare($sql);
        // Remplacement des paramètres
        $stmt->bindValue(":id_serie", $this->id_serie);
        $stmt->bindValue(":id_user", $this->id_user);
        // Exécution de la requête
        $stmt->execute();
    }

    /**
     * Méthode createTable qui permet de créer la table avis dans la base de données
     * @return void
     */
    public static function createTable() : void
    {
        // Connexion à la base de données
        $db = ConnectionFactory::makeConnection();
        // Ecriture de la requête SQL
        $sql = "CREATE TABLE IF NOT EXISTS avis (
            note INT NOT NULL,
            commentaire VARCHAR(512) NOT NULL,
            id_serie INT NOT NULL,
            id_user INT NOT NULL,
            PRIMARY KEY (id_serie, id_user),
            FOREIGN KEY (id_serie) REFERENCES serie(id) ON DELETE CASCADE,
            FOREIGN KEY (id_user) REFERENCES user(id) ON DELETE CASCADE
        )";
        // Préparation de la requête
        $stmt = $db->prepare($sql);
        // Exécution de la requête
        $stmt->execute();
    }

    /**
     * Méthode deleteTable qui permet de supprimer la table avis dans la base de données
     * @return void
     */
    public static function deleteTable() : void
    {
        // Connexion à la base de données
        $db = ConnectionFactory::makeConnection();
        // Ecriture de la requête SQL
        $sql = "DROP TABLE IF EXISTS avis";
        // Préparation de la requête
        $stmt = $db->prepare($sql);
        // Exécution de la requête
        $stmt->execute();
    }

    /**
     * Getter magique de la classe Avis
     * @throws Exception
     */
    public function __get(string $name): mixed
    {
        if (!property_exists($this, $name)) throw new InvalidPropertyNameException("$name: invalid property");
        return $this->$name;
    }

    /**
     * Setter magique de la classe Avis
     * @throws InvalidPropertyNameException si la propriété n'existe pas
     */
    public function __set(string $name, mixed $value): void
    {
        if (!property_exists($this, $name)) throw new InvalidPropertyNameException("$name: invalid property");
        $this->$name = $value;
    }
}