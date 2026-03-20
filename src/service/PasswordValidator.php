<?php

declare(strict_types=1);

namespace App\Service;

final class PasswordValidator
{
    /**
     * Valide la robustesse d'un mot de passe.
     * @return string[] Liste d'erreurs (vide si OK)
     */
    public static function validate(string $password, ?string $email = null, ?string $firstname = null, ?string $lastname = null): array
    {
        $errors = [];
        $len = mb_strlen($password);

        // 1) Longueur minimale
        if ($len < 8) {
            $errors[] = 'Le mot de passe doit contenir au moins 8 caractères.';
        }
        // (Optionnel : limite bcrypt 72 chars) — on prévient seulement
        if ($len > 72) {
            $errors[] = 'Le mot de passe est trop long (max. 72 caractères recommandés).';
        }

        // 2) Classes de caractères
        if (!preg_match('/[a-z]/u', $password)) $errors[] = 'Ajoutez au moins une lettre minuscule.';
        if (!preg_match('/[A-Z]/u', $password)) $errors[] = 'Ajoutez au moins une lettre majuscule.';
        if (!preg_match('/\d/u', $password)) $errors[] = 'Ajoutez au moins un chiffre.';
        if (!preg_match('/[^a-zA-Z0-9]/u', $password)) $errors[] = 'Ajoutez au moins un caractère spécial.';

        // 3) Pas d'espaces / contrôle
        if (preg_match('/\s/u', $password)) {
            $errors[] = 'Le mot de passe ne doit pas contenir d\'espaces.';
        }

        // 4) Éviter les mots de passe trop communs
        $low = mb_strtolower($password);
        $deny = [
            'password',
            'motdepasse',
            '123456',
            '123456789',
            'azerty',
            'qwerty',
            'admin',
            'welcome',
            'letmein'
        ];
        foreach ($deny as $bad) {
            if ($low === $bad) {
                $errors[] = 'Le mot de passe est trop commun.';
                break;
            }
        }

        // 5) Ne doit pas contenir email / prénom / nom (en clair)
        $needles = [];
        if ($email) $needles[] = explode('@', mb_strtolower($email))[0] ?? '';
        if ($firstname) $needles[] = mb_strtolower($firstname);
        if ($lastname) $needles[] = mb_strtolower($lastname);
        foreach ($needles as $needle) {
            $needle = trim($needle ?? '');
            if ($needle !== '' && mb_strlen($needle) >= 3 && str_contains($low, $needle)) {
                $errors[] = "Le mot de passe ne doit pas contenir vos informations personnelles.";
                break;
            }
        }
        return $errors;
    }
}
