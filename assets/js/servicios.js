src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js">
        function abrirEditar(datos) {
            document.getElementById('modalTitulo').innerText = "Editar Servicio";
            document.getElementById('btnAccion').value = "editar";
            document.getElementById('serv_id').value = datos.servicio_id;
            document.getElementById('serv_nombre').value = datos.nombre;
            document.getElementById('serv_desc').value = datos.descripcion;
            document.getElementById('serv_precio').value = datos.precio;
            new bootstrap.Modal(document.getElementById('modalServicio')).show();
        }
        function prepararNuevo() {
            document.getElementById('modalTitulo').innerText = "Nuevo Servicio";
            document.getElementById('btnAccion').value = "crear";
            document.getElementById('serv_id').value = "";
            document.querySelector('#modalServicio form').reset();
        }
