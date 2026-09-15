function openModal(modalId) {
    const modal = document.getElementById(modalId);

    if (!modal) {
        console.warn(`Modal not found: ${modalId}`);
        return;
    }

    modal.classList.remove('hidden');
    modal.classList.add('flex');

    document.body.classList.add('overflow-hidden');
}


function closeModal(modalId) {
    const modal = document.getElementById(modalId);

    if (!modal) {
        console.warn(`Modal not found: ${modalId}`);
        return;
    }

    modal.classList.add('hidden');
    modal.classList.remove('flex');

    document.body.classList.remove('overflow-hidden');
}


function clearErrors() {
    document.querySelectorAll('[id^="error-"]').forEach(function (el) {
        el.textContent = '';
        el.classList.add('hidden');
    });
}


function showErrors(errors) {
    clearErrors();

    for (const field in errors) {
        const errorEl = document.getElementById('error-' + field);

        if (errorEl) {
            errorEl.textContent = errors[field][0];
            errorEl.classList.remove('hidden');
        }
    }
}


function showConfirmation() {
    clearErrors();

    const form = document.getElementById('addKnowledgeForm');

    const categoryInput = form.querySelector('[name="category"]');
    const titleInput = form.querySelector('[name="title"]');
    const descriptionInput = form.querySelector('[name="description"]');
    const troubleshootingInput = form.querySelector('[name="troubleshooting_steps"]');

    const category = categoryInput ? categoryInput.value : '';
    const title = titleInput ? titleInput.value : '';
    const description = descriptionInput ? descriptionInput.value : '';
    const troubleshooting = troubleshootingInput ? troubleshootingInput.value : '';

    document.getElementById('confirmCategory').textContent = category || '-';
    document.getElementById('confirmTitle').textContent = title || '-';
    document.getElementById('confirmDescription').textContent = description || '-';
    document.getElementById('confirmTroubleshooting').textContent = troubleshooting || '-';

    closeModal('add-knowledge-modal');
    openModal('confirm-knowledge-modal');
}


function confirmKnowledge() {
    closeModal('confirm-knowledge-modal');
    openModal('knowledge-loading');

    const form = document.getElementById('addKnowledgeForm');
    const formData = new FormData(form);

    const csrfToken = document.querySelector('meta[name="csrf-token"]');

    if (!csrfToken) {
        console.error('CSRF token meta tag not found.');
        closeModal('knowledge-loading');
        openModal('add-knowledge-modal');
        return;
    }

    fetch('/admin/knowledge', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
            'Accept': 'application/json',
        },
        body: formData,
    })
    .then(function (response) {
        return response.json().then(function (data) {
            return { status: response.status, data: data };
        });
    })
    .then(function (result) {
        closeModal('knowledge-loading');

        if (result.data.success) {
            openModal('knowledge-success');

            var container = document.getElementById('knowledge-container');
            var emptyMsg = container.querySelector('.col-span-4');
            if (emptyMsg) emptyMsg.remove();

            if (result.data.html) {
                container.insertAdjacentHTML('afterbegin', result.data.html);
            }

            setTimeout(function () {
                closeModal('knowledge-success');
                form.reset();
            }, 2000);

        } else if (result.status === 422 && result.data.errors) {
            showErrors(result.data.errors);
            openModal('add-knowledge-modal');

        } else {
            alert(result.data.message || 'Something went wrong.');
            openModal('confirm-knowledge-modal');
        }
    })
    .catch(function (error) {
        console.error('Error:', error);
        closeModal('knowledge-loading');
        alert('An error occurred. Please try again.');
        openModal('confirm-knowledge-modal');
    });
}


document.addEventListener('click', function (event) {

    const closeButton = event.target.closest('[data-modal-close]');

    if (closeButton) {

        event.preventDefault();

        const modalId = closeButton.getAttribute('data-modal-close');

        closeModal(modalId);

        return;
    }

    if (event.target.matches('[data-modal]')) {

        if (!event.target.hasAttribute('data-modal-static')) {
            closeModal(event.target.id);

            if (!document.querySelector('[data-modal]:not(.hidden)')) {
                document.body.classList.remove('overflow-hidden');
            }
        }

    }

});


document.addEventListener('keydown', function (event) {

    if (event.key !== 'Escape') {
        return;
    }

    const modal = document.querySelector(
        '[data-modal]:not(.hidden):not([data-modal-static])'
    );

    if (modal) {

        closeModal(modal.id);

        if (!document.querySelector('[data-modal]:not(.hidden)')) {
            document.body.classList.remove('overflow-hidden');
        }

    }

});
