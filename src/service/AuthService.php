<?php

declare(strict_types=1);

namespace App\Service;

use App\Entities\User;
use App\Models\UserModel;

final class AuthService
{
    private const REMEMBER_COOKIE = 'remember_me';
    private const REMEMBER_LIFETIME = 2592000; // 30 jours

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

    public function enableRememberMe(User $user, UserModel $userModel): void
    {
        $token = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);
        $expiresAt = (new \DateTimeImmutable())
            ->modify('+' . self::REMEMBER_LIFETIME . ' seconds')
            ->format('Y-m-d H:i:s');

        $userModel->updateRememberToken($user->getId(), $tokenHash, $expiresAt);

        setcookie(
            self::REMEMBER_COOKIE,
            $token,
            [
                'expires' => time() + self::REMEMBER_LIFETIME,
                'path' => '/',
                'secure' => !empty($_SERVER['HTTPS']),
                'httponly' => true,
                'samesite' => 'Lax',
            ]
        );
    }

    public function clearRememberMe(UserModel $userModel, ?int $userId = null): void 
    {
        if ($userId !== null) {
            $userModel->clearRememberToken($userId);
        }

        $this->clearRememberCookie();
    }

    public function loginFromRememberCookie(UserModel $userModel): void 
    {
        if ($this->check()) {
            return;
        }

        $token = $_COOKIE[self::REMEMBER_COOKIE] ?? null;

        if (!is_string($token) || $token === '') {
            return;
        }

        $tokenHash = hash('sha256', $token);
        $user = $userModel->findByRememberToken($tokenHash);

        if ($user === null) {
            $this->clearRememberCookie();
            return;
        }

        $this->login($user);

        // rotation du token
        $this->enableRememberMe($user, $userModel);
    }

    private function clearRememberCookie(): void
    {
        setcookie(
            self::REMEMBER_COOKIE,
            '',
            [
                'expires' => time() - 3600,
                'path' => '/',
                'secure' => !empty($_SERVER['HTTPS']),
                'httponly' => true,
                'samesite' => 'Lax',
            ]
        );

        unset($_COOKIE[self::REMEMBER_COOKIE]);
    }
}
