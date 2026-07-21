<?php

require_once __DIR__ . '/../uuid.php';

class CourseService
{
    public function __construct(
        private CourseRepository $courseRepository,
        private ModuleRepository $moduleRepository,
        private SlideRepository $slideRepository
    ) {}

    public function create(
        CreateCourse $course
    ): string {
        try {
            $courseId = $this->courseRepository->create($course);
        } catch (\Exception $e) {
            throw new \Exception("Failed to create course: " . $e->getMessage());
        }
        return $courseId;        
    }

    public function update(
        CreateCourse $course
    ): void {
        $this->courseRepository->update($course);
    }

    public function delete(
        string $uuid
    ): void {
        $this->courseRepository->delete($uuid);
    }

    public function get(string $courseUuid): Course {
        return $this->courseRepository->get($courseUuid);
    }

    public function getWithDetails(string $courseUuid): Course {
        $course = $this->courseRepository->get($courseUuid);
        $course->isUnlocked = true;
        $course->isCompleted = false;

        $modules = $this->moduleRepository->getByCourseId($courseUuid);
        foreach ($modules as $module) {
            $module->slides = $this->slideRepository->getByModule($module->id);
        }

        return new Course(
            uuid: $course->uuid,
            title: $course->title,
            description: $course->description,
            prerequisiteCourseId: $course->prerequisiteCourseId,
            isUnlocked: $course->isUnlocked,
            isCompleted: $course->isCompleted,
            modules: $modules,
        );
    }

    public function getWithDetailsForUser(string $userUuid, string $courseUuid): Course {
        $course = $this->courseRepository->getCourseForUser($userUuid, $courseUuid);
        
        if (!$course) {
            throw new RuntimeException('Access denied.');
        }

        $course->isUnlocked = true;
        $course->isCompleted = false;
        
        $modules = $this->moduleRepository->getByCourseId($courseUuid);
        foreach ($modules as $module) {
            $module->slides = $this->slideRepository->getByModule($module->id);
        }

        return new Course(
            uuid: $course->uuid,
            title: $course->title,
            description: $course->description,
            prerequisiteCourseId: $course->prerequisiteCourseId,
            isUnlocked: $course->isUnlocked,
            isCompleted: $course->isCompleted,
            modules: $modules,
        );
    }

    public function getAll(): array {
        return $this->courseRepository->getAll();
    }

    public function getAllForUser(string $userUuid): array {
        return $this->courseRepository->getAllForUser($userUuid);
    }

    public function buildCourseUrl(
        string $courseUuid,
        int $moduleIndex,
        int $slideIndex
    ): string {
        return sprintf(
            'course.php?id=%s&module=%s&slide=%d',
            urlencode($courseUuid),
            $moduleIndex,
            $slideIndex
        );
    }
}

