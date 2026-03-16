document.addEventListener('DOMContentLoaded', function() {
    // Auto-ocultar alertas después de 5 segundos
    setTimeout(function() {
        document.querySelectorAll('.alert').forEach(function(alert) {
            alert.classList.remove('show');
            setTimeout(() => alert.remove(), 300); 
        });
    }, 5000);
});