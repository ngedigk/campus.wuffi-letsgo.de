<?php

namespace App\Services;

use App\Contracts\Repositories\CourseRepositoryInterface;

use App\Services\ModuleService;
use App\Services\UuidService;

use App\Dto\Course;
use App\Dto\CourseInput;

use App\Exceptions\CourseNotFoundException;

class CourseService
{
    public function __construct(
        private UuidService $uuidService,
        private CourseRepositoryInterface $courseRepository,
        private ModuleService $moduleService
    ) {}

    public function create(CourseInput $courseInput): Course
    {
        $courseUuid = $this->uuidService->generate();

        return $this->courseRepository->create($courseUuid, $courseInput);
    }

    public function update(Course $course): Course
    {
        return $this->courseRepository->update($course);
    }

    public function delete(string $uuid): void
    {
        if (!$this->courseRepository->exists($uuid)) {
            throw new CourseNotFoundException('Angeforderter Kurs nicht gefunden.');
        }

        $this->courseRepository->delete($uuid);
    }

    public function get(string $courseUuid): Course
    {
        $course = $this->courseRepository->get($courseUuid);

        if (!$course) {
            throw new CourseNotFoundException('Angeforderter Kurs nicht gefunden.');
        }

        return $course;
    }

    public function getWithDetails(string $courseUuid): Course
    {
        $course = $this->courseRepository->get($courseUuid);

        if (!$course) {
            throw new CourseNotFoundException('Angeforderter Kurs nicht gefunden.');
        }

        return new $course(
            uuid: $course->uuid,
            title: $course->title,
            description: $course->description,
            prerequisiteCourseId: $course->prerequisiteCourseId,
            sortOrder: $course->sortOrder,
            modules: $this->moduleService->getByCourseIdWithSlides($courseUuid),
        );
    }

    public function getWithDetailsForUser(string $userUuid, string $courseUuid): Course
    {
        $course = $this->courseRepository->getCourseForUser($userUuid, $courseUuid);
        
        if (!$course) {
            throw new CourseNotFoundException('Angeforderter Kurs nicht gefunden.');
        }

        return new $course(
            uuid: $course->uuid,
            title: $course->title,
            description: $course->description,
            prerequisiteCourseId: $course->prerequisiteCourseId,
            sortOrder: $course->sortOrder,
            modules: $this->moduleService->getByCourseIdWithSlides($courseUuid),
        );
    }

    public function getAll(): array
    {
        return $this->courseRepository->getAll();
    }

    public function getAllForUser(string $userUuid): array
    {
        return $this->courseRepository->getAllForUser($userUuid);
    }

    public function buildCourseUrl(string $courseUuid, int $moduleIndex, int $slideIndex): string
    {
        return sprintf(
            '/course?id=%s&module=%s&slide=%d',
            urlencode($courseUuid),
            $moduleIndex,
            $slideIndex
        );
    }
}