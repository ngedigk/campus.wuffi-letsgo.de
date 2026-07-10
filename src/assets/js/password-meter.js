const password = document.getElementById('password');
const progress = document.getElementById('password-progress');
const label = document.getElementById('password-label');

password.addEventListener('input', () => {

    const value = password.value;

    let score = 0;

    if (value.length >= 12) {
        score++;
    }

    if (/[A-Z]/.test(value)) {
        score++;
    }

    if (/[a-z]/.test(value)) {
        score++;
    }

    if (/[0-9]/.test(value)) {
        score++;
    }

    if (/[^A-Za-z0-9]/.test(value)) {
        score++;
    }

    const levels = [
        {
            width: "0%",
            text: "Enter a password",
            color: "#ef4444"
        },
        {
            width: "20%",
            text: "Very weak",
            color: "#ef4444"
        },
        {
            width: "40%",
            text: "Weak",
            color: "#f97316"
        },
        {
            width: "60%",
            text: "Fair",
            color: "#eab308"
        },
        {
            width: "80%",
            text: "Strong",
            color: "#84cc16"
        },
        {
            width: "100%",
            text: "Very strong",
            color: "#22c55e"
        }
    ];

    progress.style.width = levels[score].width;
    progress.style.backgroundColor = levels[score].color;
    label.textContent = levels[score].text;

});