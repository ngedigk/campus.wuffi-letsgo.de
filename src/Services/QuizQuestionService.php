<?php

namespace App\Services;

use App\Repositories\QuizQuestionRepository;

use App\Services\QuestionChoiceService;

use App\Dto\QuizQuestion;
use App\Dto\QuizQuestionInput;

class QuizQuestionService {
    public function __construct(
        private QuizQuestionRepository $quizQuestionRepository,
        private QuestionChoiceService $questionChoiceService
    ) {}

    public function getBySlideId(int $slideId): array
    {
        return $this->quizQuestionRepository->getBySlideId($slideId);
    }

    public function getWithChoices(int $id): ?QuizQuestion
    {
        $quizQuestion = $this->quizQuestionRepository->getById($id);
        if (!$quizQuestion) {
            return null;
        }

        $choices = $this->questionChoiceService->getByQuestionId($id);
        $quizQuestion->choices = $choices;

        return $quizQuestion;

    }

    public function create(QuizQuestionInput $quizQuestion): int
    {
        try {
            $slideId = $this->quizQuestionRepository->create($quizQuestion);
        } catch (\Exception $e) {
            throw new \Exception("Fragen Erstellung fehlgeschlagen: " . $e->getMessage());
        }
        return $slideId;        
    }

    public function update(QuizQuestion $quizQuestion): void
    {
        $this->quizQuestionRepository->update($quizQuestion);
    }

    public function delete(int $id): void
    {
        $this->quizQuestionRepository->delete($id);
    }
}