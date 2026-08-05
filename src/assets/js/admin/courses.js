let choiceIndex = 1;

const actions = {
    'add-module': () => addModule(),
    'add-slide': () => addSlide(),
    'add-question': () => addQuestion(),
    'add-choice': () => addChoice(),
    'add-edit-choice': () => addEditChoice(),
    
    'edit-question': button => editQuestion(
        button.dataset.courseId,
        button.dataset.moduleId,
        button.dataset.slideId,
        button.dataset.questionId,
        button.dataset.questionText,
        JSON.parse(button.dataset.choices)
    ),
    
    'remove-choice': button => removeChoice(
        button,
        'choices-container'
    ),
    'remove-edit-choice': button => removeChoice(
        button,
        'edit-choices-container'
    ),

    'delete-course': button => deleteCourse(
        button.dataset.courseId
    ),
    'delete-module': button => deleteModule(
        button.dataset.courseId,
        button.dataset.moduleId
    ),
    'delete-slide': button => deleteSlide(
        button.dataset.courseId,
        button.dataset.moduleId,
        button.dataset.slideId
    ),
    'delete-question': button => deleteQuestion(
        button.dataset.courseId,
        button.dataset.moduleId,
        button.dataset.slideId,
        button.dataset.questionId
    ),
};

// Audio preview handlers
document.getElementById('slide-audio-file')?.addEventListener('change', function() {
    previewAudioFile(this, 'audio-preview', 'audio-preview-container');
});
document.getElementById('new-slide-audio-file')?.addEventListener('change', function() {
    previewAudioFile(this, 'create-audio-preview', 'create-audio-preview-container');
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

function addModule() {
    document.getElementById('createModuleModal').style.display = 'flex';
}

function addSlide() {
    document.getElementById('createSlideModal').style.display = 'flex';
}

function addQuestion() {
    choiceIndex = 1;

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
            class="btn btn-sm remove-choice"
            data-action="remove-choice"
        >
            🗑
        </button>
    `;

    container.appendChild(row);
    choiceIndex++;
}

function editQuestion(courseId, moduleId, slideId, questionId, questionText, choicesJson) {
    choiceIndex = 1;

    const form = document.getElementById('edit-question-form');

    form.action =
        `/admin/courses/${encodeURIComponent(courseId)}` +
        `/modules/${encodeURIComponent(moduleId)}` +
        `/slides/${encodeURIComponent(slideId)}` +
        `/questions/${encodeURIComponent(questionId)}` +
        `/update`;

    document.getElementById('edit-question-text').value = questionText

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
            class="btn btn-sm remove-choice"
            data-action="remove-edit-choice"
        >
            🗑
        </button>
    `;

    container.appendChild(row);
    choiceIndex++;
}

function removeChoice(button, containerId) {
    const rows = document.querySelectorAll(`#${containerId} .choice-row`);

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

    const form = document.getElementById('delete-form');

    form.action = `/admin/courses/${encodeURIComponent(courseId)}/delete`;
    form.submit();
}

function deleteModule(courseId, moduleId) {
    if (!confirm(
        'Sind Sie sicher, dass Sie dieses Modul löschen möchten? ' +
        'Dies wird auch alle Folien innerhalb des Moduls löschen.'
    )) {
        return;
    }

    const form = document.getElementById('delete-form');

    form.action =
        `/admin/courses/${encodeURIComponent(courseId)}` +
        `/modules/${encodeURIComponent(moduleId)}` +
        `/delete`;
    form.submit();

    document.getElementById('delete-form').submit();
}

function deleteSlide(courseId, moduleId, slideId) {
    if (!confirm('Sind Sie sicher, dass Sie diese Folie löschen möchten?')) {
        return;
    }

    const form = document.getElementById('delete-form');

    form.action =
        `/admin/courses/${encodeURIComponent(courseId)}` +
        `/modules/${encodeURIComponent(moduleId)}` +
        `/slides/${encodeURIComponent(slideId)}` +
        `/delete`;
    form.submit();

    document.getElementById('delete-form').submit();
}

function deleteQuestion(courseId, moduleId, slideId, questionId) {
    if (!confirm('Sind Sie sicher, dass Sie diese Frage löschen möchten?')) {
        return;
    }

    const form = document.getElementById('delete-form');

    form.action =
        `/admin/courses/${encodeURIComponent(courseId)}` +
        `/modules/${encodeURIComponent(moduleId)}` +
        `/slides/${encodeURIComponent(slideId)}` +
        `/questions/${encodeURIComponent(questionId)}` +
        `/delete`;
    form.submit();

    document.getElementById('delete-form').submit();
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

