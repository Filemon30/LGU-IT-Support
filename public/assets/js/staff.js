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

        if (modalId === 'delete-confirmation-modal' && openButton.dataset.ref) {
            const currentUserId = document.getElementById('current-user-id')?.value;

            if (openButton.dataset.id === currentUserId) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.classList.remove('overflow-hidden');

                const selfModal = document.getElementById('self-delete-modal');
                selfModal.classList.remove('hidden');
                selfModal.classList.add('flex');
                document.body.classList.add('overflow-hidden');
                return;
            }

            document.getElementById('delete-staff-ref').textContent = openButton.dataset.ref;
            document.getElementById('delete-staff-name').textContent = openButton.dataset.name;
            document.getElementById('delete-staff-status').textContent = openButton.dataset.status;
            document.getElementById('delete-staff-id').value = openButton.dataset.id;
        }

        if (modalId === 'unarchive-confirmation-modal' && openButton.dataset.ref) {
            document.getElementById('unarchive-staff-ref').textContent = openButton.dataset.ref;
            document.getElementById('unarchive-staff-name').textContent = openButton.dataset.name;
            document.getElementById('unarchive-staff-id').value = openButton.dataset.id;
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

document.addEventListener('input', function (event) {
    const input = event.target;
    if (input.tagName !== 'INPUT' && input.tagName !== 'SELECT') return;

    input.classList.remove('border-red-500');
    input.classList.add('border-gray-300');

    const errorEl = input.parentElement.querySelector('.field-error');
    if (errorEl) errorEl.remove();
});

document.addEventListener('change', function (event) {
    const input = event.target;
    if (input.tagName !== 'SELECT') return;

    input.classList.remove('border-red-500');
    input.classList.add('border-gray-300');

    const errorEl = input.parentElement.querySelector('.field-error');
    if (errorEl) errorEl.remove();
});

document.addEventListener('click', function (event) {
    const summary = event.target.closest('details[data-dropdown] summary');
    if (!summary) return;

    const details = summary.closest('details[data-dropdown]');
    if (!details) return;

    const hiddenInput = details.querySelector('input[type="hidden"]');
    if (!hiddenInput) return;

    const errorEl = details.parentElement.querySelector('.field-error');
    if (errorEl) errorEl.remove();

    hiddenInput.classList.remove('border-red-500');
    hiddenInput.classList.add('border-gray-300');

    const wrapper = details.closest('div');
    if (wrapper) {
        wrapper.querySelectorAll('.field-error').forEach(el => el.remove());
    }
});

function togglePassword(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon = btn.querySelector('i');

    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('ti-eye');
        icon.classList.add('ti-eye-off');
    } else {
        input.type = 'password';
        icon.classList.remove('ti-eye-off');
        icon.classList.add('ti-eye');
    }
}

function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
    }
}

