<?php

abstract class SlideData {
    public function __construct(
        public string $title,
        public string $htmlContent,
        public ?string $audioUrl,
        public int $sortOrder,
        public bool $isQuiz,
    ) {}
}

final class CreateSlide extends SlideData {
    public function __construct(
        public int $moduleId,
        string $title,
        string $htmlContent,
        ?string $audioUrl,
        int $sortOrder,
        bool $isQuiz,
    ) {
        parent::__construct(
            $title,
            $htmlContent,
            $audioUrl,
            $sortOrder,
            $isQuiz
        );
    }
}

final class Slide extends SlideData
{
    public function __construct(
        public int $id,
        string $title,
        string $htmlContent,
        ?string $audioUrl,
        int $sortOrder,
        bool $isQuiz,
    ) {
        parent::__construct(
            $title,
            $htmlContent,
            $audioUrl,
            $sortOrder,
            $isQuiz
        );
    }


    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'htmlContent' => $this->htmlContent,
            'audioUrl' => $this->audioUrl,
            'sortOrder' => $this->sortOrder,
            'isQuiz' => $this->isQuiz
        ];
    }
}