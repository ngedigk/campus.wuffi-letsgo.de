<?php

namespace App\Services;

use App\Contracts\TransactionManager;
use App\Contracts\AccessCodeRepositoryInterface;

use App\Repositories\UserCourseRepository;

use App\Exceptions\RedeemException;
use App\Exceptions\CourseAlreadyAddedException;

class RedeemService
{
    public function __construct(
        private TransactionManager $transactionManager,
        private AccessCodeRepositoryInterface $accessCodeRepository,
        private UserCourseRepository $userCourseRepository
    ) {}

    public function redeem(string $userUuid, string $code): void
    {
        $this->transactionManager->run(
            function () use ($userUuid, $code) {
                $access = $this->accessCodeRepository
                    ->findByCodeForUpdate($code);

                if (!$access) {
                    throw new RedeemException("Ungültiger Code.");
                }

                if ($this->userCourseRepository->userHasCourse(
                    $userUuid,
                    $access['course_id']
                )) {
                    throw new RedeemException(
                        "Sie haben bereits Zugriff auf diesen Kurs."
                    );
                }

                try {
                    $this->userCourseRepository->addCourse(
                        $userUuid,
                        $access['course_id'],
                        $access['id']
                    );
                } catch (CourseAlreadyAddedException $e) {
                    throw new RedeemException(
                        "Dieser Zugangscode wurde bereits eingelöst.",
                        previous: $e
                    );
                }
            }
        );
    }
}