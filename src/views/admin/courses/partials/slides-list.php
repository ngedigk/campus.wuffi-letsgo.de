<div class="slides-section">
    <div class="section-header">
        <h3>Slides</h3>
        <button class="btn btn-primary btn-small" onclick="addSlide('<?= htmlspecialchars($selectedModule->id) ?>')">
            + Add Slide
        </button>
    </div>
    <div class="slides-table">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Audio</th>
                    <th>Sort Order</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="slidesBody">
                <?php if (empty($selectedModule->slides)): ?>
                    <tr class="empty-slides">
                        <td colspan="6"><p>No slides yet for this module.</p></td>
                    </tr>
                <?php else:
                    foreach ($selectedModule->slides as $index => $slide): ?>
                        <tr class="<?= $selectedSlide && $selectedSlide->id == $slide->id ? 'active' : '' ?>">
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($slide->title) ?></td>
                            <td><?= $slide->isQuiz ? 'Quiz' : 'Slide' ?></td>
                            <td><?= $slide->audioUrl ? 'Yes' : 'No' ?></td>
                            <td><?= $slide->sortOrder ?></td>
                            <td>
                                <a
                                    href="admin.php?page=courses&course_id=<?= urlencode($selectedCourse->uuid) ?>&module_id=<?= urlencode($selectedModule->id) ?>&slide_id=<?= urlencode($slide->id) ?>"
                                    class="btn btn-small"
                                    title="Edit Slide"
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
