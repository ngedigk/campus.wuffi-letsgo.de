document.addEventListener('click', handleClick);

const actions = {
    'delete-access-code': button => { deleteAccessCode(button.dataset.accessCodeId); },
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

function deleteAccessCode(accessCodeId) {
    if (!confirm( 'Sind Sie sicher, dass Sie diesen Access Code löschen möchten?' )) {
        return;
    }

    const form = document.getElementById('delete-access-code-form');

    form.action = `/admin/access-codes/${encodeURIComponent(accessCodeId)}/delete`;
    form.submit();
}

function editAccessCode(accessCodeId, courseId, code) {
    const form = document.getElementById('edit-access-code-form');
    form.action = `/admin/access-codes/${encodeURIComponent(accessCodeId)}/update`;

    document.getElementById('edit-access-code').value = code;
    document.getElementById('editAccessCodeModal').style.display = 'flex';
}