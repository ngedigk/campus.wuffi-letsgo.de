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
        upload: '/admin/courses/upload-image',
        uploadName: 'files',

        params: {
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

function selectAsset(asset, complete = false) {
    const component = editor.getSelected();

    if (!component || !component.is('image')) {
        return;
    }

    const src = asset.getSrc();

    component.set('src', src);

    requestAnimationFrame(() => {
        highlightAsset(src);
    });

    if (complete) {
        editor.AssetManager.close();
    }
}

function openAssetManager() {
    editor.AssetManager.open({
        types: ['image'],

        select(asset, complete) {
            selectAsset(asset, complete);
        }
    });
}

let isUploading = false;
let uploadedImageAssigned = false;

editor.on('asset:upload:start', () => {
    isUploading = true;
    uploadedImageAssigned = false;
});

editor.on('asset:add', asset => {
    if (!isUploading || uploadedImageAssigned) {
        return;
    }

    const component = editor.getSelected();

    if (!component || !component.is('image')) {
        return;
    }

    selectAsset(asset);

    uploadedImageAssigned = true;
});

editor.on('asset:upload:end', () => {
    isUploading = false;
});

function highlightAsset(src) {
    const url = new URL(src, window.location.origin);
    const filename = decodeURIComponent(
        url.pathname.split('/').pop()
    );

    document.querySelectorAll('.gjs-am-asset').forEach(el => {
        const name = el.querySelector('.gjs-am-name');

        if (!name) {
            return;
        }

        const assetName = name.textContent.trim();

        el.classList.toggle(
            'gjs-am-highlight',
            assetName === filename
        );
    });
}

editor.on('asset:remove', asset => {
    fetch('/admin/courses/delete-image', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
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