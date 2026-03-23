<?php

declare(strict_types=1);

namespace App\Models;

use App\Entities\User;
use PDO;

final class UserModel extends Model
{
    protected string $table = 'user';

    public function findByEmail(string $email): ?User
    {
        $sql = 'SELECT * FROM user WHERE email = :email LIMIT 1';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':email', mb_strtolower(trim($email), 'UTF-8'));
        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        return User::createAndHydrate($data);
    }

    public function findById(int $id): ?User
    {
        $sql = 'SELECT * FROM user WHERE id = :id LIMIT 1';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        return User::createAndHydrate($data);
    }

    public function emailExists(string $email): bool
    {
        $sql = 'SELECT COUNT(*) FROM user WHERE email = :email';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':email', mb_strtolower(trim($email), 'UTF-8'));
        $stmt->execute();

        return (int) $stmt->fetchColumn() > 0;
    }

    public function create(User $user): int
    {
        $sql = 'INSERT INTO user (
            email,
            password_hash,
            firstname,
            lastname,
            address,
            postal_code,
            city,
            phone,
            role,
            created_at
        ) VALUES (
            :email,
            :password_hash,
            :firstname,
            :lastname,
            :address,
            :postal_code,
            :city,
            :phone,
            :role,
            :created_at
        )';

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindValue(':email', $user->getEmail());
        $stmt->bindValue(':password_hash', $user->getPasswordHash());
        $stmt->bindValue(':firstname', $user->getFirstname());
        $stmt->bindValue(':lastname', $user->getLastname());
        $stmt->bindValue(':address', $user->getAddress());
        $stmt->bindValue(':postal_code', $user->getPostalCode());
        $stmt->bindValue(':city', $user->getCity());
        $stmt->bindValue(':phone', $user->getPhone());
        $stmt->bindValue(':role', $user->getRole());
        $stmt->bindValue(
            'created_at',
            $user->getCreatedAt()?->format('Y-m-d H:i:s')
        );

        $stmt->execute();

        return (int) $this->pdo->lastInsertId();
    }

    public function updateLastLogin(int $userId): bool
    {
        $sql = 'UPDATE user
            SET last_login_at = NOW()
            WHERE id = :id';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $userId, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function updatePasswordHash(int $userId, string $newHash): bool
    {
        $sql = 'UPDATE user
            SET password_hash = :password_hash
            WHERE id = :id';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':password_hash', $newHash);
        $stmt->bindValue(':id', $userId, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function updateRememberToken(int $userId, string $tokenHash, string $expiresAt): bool
    {
        $sql = 'UPDATE user 
            SET remember_token_hash = :token_hash, 
                remember_token_expires_at = :expires_at 
            WHERE id = :id';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':token_hash', $tokenHash);
        $stmt->bindValue('expires_at', $expiresAt);
        $stmt->bindValue(':id', $userId, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function clearRememberToken(int $userId): bool
    {
        $sql = 'UPDATE user 
            SET remember_token_hash = NULL, 
                remember_token_expires_at = NULL 
            WHERE id = :id';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $userId, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function findByRememberToken(string $tokenHash): ?User
    {
        $sql = 'SELECT * FROM user
            WHERE remember_token_hash = :token_hash
            AND remember_token_expires_at IS NOT NULL
            AND remember_token_expires_at >= NOW()
            LIMIT 1';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':token_hash', $tokenHash);
        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        return User::createAndHydrate($data);
    }

    public function updateResetRequest(
        int $userId,
        string $selector,
        string $tokenHash,
        string $expiresAt
    ): bool {
        $sql = 'UPDATE user
            SET reset_selector = :selector,
                reset_token_hash = :token_hash,
                reset_expires_at = :expires_at
            WHERE id = :id';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':selector', $selector);
        $stmt->bindValue(':token_hash', $tokenHash);
        $stmt->bindValue(':expires_at', $expiresAt);
        $stmt->bindValue(':id', $userId, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function findByResetSelector(string $selector): ?User
    {
        $sql = 'SELECT * FROM user
            WHERE reset_selector = :selector
              AND reset_expires_at IS NOT NULL
              AND reset_expires_at >= NOW()
            LIMIT 1';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':selector', $selector);
        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        return User::createAndHydrate($data);
    }

    public function clearResetRequest(int $userId): bool
    {
        $sql = 'UPDATE user
            SET reset_selector = NULL,
                reset_token_hash = NULL,
                reset_expires_at = NULL
            WHERE id = :id';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $userId, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
