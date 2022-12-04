<?php

namespace iutnc\netvod\activeRecord;

use Exception;
use iutnc\netvod\db\ConnectionFactory;
use iutnc\netvod\exceptions\InvalidPropertyNameException;


class Serie
{
    private int $id;
    private string $titre;
    private string $descriptif;
    private string $img;
    private int $annee;
    private string $dateAjout;


    /**
     * Constructeur d'une Série
     * @param string $titre titre de la série
     * @param string $desc description de la série
     * @param string $img image de la série
     * @param int $annee année de la série
     * @param string $dateAjout date d'ajout de la série
     * @param int $id id de la série
     */
    public function __construct(string $titre, string $desc, string $img, int $annee, string $dateAjout, int $id=-1)
    {
        $this->id= $id;
        $this->titre = $titre;
        $this->descriptif = $desc;
        $this->img = $img;
        $this->annee = $annee;
        $this->dateAjout = $dateAjout;
    }


    /**
     * Getter magique des attributs d'une série
     * @param string $name nom de l'attribut
     * @return mixed valeur de l'attribut
     * @throws InvalidPropertyNameException si l'attribut n'existe pas
     */
    public function __get(string $name): mixed
    {
        if (!property_exists($this, $name)) throw new InvalidPropertyNameException("$name: invalid property for Serie");
        return $this->$name;
    }

    /**
     * Setter magique des attributs d'une série
     * @param string $attribut
     * @param mixed $valeur
     * @return void
     * @throws InvalidPropertyNameException si l'attribut n'existe pas
     */
    public function __set(string $attribut, mixed $valeur) : void
    {
        if (property_exists($this, $attribut))
        {
            $this->$attribut = $valeur;
        }
        else
        {
            throw new InvalidPropertyNameException("La classe Serie ne possede pas d'attribut : $attribut");
        }
    }

    /**
     * Méthode findByTitre permettant de trouver la liste des series dont le titre est le titre passé en paramètre
     * @param string $titre titre des series à trouver
     * @return array liste des series dont le titre est le titre passé en paramètre
     * @throws Exception si aucune série avec le titre passé en paramètre n'est trouvée
     */
    public static function findByTitre(string $titre): array
    {
        // Ecriture de la requête permettant de trouver les series dont le titre est le titre passé en paramètre
        $sql = "select * from serie where titre like ?";
        // Recuperation de la connexion a la base de donnees
        $db = ConnectionFactory::makeConnection();
        // Preparation de la requête
        $stmt_serie = $db->prepare($sql);
        // Remplacement du paramètre de la requête par le titre
        $stmt_serie->bindParam(1, $titre);
        // Execution de la requête
        $stmt_serie->execute();
        // Initialisation de la liste des series a retourner
        $liste_series = [];
        // Tant qu'il y a des series trouvées
        while($row_serie = $stmt_serie->fetch(\PDO::FETCH_ASSOC))
        {
            // On cree une nouvelle série à partir des donnees de la série trouvée
            $serie = new Serie($row_serie['titre'], $row_serie['descriptif'], $row_serie['img'], $row_serie['annee'], $row_serie['dateAjout'], $row_serie['id']);
            // On ajoute la série a la liste des series trouvées
            $liste_series[] = $serie;
        }
        // Si aucun résultat n'a ete trouve, on leve une exception
        if ($liste_series === []) {
            throw new Exception("Aucune série $titre trouvée");
        }
        // On retourne la liste des series trouvées
        return $liste_series;
    }

    /**
     * Méthode findById permettant de trouver la série dont l'id est l'id passé en paramètre
     * @param int $id id de la série à trouver
     * @return Serie la série dont l'id est l'id passé en paramètre
     * @throws Exception si aucune série avec l'id passé en paramètre n'est trouvée
     */
    public static function findById(int $id) : Serie
    {
        // Ecriture de la requête permettant de trouver la série dont l'id est l'id passé en paramètre
        $sql = "select * from serie where id = ?";
        // Récuperation de la connexion a la base de données
        $db = ConnectionFactory::makeConnection();
        // Préparation de la requête
        $stmt_serie = $db->prepare($sql);
        // Remplacement du paramètre de la requête par l'id
        $stmt_serie->bindParam(1, $id);
        // Execution de la requête
        $stmt_serie->execute();
        // Récuperation de la série trouvée
        $row_serie = $stmt_serie->fetch(\PDO::FETCH_ASSOC);
        // Si aucune série n'a été trouvée, on lève une exception
        if ($row_serie === false) {
            throw new Exception("Aucune série $id trouvée");
        }
        // Sinon, on crée une nouvelle série à partir des donnees de la série trouvée
        $serie = new Serie($row_serie['titre'], $row_serie['descriptif'], $row_serie['img'], $row_serie['annee'], $row_serie['dateAjout'], $row_serie['id']);
        // Et on retourne cette série
        return $serie;
    }

