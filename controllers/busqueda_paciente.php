<?php
require_once '../includes/db.php';

if (isset($_GET['cedula'])) {
    $cedula = $_GET['cedula'];
    $stmt = $conexion->prepare("SELECT * FROM pacientes WHERE cedula_id = ?");
    $stmt->execute([$cedula]);
    $p = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($p) {
        echo '
        <div class="card border-0 bg-light">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-2 text-center">
                        <i class="bi bi-person-circle text-primary" style="font-size: 4rem;"></i>
                    </div>
                    <div class="col-md-10">
                        <h3 class="fw-bold mb-0">' . $p['primer_nombre'] . ' ' . $p['segundo_nombre'] . ' ' .$p['primer_apellido'] . ' ' .$p['segundo_apellido']. '</h3>
                        <p class="text-muted">C.I: ' . $p['tipo_doc'] . '-' . $p['cedula_id'] . ' | Teléfono: ' . $p['telefono'] . '</p>
                    </div>
                </div>
                <hr>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="small text-muted d-block">Correo Electrónico</label>
                        <span class="fw-bold">' . $p['correo'] . '</span>
                    </div>
                    <div class="col-md-4">
                        <label class="small text-muted d-block">Fecha de Nacimiento</label>
                        <span class="fw-bold">' . $p['fecha_nac'] . '</span>
                    </div>
                    <div class="col-md-4">
                        <label class="small text-muted d-block">Dirección</label>
                        <span class="fw-bold">' . $p['direccion'] . '</span>
                    </div>
                </div>
                
                <div class="mt-4 d-flex gap-2 flex-wrap">
                    <a href="agregar_cita.php?id=' . $p['cedula_id'] . '" class="btn btn-primary btn-lg">
                        <i class="bi bi-calendar-check me-2"></i>Agendar Cita
                    </a>

                    <a href="historial.php?id=' . $p['cedula_id'] . '" class="btn btn-dark btn-lg">
                        <i class="bi bi-file-earmark-medical me-2"></i>Historial Clínico
                    </a>

                    <button type="button" class="btn btn-outline-primary btn-lg" onclick=\'abrirEditarPaciente(' . json_encode($p) . ')\'>
                        <i class="bi bi-pencil-square me-2"></i>Editar Datos
                    </button>

                    <a href="gestion_pacientes/historial_pago_paciente.php?cedula=' . $p['cedula_id'] . '" class="btn btn-success btn-lg shadow-sm">
                        <i class="bi bi-cash-stack me-1"></i> Pagos
                    </a>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalEditarPaciente" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <form action="../controllers/registro_paciente.php" method="POST" class="modal-content text-start">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title"><i class="bi bi-person-gear me-2"></i>Editar Paciente</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit_patient">
                        <input type="hidden" name="cedula_id" id="edit_p_cedula">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Primer Nombre</label>
                                <input type="text" name="primer_nombre" id="edit_p_nom1" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Segundo Nombre</label>
                                <input type="text" name="segundo_nombre" id="edit_p_nom2" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Primer Apellido</label>
                                <input type="text" name="primer_apellido" id="edit_p_ape1" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Segundo Apellido</label>
                                <input type="text" name="segundo_apellido" id="edit_p_ape2" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Teléfono</label>
                                <input type="text" name="telefono" id="edit_p_tel" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Correo</label>
                                <input type="email" name="correo" id="edit_p_correo" class="form-control">
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">Dirección</label>
                                <textarea name="direccion" id="edit_p_dir" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary px-4">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>';
    } else {
        echo "error";
    }
}
?>