<?php

namespace iutnc\netvod\auth;

use iutnc\netvod\application\User;
use iutnc\netvod\db\ConnectionFactory;
use iutnc\netvod\exceptions\AuthException;

class Auth
{
    public static function authenticate(string $email, string $passwd) : bool
    {
        $db = ConnectionFactory::makeConnection();
        $stmt = $db->prepare("SELECT * FROM user WHERE email = ?");
        $stmt->bindParam(1, $email);
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$row)
            throw new AuthException("Auth failed : Invalid credentials");
        else {
            if (password_verify($passwd, $row['password'])) {
                $usr = new User($row['email'], $row['password'], $row['role']);
                $_SESSION['user_connected'] = serialize($usr);
                return true;
            } else {
                throw new AuthException("Auth failed : Invalid credentials");
            }
        }
    }


    public static function register(string $email, string $passwd) : string
    {
        $db = ConnectionFactory::makeConnection();
        $stmt_email_existant = $db->prepare("SELECT email FROM user");
        $stmt_email_existant->execute();

        $trouve_email = false;
        while ($row_email_existant = $stmt_email_existant->fetch(\PDO::FETCH_ASSOC) and !$trouve_email)
        {
            if ($row_email_existant['email'] === $email)
            {
                $trouve_email = true;
            }
        }

        if ($trouve_email)
        {
            $html = "Inscription échouée : Email déjà existant";
        }
        elseif (!self::verifyPasswordStrength($passwd))
        {
            $html = "Inscription échouée : Mot de passe trop court";
        }
        else
        {
            $hash = password_hash($passwd, PASSWORD_DEFAULT, ['cost' => 12]);
            $db = ConnectionFactory::makeConnection();
            $stmt = $db->prepare("INSERT INTO user (email, password, role) VALUES (:email, :passwd, 1)"); // L'identifiant de l'utilisateur est auto incrémenté
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':passwd', $hash);
            $stmt->execute();
            $html = "Inscription réussie !";
        }
        return $html;
    }

    private static function verifyPasswordStrength(string $passwd) : bool
    {
        if (strlen($passwd) < 10)
        {
            return false;
        }
        else
            return true;
    }
}