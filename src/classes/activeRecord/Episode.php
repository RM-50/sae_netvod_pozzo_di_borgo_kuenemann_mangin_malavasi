<?php



namespace iutnc\netvod\activeRecord;


use Exception;
use iutnc\netvod\db\ConnectionFactory;
use iutnc\netvod\exceptions\InvalidPropertyNameException;


class Episode
 {
    private int $numero;
    private string $titre;
    private string $resume;
    private int $duree;
    private string $file;
    private int $serie_id;
    private int $id;


    /**
     * Constructeur de la classe Episode
     * @param int $numero numéro de l'épisode dans la série
     * @param string $titre titre de l'épisode
     * @param string $resume résumé de l'épisode
     * @param string $file nom du fichier de l'épisode
     * @param int $duree durée de l'épisode
     * @param int $serie_id id de la série à laquelle appartient l'épisode
     * @param int $id id de l'épisode
     */
    public function __construct(int $numero, string $titre, string $resume, string $file, int $duree, int $serie_id, int $id = -1)
    {
        $this->numero = $numero;
        $this->titre = $titre;
        $this->resume = $resume;
        $this->duree = $duree;
        $this->file = $file;
        $this->serie_id = $serie_id;
        $this->id = $id;
    }

    /**
     * Méthode findById qui permet de récupérer un épisode à partir de son id
     * @param int $id id de l'épisode
     * @return Episode|null retourne l'épisode si il existe, null sinon
     */
    public static function findById(int $id): ?Episode
    {
        // Récupération de la connexion à la base de données
        $db = ConnectionFactory::makeConnection();
        // Ecriture la requête SQL
        $sqlEpisode = "SELECT * FROM episode where id = ?";
        // Prépararation de la requête
        $stmt_episode = $db->prepare($sqlEpisode);
        // Remplacement des paramètres
        $stmt_episode->bindParam(1, $id);
        // Exécution de la requête
        $stmt_episode->execute();
        $episode = null;
        // Récupération de l'épisode dans la base de données s'il existe
        while ($row_episode = $stmt_episode->fetch())
        {
            // Création de l'objet épisode
            $episode = new Episode($row_episode["numero"], $row_episode["titre"], $row_episode["resume"], $row_episode["file"], $row_episode["duree"], $row_episode["serie_id"], $row_episode["id"]);
        }
        // Retourner l'épisode
        return $episode;
    }

    /**
     * Méthode findBySerieId qui permet de récupérer les épisodes d'une série à partir de son id
     * @param int $serie_id
     * @return Episode|null
     */
    public static function findBySerieId(int $serie_id) : array
    {
        // Récupération de la connexion à la base de données
        $db = ConnectionFactory::makeConnection();
        // Ecriture la requête SQL
        $sqlEpisode = "SELECT * FROM episode where serie_id = ?";
        // Prépararation de la requête
        $stmt_episode = $db->prepare($sqlEpisode);
        // Remplacement des paramètres
        $stmt_episode->bindParam(1, $serie_id);
        // Exécution de la requête
        $stmt_episode->execute();
        // Création d'un tableau qui contiendra les épisodes
        $episodes = [];
        // Récupération de l'épisode dans la base de données s'il existe
        while ($row_episode = $stmt_episode->fetch())
        {
            // Création de l'objet épisode et ajout dans le tableau
            $episodes[] = new Episode($row_episode["numero"], $row_episode["titre"], $row_episode["resume"], $row_episode["file"], $row_episode["duree"], $row_episode["serie_id"], $row_episode["id"]);
        }
        // Retourner le tableau d'épisodes
        return $episodes;
    }

    /**
     * Méthode save qui permet de sauvegarder ou mettre à jour un épisode dans la base de données
     * @return void
     */
    public function save() : void
    {
        // Si l'id de l'épisode est -1, c'est qu'il n'existe pas dans la base de données
        if ($this->id == -1)
        {
            // Donc on l'insère
            $this->insert();
        }
        // Sinon, il existe déjà dans la base de données
        else
        {
            // Donc on le met à jour
            $this->update();
        }
    }

    /**
     * Méthode insert qui permet d'insérer un épisode dans la base de données
     * @return void
     */
    private function insert() : void
    {
        // Récupération de la connexion à la base de données
        $db = ConnectionFactory::makeConnection();
        // Ecriture la requête SQL
        $sqlEpisode = "INSERT INTO episode (numero, titre, resume, file, duree, serie_id) VALUES (?, ?, ?, ?, ?, ?)";
        // Prépararation de la requête
        $stmt_episode = $db->prepare($sqlEpisode);
        // Remplacement des paramètres
        $stmt_episode->bindParam(1, $this->numero);
        $stmt_episode->bindParam(2, $this->titre);
        $stmt_episode->bindParam(3, $this->resume);
        $stmt_episode->bindParam(4, $this->file);
        $stmt_episode->bindParam(5, $this->duree);
        $stmt_episode->bindParam(6, $this->serie_id);
        // Exécution de la requête
        $stmt_episode->execute();
        // Récupération de l'id de l'épisode
        $this->id = (int)$db->lastInsertId();
    }

    /**
     * Méthode update qui permet de mettre à jour un épisode dans la base de données
     * @return void
     */
    private function update() : void
    {
        // Récupération de la connexion à la base de données
        $db = ConnectionFactory::makeConnection();
        // Ecriture la requête SQL
        $sqlEpisode = "UPDATE episode SET numero = ?, titre = ?, resume = ?, file = ?, duree = ?, serie_id = ? WHERE id = ?";
        // Prépararation de la requête
        $stmt_episode = $db->prepare($sqlEpisode);
        // Remplacement des paramètres
        $stmt_episode->bindParam(1, $this->numero);
        $stmt_episode->bindParam(2, $this->titre);
        $stmt_episode->bindParam(3, $this->resume);
        $stmt_episode->bindParam(4, $this->file);
        $stmt_episode->bindParam(5, $this->duree);
        $stmt_episode->bindParam(6, $this->serie_id);
        $stmt_episode->bindParam(7, $this->id);
        // Exécution de la requête
        $stmt_episode->execute();
    }

    /**
     * Méthode delete qui permet de supprimer un épisode dans la base de données
     * @return void
     */
    public function delete() : void
    {
        // Récupération de la connexion à la base de données
        $db = ConnectionFactory::makeConnection();
        // Ecriture la requête SQL
        $sqlEpisode = "DELETE FROM episode WHERE id = ?";
        // Prépararation de la requête
        $stmt_episode = $db->prepare($sqlEpisode);
        // Remplacement des paramètres
        $stmt_episode->bindParam(1, $this->id);
        // Exécution de la requête
        $stmt_episode->execute();
    }

    /**
     * Méthode createTable qui permet de créer la table episode dans la base de données
     * @return void
     */
    public static function createTable() : void
    {
        // Récupération de la connexion à la base de données
        $db = ConnectionFactory::makeConnection();
        // Ecriture la requête SQL
        $sqlEpisode = "CREATE TABLE IF NOT EXISTS episode (
            id INT AUTO_INCREMENT PRIMARY KEY,
            numero INT NOT NULL,
            titre VARCHAR(40) NOT NULL,
            resume TEXT,
            file VARCHAR(40) NOT NULL,
            duree INT NOT NULL,
            serie_id INT NOT NULL,
            FOREIGN KEY (serie_id) REFERENCES serie(id)
        )";
        // Prépararation de la requête
        $stmt_episode = $db->prepare($sqlEpisode);
        // Exécution de la requête
        $stmt_episode->execute();
    }

    /**
     * Méthode deleteTable qui permet de supprimer la table episode dans la base de données
     * @return void
     */
    public static function deleteTable() : void
    {
        // Récupération de la connexion à la base de données
        $db = ConnectionFactory::makeConnection();
        // Ecriture la requête SQL
        $sqlEpisode = "DROP TABLE IF EXISTS episode";
        // Prépararation de la requête
        $stmt_episode = $db->prepare($sqlEpisode);
        // Exécution de la requête
        $stmt_episode->execute();
    }

    /**
     * Getter magique de la classe Episode
     * @throws Exception si la propriété n'existe pas
     */
    public function __get(string $name): mixed
    {
        if (!property_exists($this, $name)) throw new Exception("$name: invalid property");
        return $this->$name;
    }


    /**
     * Setter magique de la classe Episode
     * @param string $attribut
     * @param mixed $valeur
     * @return void
     * @throws InvalidPropertyNameException si la propriété n'existe pas
     */
    public function __set(string $attribut, mixed $valeur) : void
    {
        if (property_exists($this, $attribut))
        {
            $this->$attribut = $valeur;
        }
        else
        {
            throw new InvalidPropertyNameException("$attribut: invalid property");
        }
    }

    public function __toString() : string {
        return json_encode($this, JSON_PRETTY_PRINT);
    }

}