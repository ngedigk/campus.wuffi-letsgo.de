<?php if (!$currentSlide): ?>
    <p>No slide available for this module.</p>
<?php else: ?>
    <article class="slide-content">
        <h3><?= htmlspecialchars($currentSlide['title'] ?? 'Untitled Slide') ?></h3>

        <?php if (!empty($currentSlide['html_content'])): ?>
            <div class="slide-html">
                <?= $currentSlide['html_content'] ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($currentSlide['audio_url'])):
            $audioFile = basename(trim((string)$currentSlide['audio_url']));
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

        <?php if (!empty($currentSlide['is_quiz'])): ?>
            <div class="slide-quiz">
                <h4>Quiz</h4>
                <?php if ($quizAttempted): ?>
                    <div class="quiz-results">
                        <?php if (!empty($errors)): ?>
                            <p class="course-errors">Please answer all quiz questions.</p>
                        <?php elseif ($quizPassed): ?>
                            <p class="quiz-success-message">You answered this quiz correctly.</p>
                        <?php else: ?>
                            <p class="course-errors">Some answers were incorrect. Review the feedback below and try again.</p>
                        <?php endif; ?>
                        <?php foreach ($currentSlideQuestions as $question): ?>
                            <fieldset>
                                <legend><?= htmlspecialchars($question['question_text']) ?></legend>
                                <?php foreach ($choicesByQuestion[$question['id']] ?? [] as $choice):
                                    $qid = (string)$question['id'];
                                    $chosenArr = [];
                                    if (!empty($userAnswersByQuestion[$qid])) {
                                        $chosenArr = $userAnswersByQuestion[$qid];
                                    } elseif (!empty($submittedAnswers[$qid])) {
                                        $val = $submittedAnswers[$qid];
                                        $chosenArr = is_array($val) ? array_map('strval', $val) : [(string)$val];
                                    }
                                    $isChosen = in_array((string)$choice['id'], $chosenArr, true);
                                    $isCorrect = !empty($choice['is_correct']);
                                    $labelClass = 'answer-choice';
                                    if ($isCorrect) {
                                        $labelClass .= ' correct';
                                    } elseif ($isChosen) {
                                        $labelClass .= ' selected';
                                    }
                                ?>
                                    <div>
                                        <label class="<?= htmlspecialchars($labelClass) ?>">
                                            <input type="checkbox" disabled <?= $isChosen ? 'checked' : '' ?>>
                                            <?= htmlspecialchars($choice['choice_text']) ?>
                                            <?php if ($isCorrect): ?>
                                                <strong> (Correct)</strong>
                                            <?php elseif ($isChosen): ?>
                                                <strong> (Your answer)</strong>
                                            <?php endif; ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </fieldset>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <form method="post">
                        <?php foreach ($currentSlideQuestions as $question): ?>
                            <fieldset>
                                <legend><?= htmlspecialchars($question['question_text']) ?></legend>

                                <?php foreach ($choicesByQuestion[$question['id']] ?? [] as $choice): ?>
                                    <?php
                                        $qid = (string)$question['id'];
                                        $chosenArr = [];
                                        if (!empty($submittedAnswers[$qid])) {
                                            $val = $submittedAnswers[$qid];
                                            $chosenArr = is_array($val) ? array_map('strval', $val) : [(string)$val];
                                        } elseif (!empty($userAnswersByQuestion[$qid])) {
                                            $chosenArr = $userAnswersByQuestion[$qid];
                                        }
                                        $checked = in_array((string)$choice['id'], $chosenArr, true) ? 'checked' : '';
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
