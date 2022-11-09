<?php

namespace iutnc\netvod\video;


use Exception;
use iutnc\netvod\exceptions\InvalidPropertyNameException;
use iutnc\netvod\exceptions\InvalidPropertyValueException;
use iutnc\netvod\exceptions\NonEditablePropertyException;

class Episode
 {
    protected string $titre;
    protected string $resume;
    protected string $filename;
    protected int $duree;

    public function __construct(string $titre, string $filename)
    {
        $this->titre = $titre;
        $this->filename = $filename;
        $this->resume = "";
        $this->duree = 0;
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