<?php

namespace iutnc\netvod\application;

class User
{
    private string $email, $passwd;
    private int $role;

    public function __construct(string $email, string $passwd, int $role)
    {
        $this->email = $email;
        $this->passwd = $passwd;
        $this->role = $role;
    }
}