function showLoading(text) {
    const modal = document.getElementById('proceed-loading');
    const textEl = modal.querySelector('.loading-text');
    if (textEl) textEl.textContent = text;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function hideLoading() {
    const modal = document.getElementById('proceed-loading');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function clearErrors(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;

    modal.querySelectorAll('.field-error').forEach(el => el.remove());

    modal.querySelectorAll('input, select').forEach(input => {
        input.classList.remove('border-red-500');
        input.classList.add('border-gray-300');
    });

    modal.querySelectorAll('details[data-dropdown]').forEach(dd => {
        dd.classList.remove('border-red-500');
        dd.classList.add('border-gray-300');
    });
}

function showErrors(modalId, errors) {
    clearErrors(modalId);

    const modal = document.getElementById(modalId);
    if (!modal) return;

    for (const [field, messages] of Object.entries(errors)) {
        const input = modal.querySelector('[name="' + field + '"]');
        if (!input) continue;

        const errorMsg = Array.isArray(messages) ? messages[0] : messages;

        if (input.tagName === 'SELECT') {
            input.classList.remove('border-gray-300');
            input.classList.add('border-red-500');

            const errorEl = document.createElement('p');
            errorEl.className = 'field-error mt-1 text-xs text-red-500';
            errorEl.textContent = errorMsg;

            input.parentElement.appendChild(errorEl);
        } else if (input.type === 'hidden') {
            const dropdown = input.closest('details[data-dropdown]');
            if (dropdown) {
                const wrapper = dropdown.closest('div');
                if (wrapper) {
                    const errorEl = document.createElement('p');
                    errorEl.className = 'field-error mt-1 text-xs text-red-500';
                    errorEl.textContent = errorMsg;
                    wrapper.appendChild(errorEl);
                }

                const summary = dropdown.querySelector('summary');
                if (summary) {
                    summary.style.borderColor = '#ef4444';
                }
            }
        } else {
            input.classList.remove('border-gray-300');
            input.classList.add('border-red-500');

            const errorEl = document.createElement('p');
            errorEl.className = 'field-error mt-1 text-xs text-red-500';
            errorEl.textContent = errorMsg;

            input.parentElement.appendChild(errorEl);
        }
    }
}

function getFormData(modalId) {
    const modal = document.getElementById(modalId);
    const form = modal.querySelector('form');
    const data = {};

    new FormData(form).forEach((value, key) => {
        data[key] = value;
    });

    return data;
}

function validateAndProceedInfo() {
    const data = getFormData('info-staff-modal');

    clearErrors('info-staff-modal');
    showLoading('Proceeding to next step...');

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    fetch('/admin/staff/validate-step', {
        method: 'POST',
        body: new URLSearchParams({ ...data, step: 'info' }),
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        },
    })
    .then(response => response.json().then(body => ({ status: response.status, body })))
    .then(({ status, body }) => {
        hideLoading();

        if (status === 422 && body.errors) {
            showErrors('info-staff-modal', body.errors);
            return;
        }

        closeModal('info-staff-modal');
        openModal('account-staff-modal');
    })
    .catch(() => {
        hideLoading();
        alert('An error occurred. Please try again.');
    });
}

function validateAndProceedAccount() {
    const infoData = getFormData('info-staff-modal');
    const accountData = getFormData('account-staff-modal');

    clearErrors('account-staff-modal');
    showLoading('Proceeding to next step...');

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    fetch('/admin/staff/validate-step', {
        method: 'POST',
        body: new URLSearchParams({ ...infoData, ...accountData, step: 'account' }),
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        },
    })
    .then(response => response.json().then(body => ({ status: response.status, body })))
    .then(({ status, body }) => {
        hideLoading();

        if (status === 422 && body.errors) {
            showErrors('account-staff-modal', body.errors);
            return;
        }

        confirmAddStaff();
    })
    .catch(() => {
        hideLoading();
        alert('An error occurred. Please try again.');
    });
}

function confirmAddStaff() {
    const infoModal = document.getElementById('info-staff-modal');
    const accountModal = document.getElementById('account-staff-modal');
    const confirmModal = document.getElementById('confirmation-staff-modal');

    const infoForm = infoModal.querySelector('form');
    const accountForm = accountModal.querySelector('form');

    const infoData = new FormData(infoForm);
    const accountData = new FormData(accountForm);

    const data = {};
    infoData.forEach((value, key) => { data[key] = value; });
    accountData.forEach((value, key) => { data[key] = value; });

    const roleDropdown = accountForm.querySelector('[name="role_id"]').closest('[data-dropdown]');
    const selectedRoleText = roleDropdown?.querySelector('[data-dropdown-label]')?.textContent?.trim() || '';

    confirmModal.querySelectorAll('input').forEach(input => {
        const name = input.getAttribute('name');
        if (name && data[name] !== undefined && !name.endsWith('_display')) {
            input.value = data[name];
        }
        if (name === 'password_confirmation') {
            input.value = data['password'] || '';
        }
        if (name === 'role_display') {
            input.value = selectedRoleText;
        }
    });

    closeModal('account-staff-modal');
    openModal('confirmation-staff-modal');
}

