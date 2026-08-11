<?php

namespace App\Contracts\Repositories;

use App\Dto\QuizQuestion;
use App\Dto\QuizQuestionInput;

interface QuizQuestionRepositoryInterface
{
    public function getById(int $id): ?QuizQuestion;

    public function getBySlideId(int $slideId): array;

    public function exists(int $id): bool;

    public function create(QuizQuestionInput $quizQuestion): QuizQuestion;

    public function update(QuizQuestion $quizQuestion): QuizQuestion;

    public function delete(int $id): void;

    public function getQuizDataForSlide(int $slideId): array;
}