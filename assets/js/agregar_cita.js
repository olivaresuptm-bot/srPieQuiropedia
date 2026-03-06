    // Enfocar el campo de búsqueda al cargar la página
    document.addEventListener('DOMContentLoaded', function() {
        const buscarInput = document.querySelector('input[name="cedula_buscar"]');
        if (buscarInput) {
            buscarInput.focus();
        }
    });
