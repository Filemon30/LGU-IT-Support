document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | Event Delegation — all clicks routed here
    |--------------------------------------------------------------------------
    */
    document.addEventListener('click', function (e) {

        // Submit Ticket button
        if (e.target.id === 'btn-submit-ticket' || e.target.closest('#btn-submit-ticket')) {
            e.preventDefault();
            openConfirmModal();
            return;
        }

        // Confirm Submit button (barangay or office)
        var confirmBtn = e.target.closest('[data-action="confirm-submit"]');
        if (confirmBtn) {
            e.preventDefault();
            e.stopPropagation();
            var type = confirmBtn.getAttribute('data-type');
            closeModal(type === 'barangay' ? 'confirm-barangay-modal' : 'confirm-office-modal');
            submitTicket();
            return;
        }

        // Download PDF button
        if (e.target.id === 'btn-download-pdf' || e.target.closest('#btn-download-pdf')) {
            e.preventDefault();
            downloadTicketPDF();
            return;
        }
    });


    /*
    |--------------------------------------------------------------------------
    | Office → Division filter
    |--------------------------------------------------------------------------
    */
    var officeDropdown = document.querySelector('[name="department_office"]').closest('details[data-dropdown]');
    if (officeDropdown) {
        officeDropdown.addEventListener('dropdown-change', function () {
            var officeId = this.querySelector('[data-dropdown-input]').value;
            var divisionInput = document.querySelector('[name="division"]');
            var divisionDetails = divisionInput.closest('details[data-dropdown]');

            if (!officeId) {
                updateDropdown(divisionDetails, [], 'Select office first');
                return;
            }

            fetch('/submit-request/divisions/' + officeId)
                .then(function (response) { return response.json(); })
                .then(function (data) {
                    updateDropdown(divisionDetails, data, 'Select Division');
                });
        });
    }


    /*
    |--------------------------------------------------------------------------
    | Barangay Category → Issue filter
    |--------------------------------------------------------------------------
    */
    var brgyCategoryDropdown = document.querySelector('[name="brgy_category"]').closest('details[data-dropdown]');
    if (brgyCategoryDropdown) {
        brgyCategoryDropdown.addEventListener('dropdown-change', function () {
            var categoryId = this.querySelector('[data-dropdown-input]').value;
            var issueInput = document.querySelector('[name="brgy_issue"]');
            var issueDetails = issueInput.closest('details[data-dropdown]');

            clearError('brgy_category');

            if (!categoryId) {
                updateDropdown(issueDetails, [], 'Select category first');
                return;
            }

            fetch('/submit-request/issues/' + categoryId)
                .then(function (response) { return response.json(); })
                .then(function (data) {
                    updateDropdown(issueDetails, data, 'Select Issue');
                });
        });
    }


    /*
    |--------------------------------------------------------------------------
    | City Category → Issue filter
    |--------------------------------------------------------------------------
    */
    var cityCategoryDropdown = document.querySelector('[name="city_category"]').closest('details[data-dropdown]');
    if (cityCategoryDropdown) {
        cityCategoryDropdown.addEventListener('dropdown-change', function () {
            var categoryId = this.querySelector('[data-dropdown-input]').value;
            var issueInput = document.querySelector('[name="city_issue"]');
            var issueDetails = issueInput.closest('details[data-dropdown]');

            clearError('city_category');

            if (!categoryId) {
                updateDropdown(issueDetails, [], 'Select category first');
                return;
            }

            fetch('/submit-request/issues/' + categoryId)
                .then(function (response) { return response.json(); })
                .then(function (data) {
                    updateDropdown(issueDetails, data, 'Select Issue');
                });
        });
    }


    /*
    |--------------------------------------------------------------------------
    | Clear errors on input/change
    |--------------------------------------------------------------------------
    */
    document.querySelectorAll('textarea, input[type="password"]').forEach(function (el) {
        el.addEventListener('input', function () {
            clearError(this.getAttribute('name'));
        });
    });

    document.querySelectorAll('details[data-dropdown]').forEach(function (dd) {
        dd.addEventListener('dropdown-change', function () {
            var input = this.querySelector('[data-dropdown-input]');
            if (input) {
                clearError(input.getAttribute('name'));
            }
        });
    });

});


/*
|--------------------------------------------------------------------------
| Error Display
|--------------------------------------------------------------------------
*/

