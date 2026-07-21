const existingContent = document.querySelector('#slide-content').value;

const parser = new DOMParser();
const doc = parser.parseFromString(existingContent, 'text/html');

const html = doc.body.innerHTML;

let css = '';

doc.querySelectorAll('style').forEach(style => {
    css += style.innerHTML;
});

const editor = grapesjs.init({
    container: '#gjs',
    height: '500px',

    components: html,
    style: css,

    storageManager: false,

    assetManager: {
        upload: '/upload-image.php',
        uploadName: 'files',

        assets: [],

        params: {
            csrf_token: '<?= csrfToken() ?>'
        }
    },

    canvas: {
        styles: [
            '/assets/css/style.css'
        ],
    },

    plugins: [
        'gjs-blocks-basic'
    ],

    pluginsOpts: {
        'gjs-blocks-basic': {
            'flexGrid': true
        }
    }
});