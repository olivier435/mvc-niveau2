<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

final class LoginAttemptModel extends Model
{
    public function countRecentAttempts(string $email, string $ip, int $minutes = 15): int
    {
        $sql = "SELECT COUNT(*) FROM login_attempt
                WHERE email = :email
                AND ip_address = :ip
                AND attempted_at >= (NOW() - INTERVAL :minutes MINUTE)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':ip', $ip);
        $stmt->bindValue(':minutes', $minutes, PDO::PARAM_INT);
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    public function recordFailedAttempt(string $email, string $ip): void
    {
        $sql = "INSERT INTO login_attempt (email, ip_address, attempted_at)
                VALUES (:email, :ip, NOW())";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'email' => $email,
            'ip' => $ip,
        ]);
    }

    public function clearAttempts(string $email): void
    {
        $sql = "DELETE FROM login_attempt WHERE email = :email";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'email' => $email,
        ]);
    }
}
