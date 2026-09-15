var getPriorityColor = function(name) {
    switch (name.toLowerCase()) {
        case 'critical': return 'red';
        case 'high': return 'orange';
        case 'medium': return 'yellow';
        case 'low': return 'green';
        default: return 'gray';
    }
};

function submitAddIssue() {
    var descInput = document.querySelector('[name="description"]');
    var priorityInput = document.querySelector('[name="default_priority_level_id"]');
    var descError = document.getElementById('error-description');
    var priorityError = document.getElementById('error-default_priority_level_id');

    descError.classList.add('hidden');
    priorityError.classList.add('hidden');

    var description = descInput ? descInput.value.trim() : '';
    var priorityId = priorityInput ? priorityInput.value : '';

    var hasError = false;

    if (!description) {
        descError.textContent = 'Description is required.';
        descError.classList.remove('hidden');
        hasError = true;
    }

    if (!priorityId) {
        priorityError.textContent = 'Priority is required.';
        priorityError.classList.remove('hidden');
        hasError = true;
    }

    if (hasError) return;

    var priorityLabel = '';
    var priorityOptions = document.querySelectorAll('[data-dropdown-option][data-value="' + priorityId + '"]');
    if (priorityOptions.length > 0) {
        priorityLabel = priorityOptions[0].getAttribute('data-label');
    }

    document.getElementById('confirm-issue-desc').textContent = description;

    var priorityBadge = document.getElementById('confirm-issue-priority');
    priorityBadge.textContent = priorityLabel;
    var colorName = getPriorityColor(priorityLabel);
    priorityBadge.className = 'inline-flex items-center rounded-full border px-2 py-0.5 text-xs font-semibold border-' + colorName + '-200 bg-' + colorName + '-50 text-' + colorName + '-700';

    closeModal('add-issue-modal');
    openModal('add-issue-confirm');
}

function proceedAddIssue() {
    closeModal('add-issue-confirm');
    openModal('add-issue-loading');

    var descInput = document.querySelector('[name="description"]');
    var priorityInput = document.querySelector('[name="default_priority_level_id"]');

    var description = descInput ? descInput.value.trim() : '';
    var priorityId = priorityInput ? priorityInput.value : '';

    var priorityLabel = '';
    var priorityOptions = document.querySelectorAll('[data-dropdown-option][data-value="' + priorityId + '"]');
    if (priorityOptions.length > 0) {
        priorityLabel = priorityOptions[0].getAttribute('data-label');
    }

    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    var categoryId = document.getElementById('issues-table-body').dataset.categoryId;

    fetch('/admin/services/' + categoryId + '/store-issue', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            description: description,
            default_priority_level_id: priorityId
        })
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        closeModal('add-issue-loading');

        if (data.success) {
            var issue = data.issue;
            var colorName = getPriorityColor(priorityLabel);

            var tbody = document.getElementById('issues-table-body');
            var emptyRow = tbody.querySelector('td[colspan]');
            if (emptyRow) {
                emptyRow.closest('tr').remove();
            }

            var rowHtml = '<tr class="border-b border-gray-100 transition-colors hover:bg-gray-50" data-issue-id="' + issue.issue_id + '">' +
                '<td class="whitespace-nowrap px-4 py-3 text-gray-900">' + issue.issue_ref_num + '</td>' +
                '<td class="px-4 py-3 text-gray-600 text-sm max-w-[200px] truncate">' + (issue.description || '-') + '</td>' +
                '<td class="whitespace-nowrap px-4 py-3">' +
                    '<span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold whitespace-nowrap border-' + colorName + '-200 bg-' + colorName + '-50 text-' + colorName + '-700">' + priorityLabel + '</span>' +
                '</td>' +
                '<td class="px-4 py-3">' +
                    '<div class="flex items-center gap-1.5">' +
                        '<button type="button" class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-md bg-red-600 hover:bg-red-700 hover:shadow-sm active:scale-95 transition-all duration-200 ease-in-out" title="Delete Issue" onclick="confirmDeleteIssue(' + issue.issue_id + ', \'' + (issue.description || '').replace(/'/g, "\\'") + '\')">' +
                            '<i class="ti ti-trash text-xs text-white"></i>' +
                        '</button>' +
                    '</div>' +
                '</td>' +
            '</tr>';

            tbody.insertAdjacentHTML('beforeend', rowHtml);

            openModal('add-issue-success');

            setTimeout(function() {
                closeModal('add-issue-success');
                descInput.value = '';
                priorityInput.value = '';
                var dropdown = priorityInput.closest('details[data-dropdown]');
                if (dropdown) {
                    window.xDropdownSelect(dropdown, '');
                }
            }, 1500);
        } else {
            if (data.errors) {
                var descError = document.getElementById('error-description');
                var priorityError = document.getElementById('error-default_priority_level_id');

                if (data.errors.description) {
                    descError.textContent = data.errors.description[0];
                    descError.classList.remove('hidden');
                }
                if (data.errors.default_priority_level_id) {
                    priorityError.textContent = data.errors.default_priority_level_id[0];
                    priorityError.classList.remove('hidden');
                }
            } else if (data.message) {
                var descError = document.getElementById('error-description');
                descError.textContent = data.message;
                descError.classList.remove('hidden');
            } else {
                alert('Failed to add issue.');
            }
            openModal('add-issue-modal');
        }
    })
    .catch(function(error) {
        console.error('Error:', error);
        closeModal('add-issue-loading');
        alert('An error occurred while adding the issue.');
        openModal('add-issue-modal');
    });
}

