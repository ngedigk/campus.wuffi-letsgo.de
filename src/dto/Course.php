<?php

abstract class CourseData {
    public function __construct(
        public string $uuid,
        public string $title,
        public string $description,
        public ?string $prerequisiteCourseId,
        public int $sortOrder
    ) {}
}

final class CreateCourse extends CourseData {
    public function __construct(
        string $uuid,
        string $title,
        string $description,
        ?string $prerequisiteCourseId,
        int $sortOrder
    ) {
        parent::__construct(
            $uuid,
            $title,
            $description,
            $prerequisiteCourseId,
            $sortOrder
        );
    }
}

final class Course extends CourseData {
    /**
     * @param Module[] $modules
     */
    public function __construct(
        string $uuid,
        string $title,
        string $description,
        ?string $prerequisiteCourseId,
        int $sortOrder,
        public ?bool $isUnlocked,
        public ?bool $isCompleted,
        public ?array $modules,
    ) {
        parent::__construct(
            $uuid,
            $title,
            $description,
            $prerequisiteCourseId,
            $sortOrder
        );
    }

    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'title' => $this->title,
            'description' => $this->description,
            'prerequisiteCourseId' => $this->prerequisiteCourseId,
            'sortOrder' => $this->sortOrder,
            'isUnlocked' => $this->isUnlocked,
            'isCompleted' => $this->isCompleted,
            'modules' => $this->modules
        ];
    }
}