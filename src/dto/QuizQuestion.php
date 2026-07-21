<?php
abstract class QuizQuestionData
{
    public function __construct(
        public string $questionText,
        public int $slideId
    ) {}
}

final class CreateQuizQuestion extends QuizQuestionData
{
    public function __construct(
        int $slideId,
        string $questionText,
    ) {
        parent::__construct(
            $slideId,
            $questionText
        );
    }
}

final class QuizQuestion extends QuizQuestionData
{
    public function __construct(
        public int $id,
        int $slideId,
        string $questionText
    ) {
        parent::__construct(
            $slideId,
            $questionText
        );
    }
}
?>