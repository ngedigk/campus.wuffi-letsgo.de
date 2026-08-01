<?php
/** @var array $viewModel */
?>
<?php if (!$viewModel['currentSlide']): ?>
    <p>No slide available for this module.</p>
<?php else: ?>
    <article class="slide-content">
        <h3><?= htmlspecialchars($viewModel['currentSlide']->title ?? 'Untitled Slide') ?></h3>

        <?php if (!empty($viewModel['currentSlide']->htmlContent)): ?>
            <div class="slide-html">
                <?= $viewModel['currentSlide']->htmlContent ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($viewModel['currentSlide']->audioUrl)):
            $audioFile = basename(trim((string)$viewModel['currentSlide']->audioUrl));
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

        <?php if (!empty($viewModel['currentSlide']->isQuiz)): ?>
            <div class="slide-quiz">
                <h4>Quiz</h4>
                <?php if ($viewModel['quizResult'] && $viewModel['quizResult']->isSubmitted): ?>
                    <div class="quiz-results">
                        <p class="quiz-<?= htmlspecialchars($viewModel['quizResult']->feedbackType) ?>-message">
                            <?= htmlspecialchars($viewModel['quizResult']->feedbackMessage) ?>
                        </p>
                        <?php foreach ($viewModel['quizResult']->questions ?? [] as $question):
                            $qId = $question['id'];
                            $result = $viewModel['quizResult']->results[$qId] ?? null;
                            $isQuestionCorrect = $result && $result['is_correct'];
                        ?>
                            <fieldset>
                                <legend><?= htmlspecialchars($question['question_text']) ?>
                                    <span class="question-result <?= $isQuestionCorrect ? 'correct' : 'incorrect' ?>">
                                        <?= $isQuestionCorrect ? '✓ Correct' : '✗ Incorrect' ?>
                                    </span>
                                </legend>
                                <?php foreach ($viewModel['quizResult']->results[$qId]['choices'] ?? $viewModel['quizResult']->choicesByQuestion[$qId] ?? [] as $choice):
                                    $labelSuffix = $viewModel['quizResult']->getChoiceLabel($choice);
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
                        <?php foreach ($viewModel['quizResult']->questions ?? [] as $question): ?>
                            <fieldset>
                                <legend><?= htmlspecialchars($question['question_text']) ?></legend>

                                <?php foreach ($viewModel['quizResult']->choicesByQuestion[$question['id']] ?? [] as $choice): ?>
                                    <?php
                                        $qid = (string)$question['id'];
                                        $checked = '';
                                        
                                        if ($viewModel['quizResult'] && $viewModel['quizResult']->isSubmitted && isset($viewModel['quizResult']->results[$qid])) {
                                            if (in_array((string)$choice['id'], $viewModel['quizResult']->results[$qid]['submitted'], true)) {
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

