<?php

namespace iutnc\netvod\application;

use iutnc\netvod\db\ConnectionFactory;
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

    public function modifierEmail(string $email) : string
    {
        $db = ConnectionFactory::makeConnection();
        $stmt = $db->prepare('SELECT email FROM user');
        $stmt->execute();
        $email_existant = false;
        while($row = $stmt->fetch(\PDO::FETCH_ASSOC) and !$email_existant)
        {
            if ($email === $row['email'])
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
            $stmt = $db->prepare("UPDATE user SET email = ? WHERE id = ?");
            $stmt->bindParam(1, $email);
            $stmt->bindParam(2, $this->id);
            $stmt->execute();
            $html = 'Changement d\'adresse email réussi';
        }
        else
        {
            $html = 'Cet adresse email existe déjà';
        }
        return $html;
    }

}