    /**
     * Méthode findAll qui permet de renvoyer la liste de toutes les séries présentes dans la table
     * @return array liste de toutes les séries présentes dans la table
     * @throws Exception si aucune série n'est trouvée
     */
    public static function findAll() : array{
        // Ecriture de la requête permettant de séléctionner toutes les séries dans la table
        $sql = "select * from serie";
        // Récuperation de la connexion a la base de données
        $db = ConnectionFactory::makeConnection();
        // Préparation de la requête
        $stmt_serie = $db->prepare($sql);
        // Execution de la requête
        $stmt_serie->execute();
        // Initialisation de la liste des series a retourner
        $liste_series = [];
        // Tant qu'il y a des series trouvées
        while($row_serie = $stmt_serie->fetch(\PDO::FETCH_ASSOC))
        {
            // On crée une nouvelle série à partir des donnees de la série trouvée
            $serie = new Serie($row_serie['titre'], $row_serie['descriptif'], $row_serie['img'], $row_serie['annee'], $row_serie['dateAjout'], $row_serie['id']);
            // On ajoute la série a la liste des series trouvées
            $liste_series[] = $serie;
        }
        // Si aucun résultat n'a ete trouve, on lève une exception
        if ($liste_series === []) {
            throw new Exception("Aucune série trouvée");
        }
        // On retourne la liste des series trouvées
        return $liste_series;
    }

    /**
     * Méthode save permettant d'insérer une série dans la base ou bien de la mettre à jour si elle existe déjà
     * @return void
     */
    public function save() : void {
        // Si l'id de la série est null, c'est qu'elle n'existe pas encore dans la base de données
        if ($this->id === null) {
            // On appelle la méthode insert
            $this->insert();
        } else {
            // Sinon, on appelle la méthode update
            $this->update();
        }
    }

    /**
     * Méthode insert permettant d'insérer une série dans la base
     * @return void
     */
    private function insert() : void
    {
        // Ecriture de la requête permettant d'insérer une série dans la base
        $sql = "insert into serie (titre, descriptif, img, annee, dateAjout) values ('?', '?', '?', ?, '?')";
        // Récupération de la connexion a la base de données
        $db = ConnectionFactory::makeConnection();
        // Préparation de la requête
        $stmt_serie = $db->prepare($sql);
        // Remplacement des paramètres de la requête par les valeurs de l'objet
        $stmt_serie->bindParam(1, $this->titre);
        $stmt_serie->bindParam(2, $this->descriptif);
        $stmt_serie->bindParam(3, $this->img);
        $stmt_serie->bindParam(4, $this->annee);
        $stmt_serie->bindParam(5, $this->dateAjout);
        // Execution de la requête
        $stmt_serie->execute();
        // Mise à jour de l'id de l'objet
        $this->id = (int)$db->lastInsertId();
    }

    private function update() : void
    {
        // Ecriture de la requête permettant de mettre à jour une série dans la base
        $sql = "update serie set titre = '?', descriptif = '?', img = '?', annee = ?, dateAjout = '?' where id = ?";
        // Récupération de la connexion a la base de données
        $db = ConnectionFactory::makeConnection();
        // Préparation de la requête
        $stmt_serie = $db->prepare($sql);
        // Remplacement des paramètres de la requête par les valeurs de l'objet
        $stmt_serie->bindParam(1, $this->titre);
        $stmt_serie->bindParam(2, $this->descriptif);
        $stmt_serie->bindParam(3, $this->img);
        $stmt_serie->bindParam(4, $this->annee);
        $stmt_serie->bindParam(5, $this->dateAjout);
        $stmt_serie->bindParam(6, $this->id);
        // Execution de la requête
        $stmt_serie->execute();
    }

    /**
     * Méthode delete permettant de supprimer une série de la base
     * @return void
     */
    public function delete() : void
    {
        // Ecriture de la requête permettant de supprimer une série dans la base
        $sql = "delete from serie where id = ?";
        // Récupération de la connexion a la base de données
        $db = ConnectionFactory::makeConnection();
        // Préparation de la requête
        $stmt_serie = $db->prepare($sql);
        // Remplacement des paramètres de la requête par les valeurs de l'objet
        $stmt_serie->bindParam(1, $this->id);
        // Execution de la requête
        $stmt_serie->execute();
    }

    /**
     * Méthode permettant de créer la table série dans la base de données
     * @return void
     */
    public function createTable() : void{
        // Ecriture de la requête permettant de créer la table serie
        $sql = "create table serie (
            id int not null auto_increment,
            titre varchar(255) not null,
            descriptif text not null,
            img varchar(255) not null,
            annee int not null,
            dateAjout date not null,
            primary key (id)
        )";
        // Récupération de la connexion a la base de données
        $db = ConnectionFactory::makeConnection();
        // Préparation de la requête
        $stmt_serie = $db->prepare($sql);
        // Execution de la requête
        $stmt_serie->execute();
    }

    public function deleteTable(){
        // Ecriture de la requête permettant de supprimer la table serie
        $sql = "drop table serie";
        // Récupération de la connexion a la base de données
        $db = ConnectionFactory::makeConnection();
        // Préparation de la requête
        $stmt_serie = $db->prepare($sql);
        // Execution de la requête
        $stmt_serie->execute();
    }
}