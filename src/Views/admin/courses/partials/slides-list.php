<?php
/** @var array $viewModel */
?>
<div class="slides-section">
    <div class="section-header">
        <h3>Folien</h3>
        <button
            class="btn btn-primary btn-small"
            data-action="add-slide"
            data-module-id="<?= htmlspecialchars($viewModel['selectedModule']->id ?? '') ?>"
        >
            + Folie hinzufügen
        </button>
    </div>
    <div class="slides-table">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Titel</th>
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
                            <td><?= $slide->audioUrl ? 'Ja' : 'Nein' ?></td>
                            <td><?= $slide->sortOrder ?></td>
                            <td>
                                <a
                                    href="/admin/courses/<?= urlencode($viewModel['selectedCourse']->uuid ?? '') ?>/modules/<?= urlencode($viewModel['selectedModule']->id ?? '') ?>/slides/<?= urlencode($slide->id) ?>"
                                    class="btn btn-small"
                                    title="Folie bearbeiten"
                                >
                                    ✏️
                                </a>
                                <button
                                    data-action="delete-slide"
                                    data-course-id="<?= htmlspecialchars($viewModel['selectedCourse']->uuid) ?>"
                                    data-module-id="<?= $viewModel['selectedModule']->id ?>"
                                    data-slide-id="<?= $slide->id ?>"
                                >🗑</button>
                            </td>
                        </tr>
                    <?php endforeach;
                endif; ?>
            </tbody>
        </table>
    </div>
</div>
