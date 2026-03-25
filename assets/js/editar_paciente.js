function abrirEditarPaciente(p) {
    // Asignar valores a los inputs del modal
    document.getElementById('edit_p_cedula').value = p.cedula_id;
    document.getElementById('edit_p_nom1').value = p.primer_nombre;
    document.getElementById('edit_p_nom2').value = p.segundo_nombre || '';
    document.getElementById('edit_p_ape1').value = p.primer_apellido;
    document.getElementById('edit_p_ape2').value = p.segundo_apellido || '';
    document.getElementById('edit_p_tel').value = p.telefono;
    document.getElementById('edit_p_correo').value = p.correo || '';
    document.getElementById('edit_p_dir').value = p.direccion || '';
    document.getElementById('edit_p_inst').value = p.instagram || '';
    document.getElementById('edit_p_dia').value = p.diabetico || 'No';
    document.getElementById('edit_p_fecha').value = p.fecha_nac;

    // Mostrar el modal
    var myModal = new bootstrap.Modal(document.getElementById('modalEditarPaciente'));
    myModal.show();
}