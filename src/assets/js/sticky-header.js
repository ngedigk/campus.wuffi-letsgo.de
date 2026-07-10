document.addEventListener('DOMContentLoaded', function () {
    const header = document.getElementById('headerMain');
    const placeholder = document.getElementById('headerPlaceholder');

    const headerOffset = header.offsetTop;

    window.addEventListener('scroll', function () {
        if (window.scrollY > headerOffset) {
            if (!header.classList.contains('fixed')) {
                placeholder.style.height = header.offsetHeight + 'px';
                placeholder.style.display = 'block';
                header.classList.add('fixed');
            }
        } else {
            header.classList.remove('fixed');
            placeholder.style.display = 'none';
        }
    });
});