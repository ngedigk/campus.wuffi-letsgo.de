<?php

final class QuizQuestionInput
{
    public function __construct(
        public int $slideId,
        public string $questionText,
    ) {}
}