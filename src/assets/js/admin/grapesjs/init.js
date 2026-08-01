export default function initEditor(options = {}) {
    const existingContent = document.querySelector('#slide-content').value;

    const parser = new DOMParser();
    const doc = parser.parseFromString(existingContent, 'text/html');

    const html = doc.body.innerHTML;

    let css = '';

    doc.querySelectorAll('style').forEach(style => {
        css += style.innerHTML;
    });

    const csrfToken = window.getCsrfToken();

    return grapesjs.init({
        container: '#gjs',
        height: '500px',

        components: html,
        style: css,

        storageManager: false,

        assetManager: {
            upload: '/admin.php',
            uploadName: 'files',

            assets: [],

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
        },

        ...options
    });
}