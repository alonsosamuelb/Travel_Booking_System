document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-toggle-password]').forEach((button) => {
        button.addEventListener('click', () => {
            const target = document.querySelector(button.dataset.togglePassword);
            if (!target) return;
            target.type = target.type === 'password' ? 'text' : 'password';
            button.textContent = target.type === 'password' ? 'Show' : 'Hide';
        });
    });

    document.querySelectorAll('form[data-validate="true"]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            const requiredFields = form.querySelectorAll('[data-required="true"]');
            let hasErrors = false;

            requiredFields.forEach((field) => {
                const feedback = field.parentElement.querySelector('.invalid-feedback');
                if (field.value.trim() === '') {
                    field.classList.add('is-invalid');
                    if (feedback) feedback.textContent = 'This field is required.';
                    hasErrors = true;
                } else {
                    field.classList.remove('is-invalid');
                }
            });

            const email = form.querySelector('input[type="email"]');
            if (email && email.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
                email.classList.add('is-invalid');
                hasErrors = true;
            }

            const passwords = form.querySelectorAll('[data-password]');
            if (passwords.length === 2 && passwords[0].value !== passwords[1].value) {
                passwords.forEach((field) => field.classList.add('is-invalid'));
                hasErrors = true;
            }

            const seats = form.querySelector('[name="seats_reserved"]');
            if (seats && Number(seats.value) < 1) {
                seats.classList.add('is-invalid');
                hasErrors = true;
            }

            if (hasErrors) {
                event.preventDefault();
            }
        });
    });
});
