var MAX_DIVISIONS = 5;
var isConfirming = false;

function insertRowSorted(tbody, newRow) {
    var newRef = newRow.querySelector('td')?.textContent?.trim() || '';
    var rows = tbody.querySelectorAll('tr');

    for (var i = 0; i < rows.length; i++) {
        var existingRef = rows[i].querySelector('td')?.textContent?.trim() || '';
        if (newRef < existingRef) {
            tbody.insertBefore(newRow, rows[i]);
            return;
        }
    }
    tbody.appendChild(newRow);
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

document.addEventListener('DOMContentLoaded', function () {
    updateAddButtonVisibility();

    document.getElementById('division-inputs').addEventListener('input', function (e) {
        if (e.target.name === 'division[]') {
            var group = e.target.closest('.division-input-group');
            if (group) {
                var errorEl = group.querySelector('.division-error');
                if (errorEl) errorEl.classList.add('hidden');
            }
        }
    });

    var addDivisionModal = document.getElementById('add-division');
    if (addDivisionModal) {
        var observer = new MutationObserver(function () {
            if (addDivisionModal.classList.contains('hidden') && !isConfirming) {
                resetDivisionForm();
            }
        });
        observer.observe(addDivisionModal, { attributes: true, attributeFilter: ['class'] });
    }
});

document.addEventListener('click', function (event) {

    // Generic modal open
    var openButton = event.target.closest('[data-modal-open]');
    if (openButton) {
        var modalId = openButton.dataset.modalOpen;
        var modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }
    }

    // Generic modal close
    var closeButton = event.target.closest('[data-modal-close]');
    if (closeButton) {
        var modalId = closeButton.dataset.modalClose;
        var modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }
    }

    // Populate division info modal
    var infoBtn = event.target.closest('[data-modal-open="division-info-modal"]');
    if (infoBtn) {
        document.getElementById('view-div-ref').textContent = infoBtn.dataset.ref || '';
        document.getElementById('view-div-name').textContent = infoBtn.dataset.name || '';
        document.getElementById('view-div-key').textContent = infoBtn.dataset.key || 'N/A';

        var statusEl = document.getElementById('view-div-status');
        var status = infoBtn.dataset.status;
        var color = status === 'Active'
            ? 'background-color: rgba(34, 197, 94, 0.10); color: #4ade80; border-color: rgba(34, 197, 94, 0.30);'
            : 'background-color: rgba(239, 68, 68, 0.10); color: #f87171; border-color: rgba(239, 68, 68, 0.30);';
        var icon = status === 'Active' ? 'ti ti-circle-check' : 'ti ti-circle-x';

        statusEl.innerHTML =
            '<span class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-semibold whitespace-nowrap" style="' + color + '">' +
                '<i class="' + icon + ' text-[0.7rem]"></i>' +
                '<span class="text-[0.7rem]">' + status + '</span>' +
            '</span>';
    }

    // Populate update division modal
    var updateBtn = event.target.closest('[data-modal-open="update-division-modal"]');
    if (updateBtn) {
        var modal = document.getElementById('update-division-modal');
        var form = modal.querySelector('form');
        var idInput = form.querySelector('[name="division_id"]');
        var nameInput = form.querySelector('[name="division_name"]');
        idInput.value = updateBtn.dataset.id;
        idInput.dataset.currentStatus = updateBtn.dataset.status;
        nameInput.value = updateBtn.dataset.name;

        var statusInput = document.querySelector('#update-division-modal [name="key_status"]');
        if (statusInput) statusInput.value = updateBtn.dataset.status;
        var label = document.querySelector('#update-division-modal [data-dropdown-label]');
        if (label) {
            label.textContent = updateBtn.dataset.status;
            label.style.color = '#1f2937';
        }

        document.getElementById('update-div-status-error').classList.add('hidden');
    }
});

function getCsrfToken() {
    var meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
}

function getOfficeId() {
    var el = document.getElementById('division-inputs');
    return el ? el.dataset.officeId : null;
}

