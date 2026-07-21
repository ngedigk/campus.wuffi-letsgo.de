function deleteAccessCode(accessCodeId) {
    if (!confirm('Are you sure you want to delete this access code?')) {
        return;
    }

    document.getElementById('delete-access-code-id').value = accessCodeId;
    document.getElementById('delete-access-code-form').submit();
}