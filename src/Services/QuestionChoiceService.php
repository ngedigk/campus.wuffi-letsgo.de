<?php

namespace App\Services;

use App\Repositories\QuestionChoiceRepository;
use App\Dto\QuestionChoice;
use App\Dto\QuestionChoiceInput;

class QuestionChoiceService {
    public function __construct(
        private QuestionChoiceRepository $questionChoiceRepository
    ) {}

    public function getByQuestionId(
        int $questionId
    ): array {
        return $this->questionChoiceRepository->getByQuestionId($questionId);
    }

    public function create(
        QuestionChoiceInput $questionChoice
    ): int {
        try {
            $slideId = $this->questionChoiceRepository->create($questionChoice);
        } catch (\Exception $e) {
            throw new \Exception("Antwort Erstellung fehlgeschlagen: " . $e->getMessage());
        }
        return $slideId;        
    }

    public function update(
        QuestionChoice $questionChoice
    ): void {
        $this->questionChoiceRepository->update($questionChoice);
    }

    public function delete(
        int $id
    ): void {
        $this->questionChoiceRepository->delete($id);
    }
}