<?php



declare(strict_types=1);

namespace iutnc\netvod\activeRecord;


use iutnc\netvod\db\ConnectionFactory;
use iutnc\netvod\exceptions\InvalidPropertyNameException;


class User
{
    private string $email, $password;
    private string $activation_token, $activation_expires, $renew_token, $renew_expires;
    private int $role, $id, $active, $id_profil;

    /**
     * Constructeur de la classe User
     * @param string $email email de l'utilisateur
     * @param string $passwd mot de passe de l'utilisateur
     * @param int $role rôle de l'utilisateur
     * @param string $at jeton d'activation
     * @param string $ae date d'expiration du jeton d'activation
     * @param string $rt jeton de renouvellement de mot de passe
     * @param string $re date d'expiration du jeton de renouvellement de mot de passe
     * @param int $active 0 si l'utilisateur n'est pas activé 1 sinon
     * @param int $id_profil id du profil de l'utilisateur
     * @param int $id id de l'utilisateur
     */
    public function __construct(string $email, string $passwd, int $role, string $at, string $ae, string $rt, string $re, int $active, int $id_profil, int $id = -1)
    {
        $this->email = $email;
        $this->password = $passwd;
        $this->role = $role;
        $this->activation_token = $at;
        $this->activation_expires = $ae;
        $this->renew_token = $rt;
        $this->renew_expires = $re;
        $this->id = $id;
        $this->active = $active;
        $this->id_profil = $id_profil;
    }

    /**
     * Getter magique de la classe User
     * @throws InvalidPropertyNameException
     */
    public function __get(string $attribut) : mixed
    {
        if (property_exists($this, $attribut))
            return $this->$attribut;
        else
            throw new InvalidPropertyNameException("La classe user ne possede pas d'attribut : $attribut");
    }

    /**
     * @param string $attribut
     * @param mixed $valeur
     * @return void
     */
    public function __set(string $attribut, mixed $valeur) : void
    {
        if (property_exists($this, $attribut))
        {
            $this->$attribut = $valeur;
        }
    }

    /**
     * Méthode findById permettant de récupérer un utilisateur à partir de son id
     * @return User|null
     */
    public static function findById(int $id) : ?User
    {
        // Récupération de la connexion à la base de données
        $db = ConnectionFactory::makeConnection();
        // Préparation de la requête
        $stmt = $db->prepare("SELECT * FROM user WHERE id = ?");
        // Remplacement des paramètres
        $stmt->bindParam(1, $id);
        // Exécution de la requête
        $stmt->execute();
        // Récupération du résultat
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        // Si l'utilisateur n'existe pas
        if (!$row)
            // Alors on retourne null
            return null;
        // Sinon on retourne un objet User
        else
            return new User($row['email'], $row['password'], $row['role'], $row['activation_token'], $row['activation_expires'], $row['renew_token'], $row['renew_expires'], $row['active'], $row['id_profil'], $row['id']);
    }

    /**
     * Méthode findByEmail permettant de récupérer un utilisateur à partir de son email
     * @return User|null
     */
    public static function findByEmail(string $email) : ?User
    {
        // Récupération de la connexion à la base de données
        $db = ConnectionFactory::makeConnection();
        // Préparation de la requête
        $stmt = $db->prepare("SELECT * FROM user WHERE email = ?");
        // Remplacement des paramètres
        $stmt->bindParam(1, $email);
        // Exécution de la requête
        $stmt->execute();
        // Récupération du résultat
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        // Si l'utilisateur n'existe pas
        if (!$row)
            // Alors on retourne null
            return null;
        // Sinon on retourne un objet User
        else
            return new User($row['email'], $row['password'], $row['role'], $row['activation_token'], $row['activation_expires'], $row['renew_token'], $row['renew_expires'], $row['active'], $row['id_profil'], $row['id']);
    }

    /**
     * Méthode findByRole permettant de récupérer un tableau d'utilisateurs à partir de leur rôle
     * @param int $role rôle des utilisateurs à récupérer
     * @return array tableau d'utilisateurs
     */
    public static function findByRole(int $role) : array
    {
        // Récupération de la connexion à la base de données
        $db = ConnectionFactory::makeConnection();
        // Préparation de la requête
        $stmt = $db->prepare("SELECT * FROM user WHERE role = ?");
        // Remplacement des paramètres
        $stmt->bindParam(1, $role);
        // Exécution de la requête
        $stmt->execute();
        // Création du tableau d'utilisateurs
        $users = [];
        // Récupération des utilisateurs dont le rôle est $role
        while($row = $stmt->fetch(\PDO::FETCH_ASSOC))
        {
            // Et on les ajoute dans le tableau
            $users[] = new User($row['email'], $row['password'], $row['role'], $row['activation_token'], $row['activation_expires'], $row['renew_token'], $row['renew_expires'], $row['active'], $row['id_profil'], $row['id']);
        }
        // On retourne le tableau d'utilisateurs
        return $users;
    }

