<?php

abstract class ModuleData {
    public function __construct(
        public string $title,
        public int $sortOrder,
    ) {}
}

final class CreateModule extends ModuleData {
    public function __construct(
        public string $courseId,
        string $title,
        int $sortOrder,
    ) {
        parent::__construct(
            $title,
            $sortOrder
        );
    }
}

final class Module extends ModuleData {
    /**
     * @param Slide[] $slides
     */
    public function __construct(
        public int $id,
        string $title,
        int $sortOrder,
        public ?array $slides,
    ) {
        parent::__construct(
            $title,
            $sortOrder,
            $slides
        );
    }


    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'sortOrder' => $this->sortOrder,
            'slides' => $this->slides
        ];
    }
}