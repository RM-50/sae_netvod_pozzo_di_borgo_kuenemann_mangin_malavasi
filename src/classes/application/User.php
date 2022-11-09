<?php

namespace iutnc\netvod\application;

use iutnc\netvod\db\ConnectionFactory;
use iutnc\netvod\exceptions\InvalidePropertyException;

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
            throw new InvalidePropertyException("La classe user ne possede pas d'attribut : $attribut");
    }

    public function modifierEmail(string $email)
    {
        $db = ConnectionFactory::makeConnection();
        $stmt = $db->prepare('SELECT email FROM user');
        $stmt->execute();
        $email_existant = false;
        while($row = $stmt->fetch(\PDO::FETCH_ASSOC) and !$email_existant)
        {
            if ($email = $row['email'])
            {
                $email_existant = true;
            }
            else
            {
                $email_existant = false;
            }
        }

        if (!$email_existant)
        {
            $stmt = $db->prepare("UPDATE user SET email = ? AND id = ?");
            $stmt->bindParam(1, $email);
            $stmt->bindParam(2, $this->id);
            $stmt->execute();

        }
    }
}