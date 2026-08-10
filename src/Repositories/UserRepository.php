<?php

namespace App\Repositories;

use App\Contracts\Repositories\UserRepositoryInterface;

use App\Dto\User;

use \PDO;

class UserRepository implements UserRepositoryInterface
{
    public function __construct(
        private PDO $pdo
    ) {}

    public function findById(string $id): ?User
    {
        $stmt = $this->pdo->prepare("
            SELECT *
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
            SELECT *
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
            SELECT *
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
            SET password_hash = :passwordHash
            WHERE id = :id
        ");

        $stmt->execute([
            'passwordHash' => $passwordHash,
            'id' => $id
        ]);
    }

    public function setAdmin(string $id, bool $isAdmin): void
    {
        $stmt = $this->pdo->prepare("
            UPDATE users
            SET is_admin = :isAdmin
            WHERE id = :id
        ");

        $stmt->execute([
            'isAdmin' => (int)$isAdmin,
            'id' => $id
        ]);
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
            SET email_verified = :emailVerified
            WHERE id = :id
        ");

        $stmt->execute([
            'emailVerified' => (int)true,
            'id' => $id
        ]);
    }
    
    public function update(string $id, string $email, string $name): void
    {
        $stmt = $this->pdo->prepare("
            UPDATE users
            SET name = :name, email = :email
            WHERE id = :id
        ");

        $stmt->execute([
            'name' => $name,
            'email' => $email,
            'id' => $id
        ]);
    }

    public function create(
        string $id,
        string $email,
        string $passwordHash,
        string $name,
        bool $isAdmin = false
    ): void
    {

        $stmt = $this->pdo->prepare("
            INSERT INTO users (id, email, name, password_hash, is_admin )
            VALUES (:id, :email, :name, :passwordHash, :isAdmin)
        ");

        $stmt->execute([
            'id' => $id,
            'email' => $email,
            'name' => $name,
            'passwordHash' => $passwordHash,
            'isAdmin' => (int)$isAdmin
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
            id: $row['id'],
            email: $row['email'],
            isAdmin: (bool)$row['is_admin'],
            emailVerified: (bool)$row['email_verified'],
            createdAt: $row['created_at'] ?? null,
            passwordHash: $row['password_hash'] ?? null,
            name: $row['name'] ?? null,
        );
    }
}