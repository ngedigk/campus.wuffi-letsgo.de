document.addEventListener('click', handleClick);

const actions = {
    'delete-registration-code': button => { deleteRegistrationCode(button.dataset.registrationCodeId); },
    'edit-registration-code': button => { editRegistrationCode(
        button.dataset.registrationCodeId,
        button.dataset.registrationCode,
        button.dataset.courseIds
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

function deleteRegistrationCode(registrationCodeId) {
    if (!confirm('Sind Sie sicher, dass Sie diesen Registrierungscode löschen möchten?')) {
        return;
    }

    const form = document.getElementById('delete-registration-code-form');

    form.action = `/admin/registration-codes/${encodeURIComponent(registrationCodeId)}/delete`;
    form.submit();
}

function editRegistrationCode(registrationCodeId, registrationCode, courseIdsJson) {
    document.querySelectorAll('.edit-course-checkbox').forEach(cb => {
        cb.checked = false;
    });
    
    document.querySelectorAll('.edit-course-checkbox').forEach(cb => {
        if (courseIdsJson.includes(cb.value)) {
            cb.checked = true;
        }
    });

    const form = document.getElementById('edit-registration-code-form');
    form.action = `/admin/registration-codes/${encodeURIComponent(registrationCodeId)}/update`;

    document.getElementById('edit-registration-code').value = registrationCode;
    document.getElementById('editRegistrationCodeModal').style.display = 'flex';
}