document.addEventListener('DOMContentLoaded', function () {
    const barangayInput = document.getElementById('barangay');
    const errorMsg = document.getElementById('barangay-error');

    if (barangayInput && errorMsg) {
        barangayInput.addEventListener('input', function () {
            errorMsg.classList.add('hidden');
        });
    }
});

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

        if (modalId === 'barangay-information-modal' && openButton.dataset.ref) {
            document.getElementById('view-barangay-ref').textContent = openButton.dataset.ref;
            document.getElementById('view-barangay-name').textContent = openButton.dataset.name;
            document.getElementById('view-barangay-key').textContent = openButton.dataset.key || 'N/A';

            const statusEl = document.getElementById('view-barangay-status');
            const status = openButton.dataset.status;
            const color = status === 'Active'
                ? 'background-color: rgba(34, 197, 94, 0.10); color: #4ade80; border-color: rgba(34, 197, 94, 0.30);'
                : 'background-color: rgba(239, 68, 68, 0.10); color: #f87171; border-color: rgba(239, 68, 68, 0.30);';
            const icon = status === 'Active' ? 'ti ti-circle-check' : 'ti ti-circle-x';

            statusEl.innerHTML = `
                <span class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-semibold whitespace-nowrap" style="${color}">
                    <i class="${icon} text-[0.7rem]"></i>
                    <span class="text-[0.7rem]">${status}</span>
                </span>
            `;
        }

        if (modalId === 'update-barangay-modal' && openButton.dataset.id) {
            const idInput = document.getElementById('update-barangay-id');
            idInput.value = openButton.dataset.id;
            idInput.dataset.currentStatus = openButton.dataset.status;
            document.getElementById('barangay_name').value = openButton.dataset.name;

            const statusInput = document.querySelector('#update-barangay-modal [name="key_status"]');
            if (statusInput) statusInput.value = openButton.dataset.status;
            const label = document.querySelector('#update-barangay-modal [data-dropdown-label]');
            if (label) {
                label.textContent = openButton.dataset.status;
                label.style.color = '#1f2937';
            }

            document.getElementById('update-name-error').classList.add('hidden');
            document.getElementById('update-status-error').classList.add('hidden');
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

function showConfirmBarangayName() {
    const barangayInput = document.getElementById('barangay');
    const errorMsg = document.getElementById('barangay-error');
    const confirmName = document.getElementById('confirm-barangay-name');

    if (!barangayInput.value.trim()) {
        errorMsg.classList.remove('hidden');
        return;
    }

    errorMsg.classList.add('hidden');
    confirmName.textContent = barangayInput.value;

    // Close add modal and open confirmation
    const addModal = document.getElementById('add-barangay');
    addModal.classList.add('hidden');
    addModal.classList.remove('flex');

    const confirmModal = document.getElementById('add-barangay-confirmation');
    confirmModal.classList.remove('hidden');
    confirmModal.classList.add('flex');
}

function confirmAddBarangay() {
    const confirmModal = document.getElementById('add-barangay-confirmation');
    confirmModal.classList.add('hidden');
    confirmModal.classList.remove('flex');

    const loadingModal = document.getElementById('add-barangay-loading');
    loadingModal.classList.remove('hidden');
    loadingModal.classList.add('flex');

    const barangayInput = document.getElementById('barangay');
    const barangayName = barangayInput.value.trim();

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    fetch('/admin/barangays', {
        method: 'POST',
        body: new URLSearchParams({ barangay_name: barangayName }),
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        },
    })
    .then(response => response.json().then(body => ({ status: response.status, body })))
    .then(({ status, body }) => {
        loadingModal.classList.add('hidden');
        loadingModal.classList.remove('flex');

        if (status === 422 && body.errors) {
            const errorMsg = document.getElementById('barangay-error');
            const messages = Object.values(body.errors).flat();
            errorMsg.textContent = messages[0];
            errorMsg.classList.remove('hidden');

            const addModal = document.getElementById('add-barangay');
            addModal.classList.remove('hidden');
            addModal.classList.add('flex');
            return;
        }

        if (body.success) {
            const generatingModal = document.getElementById('generating-key-loading');
            generatingModal.classList.remove('hidden');
            generatingModal.classList.add('flex');

            setTimeout(() => {
                generatingModal.classList.add('hidden');
                generatingModal.classList.remove('flex');

                const barangay = body.barangay;

                document.getElementById('success-barangay-name').textContent = barangay.barangay_name;
                document.getElementById('success-barangay-ref').textContent = barangay.barangay_ref_num;
                document.getElementById('success-secret-key').textContent = barangay.secret_key;

                const successModal = document.getElementById('add-barangay-success');
                successModal.classList.remove('hidden');
                successModal.classList.add('flex');

                const row = document.createElement('tr');
                row.className = 'border-b border-gray-100 transition-colors hover:bg-gray-50';
                row.innerHTML = `
                    <td class="whitespace-nowrap px-4 py-3 text-gray-900">${barangay.barangay_ref_num}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-gray-900">${barangay.barangay_name}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-gray-900">
                        <span class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-semibold whitespace-nowrap" style="background-color: rgba(34, 197, 94, 0.10); color: #4ade80; border-color: rgba(34, 197, 94, 0.30);">
                            <i class="ti ti-circle-check text-[0.7rem]"></i>
                            <span class="text-[0.7rem]">${barangay.key_status}</span>
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-1.5">
                            <button type="button" class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-md bg-[#071f45] hover:bg-[#0a2d5e] hover:shadow-sm active:scale-95 transition-all duration-200 ease-in-out" title="Update Barangay">
                                <i class="ti ti-refresh text-xs text-white"></i>
                            </button>
                            <button type="button" data-modal-open="barangay-information-modal" data-ref="${barangay.barangay_ref_num}" data-name="${barangay.barangay_name}" data-key="${barangay.secret_key}" data-status="${barangay.key_status}" class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-md bg-blue-600 hover:bg-blue-700 hover:shadow-sm active:scale-95 transition-all duration-200 ease-in-out" title="View Barangay">
                                <i class="ti ti-eye text-xs text-white"></i>
                            </button>
                        </div>
                    </td>
                `;

                const tbody = document.querySelector('table tbody');
                const emptyRow = tbody.querySelector('td[colspan]');
                if (emptyRow) emptyRow.closest('tr').remove();
                tbody.insertBefore(row, tbody.firstChild);

                const totalEl = document.getElementById('total-barangays');
                const activeEl = document.getElementById('active-keys');
                if (totalEl) totalEl.textContent = parseInt(totalEl.textContent) + 1;
                if (activeEl) activeEl.textContent = parseInt(activeEl.textContent) + 1;

                barangayInput.value = '';
            }, 2000);
        } else {
            alert(body.message || 'Failed to add barangay.');
        }
    })
    .catch(() => {
        loadingModal.classList.add('hidden');
        loadingModal.classList.remove('flex');
        alert('An error occurred. Please try again.');
    });
}

