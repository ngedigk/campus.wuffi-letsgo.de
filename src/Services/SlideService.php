<?php

namespace App\Services;

use App\Repositories\SlideRepository;
use App\Repositories\QuizQuestionRepository;

use App\Dto\Slide;
use App\Dto\SlideInput;

class SlideService {
    public function __construct(
        private SlideRepository $slideRepository,
        private QuizQuestionRepository $quizQuestionRepository
    ) {}

    public function create(
        SlideInput $slide
    ): int {
        try {
            $slideId = $this->slideRepository->create($slide);
        } catch (\Exception $e) {
            throw new \Exception("Folienerstellung fehlgeschlagen: " . $e->getMessage());
        }
        return $slideId;        
    }

    public function update(
        Slide $slide
    ): void {
        $this->slideRepository->update($slide);
    }

    public function delete(
        int $id
    ): void {
        $this->slideRepository->delete($id);
    }

    public function deleteAudioAssetFromSlides(string $audioUrl): void {
        $slides = $this->slideRepository->getSlidesByAudioUrl($audioUrl);
        foreach ($slides as $slide) {
            $slide->audioUrl = '';
            $this->slideRepository->update($slide);
        }
    }

    public function hasQuiz(
        int $slideId
    ): bool {
        $questions = $this->quizQuestionRepository->getBySlideId($slideId);
        return !empty($questions);
    }
}