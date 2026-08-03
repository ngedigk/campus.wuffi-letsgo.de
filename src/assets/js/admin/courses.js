let choiceIndex = 1;

const actions = {
    'add-module': button => addModule(button.dataset.courseId),
    'add-slide': button => addSlide(button.dataset.moduleId),
    'add-question': button => addQuestion(button.dataset.slideId),
    'edit-question': button => editQuestion(button.dataset.questionId, button.dataset.questionText, button.dataset.choicesJson),
    'add-choice': () => addChoice(),
    'remove-choice': button => removeChoice(button),
    'remove-edit-choice': button => removeEditChoice(button),
    'delete-course': button => deleteCourse(button.dataset.courseId),
    'delete-module': button => deleteModule(button.dataset.moduleId),
    'delete-slide': button => deleteSlide(button.dataset.slideId),
    'delete-question': button => deleteQuestion(button.dataset.questionId),
};

// Audio preview handlers
document.getElementById('slide-audio-file')?.addEventListener('change', function() {
    previewAudioFile(this, 'audio-preview', 'audio-preview-container');
});
document.getElementById('remove-audio-preview')?.addEventListener('click', function() {
    removeAudioPreview('audio-preview', 'audio-preview-container', 'slide-audio-file');
});
document.getElementById('new-slide-audio-file')?.addEventListener('change', function() {
    previewAudioFile(this, 'create-audio-preview', 'create-audio-preview-container');
});
document.getElementById('remove-create-audio-preview')?.addEventListener('click', function() {
    removeAudioPreview('create-audio-preview', 'create-audio-preview-container', 'new-slide-audio-file');
});

// Dropdown audio preview handlers
document.getElementById('slide-audio-url')?.addEventListener('change', function() {
    handleDropdownAudioPreview(this, 'slide-dropdown-audio-preview', 'slide-dropdown-audio-preview-container');
});
document.getElementById('new-slide-audio-url')?.addEventListener('change', function() {
    handleDropdownAudioPreview(this, 'create-dropdown-audio-preview', 'create-dropdown-audio-preview-container');
});

document.addEventListener('click', event => {
    const button = event.target.closest('[data-action]');

    if (!button) {
        return;
    }

    const action = actions[button.dataset.action];

    if (action) {
        action(button);
    }
});

document.addEventListener('keydown', event => {
    if (event.key !== 'Enter') {
        return;
    }

    const target = event.target;
    const targetTag = target?.tagName?.toUpperCase() || '';

    if (targetTag === 'BUTTON' || targetTag === 'TEXTAREA') {
        return;
    }

    event.preventDefault();
});

document.addEventListener('submit', event => {
    const form = event.target;

    if (
        form.id === 'createQuestionForm' ||
        form.id === 'editQuestionForm'
    ) {
        if (!validateQuestionForm(form)) {
            event.preventDefault();
        }
    }
});

function addModule(courseId) {
    document.getElementById('module-course-id').value = courseId;
    document.getElementById('createModuleModal').style.display = 'flex';
}

function addSlide(moduleId) {
    document.getElementById('slide-module-id').value = moduleId;
    document.getElementById('createSlideModal').style.display = 'flex';
}

function addQuestion(slideId) {
    choiceIndex = 1;

    document.getElementById('question-slide-id').value = slideId;
    document.getElementById('createQuestionModal').style.display = 'flex';
}

function addChoice() {
    const container = document.getElementById('choices-container');

    const row = document.createElement('div');
    row.className = 'choice-row';

    row.innerHTML = `
        <input
            type="text"
            name="choices[${choiceIndex}][text]"
            placeholder="Antwort Text"
            required
            style="flex: 1; margin-right: 10px;"
        >

        <label>
            <input
                type="checkbox"
                name="choices[${choiceIndex}][is_correct]"
                value="1"
            >
            Korrekt
        </label>

        <button
            type="button"
            class="btn btn-danger btn-sm"
            data-action="remove-choice"
        >&times;</button>
    `;

    container.appendChild(row);
    choiceIndex++;
}

function removeChoice(button) {
    const rows = document.querySelectorAll('#choices-container .choice-row');

    if (rows.length > 1) {
        button.parentElement.remove();
    } else {
        alert('Mindestens eine Antwort muss vorhanden sein.');
    }
}

