<?php

namespace App\Repositories;

use App\Exceptions\AccessCodeGenerationException;
use App\Exceptions\DuplicateAccessCodeException;

use \PDO;
use PDOException;

class AccessCodeRepository
{
    public function __construct(
        private PDO $pdo
    ) {}

    public function findByCodeForUpdate(string $code): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT id, course_id
            FROM access_codes
            WHERE code = :code
            FOR UPDATE
        ");

        $stmt->execute(['code' => $code]);

        return $stmt->fetch() ?: null;
    }

    public function existsByCode(string $code): bool
    {
        $stmt = $this->pdo->prepare("
            SELECT 1
            FROM access_codes
            WHERE code = :code
        ");

        $stmt->execute(['code' => $code]);

        return (bool)$stmt->fetch();
    }

    public function create(string $code, string $courseUuid): void
    {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO access_codes (code, course_id)
                VALUES (:code, :courseUuid)
            ");

            $stmt->execute([
                'code' => $code,
                'courseUuid' => $courseUuid
            ]);
        } catch (PDOException $e) {
            if ($e->errorInfo[1] === 1062) {
                throw new DuplicateAccessCodeException(
                    "Dieser Registrierungscode wurde bereits angelegt.",
                    previous: $e
                );
            }
            throw $e;
        }
    }

    public function createForRegistration(string $courseUuid): int
    {
        for ($attempt = 0; $attempt < 3; $attempt++) {
            $accessCode = bin2hex(random_bytes(16));

            try {
                $this->create($accessCode, $courseUuid);

                return (int)$this->pdo->lastInsertId();
            } catch (DuplicateAccessCodeException $e) {
            }
        }
        throw new AccessCodeGenerationException(
            'Es konnte kein eindeutiger Access Code erzeugt werden.'
        );
    }

    public function update(int $id, string $code, string $courseUuid): void
    {
        $stmt = $this->pdo->prepare("
           UPDATE access_codes
            SET code = :code, course_id = :courseUuid
            WHERE id = :id
        ");
        $stmt->execute([
            'code' => $code,
            'courseUuid' => $courseUuid,
            'id' => $id
        ]);
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->prepare("
            SELECT
                ac.id,
                ac.code,
                ac.course_id,
                c.title as course_title, 
                (uc.access_code_id IS NOT NULL) AS claimed,
                uc.user_id AS claimed_by_user_id
            FROM access_codes as ac
            LEFT JOIN courses as c
                ON ac.course_id = c.id
            LEFT JOIN user_courses as uc
                ON ac.id = uc.access_code_id
        ");

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete(int $accessCodeId): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM access_codes WHERE id = :accessCodeId");
        $stmt->execute(['accessCodeId' => $accessCodeId]);
    }
}