function submitUpdateBarangay() {
    const modal = document.getElementById('update-barangay-modal');
    const form = modal.querySelector('form');

    const barangayId = form.querySelector('[name="barangay_id"]').value;
    const barangayName = form.querySelector('[name="barangay_name"]').value;
    const keyStatus = form.querySelector('[name="key_status"]').value;
    const currentStatus = document.querySelector('#update-barangay-modal [name="barangay_id"]').dataset.currentStatus;

    if (!keyStatus) {
        document.getElementById('update-status-error').textContent = 'Status is required.';
        document.getElementById('update-status-error').classList.remove('hidden');
        return;
    }

    if (keyStatus === currentStatus) {
        document.getElementById('update-status-error').textContent = 'Selected status is the same as current status.';
        document.getElementById('update-status-error').classList.remove('hidden');
        return;
    }
    document.getElementById('update-status-error').classList.add('hidden');

    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.classList.remove('overflow-hidden');

    const loadingModal = document.getElementById('update-barangay-loading');
    loadingModal.classList.remove('hidden');
    loadingModal.classList.add('flex');

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    fetch('/admin/barangays/update', {
        method: 'POST',
        body: new URLSearchParams({
            barangay_id: barangayId,
            barangay_name: barangayName,
            key_status: keyStatus,
        }),
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        },
    })
    .then(response => response.json().then(b => ({ status: response.status, body: b })))
    .then(({ status, body }) => {
        loadingModal.classList.add('hidden');
        loadingModal.classList.remove('flex');

        if (status === 422 && body.errors) {
            const messages = Object.values(body.errors).flat();
            document.getElementById('update-name-error').textContent = messages[0];
            document.getElementById('update-name-error').classList.remove('hidden');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
            return;
        }

        if (body.success) {
            const successModal = document.getElementById('update-barangay-success');
            successModal.classList.remove('hidden');
            successModal.classList.add('flex');

            setTimeout(() => {
                successModal.classList.add('hidden');
                successModal.classList.remove('flex');
                window.location.reload();
            }, 2000);
        } else {
            alert(body.message || 'Failed to update barangay.');
        }
    })
    .catch(() => {
        loadingModal.classList.add('hidden');
        loadingModal.classList.remove('flex');
        alert('An error occurred. Please try again.');
    });
}

function searchBarangay() {
    const searchInput = document.querySelector('[name="search"]');
    const search = searchInput ? searchInput.value : '';

    const statusInput = document.querySelector('[name="status"]');
    const status = statusInput ? statusInput.value : '';

    const tbody = document.querySelector('table tbody');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    fetch('/admin/barangays/search', {
        method: 'POST',
        body: new URLSearchParams({ search: search, status: status }),
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            tbody.innerHTML = data.html;
        }
    })
    .catch(() => {
        alert('An error occurred while searching.');
    });
}

let barangaySearchTimeout = null;

document.addEventListener('input', function (event) {
    if (event.target.name === 'search' && window.location.pathname.includes('barangays')) {
        clearTimeout(barangaySearchTimeout);
        barangaySearchTimeout = setTimeout(searchBarangay, 300);
    }
});

document.addEventListener('click', function (event) {
    const option = event.target.closest('[data-dropdown-option]');
    if (option && window.location.pathname.includes('barangays')) {
        const root = option.closest('details[data-dropdown]');
        if (root) {
            const input = root.querySelector('[data-dropdown-input]');
            if (input && input.name === 'status') {
                setTimeout(searchBarangay, 10);
            }
        }
    }
});