function submitAddStaff() {
    const confirmModal = document.getElementById('confirmation-staff-modal');
    const form = confirmModal.querySelector('form');

    const formData = new FormData(form);

    closeModal('confirmation-staff-modal');

    const loadingModal = document.getElementById('add-staff-loading');
    loadingModal.classList.remove('hidden');
    loadingModal.classList.add('flex');

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    fetch('/admin/staff', {
        method: 'POST',
        body: formData,
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

            const infoFields = ['last', 'first', 'middle', 'suffix', 'birthdate', 'gender', 'contact', 'barangay'];
            const accountFields = ['role_id', 'email', 'password', 'password_confirmation'];

            const infoErrors = {};
            const accountErrors = {};

            for (const [field, messages] of Object.entries(body.errors)) {
                if (infoFields.includes(field)) {
                    infoErrors[field] = messages;
                } else {
                    accountErrors[field] = messages;
                }
            }

            if (Object.keys(infoErrors).length > 0) {
                openModal('info-staff-modal');
                showErrors('info-staff-modal', infoErrors);
            } else if (Object.keys(accountErrors).length > 0) {
                openModal('account-staff-modal');
                showErrors('account-staff-modal', accountErrors);
            }

            return;
        }

        if (body.success) {
            const successModal = document.getElementById('add-staff-success');
            successModal.classList.remove('hidden');
            successModal.classList.add('flex');

            const staff = body.staff;
            const roleColor = staff.role_name === 'Admin'
                ? 'background-color: rgba(168, 85, 247, 0.10); color: #c084fc; border-color: rgba(168, 85, 247, 0.30);'
                : 'background-color: rgba(59, 130, 246, 0.10); color: #60a5fa; border-color: rgba(59, 130, 246, 0.30);';
            const staffUrl = '/admin/staff/staff_information?id=' + staff.user_id;

            const row = document.createElement('tr');
            row.className = 'border-b border-gray-100 transition-colors hover:bg-gray-50';
            row.innerHTML = `
                <td class="whitespace-nowrap px-4 py-3 text-gray-900">${staff.staff_ref_num}</td>
                <td class="whitespace-nowrap px-4 py-3 text-gray-900">${staff.full_name}</td>
                <td class="whitespace-nowrap px-4 py-3 text-gray-900">${staff.contact_number}</td>
                <td class="px-4 py-3">
                    <span class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-semibold whitespace-nowrap" style="${roleColor}">
                        <span class="text-[0.7rem]">${staff.role_name}</span>
                    </span>
                </td>
                <td class="px-4 py-3">
                    <span class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-semibold whitespace-nowrap" style="background-color: rgba(34, 197, 94, 0.10); color: #4ade80; border-color: rgba(34, 197, 94, 0.30);">
                        <i class="ti ti-circle-check text-[0.7rem]"></i>
                        <span class="text-[0.7rem]">${staff.status}</span>
                    </span>
                </td>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-1.5">
                        <a href="${staffUrl}" class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-md bg-blue-600 hover:bg-blue-700 hover:shadow-sm active:scale-95 transition-all duration-200 ease-in-out" title="View Staff">
                            <i class="ti ti-eye text-xs text-white"></i>
                        </a>
                        <button type="button" data-modal-open="archive-confirmation-modal" class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-md bg-red-600 hover:bg-red-700 hover:shadow-sm active:scale-95 transition-all duration-200 ease-in-out" title="Archive Staff">
                            <i class="ti ti-archive text-xs text-white"></i>
                        </button>
                    </div>
                </td>
            `;

            const tbody = document.querySelector('table tbody');
            const emptyRow = tbody.querySelector('td[colspan]');
            if (emptyRow) emptyRow.closest('tr').remove();
            insertRowSorted(tbody, row);

            const totalEl = document.getElementById('total-staff');
            const activeEl = document.getElementById('active-staff');
            if (totalEl) totalEl.textContent = parseInt(totalEl.textContent) + 1;
            if (activeEl) activeEl.textContent = parseInt(activeEl.textContent) + 1;

            setTimeout(() => {
                successModal.classList.add('hidden');
                successModal.classList.remove('flex');
            }, 2000);
        } else {
            alert(body.message || 'Failed to add staff.');
        }
    })
    .catch(error => {
        loadingModal.classList.add('hidden');
        loadingModal.classList.remove('flex');
        alert('An error occurred. Please try again.');
    });
}