var deleteIssueId = null;

function confirmDeleteIssue(issueId, description) {
    deleteIssueId = issueId;
    document.getElementById('delete-issue-desc').textContent = description;
    openModal('delete-issue-confirm');
}

function proceedDeleteIssue() {
    if (!deleteIssueId) return;

    closeModal('delete-issue-confirm');
    openModal('delete-issue-loading');

    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    var categoryId = document.getElementById('issues-table-body').dataset.categoryId;

    fetch('/admin/services/' + categoryId + '/issues/' + deleteIssueId + '/delete', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        closeModal('delete-issue-loading');

        if (data.success) {
            var row = document.querySelector('[data-issue-id="' + deleteIssueId + '"]');
            if (row) {
                row.closest('tr').remove();
            }

            var tbody = document.getElementById('issues-table-body');
            if (tbody.children.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">No issues found.</td></tr>';
            }

            openModal('delete-issue-success');

            setTimeout(function() {
                closeModal('delete-issue-success');
            }, 1500);

            deleteIssueId = null;
        } else {
            alert(data.message || 'Failed to delete issue.');
            deleteIssueId = null;
        }
    })
    .catch(function(error) {
        console.error('Error:', error);
        closeModal('delete-issue-loading');
        alert('An error occurred while deleting the issue.');
        deleteIssueId = null;
    });
}

var restoreIssueId = null;

function confirmRestoreIssue(issueId, ref, desc) {
    restoreIssueId = issueId;
    document.getElementById('restore-issue-ref').textContent = ref;
    document.getElementById('restore-issue-desc').textContent = desc;
    openModal('restore-issue-confirm');
}

function proceedRestoreIssue() {
    closeModal('restore-issue-confirm');
    openModal('restore-issue-loading');

    var categoryId = document.getElementById('recycle-bin-table-body').dataset.categoryId;

    fetch('/admin/services/' + categoryId + '/recycle-bin/' + restoreIssueId + '/restore', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        closeModal('restore-issue-loading');

        if (data.success) {
            var row = document.querySelector('[data-issue-id="' + restoreIssueId + '"]');
            if (row) {
                row.closest('tr').remove();
            }

            var tbody = document.getElementById('recycle-bin-table-body');
            if (tbody.children.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500">No deleted issues found.</td></tr>';
            }

            openModal('restore-issue-success');

            setTimeout(function() {
                closeModal('restore-issue-success');
            }, 1500);

            restoreIssueId = null;
        } else {
            alert(data.message || 'Failed to restore issue.');
            restoreIssueId = null;
        }
    })
    .catch(function(error) {
        console.error('Error:', error);
        closeModal('restore-issue-loading');
        alert('An error occurred while restoring the issue.');
        restoreIssueId = null;
    });
}
