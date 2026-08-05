document.addEventListener('click', handleClick);

const actions = {
    'delete-audio-asset': button => { deleteAudioAsset(button.dataset.filename); }
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

function deleteAudioAsset(filename) {
    if (!confirm( 'Sind Sie sicher, dass Sie diese Audio Datei löschen möchten?' )) {
        return;
    }

    const form = document.getElementById('delete-audio-asset-form');

    form.action = `/admin/audio-assets/${encodeURIComponent(filename)}/delete`;
    form.submit();
}