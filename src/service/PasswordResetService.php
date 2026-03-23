<?php


declare(strict_types=1);

namespace App\Service;

use App\Entities\User;
use App\Models\UserModel;

final class PasswordResetService
{
    public function createResetRequest(User $user, UserModel $userModel): array
    {
        $selector = bin2hex(random_bytes(8));
        $token = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);
        $expiresAt = (new \DateTimeImmutable('+1 hour'))->format('Y-m-d H:i:s');

        $userModel->updateResetRequest(
            $user->getId(),
            $selector,
            $tokenHash,
            $expiresAt
        );

        return [
            'selector' => $selector,
            'token' => $token,
            'expires_at' => $expiresAt,
        ];
    }

    public function isValidResetToken(User $user, string $token): bool
    {
        $expectedHash = $user->getResetTokenHash();

        if ($expectedHash === null || $token === '') {
            return false;
        }

        return hash_equals($expectedHash, hash('sha256', $token));
    }
}
