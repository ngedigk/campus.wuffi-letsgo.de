export default function initEditor(options = {}) {

    const existingContent = document.querySelector('#slide-content').value;

    const parser = new DOMParser();
    const doc = parser.parseFromString(existingContent, 'text/html');

    const html = doc.body.innerHTML;

    let css = '';

    doc.querySelectorAll('style').forEach(style => {
        css += style.innerHTML;
    });

    return grapesjs.init({
        container: '#gjs',
        height: '500px',

        components: html,
        style: css,

        storageManager: false,

        ...options
    });
}