function addDivisionInput() {
    var container = document.getElementById('division-inputs');
    var currentCount = container.querySelectorAll('.division-input-group').length;

    if (currentCount >= MAX_DIVISIONS) return;

    var group = document.createElement('div');
    group.className = 'division-input-group flex items-end gap-2';
    group.innerHTML = '<div class="flex-1">' +
        '<input type="text" name="division[]" placeholder="Division Name" ' +
        'class="w-full h-9 rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm font-regular text-gray-900 placeholder-gray-400 outline-none transition focus:border-gray-400 focus:ring-2 focus:ring-gray-100" />' +
        '<p class="division-error text-red-500 text-xs mt-1 hidden"></p>' +
        '</div>' +
        '<button type="button" onclick="removeDivisionInput(this)" ' +
        'class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-300 text-gray-400 hover:border-red-400 hover:bg-red-50 hover:text-red-500 transition-colors mb-0.5">' +
        '<i class="ti ti-x text-sm"></i></button>';

    container.appendChild(group);
    updateAddButtonVisibility();
}

function removeDivisionInput(btn) {
    var group = btn.closest('.division-input-group');
    group.remove();
    updateAddButtonVisibility();
}

function updateAddButtonVisibility() {
    var container = document.getElementById('division-inputs');
    var addBtn = document.getElementById('add-division-btn');
    var currentCount = container.querySelectorAll('.division-input-group').length;

    if (currentCount >= MAX_DIVISIONS) {
        addBtn.classList.add('hidden');
    } else {
        addBtn.classList.remove('hidden');
    }
}

function resetDivisionForm() {
    var container = document.getElementById('division-inputs');
    var groups = container.querySelectorAll('.division-input-group');

    groups.forEach(function (group, i) {
        if (i === 0) {
            var input = group.querySelector('input[name="division[]"]');
            var error = group.querySelector('.division-error');
            if (input) input.value = '';
            if (error) error.classList.add('hidden');
        } else {
            group.remove();
        }
    });

    updateAddButtonVisibility();
}

function getDivisionNames() {
    var inputs = document.querySelectorAll('#division-inputs input[name="division[]"]');
    var names = [];
    inputs.forEach(function (input) {
        var val = input.value.trim();
        if (val) names.push(val);
    });
    return names;
}

function showConfirmDivisionName() {
    var container = document.getElementById('division-inputs');
    var groups = container.querySelectorAll('.division-input-group');
    var confirmList = document.getElementById('confirm-division-list');
    var names = [];
    var hasError = false;

    groups.forEach(function (group) {
        var input = group.querySelector('input[name="division[]"]');
        var errorEl = group.querySelector('.division-error');
        var val = input.value.trim();

        if (!val) {
            if (errorEl) {
                errorEl.textContent = 'Division name is required.';
                errorEl.classList.remove('hidden');
            }
            hasError = true;
        } else {
            if (errorEl) errorEl.classList.add('hidden');
            names.push(val);
        }
    });

    if (hasError || names.length === 0) return;

    var html = '';
    names.forEach(function (name, i) {
        var borderClass = i < names.length - 1 ? 'border-b border-gray-200' : '';
        html += '<div class="flex justify-between py-2 ' + borderClass + '">';
        html += '<span class="text-xs text-gray-500">Division ' + (i + 1) + '</span>';
        html += '<span class="text-xs font-semibold text-gray-900">' + name + '</span>';
        html += '</div>';
    });
    confirmList.innerHTML = html;

    isConfirming = true;
    closeModal('add-division');
    openModal('add-division-confirmation');
}

