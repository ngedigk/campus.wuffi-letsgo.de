<?php

namespace App\Services;

use App\Contracts\Repositories\QuizQuestionRepositoryInterface;

use App\Services\QuestionChoiceService;

use App\Dto\QuizQuestion;
use App\Dto\QuizQuestionInput;

class QuizQuestionService {
    public function __construct(
        private QuizQuestionRepositoryInterface $quizQuestionRepository,
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

    public function create(QuizQuestionInput $quizQuestion): QuizQuestion
    {
        try {
            $quizQuestion = $this->quizQuestionRepository->create($quizQuestion);
        } catch (\Exception $e) {
            throw new \Exception("Fragen Erstellung fehlgeschlagen: " . $e->getMessage());
        }
        return $quizQuestion;        
    }

    public function update(QuizQuestion $quizQuestion): QuizQuestion
    {
        return $this->quizQuestionRepository->update($quizQuestion);
    }

    public function delete(int $id): void
    {
        $this->quizQuestionRepository->delete($id);
    }
}