function confirmDeleteStaff()
{
    const confirmModal = document.getElementById('delete-confirmation-modal');
    confirmModal.classList.add('hidden');
    confirmModal.classList.remove('flex');

    const loadingModal = document.getElementById('delete-staff-loading');
    loadingModal.classList.remove('hidden');
    loadingModal.classList.add('flex');

    const userId = document.getElementById('delete-staff-id').value;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    fetch('/admin/staff/archive', {
        method: 'POST',
        body: new URLSearchParams({ user_id: userId }),
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

        if (body.success) {
            const successModal = document.getElementById('delete-staff-success');
            successModal.classList.remove('hidden');
            successModal.classList.add('flex');

            setTimeout(() => {
                successModal.classList.add('hidden');
                successModal.classList.remove('flex');
                window.location.href = '/admin/staff';
            }, 2000);
        } else {
            alert(body.message || 'Failed to delete staff.');
        }
    })
    .catch(() => {
        loadingModal.classList.add('hidden');
        loadingModal.classList.remove('flex');
        alert('An error occurred. Please try again.');
    });
}


function confirmUnarchivedStaff()
{
    const confirmModal = document.getElementById('unarchive-confirmation-modal');
    confirmModal.classList.add('hidden');
    confirmModal.classList.remove('flex');

    const loadingModal = document.getElementById('unarchive-staff-loading');
    loadingModal.classList.remove('hidden');
    loadingModal.classList.add('flex');

    const userId = document.getElementById('unarchive-staff-id').value;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    fetch('/admin/staff/unarchive', {
        method: 'POST',
        body: new URLSearchParams({ user_id: userId }),
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

        if (body.success) {
            const successModal = document.getElementById('unarchive-staff-success');
            successModal.classList.remove('hidden');
            successModal.classList.add('flex');

            setTimeout(() => {
                successModal.classList.add('hidden');
                successModal.classList.remove('flex');
                window.location.href = '/admin/staff/recycle-bin';
            }, 2000);
        } else {
            alert(body.message || 'Failed to restore staff.');
        }
    })
    .catch(() => {
        loadingModal.classList.add('hidden');
        loadingModal.classList.remove('flex');
        alert('An error occurred. Please try again.');
    });
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

document.addEventListener('click', function (event) {
    const openButton = event.target.closest('[data-modal-open="update-staff-modal"]');
    if (openButton) {
        setTimeout(function () {
            const passwordFields = document.getElementById('password-fields');
            const statusFields = document.getElementById('status-fields');
            if (passwordFields) passwordFields.classList.add('hidden');
            if (statusFields) statusFields.classList.add('hidden');

            const modal = document.getElementById('update-staff-modal');
            if (modal) {
                const dropdown = modal.querySelector('[name="update_type"]');
                if (dropdown) dropdown.value = '';
                const label = modal.querySelector('[data-dropdown-label]');
                if (label) {
                    label.textContent = 'Select Update Type';
                    label.style.color = '#9ca3af';
                }
            }
        }, 50);
    }
});

document.addEventListener('click', function (event) {
    const option = event.target.closest('[data-dropdown-option]');
    if (!option) return;

    const root = option.closest('details[data-dropdown]');
    if (!root) return;

    const input = root.querySelector('[data-dropdown-input]');
    if (!input || input.name !== 'update_type') return;

    setTimeout(function () {
        const passwordFields = document.getElementById('password-fields');
        const statusFields = document.getElementById('status-fields');

        if (input.value === 'password') {
            passwordFields.classList.remove('hidden');
            statusFields.classList.add('hidden');
        } else if (input.value === 'status') {
            statusFields.classList.remove('hidden');
            passwordFields.classList.add('hidden');
        } else {
            passwordFields.classList.add('hidden');
            statusFields.classList.add('hidden');
        }
    }, 10);
});

function submitUpdateStaff() {
    const modal = document.getElementById('update-staff-modal');
    const form = modal.querySelector('form');
    const updateType = form.querySelector('[name="update_type"]').value;

    if (!updateType) {
        return;
    }

    const userId = form.querySelector('[name="user_id"]').value;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    let url = '';
    let body = {};

    if (updateType === 'password') {
        const password = form.querySelector('[name="password"]').value;
        const passwordConfirmation = form.querySelector('[name="password_confirmation"]').value;

        if (!password) {
            document.getElementById('password-error').textContent = 'Password is required.';
            document.getElementById('password-error').classList.remove('hidden');
            return;
        }
        if (password.length < 6) {
            document.getElementById('password-error').textContent = 'Password must be at least 6 characters.';
            document.getElementById('password-error').classList.remove('hidden');
            return;
        }
        if (password !== passwordConfirmation) {
            document.getElementById('password-error').textContent = 'Passwords do not match.';
            document.getElementById('password-error').classList.remove('hidden');
            return;
        }

        document.getElementById('password-error').classList.add('hidden');
        url = '/admin/staff/update-password';
        body = { user_id: userId, password: password, password_confirmation: passwordConfirmation };

    } else if (updateType === 'status') {
        const status = form.querySelector('[name="status"]').value;
        const currentStatus = document.getElementById('archive-staff-status').value;

        if (!status) {
            document.getElementById('status-error').textContent = 'Status is required.';
            document.getElementById('status-error').classList.remove('hidden');
            return;
        }

        if (status === currentStatus) {
            document.getElementById('status-error').textContent = 'Selected status is the same as current status.';
            document.getElementById('status-error').classList.remove('hidden');
            return;
        }

        document.getElementById('status-error').classList.add('hidden');
        url = '/admin/staff/update-status';
        body = { user_id: userId, status: status };
    }

    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.classList.remove('overflow-hidden');

    const loadingModal = document.getElementById('update-staff-loading');
    loadingModal.classList.remove('hidden');
    loadingModal.classList.add('flex');

    fetch(url, {
        method: 'POST',
        body: new URLSearchParams(body),
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
            if (updateType === 'password') {
                document.getElementById('password-error').textContent = messages[0];
                document.getElementById('password-error').classList.remove('hidden');
            } else {
                document.getElementById('status-error').textContent = messages[0];
                document.getElementById('status-error').classList.remove('hidden');
            }
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
            return;
        }

        if (body.success) {
            const successModal = document.getElementById('update-staff-success');
            successModal.classList.remove('hidden');
            successModal.classList.add('flex');

            setTimeout(() => {
                successModal.classList.add('hidden');
                successModal.classList.remove('flex');
                window.location.reload();
            }, 2000);
        } else {
            alert(body.message || 'Failed to update staff.');
        }
    })
    .catch(() => {
        loadingModal.classList.add('hidden');
        loadingModal.classList.remove('flex');
        alert('An error occurred. Please try again.');
    });
}

