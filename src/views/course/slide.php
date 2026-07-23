<?php if (!$currentSlide): ?>
    <p>No slide available for this module.</p>
<?php else: ?>
    <article class="slide-content">
        <h3><?= htmlspecialchars($currentSlide->title ?? 'Untitled Slide') ?></h3>

        <?php if (!empty($currentSlide->htmlContent)): ?>
            <div class="slide-html">
                <?= $currentSlide->htmlContent ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($currentSlide->audioUrl)):
            $audioFile = basename(trim((string)$currentSlide->audioUrl));
            $audioFile = preg_replace('/\.mp3$/i', '', $audioFile);
            $audioSrc = '/assets/audio/' . rawurlencode($audioFile) . '.mp3';
        ?>
            <div class="slide-audio">
                <audio controls preload="none">
                    <source src="<?= htmlspecialchars($audioSrc) ?>" type="audio/mpeg">
                    Your browser does not support audio playback.
                </audio>
            </div>
        <?php endif; ?>

        <?php if (!empty($currentSlide->isQuiz)): ?>
            <div class="slide-quiz">
                <h4>Quiz</h4>
                <?php if ($quizResult && $quizResult->isSubmitted): ?>
                    <div class="quiz-results">
                        <p class="quiz-<?= htmlspecialchars($quizResult->feedbackType) ?>-message">
                            <?= htmlspecialchars($quizResult->feedbackMessage) ?>
                        </p>
                        <?php foreach ($quizResult->questions as $question):
                            $qId = $question['id'];
                            $result = $quizResult->results[$qId] ?? null;
                            $isQuestionCorrect = $result && $result['is_correct'];
                        ?>
                            <fieldset>
                                <legend><?= htmlspecialchars($question['question_text']) ?>
                                    <span class="question-result <?= $isQuestionCorrect ? 'correct' : 'incorrect' ?>">
                                        <?= $isQuestionCorrect ? '✓ Correct' : '✗ Incorrect' ?>
                                    </span>
                                </legend>
                                <?php foreach ($quizResult->results[$qId]['choices'] ?? $quizResult->choicesByQuestion[$qId] ?? [] as $choice):
                                    $labelSuffix = $quizResult->getChoiceLabel($choice);
                                    $isChosen = $choice['was_chosen'] ?? false;
                                    $isCorrect = $choice['is_correct'] ?? false;
                                    $labelClass = 'answer-choice';
                                    if ($isCorrect && $isChosen) {
                                        $labelClass .= ' correct chosen';
                                    } elseif ($isCorrect) {
                                        $labelClass .= ' correct';
                                    } elseif ($isChosen) {
                                        $labelClass .= ' incorrect chosen';
                                    }
                                ?>
                                    <div>
                                        <label class="<?= htmlspecialchars($labelClass) ?>">
                                            <input type="checkbox" disabled <?= $isChosen ? 'checked' : '' ?>>
                                            <?= htmlspecialchars($choice['choice_text']) ?>
                                            <?= $labelSuffix ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </fieldset>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <form method="post">
                        <?php foreach ($quizResult->questions as $question): ?>
                            <fieldset>
                                <legend><?= htmlspecialchars($question['question_text']) ?></legend>

                                <?php foreach ($quizResult->choicesByQuestion[$question['id']] ?? [] as $choice): ?>
                                    <?php
                                        $qid = (string)$question['id'];
                                        $checked = '';
                                        // Check if there was a previous submission
                                        if ($quizResult && $quizResult->isSubmitted && isset($quizResult->results[$qid])) {
                                            if (in_array((string)$choice['id'], $quizResult->results[$qid]['submitted'], true)) {
                                                $checked = 'checked';
                                            }
                                        }
                                    ?>
                                    <div>
                                        <label>
                                            <input
                                                type="checkbox"
                                                name="answers[<?= (int)$question['id'] ?>][]"
                                                value="<?= (int)$choice['id'] ?>"
                                                <?= $checked ?>
                                            >
                                            <?= htmlspecialchars($choice['choice_text']) ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </fieldset>
                        <?php endforeach; ?>

                        <button type="submit" name="quiz_submit" value="1">Submit Quiz</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </article>
<?php endif; ?>

