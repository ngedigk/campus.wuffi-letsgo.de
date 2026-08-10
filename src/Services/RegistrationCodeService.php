<?php

namespace App\Services;

use App\Contracts\Repositories\CourseRepositoryInterface;
use App\Contracts\Repositories\RegistrationCodeRepositoryInterface;

use App\Exceptions\DuplicateRegistrationCodeException;
use App\Exceptions\RegistrationCodeException;

class RegistrationCodeService
{
    public function __construct(
        private RegistrationCodeRepositoryInterface $registrationCodeRepository,
        private CourseRepositoryInterface $courseRepository
    ) {}

    public function getAll(): array
    {
        $codes = $this->registrationCodeRepository->getAll();
        $courses = $this->courseRepository->getAll();

        $courseMap = [];
        foreach ($courses as $course) {
            $courseMap[$course->uuid] = $course->title;
        }

        $result = [];
        foreach ($codes as $code) {
            $courseIds = !empty($code['course_ids']) ? explode(',', $code['course_ids']) : [];
            $result[] = [
                'id' => (int)$code['id'],
                'code' => $code['code'],
                'used_by_user_id' => $code['used_by_user_id'],
                'used_at' => $code['used_at'],
                'courses' => array_map(
                    fn($courseId) => [
                        'id' => $courseId,
                        'title' => $courseMap[$courseId] ?? 'Unbekannter Kurs',
                    ],
                    $courseIds
                ),
            ];
        }

        return $result;
    }

    public function create(string $code, array $courseIds): void
    {
        try {
            $this->registrationCodeRepository->create($code, $courseIds);
        } catch (DuplicateRegistrationCodeException $e) {
            throw new RegistrationCodeException(
                "Dieser Registrierungscode wurde bereits angelegt.",
                previous: $e
            );
        }
    }

    public function update(int $registrationCodeId, string $code): void
    {
        $this->registrationCodeRepository->update($registrationCodeId, $code);
    }

    public function addCourses(int $registrationCodeId, array $courseIds): void
    {
        $this->registrationCodeRepository->addCourses($registrationCodeId, $courseIds);
    }

    public function removeCourses(int $registrationCodeId, array $courseIds): void
    {
        $this->registrationCodeRepository->removeCourses($registrationCodeId, $courseIds);
    }

    public function removeAllCourses(int $registrationCodeId): void
    {
        $this->registrationCodeRepository->removeAllCourses($registrationCodeId);
    }

    public function delete(int $id): void
    {
        $this->registrationCodeRepository->delete($id);
    }
}