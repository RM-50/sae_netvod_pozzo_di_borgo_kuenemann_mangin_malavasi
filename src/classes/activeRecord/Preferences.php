<?php

namespace iutnc\netvod\activeRecord;

use Exception;
use iutnc\netvod\db\ConnectionFactory;
use iutnc\netvod\exceptions\InvalidPropertyNameException;


class Preferences
{
    private int $id_serie;
    private int $id_user;

    /**
     * Constructeur de la classe Preferences
     * @param int $id_serie id de la série
     * @param int $id_user id de l'utilisateur
     */
    public function __construct(int $id_serie = -1, int $id_user = -1)
    {
        $this->id_serie = $id_serie;
        $this->id_user = $id_user;
    }

    /**
     * Méthode findAll qui permet de retourner toutes les préférences de la base de données
     * @return array tableau des préférences
     */
    public static function findAll() : array
    {
        // Récupération de la connexion à la base de données
        $db = ConnectionFactory::makeConnection();
        // Ecriture la requête SQL
        $sql = "select * from preferences";
        // Prépararation de la requête
        $st = $db->prepare($sql);
        // Exécution de la requête
        $st->execute();
        $result = $st->fetchAll();
        // Création et remplissage du tableau de préférences
        $preferences = [];
        foreach ($result as $row) {
            $preferences[] = new Preferences($row['id_serie'], $row['id_user']);
        }
        // Retour du tableau de préférences
        return $preferences;
    }

    /**
     * Méthode findBySerieUser qui permet de renvoyer la preference d'un utilisateur pour une série
     * @param int $id_serie
     * @param int $id_user
     * @return Preferences|null
     */
    public static function findBySerieUser(int $id_serie, int $id_user) : ?Preferences
    {
        // Récupération de la connexion à la base de données
        $db = ConnectionFactory::makeConnection();
        // Ecriture la requête SQL
        $sql = "select * from preferences where id_serie = ? and id_user = ?";
        // Prépararation de la requête
        $st = $db->prepare($sql);
        // Remplacement des paramètres
        $st->bindParam(1, $id_serie);
        $st->bindParam(2, $id_user);
        // Exécution de la requête
        $st->execute();
        $result = $st->fetch();
        // Si le résultat est vide, on retourne null
        if (empty($result)) {
            return null;
        }
        // Sinon, on retourne la préférence correspondante
        return new Preferences($result['id_serie'], $result['id_user']);
    }

    /**
     * Méthode findByUser qui permet de renvoyer les préférences d'un utilisateur
     * @param int $user_id id de l'utilisateur
     * @return array tableau des préférences
     */
    public static function findByUser(int $user_id) : array
    {
        // Récupération de la connexion à la base de données
        $db = ConnectionFactory::makeConnection();
        // Ecriture la requête SQL
        $sql = "select * from preferences where id_user = ?";
        // Prépararation de la requête
        $st = $db->prepare($sql);
        // Remplacement des paramètres
        $st->bindParam(1, $user_id);
        // Exécution de la requête
        $st->execute();
        $result = $st->fetchAll();
        // Création et remplissage du tableau de préférences
        $preferences = [];
        foreach ($result as $row) {
            $preferences[] = new Preferences($row['id_serie'], $row['id_user']);
        }
        // Retour du tableau de préférences
        return $preferences;
    }

    /**
     * Méthode findBySerie qui permet de trouver les utilisateurs qui ont mis cette serie en favori
     * @param int $id_serie id de la série
     * @return array tableau des préférences
     */
    public static function findBySerie(int $id_serie) : array
    {
        // Récupération de la connexion à la base de données
        $db = ConnectionFactory::makeConnection();
        // Ecriture la requête SQL
        $sql = "select * from preferences where id_serie = ?";
        // Prépararation de la requête
        $st = $db->prepare($sql);
        // Remplacement des paramètres
        $st->bindParam(1, $id_serie);
        // Exécution de la requête
        $st->execute();
        $result = $st->fetchAll();
        // Création et remplissage du tableau de préférences
        $preferences = [];
        foreach ($result as $row) {
            $preferences[] = new Preferences($row['id_serie'], $row['id_user']);
        }
        // Retour du tableau de préférences
        return $preferences;
    }

    /**
     * Méthode delete qui permet de supprimer les préférences de la série dont l'id est passé en paramètre
     * @param int $id_serie id de la série à supprimer
     * @return void
     */
    public static function deleteBySerie(int $id_serie) : void
    {
        // Récupération de la connection à la base de données
        $db = ConnectionFactory::makeConnection();
        // Ecriture de la requête SQL
        $sql = "delete from preferences where id_serie = ?";
        // Préparation de la requête
        $st = $db->prepare($sql);
        // Remplacement des paramètres
        $st->bindParam(1, $id_serie);
        // Exécution de la requête
        $st->execute();
    }

