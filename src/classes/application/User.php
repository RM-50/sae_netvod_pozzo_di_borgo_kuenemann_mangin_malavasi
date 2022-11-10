<?php
declare(strict_types=1);
namespace iutnc\netvod\application;

use iutnc\netvod\auth\Auth;
use iutnc\netvod\db\ConnectionFactory;
use iutnc\netvod\exceptions\InvalidPropertyNameException;
use iutnc\netvod\preference\Preferences;

class User
{
    private string $email, $passwd;
    private int $role, $id, $active = 0;
    private Preferences $pref;

    public function __construct(int $id, string $email, string $passwd, int $role)
    {
        $this->id = $id;
        $this->email = $email;
        $this->passwd = $passwd;
        $this->role = $role;
        $this->pref = new Preferences($id);
    }

    /**
     * @throws InvalidPropertyNameException
     */
    public function __get(string $attribut) : mixed
    {
        if (property_exists($this, $attribut))
            return $this->$attribut;
        else
            throw new InvalidPropertyNameException("La classe user ne possede pas d'attribut : $attribut");
    }

    public function __set(string $attribut, mixed $valeur) : void
    {
        if (property_exists($this, $attribut))
        {
            $this->$attribut = $valeur;
        }
    }

    public function modifierEmail(string $email) : string
    {
        if (!self::verifierEmail($email))
        {
            $db = ConnectionFactory::makeConnection();
            $stmt = $db->prepare("UPDATE user SET email = ? WHERE id = ?");
            $stmt->bindParam(1, $email);
            $id = $this->id;
            $stmt->bindParam(2, $id);
            $stmt->execute();
            $this->email = $email;
            $_SESSION['user_connected'] = serialize($this);
            $html = 'Changement d\'adresse email réussi';
        }
        else
        {
            $html = 'Cet adresse email existe déjà';
        }
        return $html;
    }

    public function modifierMotDePasse(string $passwd) : string
    {
        if (!Auth::verifyPasswordStrength($passwd))
        {
            $html = "Mot de passe trop court";
        }
        else
        {
            $hash = password_hash($passwd, PASSWORD_DEFAULT, ['cost' => 12]);
            $db = ConnectionFactory::makeConnection();
            $stmt = $db->prepare("UPDATE user SET password = ? WHERE id = ?");
            $stmt->bindParam(1, $hash);
            $id = $this->id;
            $stmt->bindParam(2, $id);
            $stmt->execute();
            $this->passwd = $hash;
            $_SESSION['user_connected'] = serialize($this);
            $html = 'Changement de mot de passe réussi';
        }
        return $html;
    }

    public static function verifierEmail(string $email) : bool
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
        return $email_existant;
    }

    public static function getID(string $email) : string
    {
        $db = ConnectionFactory::makeConnection();
        $stmt = $db->prepare("SELECT id from user WHERE email = '$email'");
        return '';
    }
}