    /**
     * Méthode save permettant de sauvegarder ou de mettre à jour un utilisateur dans la base de données
     * @return void
     */
    public function save() : void
    {
        // Si l'id de l'utilisateur est -1, alors il n'existe pas en base de données
        if ($this->id == -1)
        {
            // Alors on l'ajoute dans la base de données
            $this->insert();
        }
        // Sinon il existe déjà en base de données
        else
        {
            // Alors on le met à jour
            $this->update();
        }
    }

    /**
     * Méthode insert permettant d'ajouter un utilisateur dans la base de données
     * @return void
     */
    private function insert() : void
    {
        // Récupération de la connexion à la base de données
        $db = ConnectionFactory::makeConnection();
        // Préparation de la requête
        $stmt = $db->prepare("INSERT INTO user (email, password, role, activation_token, activation_expires, renew_token, renew_expires, active, id_profil) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        // Remplacement des paramètres
        $stmt->bindParam(1, $this->email);
        $stmt->bindParam(2, $this->password);
        $stmt->bindParam(3, $this->role);
        $stmt->bindParam(4, $this->activation_token);
        $stmt->bindParam(5, $this->activation_expires);
        $stmt->bindParam(6, $this->renew_token);
        $stmt->bindParam(7, $this->renew_expires);
        $stmt->bindParam(8, $this->active);
        $stmt->bindParam(9, $this->id_profil);
        // Exécution de la requête
        $stmt->execute();
        // Récupération de l'id de l'utilisateur
        $this->id = (int)$db->lastInsertId();
    }

    /**
     * Méthode update permettant de mettre à jour un utilisateur dans la base de données
     * @return void
     */
    private function update() : void
    {
        // Récupération de la connexion à la base de données
        $db = ConnectionFactory::makeConnection();
        // Préparation de la requête
        $stmt = $db->prepare("UPDATE user SET email = ?, password = ?, role = ?, activation_token = ?, activation_expires = ?, renew_token = ?, renew_expires = ?, active = ?, id_profil = ? WHERE id = ?");
        // Remplacement des paramètres
        $stmt->bindParam(1, $this->email);
        $stmt->bindParam(2, $this->password);
        $stmt->bindParam(3, $this->role);
        $stmt->bindParam(4, $this->activation_token);
        $stmt->bindParam(5, $this->activation_expires);
        $stmt->bindParam(6, $this->renew_token);
        $stmt->bindParam(7, $this->renew_expires);
        $stmt->bindParam(8, $this->active);
        $stmt->bindParam(9, $this->id_profil);
        $stmt->bindParam(10, $this->id);
        // Exécution de la requête
        $stmt->execute();
    }

    /**
     * Méthode delete permettant de supprimer un utilisateur de la base de données
     * @return void
     */
    public function delete() : void
    {
        // Récupération de la connexion à la base de données
        $db = ConnectionFactory::makeConnection();
        // Préparation de la requête
        $stmt = $db->prepare("DELETE FROM user WHERE id = ?");
        // Remplacement des paramètres
        $stmt->bindParam(1, $this->id);
        // Exécution de la requête
        $stmt->execute();
    }

    /**
     * Méthode createTable permettant de créer la table user dans la base de données
     * @return void
     */
    public static function createTable() : void
    {
        // Récupération de la connexion à la base de données
        $db = ConnectionFactory::makeConnection();
        // Préparation de la requête
        $stmt = $db->prepare("CREATE TABLE IF NOT EXISTS user (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) NOT NULL,
            password VARCHAR(255) NOT NULL,
            role INT(11) NOT NULL,
            activation_token VARCHAR(128),
            activation_expires TIMESTAMP,
            renew_token VARCHAR(128),
            renew_expires TIMESTAMP,
            active TINYINT(1) NOT NULL,
            id_profil INT,
            FOREIGN KEY (id_profil) REFERENCES profil(id)
        )");
        // Exécution de la requête
        $stmt->execute();
    }

    /**
     * Méthode deleteTable permettant de supprimer la table user de la base de données
     * @return void
     */
    public static function deleteTable() : void
    {
        // Récupération de la connexion à la base de données
        $db = ConnectionFactory::makeConnection();
        // Préparation de la requête
        $stmt = $db->prepare("DROP TABLE IF EXISTS user");
        // Exécution de la requête
        $stmt->execute();
    }
}