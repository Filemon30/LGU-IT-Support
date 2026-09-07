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

function confirmAddStaff() {
    // Close confirmation modal
    const confirmModal = document.getElementById('confirmation-staff-modal');
    confirmModal.classList.add('hidden');
    confirmModal.classList.remove('flex');

    // Show loading modal
    const loadingModal = document.getElementById('add-staff-loading');
    loadingModal.classList.remove('hidden');
    loadingModal.classList.add('flex');

    // After 2 seconds, hide loading and show success
    setTimeout(() => {
        loadingModal.classList.add('hidden');
        loadingModal.classList.remove('flex');

        const successModal = document.getElementById('add-staff-success');
        successModal.classList.remove('hidden');
        successModal.classList.add('flex');

        // Auto close success modal and redirect after 2 seconds
        setTimeout(() => {
            successModal.classList.add('hidden');
            successModal.classList.remove('flex');
            window.location.href = '/admin/staff';
        }, 2000);
    }, 2000);
}

function confirmArchivedStaff()
{
    const confirmModal = document.getElementById('archive-confirmation-modal');
            confirmModal.classList.add('hidden');
            confirmModal.classList.remove('flex');

            // Show loading modal
            const loadingModal = document.getElementById('archive-staff-loading');
            loadingModal.classList.remove('hidden');
            loadingModal.classList.add('flex');

            // After 2 seconds, hide loading and show success
            setTimeout(() => {
                loadingModal.classList.add('hidden');
                loadingModal.classList.remove('flex');

                const successModal = document.getElementById('archive-staff-success');
                successModal.classList.remove('hidden');
                successModal.classList.add('flex');

                // Auto close success modal and redirect after 2 seconds
                setTimeout(() => {
                    successModal.classList.add('hidden');
                    successModal.classList.remove('flex');
                    window.location.href = '/admin/staff';
                }, 2000);
            }, 2000);
}

function confirmArchivedStaff()
{
    const confirmModal = document.getElementById('archive-confirmation-modal');
            confirmModal.classList.add('hidden');
            confirmModal.classList.remove('flex');

            // Show loading modal
            const loadingModal = document.getElementById('archive-staff-loading');
            loadingModal.classList.remove('hidden');
            loadingModal.classList.add('flex');

            // After 2 seconds, hide loading and show success
            setTimeout(() => {
                loadingModal.classList.add('hidden');
                loadingModal.classList.remove('flex');

                const successModal = document.getElementById('archive-staff-success');
                successModal.classList.remove('hidden');
                successModal.classList.add('flex');

                // Auto close success modal and redirect after 2 seconds
                setTimeout(() => {
                    successModal.classList.add('hidden');
                    successModal.classList.remove('flex');
                    window.location.href = '/admin/staff';
                }, 2000);
            }, 2000);
}


function confirmUnarchivedStaff()
{
    const confirmModal = document.getElementById('unarchive-confirmation-modal');
            confirmModal.classList.add('hidden');
            confirmModal.classList.remove('flex');

            // Show loading modal
            const loadingModal = document.getElementById('unarchive-staff-loading');
            loadingModal.classList.remove('hidden');
            loadingModal.classList.add('flex');

            // After 2 seconds, hide loading and show success
            setTimeout(() => {
                loadingModal.classList.add('hidden');
                loadingModal.classList.remove('flex');

                const successModal = document.getElementById('unarchive-staff-success');
                successModal.classList.remove('hidden');
                successModal.classList.add('flex');

                // Auto close success modal and redirect after 2 seconds
                setTimeout(() => {
                    successModal.classList.add('hidden');
                    successModal.classList.remove('flex');
                    window.location.href = '/admin/staff/staff_archives';
                }, 2000);
            }, 2000);
}


function toggleRequestType(type) {

    var passwordContaier = document.getElementById('password-container');
    var statusContainer = document.getElementById('status-container');

    if (type === 'password'){
        statusContainer.classList.remove('hidden');
        passwordContaier.classList.add('hidden');
    } else {
        passwordContaier.classList.remove('hidden');
        statusContainer.classList.add('hidden');
    }
}