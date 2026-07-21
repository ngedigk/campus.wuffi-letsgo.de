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
});