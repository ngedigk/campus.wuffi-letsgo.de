<?php

namespace App\Services;

use App\Repositories\QuizQuestionRepository;
use App\Dto\QuizQuestion;
use App\Dto\QuizQuestionInput;

class QuizQuestionService {
    public function __construct(
        private QuizQuestionRepository $quizQuestionRepository
    ) {}

    public function create(
        QuizQuestionInput $quizQuestion
    ): int {
        try {
            $slideId = $this->quizQuestionRepository->create($quizQuestion);
        } catch (\Exception $e) {
            throw new \Exception("Failed to create slide: " . $e->getMessage());
        }
        return $slideId;        
    }

    public function update(
        QuizQuestion $quizQuestion
    ): void {
        $this->quizQuestionRepository->update($quizQuestion);
    }

    public function delete(
        int $id
    ): void {
        $this->quizQuestionRepository->delete($id);
    }
}