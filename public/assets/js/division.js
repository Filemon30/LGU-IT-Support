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
    const officeInput = document.getElementById('division');
    const errorMsg = document.getElementById('division-error');

    if (officeInput && errorMsg) {
        officeInput.addEventListener('input', function () {
            errorMsg.classList.add('hidden');
        });
    }
});

function showConfirmDivisionName() {
    const officeInput = document.getElementById('division');
    const errorMsg = document.getElementById('division-error');
    const confirmName = document.getElementById('confirm-division-name');

    if (!officeInput.value.trim()) {
        errorMsg.classList.remove('hidden');
        return;
    }

    errorMsg.classList.add('hidden');
    confirmName.textContent = officeInput.value;

    // Close add modal and open confirmation
    const addModal = document.getElementById('add-division');
    addModal.classList.add('hidden');
    addModal.classList.remove('flex');

    const confirmModal = document.getElementById('add-division-confirmation');
    confirmModal.classList.remove('hidden');
    confirmModal.classList.add('flex');
}

function confirmAddDivision() {
    // =========================
    // CLOSE CONFIRMATION
    // =========================
    const confirmModal = document.getElementById('add-division-confirmation');

    confirmModal.classList.add('hidden');
    confirmModal.classList.remove('flex');


    // =========================
    // SHOW LOADING
    // =========================
    const loadingModal = document.getElementById('add-division-loading');

    loadingModal.classList.remove('hidden');
    loadingModal.classList.add('flex');


    // =========================
    // LOADING → GENERATING
    // =========================
    setTimeout(() => {

        loadingModal.classList.add('hidden');
        loadingModal.classList.remove('flex');


        const generatingModal = document.getElementById('generating-key-loading');

        generatingModal.classList.remove('hidden');
        generatingModal.classList.add('flex');


        // =========================
        // GENERATING → SUCCESS
        // =========================
        setTimeout(() => {

            generatingModal.classList.add('hidden');
            generatingModal.classList.remove('flex');


            const successModal = document.getElementById('add-division-success');

            successModal.classList.remove('hidden');
            successModal.classList.add('flex');

            
        }, 3000);

    }, 2000);
}