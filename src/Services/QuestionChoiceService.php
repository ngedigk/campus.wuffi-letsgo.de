<?php

namespace App\Services;

use App\Contracts\Repositories\QuestionChoiceRepositoryInterface;

use App\Dto\QuestionChoice;
use App\Dto\QuestionChoiceInput;

class QuestionChoiceService {
    public function __construct(
        private QuestionChoiceRepositoryInterface $questionChoiceRepository
    ) {}

    public function getByQuestionId(int $questionId): array
    {
        return $this->questionChoiceRepository->getByQuestionId($questionId);
    }

    public function create(int $questionId, QuestionChoiceInput $questionChoice): QuestionChoice
    {
        return $this->questionChoiceRepository->create($questionId, $questionChoice);
    }

    public function update(QuestionChoice $questionChoice): void
    {
        $this->questionChoiceRepository->update($questionChoice);
    }

    public function delete(int $id): void
    {
        $this->questionChoiceRepository->delete($id);
    }

    public function deleteByQuestionId(int $questionId): void
    {
        $this->questionChoiceRepository->deleteByQuestionId($questionId);
    }
}