function confirmAddDivision() {
    var names = getDivisionNames();
    var officeId = getOfficeId();
    isConfirming = false;

    closeModal('add-division-confirmation');

    var loadingModal = document.getElementById('add-division-loading');
    loadingModal.classList.remove('hidden');
    loadingModal.classList.add('flex');

    fetch('/admin/offices/' + officeId + '/divisions', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken(),
            'Accept': 'application/json',
        },
        body: JSON.stringify({ division_names: names }),
    })
    .then(function (response) { return response.json().then(function (body) { return { status: response.status, body: body }; }); })
    .then(function (result) {
        loadingModal.classList.add('hidden');
        loadingModal.classList.remove('flex');

        var status = result.status;
        var body = result.body;

        if (status === 422 && body.errors) {
            var groups = document.querySelectorAll('#division-inputs .division-input-group');

            groups.forEach(function (group) {
                var errorEl = group.querySelector('.division-error');
                if (errorEl) errorEl.classList.add('hidden');
            });

            var errorKeys = Object.keys(body.errors);
            errorKeys.forEach(function (key) {
                if (key === 'division_names') {
                    var firstGroup = document.querySelector('#division-inputs .division-input-group');
                    if (firstGroup) {
                        var errorEl = firstGroup.querySelector('.division-error');
                        if (errorEl) {
                            errorEl.textContent = body.errors[key][0];
                            errorEl.classList.remove('hidden');
                        }
                    }
                } else {
                    var match = key.match(/division_names\.(\d+)/);
                    if (match) {
                        var index = parseInt(match[1]);
                        var groups = document.querySelectorAll('#division-inputs .division-input-group');
                        if (groups[index]) {
                            var errorEl = groups[index].querySelector('.division-error');
                            if (errorEl) {
                                errorEl.textContent = body.errors[key][0];
                                errorEl.classList.remove('hidden');
                            }
                        }
                    }
                }
            });

            openModal('add-division');
            return;
        }

        if (body.success) {
            var generatingModal = document.getElementById('generating-key-loading');
            generatingModal.classList.remove('hidden');
            generatingModal.classList.add('flex');

            setTimeout(function () {
                generatingModal.classList.add('hidden');
                generatingModal.classList.remove('flex');

                var divisions = body.divisions;
                var tbody = document.querySelector('table tbody');
                var emptyRow = tbody.querySelector('td[colspan]');
                if (emptyRow) emptyRow.closest('tr').remove();

                divisions.forEach(function (div) {
                    var row = document.createElement('tr');
                    row.className = 'border-b border-gray-100 transition-colors hover:bg-gray-50';
                    row.innerHTML =
                        '<td class="whitespace-nowrap px-4 py-3 text-gray-900">' + div.division_ref_num + '</td>' +
                        '<td class="whitespace-nowrap px-4 py-3 text-gray-900">' + div.division_name + '</td>' +
                        '<td class="whitespace-nowrap px-4 py-3 text-gray-900">' +
                            '<span class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-semibold whitespace-nowrap" style="background-color: rgba(34, 197, 94, 0.10); color: #4ade80; border-color: rgba(34, 197, 94, 0.30);">' +
                                '<i class="ti ti-circle-check text-[0.7rem]"></i>' +
                                '<span class="text-[0.7rem]">' + div.key_status + '</span>' +
                            '</span>' +
                        '</td>' +
                        '<td class="px-4 py-3"><div class="flex items-center gap-1.5">' +
                            '<button type="button" data-modal-open="update-division-modal" data-id="' + div.division_id + '" data-name="' + div.division_name + '" data-status="' + div.key_status + '" class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-md bg-blue-600 hover:bg-blue-700 hover:shadow-sm active:scale-95 transition-all duration-200 ease-in-out" title="Update Division">' +
                                '<i class="ti ti-refresh text-xs text-white"></i>' +
                            '</button>' +
                            '<button type="button" data-modal-open="division-info-modal" data-ref="' + div.division_ref_num + '" data-name="' + div.division_name + '" data-key="' + div.secret_key + '" data-status="' + div.key_status + '" class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-md bg-[#071f45] hover:bg-[#0a2d5e] hover:shadow-sm active:scale-95 transition-all duration-200 ease-in-out" title="View Division\'s Secret Key">' +
                                '<i class="ti ti-eye text-xs text-white"></i>' +
                            '</button>' +
                        '</div></td>';
                    insertRowSorted(tbody, row);
                });

                // Update card counts
                var totalDivEl = document.getElementById('total-divisions');
                var activeKeysEl = document.getElementById('active-keys');
                if (totalDivEl) totalDivEl.textContent = parseInt(totalDivEl.textContent) + divisions.length;
                if (activeKeysEl) activeKeysEl.textContent = parseInt(activeKeysEl.textContent) + divisions.length;

                var successList = document.getElementById('success-division-list');
                var html = '';
                divisions.forEach(function (div, i) {
                    var borderClass = i < divisions.length - 1 ? 'border-b border-gray-200' : '';
                    html += '<div class="py-2 ' + borderClass + '">';
                    html += '<div class="flex justify-between mb-1">';
                    html += '<span class="text-xs text-gray-500">Division Name</span>';
                    html += '<span class="text-xs font-semibold text-gray-900">' + div.division_name + '</span>';
                    html += '</div>';
                    html += '<div class="flex justify-between">';
                    html += '<span class="text-xs text-gray-500">Secret Key</span>';
                    html += '<span class="text-xs font-semibold text-gray-900">' + div.secret_key + '</span>';
                    html += '</div>';
                    html += '</div>';
                });
                successList.innerHTML = html;

                openModal('add-division-success');

            }, 2000);
        } else {
            alert(body.message || 'Failed to add divisions.');
        }
    })
    .catch(function () {
        loadingModal.classList.add('hidden');
        loadingModal.classList.remove('flex');
        alert('An error occurred. Please try again.');
    });
}

