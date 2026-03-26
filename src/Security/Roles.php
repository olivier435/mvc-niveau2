<?php

declare(strict_types=1);

namespace App\Security;

final class Roles
{
    public const USER = 'ROLE_USER';
    public const EDITOR = 'ROLE_EDITOR';
    public const ADMIN = 'ROLE_ADMIN';

    /**
     * Liste des rôles autorisés
     */
    public const ALL = [
        self::USER,
        self::EDITOR,
        self::ADMIN,
    ];

    /**
     * Hiérarchie simple (optionnelle mais très utile)
     */
    public const HIERARCHY = [
        self::ADMIN  => [self::ADMIN, self::EDITOR, self::USER],
        self::EDITOR => [self::EDITOR, self::USER],
        self::USER   => [self::USER],
    ];

    /**
     * Vérifie si un rôle est valide
     */
    public static function isValid(string $role): bool
    {
        return in_array($role, self::ALL, true);
    }

    /**
     * Vérifie si un rôle peut accéder à un autre (hiérarchie)
     */
    public static function can(string $currentRole, string $requiredRole): bool
    {
        return in_array($requiredRole, self::HIERARCHY[$currentRole] ?? [], true);
    }
}
