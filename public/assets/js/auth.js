document.querySelectorAll('.password-field').forEach(function (wrapper) {

    var input = wrapper.querySelector('input');
    var toggle = wrapper.querySelector('.input-icon-right');

    if (!input || !toggle) return;

    toggle.addEventListener('click', function () {

        var hidden = input.type === 'password';

        input.type = hidden ? 'text' : 'password';

        toggle.querySelector('i').className =
            hidden
                ? 'ti ti-eye'
                : 'ti ti-eye-off';
    });
});


// ========================================
// FIX FLOATING LABELS ON AUTOFILL
// ========================================

function initFloatingLabels() {

    document.querySelectorAll('.floating-input input').forEach(function (input) {

        function checkValue() {
            var wrapper = input.closest('.floating-input');
            if (!wrapper) return;

            if (input.value.trim()) {
                wrapper.classList.add('has-value');
            } else {
                wrapper.classList.remove('has-value');
            }
        }

        checkValue();
        input.addEventListener('input', checkValue);
        input.addEventListener('change', checkValue);
    });

}

document.addEventListener('DOMContentLoaded', function () {
    initFloatingLabels();
    setTimeout(initFloatingLabels, 200);
    setTimeout(initFloatingLabels, 500);
});


// ========================================
// LOGIN VALIDATION
// ========================================

var loginForm = document.getElementById('login-form');

if (loginForm) {

    loginForm.addEventListener('submit', function (event) {

        var isValid = true;

        loginForm.querySelectorAll('.floating-input').forEach(function (wrapper) {

            var input = wrapper.querySelector('input');
            var errorText = wrapper.querySelector('.error-text');

            if (!input) return;

            var empty = !input.value.trim();

            // Add red error state when empty
            wrapper.classList.toggle('input-error', empty);

            if (empty) {

                isValid = false;

                if (errorText) {
                    errorText.textContent = 'Must not be empty.';
                }

            } else {

                if (errorText) {
                    errorText.textContent = '';
                }
            }
        });


        // Block submission if any input is empty
        if (!isValid) {
            event.preventDefault();
        }
    });


    // ========================================
    // CLEAR ERROR WHEN USER TYPES
    // ========================================

    loginForm.querySelectorAll('.floating-input input').forEach(function (input) {

        input.addEventListener('input', function () {

            var wrapper = input.closest('.floating-input');

            if (!wrapper) return;

            var errorText = wrapper.querySelector('.error-text');

            if (input.value.trim()) {

                // Remove red border and icon
                wrapper.classList.remove('input-error');

                // Remove error message
                if (errorText) {
                    errorText.textContent = '';
                }
            }
        });
    });
}

function openModal(modalId) {

    var modal = document.getElementById(modalId);

    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
    }

}

function closeModal(modalId) {

    var modal = document.getElementById(modalId);

    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
    }

}

document.addEventListener('click', function (e) {

    var openBtn = e.target.closest('[data-modal-open]');
    if (openBtn) {
        openModal(openBtn.dataset.modalOpen);
    }

    var closeBtn = e.target.closest('[data-modal-close]');
    if (closeBtn) {
        closeModal(closeBtn.dataset.modalClose);
    }

    if (e.target.hasAttribute('data-modal')) {
        closeModal(e.target.id);
    }

});