    /**
     * Méthode delete qui permet de supprimer les préférences de l'utilisateur dont l'id est passé en paramètre
     * @param int $id_user id de l'utilisateur à supprimer
     * @return void
     */
    public static function deleteByUser(int $id_user) : void
    {
        // Récupération de la connection à la base de données
        $db = ConnectionFactory::makeConnection();
        // Ecriture de la requête SQL
        $sql = "delete from preferences where id_user = ?";
        // Préparation de la requête
        $st = $db->prepare($sql);
        // Remplacement des paramètres
        $st->bindParam(1, $id_user);
        // Exécution de la requête
        $st->execute();
    }

    /**
     * Méthode save qui permet d'enregistrer ou de mettre à jour une préférence dans la base de données
     * @return void
     * @throws Exception si la préférence n'est pas valide
     */
    public function save() : void
    {
        // Si la serie ou l'utilisateur n'existent pas, on lance une exception
        if ($this->id_serie === -1 || $this->id_user === -1) {
            throw new Exception("Impossible de sauvegarder une préférence pour une serie ou un utilisateur inexistant(e)");
        }
        // Si la préférence n'existe pas, on l'ajoute
        $prefs = Preferences::findBySerieUser($this->id_serie, $this->id_user);
        if ($prefs === null) {
            $this->insert();
        }
        // Sinon on la met a jour
        else {
            $this->update();
        }
    }

    /**
     * Méthode insert qui permet d'ajouter une préférence à la base de données
     * @param int $idUser id de l'utilisateur
     * @param int $idSerie id de la série
     * @return void
     */
    private function insert(): void
    {
        // Récupération de la connection à la base de données
        $db = ConnectionFactory::makeConnection();
        // Ecriture de la requête SQL
        $sql = "insert into preferences (id_serie,id_user) values (?,?)";
        // Préparation de la requête
        $st = $db->prepare($sql);
        // Remplacement des paramètres
        $st->bindParam(1, $this->id_user);
        $st->bindParam(2, $this->id_serie);
        // Exécution de la requête
        $st->execute();
    }

    /**
     * Méthode update qui permet de mettre à jour une préférence dans la base de données
     * @return void
     */
    private function update() : void
    {
        // Récupération de la connection à la base de données
        $db = ConnectionFactory::makeConnection();
        // Ecriture de la requête SQL
        $sql = "update preferences set id_serie = ?, id_user = ? where id_serie = ? and id_user = ?";
        // Préparation de la requête
        $st = $db->prepare($sql);
        // Remplacement des paramètres
        $st->bindParam(1, $this->id_serie);
        $st->bindParam(2, $this->id_user);
        $st->bindParam(3, $this->id_serie);
        $st->bindParam(4, $this->id_user);
        // Exécution de la requête
        $st->execute();
    }

    /**
     * Méthode delete qui permet de supprimer la préférence de l'utilisateur pour la série
     * @return void
     */
    public function delete() : void
    {
        // Récupération de la connection à la base de données
        $db = ConnectionFactory::makeConnection();
        // Ecriture de la requête SQL
        $sql = "delete from preferences where id_user = ? and id_serie = ?";
        // Préparation de la requête
        $st = $db->prepare($sql);
        // Remplacement des paramètres
        $st->bindParam(1, $this->id_user);
        $st->bindParam(2, $this->id_serie);
        // Exécution de la requête
        $st->execute();
    }

    /**
     * Méthode deleteTable qui permet de supprimer la table des préférences de la base de données
     * @return void
     */
    public static function deleteTable() : void
    {
        // Récupération de la connection à la base de données
        $db = ConnectionFactory::makeConnection();
        // Ecriture de la requête SQL
        $sql = "drop table preferences";
        // Préparation de la requête
        $st = $db->prepare($sql);
        // Exécution de la requête
        $st->execute();
    }

    /**
     * Méthode createTable qui permet de créer la table des préférences dans la base de données
     * @return void
     */
    public static function createTable() : void
    {
        // Récupération de la connection à la base de données
        $db = ConnectionFactory::makeConnection();
        // Ecriture de la requête SQL
        $sql = "create table preferences (
            id_serie int not null,
            id_user int not null,
            primary key (id_serie, id_user),
            foreign key (id_serie) references series(id),
            foreign key (id_user) references users(id)
        )";
        // Préparation de la requête
        $st = $db->prepare($sql);
        // Exécution de la requête
        $st->execute();
    }

    /**
     * Getter magique de la classe Preferences
     * @throws Exception
     */
    public function __get(string $name): mixed
    {
        if (!property_exists($this, $name)) throw new InvalidPropertyNameException("$name: invalid property");
        return $this->$name;
    }

    /**
     * Setter magique de la classe Preferences
     * @throws InvalidPropertyNameException si la propriété n'existe pas
     */
    public function __set(string $name, mixed $value): void
    {
        if (!property_exists($this, $name)) throw new InvalidPropertyNameException("$name: invalid property");
        $this->$name = $value;
    }
}