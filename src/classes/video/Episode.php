<?php



namespace iutnc\netvod\video;


use Exception;
use iutnc\netvod\db\ConnectionFactory;
use iutnc\netvod\exceptions\InvalidPropertyNameException;
use iutnc\netvod\exceptions\InvalidPropertyValueException;
use iutnc\netvod\exceptions\NonEditablePropertyException;



class Episode
 {
    protected string $titre;
    protected string $resume;
    protected string $filename;
    protected int $duree;
    protected int $id;


    /**
     * @param int $id
     * @param string $titre
     * @param string $filename
     */
    public function __construct(int $id, string $titre, string $filename)
    {
        $this->titre = $titre;
        $this->filename = $filename;
        $this->resume = "";
        $this->duree = 0;
        $this->id = $id;
    }

    public static function getEpisode(int $id):mixed
    {
        $db = ConnectionFactory::makeConnection();
        $sqlEpisode = "SELECT * FROM episode where id = ?";
        $stmt_episode = $db->prepare($sqlEpisode);
        $stmt_episode->bindParam(1, $id);
        $stmt_episode->execute();
        return $stmt_episode->fetch(\PDO::FETCH_ASSOC);
    }

    public static function getSerieEpisode(int $id):string
    {
        $db = ConnectionFactory::makeConnection();
        $sqlEpisode = "SELECT titre FROM serie where id = ?";
        $stmt_episode = $db->prepare($sqlEpisode);
        $stmt_episode->bindParam(1, $id);
        $stmt_episode->execute();
        return $stmt_episode->fetch(\PDO::FETCH_ASSOC)['titre'];
    }


    /**
     * @throws NonEditablePropertyException
     * @throws InvalidPropertyValueException
     */

    public function __set(string $name, mixed $value): void {
        if($name == "titre" or $name == "filename") { throw new NonEditablePropertyException("Propriété non-éditable"); }
        if($name == "duree" and $value < 0) { throw new InvalidPropertyValueException("Valeur non-valide"); }
        $this->$name = $value;
    }




    /**
     * @throws Exception
     */

    public function __get(string $name): mixed
    {
        if (!property_exists($this, $name)) throw new Exception("$name: invalid property");
        return $this->$name;
    }

    public function __toString() : string {
        return json_encode($this, JSON_PRETTY_PRINT);
    }

}