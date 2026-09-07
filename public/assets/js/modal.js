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
}


/*
|--------------------------------------------------------------------------
| Modal Click Events
|--------------------------------------------------------------------------
*/

document.addEventListener('click', function (event) {

    /*
    |--------------------------------------------------------------------------
    | Close + Open Another Modal
    |--------------------------------------------------------------------------
    */

    const switchButton = event.target.closest(
        '[data-modal-close][data-modal-open]'
    );

    if (switchButton) {

        event.preventDefault();

        const closeModalId = switchButton.getAttribute('data-modal-close');
        const openModalId = switchButton.getAttribute('data-modal-open');

        // Close current modal
        closeModal(closeModalId);

        // Open next modal
        openModal(openModalId);

        return;
    }


    /*
    |--------------------------------------------------------------------------
    | Open Modal
    |--------------------------------------------------------------------------
    */

    const openButton = event.target.closest('[data-modal-open]');

    if (openButton) {

        event.preventDefault();

        const modalId = openButton.getAttribute('data-modal-open');

        openModal(modalId);

        return;
    }


    /*
    |--------------------------------------------------------------------------
    | Close Modal
    |--------------------------------------------------------------------------
    */

    const closeButton = event.target.closest('[data-modal-close]');

    if (closeButton) {

        event.preventDefault();

        const modalId = closeButton.getAttribute('data-modal-close');

        closeModal(modalId);

        // Remove body lock only if there are no other open modals
        if (!document.querySelector('[data-modal]:not(.hidden)')) {
            document.body.classList.remove('overflow-hidden');
        }

        return;
    }


    /*
    |--------------------------------------------------------------------------
    | Close By Clicking Backdrop
    |--------------------------------------------------------------------------
    */

    if (event.target.matches('[data-modal]')) {

        closeModal(event.target.id);

        if (!document.querySelector('[data-modal]:not(.hidden)')) {
            document.body.classList.remove('overflow-hidden');
        }

    }

});


/*
|--------------------------------------------------------------------------
| Close With ESC
|--------------------------------------------------------------------------
*/

document.addEventListener('keydown', function (event) {

    if (event.key !== 'Escape') {
        return;
    }

    const modal = document.querySelector(
        '[data-modal]:not(.hidden)'
    );

    if (modal) {

        closeModal(modal.id);

        if (!document.querySelector('[data-modal]:not(.hidden)')) {
            document.body.classList.remove('overflow-hidden');
        }

    }

});