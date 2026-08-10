<?php

namespace App\Services;

use App\Contracts\Repositories\CourseRepositoryInterface;
use App\Contracts\Repositories\ModuleRepositoryInterface;

use App\Repositories\SlideRepository;

use App\Dto\Course;
use App\Dto\CourseInput;
use App\Exceptions\CourseNotFoundException;

class CourseService
{
    public function __construct(
        private UuidService $uuidService,
        private CourseRepositoryInterface $courseRepository,
        private ModuleRepositoryInterface $moduleRepository,
        private SlideRepository $slideRepository
    ) {}

    public function create(CourseInput $courseInput): Course
    {
        try {
            $courseUuid = $this->uuidService->generate();

            return $this->courseRepository->create($courseUuid, $courseInput);
        } catch (\Exception $e) {
            throw new \Exception("Failed to create course: " . $e->getMessage());
        }
    }

    public function update(Course $course): Course
    {
        return $this->courseRepository->update($course);
    }

    public function delete(string $uuid): void
    {
        $this->courseRepository->delete($uuid);
    }

    public function get(string $courseUuid): Course
    {
        return $this->courseRepository->get($courseUuid);
    }

    public function getWithDetails(string $courseUuid): Course
    {
        $course = $this->courseRepository->get($courseUuid);

        return new $course(
            uuid: $course->uuid,
            title: $course->title,
            description: $course->description,
            prerequisiteCourseId: $course->prerequisiteCourseId,
            sortOrder: $course->sortOrder,
            modules: $this->loadModules($courseUuid),
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
            modules: $this->loadModules($courseUuid),
        );
    }

    private function loadModules(string $courseUuid): array
    {
        $modules = $this->moduleRepository->getByCourseId($courseUuid);

        foreach ($modules as $module) {
            $module->slides = $this->slideRepository->getByModuleId($module->id);
        }

        return $modules;
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