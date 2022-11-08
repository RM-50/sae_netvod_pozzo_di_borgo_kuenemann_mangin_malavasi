<?php

namespace iutnc\netvod\video;


use iutnc\netvod\exception\InvalidPropertyNameException;
use iutnc\netvod\exception\InvalidPropertyValueException;
use iutnc\netvod\exception\NonEditablePropertyException;

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
     * @throws InvalidPropertyNameException
     */
    public function __get(string $name): mixed {
        return (isset($this->$name)) ? $this->$name : throw new InvalidPropertyNameException("Proprietée invalide");
    }

    public function __toString() : string {
        return json_encode($this, JSON_PRETTY_PRINT);
    }

}