<?php

namespace App\Services;

use App\Repositories\AccessCodeRepository;
use App\Repositories\UserCourseRepository;

use App\Exceptions\RedeemException;

use \PDO;
use \PDOException;
use \Exception;
use \Throwable;

class RedeemService
{
    public function __construct(
        private PDO $pdo,
        private AccessCodeRepository $accessCodeRepository,
        private UserCourseRepository $userCourseRepository
    ) {}

    public function redeem(string $userUuid, string $code): void
    {
        $this->pdo->beginTransaction();

        try {

            $access = $this->accessCodeRepository
                ->findByCodeForUpdate($code);

            if (!$access) {
                throw new Exception("Ungültiger Code.");
            }

            if ($this->userCourseRepository->userHasCourse(
                $userUuid,
                $access['course_id']
            )) {
                throw new Exception(
                    "Sie haben bereits Zugriff auf diesen Kurs."
                );
            }

            try {
                $this->userCourseRepository->addCourse(
                    $userUuid,
                    $access['course_id'],
                    $access['id']
                );
            } catch (PDOException $e) {

                if ($e->errorInfo[1] === 1062) {

                    throw new RedeemException(
                        "Dieser Zugangscode wurde bereits eingelöst."
                    );
                }

                throw $e;
            }

            $this->pdo->commit();

        } catch (Throwable $e) {

            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $e;
        }
    }
}