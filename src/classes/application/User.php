<?php
declare(strict_types=1);
namespace iutnc\netvod\application;

use iutnc\netvod\auth\Auth;
use iutnc\netvod\db\ConnectionFactory;
use iutnc\netvod\exceptions\InvalidPropertyNameException;
use iutnc\netvod\preference\Preferences;
use iutnc\netvod\video\Serie;
use iutnc\netvod\visionnage\ClassVisio;

class User
{
    private string $email, $passwd;
    private int $role, $id, $active = 0;
    private Preferences $pref;
    private ClassVisio $visio;

    public function __construct(int $id, string $email, string $passwd, int $role)
    {
        $this->id = $id;
        $this->email = $email;
        $this->passwd = $passwd;
        $this->role = $role;
        $this->pref = new Preferences($id);
        $this->visio = new ClassVisio($id);
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

    public function getPrefs(): Preferences
    {
        return new Preferences($this->id);
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

    public static function modifierMotDePasse(string $passwd, string $email) : string
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
            $id = self::getID($email);
            $stmt->bindParam(2, $id);
            $stmt->execute();
            $usr = new User(intval($id), $email, $hash, 1);
            $usr->active = 1;
            $_SESSION['user_connected'] = serialize($usr);
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

    public static function getID(string $email) : int
    {
        $db = ConnectionFactory::makeConnection();
        $stmt = $db->prepare("SELECT id from user WHERE email = '$email'");
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$row)
        {
            return 0;
        }
        else
        {
            return $row['id'];
        }
    }
}