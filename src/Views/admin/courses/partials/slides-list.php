<?php
/** @var array $viewModel */
?>
<div class="slides-section">
    <div class="section-header">
        <h3>Folien</h3>
        <button class="btn btn-primary btn-small" onclick="addSlide('<?= htmlspecialchars($viewModel['selectedModule']->id ?? '') ?>')">
            + Folie hinzufügen
        </button>
    </div>
    <div class="slides-table">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Titel</th>
                    <th>Art</th>
                    <th>Audio</th>
                    <th>Reihenfolge</th>
                    <th>Aktionen</th>
                </tr>
            </thead>
            <tbody id="slidesBody">
                <?php if (empty($selectedModule->slides)): ?>
                    <tr class="empty-slides">
                        <td colspan="6"><p>Dieses Modul hat bisher keine Folien.</p></td>
                    </tr>
                <?php else:
                    foreach ($selectedModule->slides as $index => $slide): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($slide->title) ?></td>
                            <td><?= $slide->isQuiz ? 'Quiz' : 'Folie' ?></td>
                            <td><?= $slide->audioUrl ? 'Ja' : 'Nein' ?></td>
                            <td><?= $slide->sortOrder ?></td>
                            <td>
                                <a
                                    href="admin.php?page=courses&course_id=<?= urlencode($viewModel['selectedCourse']->uuid ?? '') ?>&module_id=<?= urlencode($viewModel['selectedModule']->id ?? '') ?>&slide_id=<?= urlencode($slide->id) ?>"
                                    class="btn btn-small"
                                    title="Folie bearbeiten"
                                >
                                    ✏️
                                </a>
                                <button onclick="deleteSlide(<?= $slide->id ?>)">🗑</button>
                            </td>
                        </tr>
                    <?php endforeach;
                endif; ?>
            </tbody>
        </table>
    </div>
</div>
