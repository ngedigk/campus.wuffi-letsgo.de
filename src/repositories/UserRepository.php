<?php

class UserRepository
{
    public function __construct(
        private PDO $pdo
    ) {}


    public function getAll(): array
    {
        $stmt = $this->pdo->query("
            SELECT id, email, is_admin, email_verified, created_at
            FROM users
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function existsByEmail(string $email): bool
    {
        $stmt = $this->pdo->prepare("
            SELECT 1
            FROM users
            WHERE email = ?
        ");

        $stmt->execute([$email]);

        return (bool)$stmt->fetch();
    }


    public function findByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT id, email, is_admin
            FROM users
            WHERE email = ?
        ");

        $stmt->execute([$email]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function setAdmin(string $id, bool $isAdmin): void
    {
        $stmt = $this->pdo->prepare("
            UPDATE users
            SET is_admin = ?
            WHERE id = ?
        ");

        $stmt->execute([(int)$isAdmin, $id]);
    }

    public function verify(string $id): void
    {
        $stmt = $this->pdo->prepare("
            UPDATE users
            SET email_verified = ?
            WHERE id = ?
        ");

        $stmt->execute([true, $id]);
    }

    public function create(
        string $id,
        string $email,
        string $passwordHash,
        bool $isAdmin
    ): void {

        $stmt = $this->pdo->prepare("
            INSERT INTO users
            (
                id,
                email,
                password_hash,
                is_admin
            )
            VALUES (?, ?, ?, ?)
        ");

        $stmt->execute([
            $id,
            $email,
            $passwordHash,
            (int)$isAdmin
        ]);
    }

    public function enrollInCourses(string $userId, array $courseAccessPairs): void
    {
        if (empty($courseAccessPairs)) {
            return;
        }
        $placeholders = [];
        $values = [];
        foreach ($courseAccessPairs as $pair) {
            $placeholders[] = '(?, ?, ?)';
            $values[] = $userId;
            $values[] = $pair['course_id'];
            $values[] = $pair['access_code_id'];
        }
        $sql = "INSERT IGNORE INTO user_courses (user_id, course_id, access_code_id) VALUES " . implode(', ', $placeholders);
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($values);
    }
}