function showError(fieldName, message) {
    var el = document.getElementById('error-' + fieldName);
    if (el) {
        el.textContent = message;
        el.classList.remove('hidden');
    }

    var input = document.querySelector('[name="' + fieldName + '"]');
    if (input) {
        var wrapper = input.closest('details[data-dropdown]') || input;
        var summary = wrapper.querySelector('summary') || wrapper;
        summary.style.borderColor = '#ef4444';
    }
}

function clearError(fieldName) {
    var el = document.getElementById('error-' + fieldName);
    if (el) {
        el.textContent = '';
        el.classList.add('hidden');
    }

    var input = document.querySelector('[name="' + fieldName + '"]');
    if (input) {
        var wrapper = input.closest('details[data-dropdown]') || input;
        var summary = wrapper.querySelector('summary') || wrapper;
        summary.style.borderColor = '';
    }
}

function clearAllErrors() {
    document.querySelectorAll('[id^="error-"]').forEach(function (el) {
        el.textContent = '';
        el.classList.add('hidden');
    });
    document.querySelectorAll('details[data-dropdown] summary').forEach(function (s) {
        s.style.borderColor = '';
    });
    document.querySelectorAll('textarea, input[type="password"]').forEach(function (s) {
        s.style.borderColor = '';
    });
}


/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

function getSelectedValue(name) {
    var input = document.querySelector('[data-dropdown-input][name="' + name + '"]');
    if (!input) return null;
    return input.value || null;
}

function getSelectedLabel(name) {
    var input = document.querySelector('[data-dropdown-input][name="' + name + '"]');
    if (!input) return '-';
    var root = input.closest('details[data-dropdown]');
    if (!root) return '-';
    var label = root.querySelector('[data-dropdown-label]');
    return label ? label.textContent.trim() || '-' : '-';
}


/*
|--------------------------------------------------------------------------
| Validation
|--------------------------------------------------------------------------
*/

function validateForm() {
    var requestType = document.querySelector('input[name="request_type"]:checked').value;
    var hasError = false;

    clearAllErrors();

    if (requestType === 'barangay') {
        if (!getSelectedValue('barangay')) {
            showError('barangay', 'Barangay is required.');
            hasError = true;
        }
        if (!getSelectedValue('brgy_category')) {
            showError('brgy_category', 'Category is required.');
            hasError = true;
        }
        if (!getSelectedValue('brgy_issue')) {
            showError('brgy_issue', 'Issue is required.');
            hasError = true;
        }

        var description = document.querySelector('[name="brgy_description"]');
        if (!description || !description.value.trim()) {
            showError('brgy_description', 'Description is required.');
            hasError = true;
        }

        var secretKey = document.querySelector('[name="brgy_secret_key"]');
        if (!secretKey || !secretKey.value.trim()) {
            showError('brgy_secret_key', 'Secret key is required.');
            hasError = true;
        }
    } else {
        if (!getSelectedValue('department_office')) {
            showError('department_office', 'Department/Office is required.');
            hasError = true;
        }
        if (!getSelectedValue('division')) {
            showError('division', 'Division is required.');
            hasError = true;
        }
        if (!getSelectedValue('city_category')) {
            showError('city_category', 'Category is required.');
            hasError = true;
        }
        if (!getSelectedValue('city_issue')) {
            showError('city_issue', 'Issue is required.');
            hasError = true;
        }

        var description = document.querySelector('[name="city_description"]');
        if (!description || !description.value.trim()) {
            showError('city_description', 'Description is required.');
            hasError = true;
        }

        var secretKey = document.querySelector('[name="city_secret_key"]');
        if (!secretKey || !secretKey.value.trim()) {
            showError('city_secret_key', 'Secret key is required.');
            hasError = true;
        }
    }

    return !hasError;
}


/*
|--------------------------------------------------------------------------
| Confirm Modal
|--------------------------------------------------------------------------
*/

function openConfirmModal() {
    if (!validateForm()) {
        return;
    }

    var requestType = document.querySelector('input[name="request_type"]:checked').value;

    if (requestType === 'barangay') {
        var description = document.querySelector('[name="brgy_description"]');
        var descriptionText = description && description.value.trim() ? description.value.trim() : '-';

        document.getElementById('brgy-confirm-dept').textContent = getSelectedLabel('barangay');
        document.getElementById('brgy-confirm-category').textContent = getSelectedLabel('brgy_category');
        document.getElementById('brgy-confirm-issue').textContent = getSelectedLabel('brgy_issue');
        document.getElementById('brgy-confirm-details').textContent = descriptionText;

        openModal('confirm-barangay-modal');
    } else {
        var description = document.querySelector('[name="city_description"]');
        var descriptionText = description && description.value.trim() ? description.value.trim() : '-';

        document.getElementById('office-confirm-dept').textContent = getSelectedLabel('department_office');
        document.getElementById('office-confirm-division').textContent = getSelectedLabel('division');
        document.getElementById('office-confirm-category').textContent = getSelectedLabel('city_category');
        document.getElementById('office-confirm-issue').textContent = getSelectedLabel('city_issue');
        document.getElementById('office-confirm-details').textContent = descriptionText;

        openModal('confirm-office-modal');
    }
}