function handleDropdownAudioPreview(select, audioId, containerId) {
    const selectedFile = select.value;
    const audio = document.getElementById(audioId);
    const container = document.getElementById(containerId);

    if (selectedFile) {
        audio.src = '/assets/audio/' + selectedFile;
        container.style.display = 'block';
    } else {
        audio.src = '';
        container.style.display = 'none';
    }
}

function validateQuestionForm(form) {
    const choices = form.querySelectorAll('.choice-row');

    let hasCorrect = false;

    for (const row of choices) {
        const input = row.querySelector(
            'input[name^="choices"][name$="[text]"]'
        );

        if (input && input.value.trim() === '') {
            alert('Bitte füllen Sie alle Antwortfelder aus.');
            return false;
        }

        const checkbox = row.querySelector(
            'input[name^="choices"][name$="[is_correct]"]'
        );

        if (checkbox?.checked) {
            hasCorrect = true;
        }
    }

    if (!hasCorrect) {
        alert('Bitte markieren Sie mindestens eine korrekte Antwort.');
        return false;
    }

    return true;
}

function editQuestion(questionId, questionText, choicesJson) {
    choiceIndex = 1;

    document.getElementById('edit-question-id').value = questionId;

    document.getElementById('edit-question-slide-id').value =
        document.getElementById('question-slide-id').value;

    document.getElementById('edit-question-text').value = questionText;

    populateEditModal(choicesJson);

    document.getElementById('editQuestionModal').style.display = 'flex';
}

function populateEditModal(choicesJson) {
    const container = document.getElementById('edit-choices-container');

    container.innerHTML = '';

    if (choicesJson.length === 0) {
        addEditChoice();
        return;
    }

    choicesJson.forEach(choice => {
        addEditChoice(
            choice.choiceText,
            choice.isCorrect
        );
    });
}

function addEditChoice(text = '', isCorrect = false) {
    const container = document.getElementById('edit-choices-container');

    const row = document.createElement('div');
    row.className = 'choice-row';

    row.innerHTML = `
        <input
            type="text"
            name="choices[${choiceIndex}][text]"
            value="${escapeHtml(text)}"
            placeholder="Antwort Text"
            required
            style="flex: 1; margin-right: 10px;"
        >

        <label>
            <input
                type="checkbox"
                name="choices[${choiceIndex}][is_correct]"
                value="1"
                ${isCorrect ? 'checked' : ''}
            >
            Korrekt
        </label>

        <button
            type="button"
            class="btn btn-danger btn-sm"
            data-action="remove-edit-choice"
        >&times;</button>
    `;

    container.appendChild(row);
    choiceIndex++;
}

function removeEditChoice(button) {
    const rows = document.querySelectorAll(
        '#edit-choices-container .choice-row'
    );

    if (rows.length > 1) {
        button.parentElement.remove();
    } else {
        alert('Mindestens eine Antwort muss vorhanden sein.');
    }
}

function deleteCourse(courseId) {
    if (!confirm(
        'Sind Sie sicher, dass Sie diesen Kurs löschen möchten? ' +
        'Dies wird auch alle Untermodule und Folien innerhalb des Kurses löschen.'
    )) {
        return;
    }

    document.getElementById('delete-course-form').submit();
}

function deleteModule(moduleId) {
    if (!confirm(
        'Sind Sie sicher, dass Sie dieses Modul löschen möchten? ' +
        'Dies wird auch alle Folien innerhalb des Moduls löschen.'
    )) {
        return;
    }

    document.getElementById('delete-module-id').value = moduleId;

    document.getElementById('delete-module-form').submit();
}

function deleteSlide(slideId) {
    if (!confirm('Sind Sie sicher, dass Sie diese Folie löschen möchten?')) {
        return;
    }

    document.getElementById('delete-slide-id').value = slideId;

    document.getElementById('delete-slide-form').submit();
}

function deleteQuestion(questionId) {
    if (!confirm('Sind Sie sicher, dass Sie diese Frage löschen möchten?')) {
        return;
    }

    document.getElementById('delete-question-id').value = questionId;

    document.getElementById('delete-question-form').submit();
}


function escapeHtml(value) {
    const div = document.createElement('div');
    div.textContent = value;
    return div.innerHTML;
}

function previewAudioFile(fileInput, audioId, containerId) {
    const file = fileInput.files[0];
    if (!file) {
        return;
    }

    const audio = document.getElementById(audioId);
    const container = document.getElementById(containerId);

    if (file && audio && container) {
        const url = URL.createObjectURL(file);
        audio.src = url;
        audio.style.display = 'block';
        container.style.display = 'block';
    }
}

