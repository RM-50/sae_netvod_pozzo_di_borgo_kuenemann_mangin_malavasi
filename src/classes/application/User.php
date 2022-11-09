<?php

namespace iutnc\netvod\application;

use iutnc\netvod\exceptions\InvalidPropertyNameException;
use iutnc\netvod\preference\Preferences;

class User
{
    private string $email, $passwd;
    private int $role, $id;
    private Preferences $pref;

    public function __construct(int $id, string $email, string $passwd, int $role)
    {
        $this->pref = new Preferences();
        $this->id = $id;
        $this->email = $email;
        $this->passwd = $passwd;
        $this->role = $role;
    }

    /**
     * @throws InvalidPropertyNameException
     */
    public function __get(string $attribut) : mixed
    {
        if (property_exists($this, $attribut))
            return $this->$attribut;
        else throw new InvalidPropertyNameException("La classe user ne possede pas d'attribut : $attribut");
    }

}