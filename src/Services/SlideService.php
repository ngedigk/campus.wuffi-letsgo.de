<?php

namespace App\Services;

use App\Contracts\Repositories\QuizQuestionRepositoryInterface;
use App\Contracts\Repositories\SlideRepositoryInterface;

use App\Dto\Slide;
use App\Dto\SlideInput;

use App\Exceptions\CourseSlideNotFoundException;

class SlideService {
    public function __construct(
        private SlideRepositoryInterface $slideRepository,
        private QuizQuestionRepositoryInterface $quizQuestionRepository
    ) {}

    public function create(SlideInput $slide): Slide
    {
        return $this->slideRepository->create($slide);
    }

    public function update(Slide $slide): Slide
    {
        return $this->slideRepository->update($slide);
    }

    public function delete(int $id): void
    {
        if (!$this->slideRepository->exists($id)) {
            throw new CourseSlideNotFoundException("Folie {$id} nicht gefunden.");
        }

        $this->slideRepository->delete($id);
    }

    public function deleteAudioAssetFromSlides(string $audioUrl): void
    {
        $slides = $this->slideRepository->getSlidesByAudioUrl($audioUrl);
        foreach ($slides as $slide) {
            $slide->audioUrl = '';
            $this->slideRepository->update($slide);
        }
    }

    public function getByModuleId(int $moduleId): array
    {
        return $this->slideRepository->getByModuleId($moduleId);
    }

    public function hasQuiz(int $slideId): bool
    {
        $questions = $this->quizQuestionRepository->getBySlideId($slideId);
        return !empty($questions);
    }
}