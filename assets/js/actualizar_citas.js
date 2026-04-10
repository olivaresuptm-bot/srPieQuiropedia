document.addEventListener('DOMContentLoaded', function() {
    // Auto-ocultar alertas después de 5 segundos
    setTimeout(function() {
        document.querySelectorAll('.alert').forEach(function(alert) {
            alert.classList.remove('show');
            setTimeout(() => alert.remove(), 300); 
        });
    }, 5000);
});
//Este es para editar datos de la cita
        function abrirEditarCita(cita) {
            
            document.getElementById('edit_cita_id').value = cita.cita_id;
            document.getElementById('edit_paciente_nombre').value = cita.paciente_nombre + ' ' + cita.paciente_apellido + ' (' + cita.paciente_cedula + ')';
            
            
            document.getElementById('edit_quiropedista').value = cita.quiropedista_cedula;
            document.getElementById('edit_servicio').value = cita.servicio_id;
            
           
            document.getElementById('edit_fecha').value = cita.fecha;
            
            document.getElementById('edit_hora').value = cita.hora.substring(0, 5);
            
            
            var modal = new bootstrap.Modal(document.getElementById('modalEditarCita'));
            modal.show();
        }
