document.querySelectorAll('.password-toggle').forEach(button => {

    button.addEventListener('click', function () {

        const input = document.getElementById(this.dataset.target);
        const icon = this.querySelector('img');

        if (!input) {
            return;
        }

        const isPassword = input.type === 'password';

        input.type = isPassword ? 'text' : 'password';

        this.setAttribute(
            'aria-label',
            isPassword ? 'Passwort verbergen' : 'Passwort anzeigen'
        );

        this.setAttribute(
            'aria-pressed',
            isPassword ? 'true' : 'false'
        );

        if (icon) {
            const currentSrc = icon.getAttribute('src');
            const altSrc = icon.dataset.altIcon;
            icon.setAttribute('src', altSrc);
            icon.dataset.altIcon = currentSrc;
        }

    });

});