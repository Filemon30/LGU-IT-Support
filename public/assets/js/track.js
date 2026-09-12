document.getElementById('trackForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const input = document.getElementById('refInput').querySelector('input');
    const error = document.getElementById('refError');

    if (!input.value.trim()) {
        error.classList.remove('hidden');
        input.closest('.floating-input').classList.add('input-error');
        return;
    }

    error.classList.add('hidden');
    input.closest('.floating-input').classList.remove('input-error');

    const loading = document.getElementById('trackLoading');
    loading.classList.remove('hidden');
    loading.classList.add('flex');

    setTimeout(function() {
        loading.classList.add('hidden');
        loading.classList.remove('flex');
        document.getElementById('resultCard').classList.remove('hidden');
    }, 2000);
});

document.getElementById('refInput').querySelector('input').addEventListener('input', function() {
    if (this.value.trim()) {
        document.getElementById('refError').classList.add('hidden');
        this.closest('.floating-input').classList.remove('input-error');
    }
});
