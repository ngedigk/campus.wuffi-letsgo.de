document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.password-meter').forEach(meter => {
        const form = meter.closest('form');
        if (!form) return;

        const passwordInput = form.querySelector('#password');
        if (!passwordInput) return;

        const progress = meter.querySelector('#password-progress');
        const label = meter.querySelector('#password-label');

        const confirmInput = form.querySelector('[name="password_confirm"]');

        let statusDiv = meter.querySelector('.pw-status');

        if (!statusDiv && confirmInput) {
            statusDiv = document.createElement('div');
            statusDiv.className = 'pw-status';
            meter.appendChild(statusDiv);
        }

        const update = () => {
            const val = passwordInput.value;

            let score = 0;

            if (val.length >= 12) score++;
            if (/[A-Z]/.test(val)) score++;
            if (/[a-z]/.test(val)) score++;
            if (/[0-9]/.test(val)) score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;

            const levels = [
                { className: 'empty',       label: 'Passwort eingeben' },
                { className: 'very-weak',   label: 'Sehr schwach' },
                { className: 'weak',        label: 'Schwach' },
                { className: 'fair',        label: 'Fair' },
                { className: 'strong',      label: 'Stark' },
                { className: 'very-strong', label: 'Sehr stark' }
            ];

            const level = levels[score];

            // Let CSS handle width and colors
            progress.classList.remove(
                'empty',
                'very-weak',
                'weak',
                'fair',
                'strong',
                'very-strong'
            );

            progress.classList.add(level.className);

            label.textContent = level.label;

            // Password confirmation
            const submitBtn = form.querySelector('button[type="submit"]');

            if (confirmInput && confirmInput.value) {
                const match = val === confirmInput.value;

                statusDiv.textContent = match
                    ? 'Passwörter stimmen überein'
                    : 'Passwörter stimmen nicht überein';

                statusDiv.classList.remove('match', 'no-match');
                statusDiv.classList.add(match ? 'match' : 'no-match');

                if (submitBtn) {
                    submitBtn.disabled = !match;
                }
            } else {
                if (statusDiv) {
                    statusDiv.textContent = '';
                    statusDiv.classList.remove('match', 'no-match');
                }

                if (submitBtn) {
                    submitBtn.disabled = false;
                }
            }
        };

        passwordInput.addEventListener('input', update);

        if (confirmInput) {
            confirmInput.addEventListener('input', update);
        }

        update();
    });
});