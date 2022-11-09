<?php

namespace iutnc\netvod\application;

use iutnc\netvod\exceptions\InvalidPropertyNameException;

class User
{
    private string $email, $passwd;
    private int $role, $id;

    public function __construct(int $id, string $email, string $passwd, int $role)
    {
        $this->id = $id;
        $this->email = $email;
        $this->passwd = $passwd;
        $this->role = $role;
    }

    public function __get(string $attribut) : mixed
    {
        if (property_exists($this, $attribut))
            return $this->$attribut;
        else
            throw new InvalidPropertyNameException("La classe user ne possede pas d'attribut : $attribut");
    }
}