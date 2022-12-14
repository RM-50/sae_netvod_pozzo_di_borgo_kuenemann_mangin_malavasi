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

    /**
     * @param int $id
     * @return mixed
     */
    public static function getAllAttributesEpisode(int $id):mixed
    {
        $db = ConnectionFactory::makeConnection();
        $sqlEpisode = "SELECT * FROM episode where id = ?";
        $stmt_episode = $db->prepare($sqlEpisode);
        $stmt_episode->bindParam(1, $id);
        $stmt_episode->execute();
        return $stmt_episode->fetch(\PDO::FETCH_ASSOC);
    }

    public static function getEpisodeByIdSeries(int $id):array
    {
        $db = ConnectionFactory::makeConnection();
        $sqlEpisode = "SELECT * FROM episode where serie_id = ?";
        $stmt_episode = $db->prepare($sqlEpisode);
        $stmt_episode->bindParam(1, $id);
        $stmt_episode->execute();
        $episodes = [];
        while ($row = $stmt_episode->fetch(\PDO::FETCH_ASSOC)) {
            $episodes[] = new Episode($row['id'], $row['titre'], $row['file']);
        }
        return $episodes;
    }

    /**
     * @param int $id
     * @return string
     */
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
     * @param string $name
     * @param mixed $value
     * @return void
     * @throws InvalidPropertyValueException
     * @throws NonEditablePropertyException
     */
    public function __set(string $name, mixed $value): void {
        if($name == "titre" or $name == "filename") { throw new NonEditablePropertyException("Propriété non-éditable"); }
        if($name == "duree" and $value < 0) { throw new InvalidPropertyValueException("Valeur non-valide"); }
        $this->$name = $value;
    }

    /**
     * @param string $name
     * @return mixed
     * @throws InvalidPropertyNameException|Exception
     */
    public function __get(string $name): mixed
    {
        if (!property_exists($this, $name)) throw new Exception("$name: invalid property");
        return $this->$name;
    }

    /**
     * @return string
     */
    public function __toString() : string {
        return json_encode($this, JSON_PRETTY_PRINT);
    }

}