document.addEventListener('click', handleClick);

const actions = {
    'delete-access-code': button => { deleteAudioAsset(button.dataset.accessCodeId); },
    'edit-access-code': button => { editAccessCode(
        button.dataset.accessCodeId,
        button.dataset.courseId,
        button.dataset.code
    ); }
};

function handleClick(event) {
    const button = event.target.closest('[data-action]');
    
    if (!button) {
        return;
    }

    const action = actions[button.dataset.action];
    
    if (action) {
        action(button);
    }
}

function deleteAudioAsset(accessCodeId) {
    if (!confirm( 'Sind Sie sicher, dass Sie diesen Access Code löschen möchten?' )) {
        return;
    }
    
    document.getElementById('delete-access-code-id').value = accessCodeId;
    document.getElementById('delete-access-code-form').submit();
}

function editAccessCode(accessCodeId, courseId, code) {
    document.getElementById('edit-access-code-id').value = accessCodeId;
    document.getElementById('edit-access-course').value = courseId;
    document.getElementById('edit-access-code').value = code;
    document.getElementById('editAccessCodeModal').style.display = 'flex';
}