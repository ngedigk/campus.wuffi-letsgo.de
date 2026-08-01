if (typeof editor !== 'undefined' && typeof existingAssets !== 'undefined') {
    editor.AssetManager.add(existingAssets);
}

function addModule(courseId) {
    document.getElementById('module-course-id').value = courseId;
    document.getElementById('createModuleModal').style.display = 'flex';
}

function addSlide(moduleId) {
    document.getElementById('slide-module-id').value = moduleId;
    document.getElementById('createSlideModal').style.display = 'flex';
}

function addQuestion(slideId) {
    document.getElementById('question-slide-id').value = slideId;
    document.getElementById('createQuestionModal').style.display = 'flex';
}

let choiceIndex = 1;
function addChoice() {
    const container = document.getElementById('choices-container');
    const row = document.createElement('div');
    row.className = 'choice-row';
    row.innerHTML = `
        <input type="text" name="choices[${choiceIndex}][text]" placeholder="Antwort Text" required style="flex: 1; margin-right: 10px;">
        <label><input type="checkbox" name="choices[${choiceIndex}][is_correct]" unchecked> Korrekt</label>
        <button type="button" class="btn btn-danger btn-sm remove-choice" onclick="removeChoice(this)">&times;</button>
    `;
    container.appendChild(row);
    choiceIndex++;
}

function removeChoice(btn) {
    const rows = document.querySelectorAll('.choice-row');
    if (rows.length > 1) {
        btn.parentElement.remove();
    } else {
        alert('Mindestens eine Antwort muss vorhanden sein.');
    }
}

function validateQuestionForm() {
    const choices = document.querySelectorAll('.choice-row');
    let hasCorrect = false;
    
    for (let row of choices) {
        const input = row.querySelector('input[name="choices[][text]"]');
        if (input && input.value.trim() === '') {
            alert('Bitte füllen Sie alle Antwortfelder aus.');
            return false;
        }
        
        const checkbox = row.querySelector('input[name="choices[][is_correct]"]');
        if (checkbox && checkbox.checked) {
            hasCorrect = true;
        }
    }

    if (!hasCorrect) {
        alert('Bitte markieren Sie mindestens eine korrekte Antwort.');
        return false;
    }
    
    return true;
}

function deleteCourse(courseId) {
    if (!confirm('Sind Sie sicher, dass Sie diesen Kurs löschen möchten? Dies wird auch alle Untermodule und Folien innerhalb des Kurses löschen.')) {
        return;
    }
    
    document.getElementById('delete-course-form').submit();
}

function deleteModule(moduleId) {
    if (!confirm('Sind Sie sicher, dass Sie dieses Modul löschen möchten? Dies wird auch alle Folien innerhalb des Moduls löschen.')) {
        return;
    }

    document.getElementById('delete-module-id').value = moduleId;
    document.getElementById('delete-module-form').submit();
}

function prepareSlideContent() {

    document.getElementById('slide-content').value =
        editor.getHtml() +
        '<style>' +
        editor.getCss() +
        '</style>';
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

