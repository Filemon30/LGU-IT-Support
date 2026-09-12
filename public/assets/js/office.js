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

function getCsrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
}

function insertRowSorted(tbody, newRow) {
    const newRef = newRow.querySelector('td')?.textContent?.trim() || '';
    const rows = tbody.querySelectorAll('tr');

    for (let i = 0; i < rows.length; i++) {
        const existingRef = rows[i].querySelector('td')?.textContent?.trim() || '';
        if (newRef < existingRef) {
            tbody.insertBefore(newRow, rows[i]);
            return;
        }
    }
    tbody.appendChild(newRow);
}

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
    const officeInput = document.getElementById('office');
    const officeName = officeInput.value.trim();

    // Close confirmation modal
    const confirmModal = document.getElementById('add-office-confirmation');
    confirmModal.classList.add('hidden');
    confirmModal.classList.remove('flex');

    // Show loading modal
    const loadingModal = document.getElementById('add-office-loading');
    loadingModal.classList.remove('hidden');
    loadingModal.classList.add('flex');

    fetch('/admin/offices', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken(),
            'Accept': 'application/json',
        },
        body: JSON.stringify({ office_name: officeName }),
    })
    .then(response => response.json())
    .then(data => {
        // Hide loading modal
        loadingModal.classList.add('hidden');
        loadingModal.classList.remove('flex');

        if (data.success) {
            // Show success modal
            const successModal = document.getElementById('add-office-success');
            successModal.classList.remove('hidden');
            successModal.classList.add('flex');

            const office = data.office;

            // Append new row directly to table
            const tbody = document.querySelector('table tbody');
            const emptyRow = tbody.querySelector('td[colspan]');
            if (emptyRow) emptyRow.closest('tr').remove();

            const row = document.createElement('tr');
            row.className = 'border-b border-gray-100 transition-colors hover:bg-gray-50';
            row.innerHTML = `
                <td class="whitespace-nowrap px-4 py-3 text-gray-900">${office.office_ref_num}</td>
                <td class="whitespace-nowrap px-4 py-3 text-gray-900">${office.office_name}</td>
                <td class="whitespace-nowrap px-4 py-3 text-gray-900">0</td>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-1.5">
                        <a href="/admin/offices/${office.office_id}/divisions" class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-md bg-blue-600 hover:bg-blue-700 hover:shadow-sm active:scale-95 transition-all duration-200 ease-in-out" title="View Office">
                            <i class="ti ti-eye text-xs text-white"></i>
                        </a>
                    </div>
                </td>
            `;
            insertRowSorted(tbody, row);

            // Reset form
            officeInput.value = '';

            // Auto close success modal after 2 seconds
            setTimeout(() => {
                successModal.classList.add('hidden');
                successModal.classList.remove('flex');
            }, 2000);
        } else {
            // Show error (e.g. duplicate office)
            if (data.errors && data.errors.office_name) {
                const duplicateError = document.getElementById('office-duplicate-error');
                if (duplicateError) {
                    duplicateError.textContent = data.errors.office_name[0];
                    duplicateError.classList.remove('hidden');
                }
            }

            // Re-open add modal
            const addModal = document.getElementById('add-office');
            addModal.classList.remove('hidden');
            addModal.classList.add('flex');
        }
    })
    .catch(error => {
        // Hide loading modal
        loadingModal.classList.add('hidden');
        loadingModal.classList.remove('flex');

        // Re-open add modal
        const addModal = document.getElementById('add-office');
        addModal.classList.remove('hidden');
        addModal.classList.add('flex');
    });
}

let searchTimeout = null;

function debounceSearch() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        searchOffices();
    }, 300);
}

function searchOffices() {
    const searchInput = document.querySelector('input[name="search"]');
    const search = searchInput ? searchInput.value.trim() : '';

    fetch('/admin/offices/search', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken(),
            'Accept': 'application/json',
        },
        body: JSON.stringify({ search: search }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const tbody = document.querySelector('table tbody');
            if (tbody) {
                tbody.innerHTML = data.html;
            }
        }
    })
    .catch(error => {});
}
