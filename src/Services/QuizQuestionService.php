<?php

namespace App\Services;

use App\Contracts\Repositories\QuizQuestionRepositoryInterface;

use App\Services\QuestionChoiceService;

use App\Dto\QuizQuestion;
use App\Dto\QuizQuestionInput;

use App\Exceptions\QuizQuestionNotFoundException;

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
        return $this->quizQuestionRepository->create($quizQuestion);        
    }

    public function update(QuizQuestion $quizQuestion): QuizQuestion
    {
        return $this->quizQuestionRepository->update($quizQuestion);
    }

    public function delete(int $id): void
    {
        if (!$this->quizQuestionRepository->exists($id)) {
            throw new QuizQuestionNotFoundException("Frage {$id} nicht gefunden.");
        }

        $this->quizQuestionRepository->delete($id);
    }
}