/*
|--------------------------------------------------------------------------
| Submit Ticket
|--------------------------------------------------------------------------
*/

function submitTicket() {
    var loading = document.getElementById('loading-submit-modal');
    if (loading) {
        loading.classList.remove('hidden');
        loading.classList.add('flex');
    }

    var csrfMeta = document.querySelector('meta[name="csrf-token"]');
    if (!csrfMeta) {
        if (loading) {
            loading.classList.add('hidden');
            loading.classList.remove('flex');
        }
        alert('CSRF token not found. Please refresh the page.');
        return;
    }
    var csrfToken = csrfMeta.getAttribute('content');
    var requestType = document.querySelector('input[name="request_type"]:checked').value;

    var url, data;

    if (requestType === 'barangay') {
        url = '/submit-request/barangay';
        data = {
            barangay_id: getSelectedValue('barangay'),
            category_id: getSelectedValue('brgy_category'),
            issue_id: getSelectedValue('brgy_issue'),
            description: document.querySelector('[name="brgy_description"]').value.trim(),
            secret_key: document.querySelector('[name="brgy_secret_key"]').value,
        };
    } else {
        url = '/submit-request/office';
        data = {
            division_id: getSelectedValue('division'),
            category_id: getSelectedValue('city_category'),
            issue_id: getSelectedValue('city_issue'),
            description: document.querySelector('[name="city_description"]').value.trim(),
            secret_key: document.querySelector('[name="city_secret_key"]').value,
        };
    }

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
        },
        body: JSON.stringify(data),
    })
    .then(function (response) {
        return response.json().then(function (json) {
            return { status: response.status, json: json };
        });
    })
    .then(function (result) {
        setTimeout(function () {
            if (loading) {
                loading.classList.add('hidden');
                loading.classList.remove('flex');
            }

            if (result.status === 200 && result.json.success) {
                var ticketNumber = document.getElementById('ticket-number');
                if (ticketNumber) {
                    ticketNumber.textContent = result.json.ticket_ref_num;
                }

                var dateSubmitted = document.getElementById('date-submitted');
                if (dateSubmitted) {
                    var now = new Date();
                    var options = { year: 'numeric', month: 'long', day: 'numeric' };
                    dateSubmitted.textContent = now.toLocaleDateString('en-US', options);
                }

                var successModal = document.getElementById('success-submit-modal');
                if (successModal) {
                    successModal.classList.remove('hidden');
                    successModal.classList.add('flex');
                }
            } else {
                var errors = result.json.errors || {};
                var requestType = document.querySelector('input[name="request_type"]:checked').value;

                if (requestType === 'barangay') {
                    mapServerErrors(errors, {
                        'barangay_id': 'barangay',
                        'category_id': 'brgy_category',
                        'issue_id': 'brgy_issue',
                        'description': 'brgy_description',
                        'secret_key': 'brgy_secret_key',
                    });
                } else {
                    mapServerErrors(errors, {
                        'division_id': 'division',
                        'category_id': 'city_category',
                        'issue_id': 'city_issue',
                        'description': 'city_description',
                        'secret_key': 'city_secret_key',
                        'office_id': 'department_office',
                    });
                }
            }
        }, 1500);
    })
    .catch(function (error) {
        if (loading) {
            loading.classList.add('hidden');
            loading.classList.remove('flex');
        }
        alert('An error occurred. Please try again.');
    });
}

function mapServerErrors(errors, fieldMap) {
    for (var serverField in errors) {
        if (!errors.hasOwnProperty(serverField)) continue;

        var messages = errors[serverField];
        if (!Array.isArray(messages)) {
            messages = [messages];
        }

        var clientField = fieldMap[serverField] || serverField;
        showError(clientField, messages[0]);
    }
}


/*
|--------------------------------------------------------------------------
| Download PDF
|--------------------------------------------------------------------------
*/

