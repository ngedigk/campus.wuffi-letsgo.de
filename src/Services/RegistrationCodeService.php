<?php

namespace App\Services;

use App\Repositories\RegistrationCodeRepository;
use App\Repositories\CourseRepository;

class RegistrationCodeService
{
    public function __construct(
        private RegistrationCodeRepository $registrationCodeRepository,
        private CourseRepository $courseRepository
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
                'course_titles' => array_map(
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
        $this->registrationCodeRepository->create($code, $courseIds);
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