import initEditor from './init.js';

import headlinePlugin from './plugins/headline.js';
import listPlugin from './plugins/lists.js';

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
        assets: window.existingAssets
    }
});

document.getElementById('save-slide').addEventListener('click', () => {
    document.getElementById('slide-content').value =
        editor.getHtml() +
        '<style>' +
        editor.getCss() +
        '</style>';
});