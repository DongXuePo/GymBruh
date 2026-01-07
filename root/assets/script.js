document.getElementById('post-submit')?.addEventListener('click', function() {
    const content = document.getElementById('post-content').value.trim();
    const errorDiv = document.getElementById('post-error');

    if (!content) {
        errorDiv.style.display = 'block';
    } else {
        errorDiv.style.display = 'none';
        alert('Post pubblicato (simulazione frontend)!');
        document.getElementById('post-content').value = '';
    }
});
