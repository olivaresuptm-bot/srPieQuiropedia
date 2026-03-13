        function abrirEditar(datos) {
            document.getElementById('edit_cedula').value = datos.cedula_id;
            document.getElementById('edit_primer_nombre').value = datos.primer_nombre;
            document.getElementById('edit_segundo_nombre').value = datos.segundo_nombre;
            document.getElementById('edit_primer_apellido').value = datos.primer_apellido;
            document.getElementById('edit_segundo_apellido').value = datos.segundo_apellido;
            document.getElementById('edit_rol').value = datos.rol;
            
            var modal = new bootstrap.Modal(document.getElementById('modalEditarUsuario'));
            modal.show();
        }
   