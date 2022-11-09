<?php

namespace iutnc\netvod\action;

class AddPreferencesAction extends Action
{

    public function execute(): string
    {
        //TODO: A modifier
        $user = $_SESSION["*user*"];
        if (!isset($user->preferences["*nomserie*"])) {
            $html = "<p>Ajouté aux favoris avec succès</p>";
        } else {
            $html = "<p>Favoris</p>";
        }
        return $html;
    }
}