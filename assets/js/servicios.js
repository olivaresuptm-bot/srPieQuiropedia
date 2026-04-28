function prepararNuevo() {
    document.getElementById('serv_id').value = '';
    document.getElementById('serv_nombre').value = '';
    document.getElementById('serv_desc').value = '';
    document.getElementById('serv_precio').value = '';
    // La comisión por defecto al crear uno nuevo
    document.getElementById('serv_comision').value = '40';
    
    document.getElementById('inputAccion').value = 'crear';
    document.getElementById('modalTitulo').innerHTML = '<i class="bi bi-tag me-2"></i>Nuevo Servicio';
}

function abrirEditar(servicio) {
    document.getElementById('serv_id').value = servicio.servicio_id;
    document.getElementById('serv_nombre').value = servicio.nombre;
    document.getElementById('serv_desc').value = servicio.descripcion;
    document.getElementById('serv_precio').value = servicio.precio;
    document.getElementById('serv_comision').value = servicio.comision_porcentaje; 
    
    document.getElementById('inputAccion').value = 'editar';
    document.getElementById('modalTitulo').innerHTML = '<i class="bi bi-pencil-square me-2"></i>Editar Servicio';

    var myModal = new bootstrap.Modal(document.getElementById('modalServicio'));
    myModal.show();
}