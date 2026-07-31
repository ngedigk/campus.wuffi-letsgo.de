<?php

class UserRepository
{
    public function __construct(
        private PDO $pdo
    ) {}

    public function findById(string $id): ?User
    {
        $stmt = $this->pdo->prepare("
            SELECT id, email, is_admin, email_verified, created_at
            FROM users
            WHERE id = ?
        ");

        $stmt->execute([$id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->createDto($row) : null;
    }


    public function getAll(): array
    {
        $stmt = $this->pdo->query("
            SELECT id, email, is_admin, email_verified, created_at
            FROM users
        ");

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($row) => $this->createDto($row), $rows);
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


    public function findByEmail(string $email): ?User
    {
        $stmt = $this->pdo->prepare("
            SELECT id, email, is_admin, email_verified, created_at, password_hash
            FROM users
            WHERE email = ?
        ");

        $stmt->execute([$email]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->createDto($row) : null;
    }

    public function setPassword(string $id, string $passwordHash): void
    {
        $stmt = $this->pdo->prepare("
            UPDATE users
            SET password_hash = ?
            WHERE id = ?
        ");

        $stmt->execute([$passwordHash, $id]);
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

    public function hasAnyAdmin(): bool
    {
        $stmt = $this->pdo->prepare("
            SELECT 1
            FROM users
            WHERE is_admin = ?
            LIMIT 1
        ");

        $stmt->execute([(int)true]);

        return $stmt->fetchColumn() !== false;
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
        bool $isAdmin = false
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

    private function createDto(array $row): User
    {
        return new User(
            $row['id'],
            $row['email'],
            (bool)$row['is_admin'],
            (bool)$row['email_verified'],
            $row['created_at'] ?? null,
            $row['password_hash'] ?? null,
            $row['name'] ?? null,
        );
    }
}

