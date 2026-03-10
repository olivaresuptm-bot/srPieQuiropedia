//para cargar estadísticas de citas
    document.addEventListener('DOMContentLoaded', function() {
        // Aquí se puede agregar una llamada AJAX para cargar estadísticas reales
        // Por ahora solo mostramos datos de ejemplo
        setTimeout(function() {
            document.getElementById('citas_hoy').innerText = '3';
            document.getElementById('citas_proximas').innerText = '7';
            document.getElementById('citas_mes').innerText = '24';
        }, 500);
    });