document.addEventListener('DOMContentLoaded', function () {

    var loadingModal = document.getElementById('loading-submit-modal');

    if (!loadingModal) return;

    var observer = new MutationObserver(function (mutations) {
    mutations.forEach(function (mutation) {
        if (mutation.attributeName === 'class') {
            if (!loadingModal.classList.contains('hidden')) {
                setTimeout(function () {
                    closeModal('loading-submit-modal');
                    var dateSubmitted = document.getElementById('date-submitted');
                    if (dateSubmitted) {
                        var now = new Date();
                        var options = { year: 'numeric', month: 'long', day: 'numeric' };
                        dateSubmitted.textContent = now.toLocaleDateString('en-US', options);
                    }
                    openModal('success-submit-modal');
                }, 2000);
            }
        }
    });
});

observer.observe(loadingModal, { attributes: true });

});

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

function toggleRequestType(type) {
    var barangayContainer = document.getElementById('barangay-container');
    var cityOfficeContainer = document.getElementById('city-office-container');

    if (type === 'barangay') {
        barangayContainer.classList.remove('hidden');
        cityOfficeContainer.classList.add('hidden');
    } else {
        barangayContainer.classList.add('hidden');
        cityOfficeContainer.classList.remove('hidden');
    }
}

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

function openConfirmModal() {
    var getSelectedLabel = function(name) {
        var input = document.querySelector('[data-dropdown-input][name="' + name + '"]');
        if (!input) return '-';
        var root = input.closest('details[data-dropdown]');
        if (!root) return '-';
        var label = root.querySelector('[data-dropdown-label]');
        return label ? label.textContent.trim() || '-' : '-';
    };

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