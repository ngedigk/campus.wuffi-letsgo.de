<?php

class UserRepository
{
    public function __construct(
        private PDO $pdo
    ) {}


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

    public function create(
        string $id,
        string $email,
        string $passwordHash
    ): void {

        $adminExistsStmt = $this->pdo->query(
            "SELECT 1 FROM users WHERE is_admin = 1 LIMIT 1"
        );
        $hasAdmin = (bool)$adminExistsStmt->fetch();
        $isAdmin = $hasAdmin ? 0 : 1;

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
            $isAdmin
        ]);
    }
}