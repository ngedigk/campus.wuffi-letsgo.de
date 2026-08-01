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
            statusDiv.style.marginTop = '6px';
            statusDiv.style.fontSize = '0.85em';
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
                { w: "0%", t: "Passwort eingeben", c: "#ef4444" },
                { w: "20%", t: "Sehr schwach", c: "#ef4444" },
                { w: "40%", t: "Schwach", c: "#f97316" },
                { w: "60%", t: "Fair", c: "#eab308" },
                { w: "80%", t: "Stark", c: "#84cc16" },
                { w: "100%", t: "Sehr stark", c: "#22c55e" }
            ];

            progress.style.width = levels[score].w;
            progress.style.backgroundColor = levels[score].c;

            let text = levels[score].t;

            const submitBtn = form.querySelector('button[type="submit"]');

            if (confirmInput && confirmInput.value) {
                const match = val === confirmInput.value;
                statusDiv.textContent = match ? 'Passwörter stimmen überein' : 'Passwörter stimmen nicht überein';
                statusDiv.style.color = match ? '#22c55e' : '#ef4444';

                if (submitBtn) {
                    submitBtn.disabled = !match;
                }
            } else {
                if (statusDiv) statusDiv.textContent = '';
                if (submitBtn) submitBtn.disabled = false;
            }

            label.textContent = text;
        };

        passwordInput.addEventListener('input', update);
        if (confirmInput) {
            confirmInput.addEventListener('input', update);
        }
    });
});

