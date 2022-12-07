<?php

namespace iutnc\netvod\activeRecord;

use iutnc\netvod\db\ConnectionFactory;
use iutnc\netvod\exceptions\InvalidPropertyNameException;

class Genre
{
    private int $id;
    private string $libelle;

    /**
     * Constructeur de la classe Genre
     * @param string $libelle libelle du genre
     * @param int $id id du genre
     */
    public function __construct(string $libelle, int $id = -1)
    {
        $this->id = $id;
        $this->libelle = $libelle;
    }

    /**
     * Méthode findById qui permet de récupérer un genre à partir de son id
     * @param int $id id du genre
     * @return Genre|null retourne le genre si il existe, null sinon
     */
    public static function findById(int $id): ?Genre
    {
        // Récupération de la connexion à la base de données
        $db = ConnectionFactory::makeConnection();
        // Ecriture la requête SQL
        $sqlGenre = "SELECT * FROM genre where id = ?";
        // Prépararation de la requête
        $stmt_genre = $db->prepare($sqlGenre);
        // Remplacement des paramètres
        $stmt_genre->bindParam(1, $id);
        // Exécution de la requête
        $stmt_genre->execute();
        // Récupération du résultat
        $result = $stmt_genre->fetch();
        // Si le résultat est vide, on retourne null
        if (empty($result)) {
            return null;
        }
        // Sinon on retourne un objet Genre
        return new Genre($result["libelle"], $result["id"]);
    }

    /**
     * Méthode findByLibelle qui permet de récupérer tous les genres  dont le libelle contient la chaine de caractère passée en paramètre
     * @return Genre[] tableau contenant les genres
     */
    public static function findByLibelle(string $libelle) : array
    {
        // Récupération de la connexion à la base de données
        $db = ConnectionFactory::makeConnection();
        // Ecriture la requête SQL
        $sqlGenre = "SELECT * FROM genre where libelle like '%?%'";
        // Prépararation de la requête
        $stmt_genre = $db->prepare($sqlGenre);
        // Remplacement des paramètres
        $stmt_genre->bindParam(1, $libelle);
        // Exécution de la requête
        $stmt_genre->execute();
        // Récupération du résultat
        $result = $stmt_genre->fetchAll();
        // Création et remplissage du tableau de genres
        $genres = [];
        foreach ($result as $row) {
            $genres[] = new Genre($row["libelle"], $row["id"]);
        }
        // Retour du tableau de genres
        return $genres;
    }

    /**
     * Méthode findAll qui permet de récupérer tous les genres
     * @return Genre[] tableau contenant les genres
     */
    public static function findAll() : array
    {
        // Récupération de la connexion à la base de données
        $db = ConnectionFactory::makeConnection();
        // Ecriture la requête SQL
        $sqlGenre = "SELECT * FROM genre";
        // Prépararation de la requête
        $stmt_genre = $db->prepare($sqlGenre);
        // Exécution de la requête
        $stmt_genre->execute();
        // Récupération du résultat
        $result = $stmt_genre->fetchAll();
        // Création et remplissage du tableau de genres
        $genres = [];
        foreach ($result as $row) {
            $genres[] = new Genre($row["libelle"], $row["id"]);
        }
        // Retour du tableau de genres
        return $genres;
    }

    /**
     * Méthode save qui permet de sauvegarder un genre dans la base de données
     * @return void
     */
    public function save() : void
    {
        // Si l'id est -1, on insère le genre
        if ($this->id == -1) {
            $this->insert();
        }
        // Sinon on met à jour le genre
        else {
            $this->update();
        }
    }

    /**
     * Méthode insert qui permet d'insérer un genre dans la base de données
     * @return void
     */
    private function insert() : void
    {
        // Récupération de la connexion à la base de données
        $db = ConnectionFactory::makeConnection();
        // Ecriture la requête SQL
        $sqlGenre = "INSERT INTO genre (libelle) VALUES (?)";
        // Prépararation de la requête
        $stmt_genre = $db->prepare($sqlGenre);
        // Remplacement des paramètres
        $stmt_genre->bindParam(1, $this->libelle);
        // Exécution de la requête
        $stmt_genre->execute();
        // Récupération de l'id généré
        $this->id = $db->lastInsertId();
    }

    /**
     * Méthode update qui permet de mettre à jour un genre dans la base de données
     * @return void
     */
    private function update() : void
    {
        // Récupération de la connexion à la base de données
        $db = ConnectionFactory::makeConnection();
        // Ecriture la requête SQL
        $sqlGenre = "UPDATE genre SET libelle = ? WHERE id = ?";
        // Prépararation de la requête
        $stmt_genre = $db->prepare($sqlGenre);
        // Remplacement des paramètres
        $stmt_genre->bindParam(1, $this->libelle);
        $stmt_genre->bindParam(2, $this->id);
        // Exécution de la requête
        $stmt_genre->execute();
    }

    /**
     * Méthode delete qui permet de supprimer un genre dans la base de données
     * @return void
     */
    public function delete() : void
    {
        // Récupération de la connexion à la base de données
        $db = ConnectionFactory::makeConnection();
        // Ecriture la requête SQL
        $sqlGenre = "DELETE FROM genre WHERE id = ?";
        // Prépararation de la requête
        $stmt_genre = $db->prepare($sqlGenre);
        // Remplacement des paramètres
        $stmt_genre->bindParam(1, $this->id);
        // Exécution de la requête
        $stmt_genre->execute();
    }

    /**
     * Méthode createTable qui permet de créer la table genre dans la base de données
     * @return void
     */
    public static function createTable() : void
    {
        // Récupération de la connexion à la base de données
        $db = ConnectionFactory::makeConnection();
        // Ecriture la requête SQL
        $sqlGenre = "CREATE TABLE IF NOT EXISTS genre (
            id INT AUTO_INCREMENT PRIMARY KEY,
            libelle VARCHAR(255) NOT NULL
        )";
        // Prépararation de la requête
        $stmt_genre = $db->prepare($sqlGenre);
        // Exécution de la requête
        $stmt_genre->execute();
    }

    /**
     * Méthode deleteTable qui permet de supprimer la table genre dans la base de données
     * @return void
     */
    public static function deleteTable() : void
    {
        // Récupération de la connexion à la base de données
        $db = ConnectionFactory::makeConnection();
        // Ecriture la requête SQL
        $sqlGenre = "DROP TABLE IF EXISTS genre";
        // Prépararation de la requête
        $stmt_genre = $db->prepare($sqlGenre);
        // Exécution de la requête
        $stmt_genre->execute();
    }

    /**
     * Getter magique de la classe genre
     * @param $attribute nom de l'attribut à récupérer
     * @return mixed valeur de l'attribut
     * @throws InvalidPropertyNameException si l'attribut n'existe pas
     */
    public function __get($attribute) : mixed
    {
        if (property_exists($this, $attribute)) {
            return $this->$attribute;
        }
        else {
            throw new InvalidPropertyNameException("L'attribut $attribute n'existe pas");
        }
    }

    /**
     * Setter magique de la classe genre
     * @param $attribute nom de l'attribut à modifier
     * @param $value valeur de l'attribut
     * @return void
     * @throws InvalidPropertyNameException si l'attribut n'existe pas
     */
    public function __set($attribute, $value) : void
    {
        if (property_exists($this, $attribute)) {
            $this->$attribute = $value;
        }
        else {
            throw new InvalidPropertyNameException("L'attribut $attribute n'existe pas");
        }
    }
}