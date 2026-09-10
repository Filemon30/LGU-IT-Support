var MAX_DIVISIONS = 5;
var isConfirming = false;

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

function addDivisionInput() {
    var container = document.getElementById('division-inputs');
    var currentCount = container.querySelectorAll('.division-input-group').length;

    if (currentCount >= MAX_DIVISIONS) return;

    var group = document.createElement('div');
    group.className = 'division-input-group flex items-end gap-2';
    group.innerHTML = '<div class="flex-1">' +
        '<input type="text" name="division[]" placeholder="Division Name" ' +
        'class="w-full h-9 rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm font-regular text-gray-900 placeholder-gray-400 outline-none transition focus:border-gray-400 focus:ring-2 focus:ring-gray-100" />' +
        '<p class="division-error text-red-500 text-xs mt-1 hidden">Division name is required</p>' +
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
            if (errorEl) errorEl.classList.remove('hidden');
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
    isConfirming = false;

    closeModal('add-division-confirmation');

    var loadingModal = document.getElementById('add-division-loading');
    loadingModal.classList.remove('hidden');
    loadingModal.classList.add('flex');

    setTimeout(function () {
        loadingModal.classList.add('hidden');
        loadingModal.classList.remove('flex');

        var generatingModal = document.getElementById('generating-key-loading');
        generatingModal.classList.remove('hidden');
        generatingModal.classList.add('flex');

        setTimeout(function () {
            generatingModal.classList.add('hidden');
            generatingModal.classList.remove('flex');

            var successList = document.getElementById('success-division-list');
            var html = '';
            names.forEach(function (name, i) {
                var borderClass = i < names.length - 1 ? 'border-b border-gray-200' : '';
                html += '<div class="py-2 ' + borderClass + '">';
                html += '<div class="flex justify-between mb-1">';
                html += '<span class="text-xs text-gray-500">Division Name</span>';
                html += '<span class="text-xs font-semibold text-gray-900">' + name + '</span>';
                html += '</div>';
                html += '<div class="flex justify-between">';
                html += '<span class="text-xs text-gray-500">Secret Key</span>';
                html += '<span class="text-xs font-semibold text-gray-900">-</span>';
                html += '</div>';
                html += '</div>';
            });
            successList.innerHTML = html;

            openModal('add-division-success');

        }, 3000);

    }, 2000);
}
