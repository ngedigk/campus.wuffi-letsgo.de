function deleteAccessCode(accessCodeId) {
    if (!confirm('Are you sure you want to delete this access code?')) {
        return;
    }

    document.getElementById('delete-access-code-id').value = accessCodeId;
    document.getElementById('delete-access-code-form').submit();
}

function editAccessCode(accessCodeId) {
    const btn = event.target.closest('button');
    const courseId = btn?.dataset.courseId || '';
    const code = btn?.dataset.code || '';
    
    document.getElementById('edit-access-code-id').value = accessCodeId;
    document.getElementById('edit-access-course').value = courseId;
    document.getElementById('edit-access-code').value = code;
    
    const editModal = document.getElementById('editAccessCodeModal');
    editModal.style.display = 'flex';
}