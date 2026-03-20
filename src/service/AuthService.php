<?php

declare(strict_types=1);

namespace App\Service;

use App\Entities\User;

final class AuthService
{
    public function login(User $user): void
    {
        session_regenerate_id(true);
        $_SESSION['user'] = $user->toSessionArray();
    }

    public function logout(): void
    {
        $_SESSION = [];
        session_regenerate_id(true);
    }

    public function user(): ?array
    {
        $user = $_SESSION['user'] ?? null;
        return is_array($user) ? $user : null;
    }

    public function check(): bool
    {
        return $this->user() !== null;
    }

    public function id(): ?int
    {
        $user = $this->user();
        return isset($user['id']) ? (int) $user['id'] : null;
    }

    public function role(): ?string
    {
        $user = $this->user();
        return isset($user['role']) ? (string) $user['role'] : null;
    }

    public function isGranted(string $role): bool
    {
        $currentRole = $this->role();

        if ($currentRole === null) {
            return false;
        }

        return $currentRole === $role;
    }

    public function rememberTargetUrl(string $url): void
    {
        $_SESSION['_auth_target'] = $url;
    }

    public function pullTargetUrl(string $default = '/'): string
    {
        $url = $_SESSION['_auth_target'] ?? $default;
        unset($_SESSION['_auth_target']);
        
        return is_string($url) ? $url : $default;
    }
}