function downloadTicketPDF() {
    var ticketNumber = document.getElementById('ticket-number').textContent;
    var dateSubmitted = document.getElementById('date-submitted').textContent;

    var win = window.open('', '_blank');
    win.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>${ticketNumber}</title>
            <style>
                body { font-family: 'Poppins', Arial, sans-serif; padding: 40px; color: #1f2937; }
                .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #2c51ec; padding-bottom: 20px; }
                .header h1 { color: #2c51ec; font-size: 22px; margin: 0; }
                .header p { color: #6b7280; font-size: 12px; margin: 5px 0 0; }
                .info-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                .info-table td { padding: 10px 12px; border-bottom: 1px solid #e5e7eb; font-size: 13px; }
                .info-table td:first-child { color: #6b7280; width: 40%; }
                .info-table td:last-child { font-weight: 600; color: #1f2937; }
                .footer { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb; }
                .footer p { color: #9ca3af; font-size: 11px; }
                @media print { body { padding: 20px; } }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>IT Support Ticket</h1>
                <p>City of Biringan - IT Support</p>
            </div>
            <table class="info-table">
                <tr><td>Ticket Number</td><td>${ticketNumber}</td></tr>
                <tr><td>Date Submitted</td><td>${dateSubmitted}</td></tr>
                <tr><td>Status</td><td>Pending</td></tr>
            </table>
            <div class="footer">
                <p>Please keep this ticket for your records.</p>
            </div>
            <script>window.onload=function(){window.print();}<\/script>
        </body>
        </html>
    `);
    win.document.close();
}


/*
|--------------------------------------------------------------------------
| Toggle Request Type
|--------------------------------------------------------------------------
*/

function toggleRequestType(type) {
    var barangayContainer = document.getElementById('barangay-container');
    var cityOfficeContainer = document.getElementById('city-office-container');

    clearAllErrors();

    if (type === 'barangay') {
        barangayContainer.classList.remove('hidden');
        cityOfficeContainer.classList.add('hidden');
    } else {
        barangayContainer.classList.add('hidden');
        cityOfficeContainer.classList.remove('hidden');
    }
}


/*
|--------------------------------------------------------------------------
| Toggle Password
|--------------------------------------------------------------------------
*/

function togglePassword(inputId) {
    var input = document.getElementById(inputId);
    var icon = input.parentElement.querySelector('i');
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


/*
|--------------------------------------------------------------------------
| Update Dropdown Options
|--------------------------------------------------------------------------
*/

function updateDropdown(details, items, placeholder) {
    var input = details.querySelector('[data-dropdown-input]');
    var label = details.querySelector('[data-dropdown-label]');

    var optionsContainer = details.querySelector('.absolute.z-20');

    var existingButtons = optionsContainer.querySelectorAll('[data-dropdown-option]');
    existingButtons.forEach(function (btn) { btn.remove(); });

    input.value = '';
    label.textContent = placeholder;
    label.style.color = details.style.getPropertyValue('--dd-placeholder');

    var placeholderBtn = document.createElement('button');
    placeholderBtn.type = 'button';
    placeholderBtn.setAttribute('data-dropdown-option', '');
    placeholderBtn.setAttribute('data-value', '');
    placeholderBtn.setAttribute('data-label', placeholder);
    placeholderBtn.className = 'flex w-full items-center whitespace-nowrap pr-3 text-left transition-colors h-9 p-2.5 text-sm';
    placeholderBtn.style.color = details.style.getPropertyValue('--dd-placeholder');
    placeholderBtn.style.backgroundColor = details.style.getPropertyValue('--dd-hover');
    placeholderBtn.textContent = placeholder;
    optionsContainer.appendChild(placeholderBtn);

    items.forEach(function (item) {
        var keys = Object.keys(item);
        var btn = document.createElement('button');
        btn.type = 'button';
        btn.setAttribute('data-dropdown-option', '');
        btn.setAttribute('data-value', item[keys[0]]);
        btn.setAttribute('data-label', item[keys[1]]);
        btn.className = 'flex w-full items-center whitespace-nowrap pr-3 text-left transition-colors h-9 p-2.5 text-sm';
        btn.style.color = details.style.getPropertyValue('--dd-text');
        btn.textContent = item[keys[1]];
        btn.onmouseenter = function () { this.style.backgroundColor = details.style.getPropertyValue('--dd-hover'); };
        btn.onmouseleave = function () { this.style.backgroundColor = 'transparent'; };
        optionsContainer.appendChild(btn);
    });
}
