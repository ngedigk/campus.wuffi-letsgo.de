import initEditor from './init.js';

import headlinePlugin from './plugins/headline.js';
import listPlugin from './plugins/lists.js';

const csrfToken = window.getCsrfToken();

const editor = initEditor({
    plugins: [
        'gjs-blocks-basic',
        headlinePlugin,
        listPlugin
    ],

    pluginsOpts: {
        'gjs-blocks-basic': {
            flexGrid: true
        },
    },

    assetManager: {
        assets: window.existingAssets,
        upload: '/admin.php',
        uploadName: 'files',

        params: {
            action: 'upload_image',
            csrf_token: csrfToken
        }
    },

    canvas: {
        styles: [
            '/assets/css/style.css',
            '/assets/css/slides.css'
        ],
    }
});

editor.on('asset:remove', asset => {
    fetch('/admin.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            action: 'delete_image',
            csrf_token: window.getCsrfToken(),
            src: asset.get('src'),
        }),
    })
        .then(response => response)
        .catch(error => {
            console.error('Delete failed:', error);
        });
});

document.getElementById('save-slide').addEventListener('click', () => {
    document.getElementById('slide-content').value =
        editor.getHtml() +
        '<style>' +
        editor.getCss() +
        '</style>';
});