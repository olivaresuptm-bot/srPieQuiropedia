    // Validación solo de horario laboral, sin restricción de minutos
    document.getElementById('formCita').addEventListener('submit', function(e) {
        const hora = document.getElementById('hora').value;
        if (hora < '08:00' || hora > '18:00') {
            e.preventDefault();
            alert('❌ La hora debe estar entre 8:00 AM y 6:00 PM');
        }
    });
    
    // Mostrar hora actual como sugerencia
    window.onload = function() {
        const ahora = new Date();
        const horaActual = ahora.getHours().toString().padStart(2, '0') + ':' + 
                          ahora.getMinutes().toString().padStart(2, '0');
        document.getElementById('hora').setAttribute('placeholder', 'Ej: ' + horaActual);
    }