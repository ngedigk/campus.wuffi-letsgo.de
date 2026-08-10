<?php

namespace App\Contracts\Repositories;

use App\Dto\QuestionChoice;
use App\Dto\QuestionChoiceInput;

interface QuestionChoiceRepositoryInterface
{
    public function getByQuestionId(int $questionId): array;

    public function create(int $questionId, QuestionChoiceInput $questionChoice): QuestionChoice;

    public function update(QuestionChoice $questionChoice): void;

    public function delete(int $id): void;

    public function deleteByQuestionId(int $questionId): void;
}