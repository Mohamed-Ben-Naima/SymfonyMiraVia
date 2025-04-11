document.addEventListener('DOMContentLoaded', function() {
    // Password toggle functionality
    const togglePassword = document.querySelector('.toggle-password');
    if (togglePassword) {
        togglePassword.addEventListener('click', function() {
            const passwordInput = document.querySelector('.password-input');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        });
    }

    // Password strength validation
    const passwordInput = document.querySelector('.password-input');
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            const value = this.value;
            const requirements = {
                upper: /[A-Z]/.test(value),
                lower: /[a-z]/.test(value),
                number: /[0-9]/.test(value),
                special: /[@$!%*?&]/.test(value),
                length: value.length >= 8
            };

            // Update requirement indicators
            Object.keys(requirements).forEach(key => {
                const element = document.querySelector(`.req-${key} i`);
                if (element) {
                    if (requirements[key]) {
                        element.classList.remove('bi-x-circle-fill', 'text-danger');
                        element.classList.add('bi-check-circle-fill', 'text-success');
                    } else {
                        element.classList.remove('bi-check-circle-fill', 'text-success');
                        element.classList.add('bi-x-circle-fill', 'text-danger');
                    }
                }
            });
        });
    }

    // Form validation
    const forms = document.querySelectorAll('.needs-validation');
    Array.prototype.slice.call(forms).forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }

            form.classList.add('was-validated');
        }, false);
    });
});