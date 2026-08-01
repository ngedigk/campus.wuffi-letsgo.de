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

