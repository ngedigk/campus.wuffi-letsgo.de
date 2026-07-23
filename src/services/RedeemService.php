<?php

class RedeemService
{
    public function __construct(
        private PDO $pdo,
        private AccessCodeRepository $accessCodeRepository,
        private UserCourseRepository $userCourseRepository
    ) {}

    public function redeem(
        string $userUuid,
        string $code
    ): void {
        $this->pdo->beginTransaction();

        try {

            $access = $this->accessCodeRepository
                ->findByCodeForUpdate($code);

            if (!$access) {
                throw new Exception("Invalid code.");
            }

            if ($this->userCourseRepository->userHasCourse(
                $userUuid,
                $access['course_id']
            )) {
                throw new Exception(
                    "You already have access to this course."
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
                        "This access code has already been redeemed."
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