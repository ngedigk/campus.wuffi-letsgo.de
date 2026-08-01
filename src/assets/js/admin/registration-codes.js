function deleteRegistrationCode(registrationCodeId) {
    if (!confirm('Are you sure you want to delete this registration code?')) {
        return;
    }

    document.getElementById('delete-registration-code-id').value = registrationCodeId;
    document.getElementById('delete-registration-code-form').submit();
}

function editRegistrationCode(registrationCodeId) {
    document.querySelectorAll('.edit-course-checkbox').forEach(cb => {
        cb.checked = false;
    });
    
    const btn = event.target.closest('button');
    const currentCourseIds = (btn?.dataset.courseIds || '').split(',').filter(id => id);
    
    document.querySelectorAll('.edit-course-checkbox').forEach(cb => {
        if (currentCourseIds.includes(cb.value)) {
            cb.checked = true;
        }
    });
    
    document.getElementById('edit-registration-code-id').value = registrationCodeId;
    document.getElementById('editRegistrationCodeModal').style.display = 'flex';
}