var refInput = document.getElementById('refInput').querySelector('input');
var clearBtn = document.getElementById('clearInputBtn');

function updateClearBtn() {
    if (refInput.value.trim()) {
        clearBtn.classList.remove('hidden');
    } else {
        clearBtn.classList.add('hidden');
    }
}

function clearInput() {
    refInput.value = '';
    updateClearBtn();
    document.getElementById('resultCard').classList.add('hidden');
    document.getElementById('noResultMessage').classList.add('hidden');
    document.getElementById('refError').classList.add('hidden');
    refInput.closest('.floating-input').classList.remove('input-error', 'has-value');
    refInput.focus();
}

refInput.addEventListener('input', function() {
    updateClearBtn();
    if (this.value.trim()) {
        document.getElementById('refError').classList.add('hidden');
        this.closest('.floating-input').classList.remove('input-error');
        document.getElementById('noResultMessage').classList.add('hidden');
    }
});

document.getElementById('trackForm').addEventListener('submit', function(e) {
    e.preventDefault();

    if (!refInput.value.trim()) {
        document.getElementById('refError').classList.remove('hidden');
        refInput.closest('.floating-input').classList.add('input-error');
        return;
    }

    document.getElementById('refError').classList.add('hidden');
    refInput.closest('.floating-input').classList.remove('input-error');

    var loading = document.getElementById('trackLoading');
    loading.classList.remove('hidden');
    loading.classList.add('flex');

    var csrfMeta = document.querySelector('meta[name="csrf-token"]');
    var csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

    fetch('/api/track-ticket', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
        },
        body: JSON.stringify({ ref: refInput.value.trim() }),
    })
    .then(function(response) { return response.json(); })
    .then(function(result) {
        setTimeout(function() {
            loading.classList.add('hidden');
            loading.classList.remove('flex');

            var resultCard = document.getElementById('resultCard');
            var noResultMsg = document.getElementById('noResultMessage');

            if (!result.success) {
                resultCard.classList.add('hidden');
                noResultMsg.classList.remove('hidden');
                return;
            }

            noResultMsg.classList.add('hidden');

            var ticket = result.ticket;

            document.getElementById('result-ref-num').textContent = ticket.ref_num;
            document.getElementById('result-date').textContent = ticket.date_requested;
            document.getElementById('result-requester-type').textContent = ticket.requester_type;
            document.getElementById('result-requester-name').textContent = ticket.requester_name;
            document.getElementById('result-category').textContent = ticket.category;
            document.getElementById('result-issue').textContent = ticket.issue;
            document.getElementById('result-description').textContent = ticket.description;

            var priorityBadge = document.getElementById('result-priority-badge');
            var priorityColors = getPriorityColor(ticket.priority);
            priorityBadge.style.backgroundColor = priorityColors.bg;
            priorityBadge.style.color = priorityColors.text;
            priorityBadge.style.borderColor = priorityColors.border;
            priorityBadge.querySelector('span').textContent = ticket.priority;

            var statusBadge = document.getElementById('result-status-badge');
            var statusColors = getStatusColor(ticket.status);
            statusBadge.style.backgroundColor = statusColors.bg;
            statusBadge.style.color = statusColors.text;
            statusBadge.style.borderColor = statusColors.border;
            statusBadge.querySelector('span').textContent = ticket.status;

            var cancellationRow = document.getElementById('result-cancellation-row');
            if (ticket.status.toLowerCase() === 'cancelled' && ticket.cancellation_reason) {
                cancellationRow.classList.remove('hidden');
                document.getElementById('result-cancellation-reason').textContent = ticket.cancellation_reason;
            } else {
                cancellationRow.classList.add('hidden');
            }

            resultCard.classList.remove('hidden');
        }, 1500);
    })
    .catch(function() {
        loading.classList.add('hidden');
        loading.classList.remove('flex');
        alert('An error occurred. Please try again.');
    });
});

function getPriorityColor(priority) {
    switch (priority.toLowerCase()) {
        case 'critical': return { bg: 'rgba(239, 68, 68, 0.10)', text: '#f87171', border: 'rgba(239, 68, 68, 0.30)' };
        case 'high': return { bg: 'rgba(249, 115, 22, 0.10)', text: '#fb923c', border: 'rgba(249, 115, 22, 0.30)' };
        case 'medium': return { bg: 'rgba(234, 179, 8, 0.10)', text: '#facc15', border: 'rgba(234, 179, 8, 0.30)' };
        case 'low': return { bg: 'rgba(34, 197, 94, 0.10)', text: '#4ade80', border: 'rgba(34, 197, 94, 0.30)' };
        default: return { bg: 'rgba(107, 114, 128, 0.10)', text: '#9ca3af', border: 'rgba(107, 114, 128, 0.30)' };
    }
}

function getStatusColor(status) {
    switch (status.toLowerCase()) {
        case 'pending': return { bg: 'rgba(249, 115, 22, 0.10)', text: '#fb923c', border: 'rgba(249, 115, 22, 0.30)' };
        case 'confirmed': return { bg: 'rgba(34, 197, 94, 0.10)', text: '#4ade80', border: 'rgba(34, 197, 94, 0.30)' };
        case 'in progress': return { bg: 'rgba(234, 179, 8, 0.10)', text: '#facc15', border: 'rgba(234, 179, 8, 0.30)' };
        case 'resolved': return { bg: 'rgba(59, 130, 246, 0.10)', text: '#60a5fa', border: 'rgba(59, 130, 246, 0.30)' };
        case 'cancelled': return { bg: 'rgba(239, 68, 68, 0.10)', text: '#f87171', border: 'rgba(239, 68, 68, 0.30)' };
        case 'request reassignment': return { bg: 'rgba(168, 85, 247, 0.10)', text: '#c084fc', border: 'rgba(168, 85, 247, 0.30)' };
        default: return { bg: 'rgba(107, 114, 128, 0.10)', text: '#9ca3af', border: 'rgba(107, 114, 128, 0.30)' };
    }
}

updateClearBtn();
