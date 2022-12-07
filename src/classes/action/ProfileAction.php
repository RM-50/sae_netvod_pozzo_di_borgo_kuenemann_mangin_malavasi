<?php

namespace iutnc\netvod\action;

class ProfileAction extends Action
{

    public function __construct()
    {

    }

    public function execute(): string
    {
        $html = <<< END
            <div class="profile-action-group">
                <div class="profile-action-title">
                    <h1>Mon profil</h1>
                </div>
                <div class="profile-action-item">
                    <label for="modify-email"><a href="?action=modify-email">Modifier mon email</a></label>
                </div>
                <div class="profile-action-item">
                    <label for="modify-password"><a href="?action=modify-passwd">Modifier mon mot de passe</a></label>
                </div>
                <div class="profile-action-item">
                    <label for="delete-account"><a href="?action=delete-account">Supprimer mon compte</a></label>
                </div>
            </div>
            END;
        return $html;
    }
}