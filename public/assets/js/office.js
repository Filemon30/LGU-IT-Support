document.addEventListener('click', function (event) {

    // Open modal
    const openButton = event.target.closest('[data-modal-open]');

    if (openButton) {
        const modalId = openButton.dataset.modalOpen;
        const modal = document.getElementById(modalId);

        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }
    }

    // Close modal
    const closeButton = event.target.closest('[data-modal-close]');

    if (closeButton) {
        const modalId = closeButton.dataset.modalClose;
        const modal = document.getElementById(modalId);

        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }
    }

});

document.addEventListener('DOMContentLoaded', function () {
    const officeInput = document.getElementById('office');
    const errorMsg = document.getElementById('office-error');

    if (officeInput && errorMsg) {
        officeInput.addEventListener('input', function () {
            errorMsg.classList.add('hidden');
        });
    }
});

function showConfirmOfficeName() {
    const officeInput = document.getElementById('office');
    const errorMsg = document.getElementById('office-error');
    const confirmName = document.getElementById('confirm-office-name');

    if (!officeInput.value.trim()) {
        errorMsg.classList.remove('hidden');
        return;
    }

    errorMsg.classList.add('hidden');
    confirmName.textContent = officeInput.value;

    // Close add modal and open confirmation
    const addModal = document.getElementById('add-office');
    addModal.classList.add('hidden');
    addModal.classList.remove('flex');

    const confirmModal = document.getElementById('add-office-confirmation');
    confirmModal.classList.remove('hidden');
    confirmModal.classList.add('flex');
}

function confirmAddOffice() {
    // Close confirmation modal
    const confirmModal = document.getElementById('add-office-confirmation');
    confirmModal.classList.add('hidden');
    confirmModal.classList.remove('flex');

    // Show loading modal
    const loadingModal = document.getElementById('add-office-loading');
    loadingModal.classList.remove('hidden');
    loadingModal.classList.add('flex');

    // After 2 seconds, hide loading and show success
    setTimeout(() => {
        loadingModal.classList.add('hidden');
        loadingModal.classList.remove('flex');

        const successModal = document.getElementById('add-office-success');
        successModal.classList.remove('hidden');
        successModal.classList.add('flex');

        // Auto close success modal and redirect after 2 seconds
        setTimeout(() => {
            successModal.classList.add('hidden');
            successModal.classList.remove('flex');
            window.location.href = '/admin/offices';
        }, 2000);
    }, 2000);
}