function searchStaff() {
    const searchInput = document.querySelector('[name="search"]');
    const search = searchInput ? searchInput.value : '';

    const statusInput = document.querySelector('[name="status"]');
    const status = statusInput ? statusInput.value : '';

    const tbody = document.querySelector('table tbody');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    const isArchives = window.location.pathname.includes('recycle-bin');
    const url = isArchives ? '/admin/staff/search-archives' : '/admin/staff/search';

    const body = isArchives
        ? new URLSearchParams({ search: search })
        : new URLSearchParams({ search: search, status: status });

    fetch(url, {
        method: 'POST',
        body: body,
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

document.addEventListener('click', function (event) {
    const searchBtn = event.target.closest('button');
    if (searchBtn && searchBtn.textContent.trim() === 'Search') {
        searchStaff();
    }

    const option = event.target.closest('[data-dropdown-option]');
    if (option) {
        const root = option.closest('details[data-dropdown]');
        if (root) {
            const input = root.querySelector('[data-dropdown-input]');
            if (input && input.name === 'status') {
                setTimeout(searchStaff, 10);
            }
        }
    }
});

document.addEventListener('keydown', function (event) {
    if (event.key === 'Enter' && event.target.name === 'search') {
        searchStaff();
    }
});

let searchTimeout = null;
document.addEventListener('input', function (event) {
    if (event.target.name === 'search') {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(searchStaff, 300);
    }
});
