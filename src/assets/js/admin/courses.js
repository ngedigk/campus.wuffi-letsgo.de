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

function deleteCourse(courseId) {
    if (!confirm('Are you sure you want to delete this course? This will also delete all submodules and slides within it.')) {
        return;
    }
    
    document.getElementById('delete-course-form').submit();
}

function deleteModule(moduleId) {
    if (!confirm('Are you sure you want to delete this module? This will also delete all slides within it.')) {
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
    if (!confirm('Are you sure you want to delete this slide?')) {
        return;
    }

    document.getElementById('delete-slide-id').value = slideId;
    document.getElementById('delete-slide-form').submit();
}

const currentCourseCountElement = document.getElementById('current-course-count');

document.getElementById('courseSearch')?.addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const coursesList = document.querySelectorAll('.course-item');
    let filteredCourses = [];
    coursesList.forEach(item => {
        const title = item.querySelector('h4').textContent.toLowerCase();
        const displayMode = title.includes(searchTerm) ? 'block' : 'none';
        item.style.display = displayMode;
        if (displayMode === 'block') {
            filteredCourses.push(item);
        }
    });
    currentCourseCountElement.innerText = filteredCourses.length;
    
});