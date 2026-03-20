<?php

declare(strict_types=1);

namespace App\Service;

final class PasswordService
{
    /**
     * hash un mot de passe en utilisant l'algorithme par defaut de php
     *
     */
    public function hash(string $plainPassword): string
    //assecible a tout le monde
    {    //password native 
        return password_hash($plainPassword, PASSWORD_DEFAULT);
    }
    /**
     * verifie qu'un mot de passe correspont a son hash
     *
     */
    public function verify(string $plainPassword, string $passwordHash): bool
    {
        return password_verify($plainPassword, $passwordHash);
    }

    public function needsRehash(string $passwordHash): bool
    {
        return password_needs_rehash($passwordHash, PASSWORD_DEFAULT);
    }
}