function submitUpdateDivision() {
    var modal = document.getElementById('update-division-modal');
    var form = modal.querySelector('form');

    var divisionId = form.querySelector('[name="division_id"]').value;
    var keyStatus = form.querySelector('[name="key_status"]').value;
    var currentStatus = document.querySelector('#update-division-modal [name="division_id"]').dataset.currentStatus;

    if (!keyStatus) {
        document.getElementById('update-div-status-error').textContent = 'Status is required.';
        document.getElementById('update-div-status-error').classList.remove('hidden');
        return;
    }

    if (keyStatus === currentStatus) {
        document.getElementById('update-div-status-error').textContent = 'Selected status is the same as current status.';
        document.getElementById('update-div-status-error').classList.remove('hidden');
        return;
    }
    document.getElementById('update-div-status-error').classList.add('hidden');

    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.classList.remove('overflow-hidden');

    var loadingModal = document.getElementById('update-division-loading');
    loadingModal.classList.remove('hidden');
    loadingModal.classList.add('flex');

    fetch('/admin/offices/divisions/update', {
        method: 'POST',
        body: new URLSearchParams({
            division_id: divisionId,
            key_status: keyStatus,
        }),
        headers: {
            'X-CSRF-TOKEN': getCsrfToken(),
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        },
    })
    .then(function (response) { return response.json().then(function (b) { return { status: response.status, body: b }; }); })
    .then(function (result) {
        loadingModal.classList.add('hidden');
        loadingModal.classList.remove('flex');

        if (result.status === 422 && result.body.errors) {
            var messages = Object.values(result.body.errors).flat();
            document.getElementById('update-div-status-error').textContent = messages[0];
            document.getElementById('update-div-status-error').classList.remove('hidden');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
            return;
        }

        if (result.body.success) {
            var successModal = document.getElementById('update-division-success');
            successModal.classList.remove('hidden');
            successModal.classList.add('flex');

            // Update the row in the table
            var tbody = document.querySelector('table tbody');
            var rows = tbody.querySelectorAll('tr');
            rows.forEach(function (row) {
                var cells = row.querySelectorAll('td');
                if (cells[0] && cells[0].textContent.trim() === result.body.division.division_ref_num) {
                    var statusCell = cells[2];
                    if (statusCell) {
                        var color = keyStatus === 'Active'
                            ? 'background-color: rgba(34, 197, 94, 0.10); color: #4ade80; border-color: rgba(34, 197, 94, 0.30);'
                            : 'background-color: rgba(239, 68, 68, 0.10); color: #f87171; border-color: rgba(239, 68, 68, 0.30);';
                        var icon = keyStatus === 'Active' ? 'ti ti-circle-check' : 'ti ti-circle-x';
                        statusCell.innerHTML =
                            '<span class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-semibold whitespace-nowrap" style="' + color + '">' +
                                '<i class="' + icon + ' text-[0.7rem]"></i>' +
                                '<span class="text-[0.7rem]">' + keyStatus + '</span>' +
                            '</span>';
                    }

                    // Update the update button data attributes
                    var updateBtn = row.querySelector('[data-modal-open="update-division-modal"]');
                    if (updateBtn) updateBtn.dataset.status = keyStatus;

                    // Update the view button data attributes
                    var viewBtn = row.querySelector('[data-modal-open="division-info-modal"]');
                    if (viewBtn) viewBtn.dataset.status = keyStatus;
                }
            });

            // Update card counts
            var activeKeysEl = document.getElementById('active-keys');
            var disabledKeysEl = document.getElementById('disabled-keys');
            if (keyStatus === 'Active') {
                if (activeKeysEl) activeKeysEl.textContent = parseInt(activeKeysEl.textContent) + 1;
                if (disabledKeysEl) disabledKeysEl.textContent = parseInt(disabledKeysEl.textContent) - 1;
            } else {
                if (activeKeysEl) activeKeysEl.textContent = parseInt(activeKeysEl.textContent) - 1;
                if (disabledKeysEl) disabledKeysEl.textContent = parseInt(disabledKeysEl.textContent) + 1;
            }

            setTimeout(function () {
                successModal.classList.add('hidden');
                successModal.classList.remove('flex');
            }, 2000);
        } else {
            alert(result.body.message || 'Failed to update division.');
        }
    })
    .catch(function () {
        loadingModal.classList.add('hidden');
        loadingModal.classList.remove('flex');
        alert('An error occurred. Please try again.');
    });
}
