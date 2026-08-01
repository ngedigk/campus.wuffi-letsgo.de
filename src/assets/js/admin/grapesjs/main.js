import initEditor from './init.js';

import headlinePlugin from './plugins/headline.js';
import listPlugin from './plugins/lists.js';

initEditor({
    plugins: [
        'gjs-blocks-basic',
        headlinePlugin,
        listPlugin
    ],

    pluginsOpts: {
        'gjs-blocks-basic': {
            flexGrid: true
        }
    }
});