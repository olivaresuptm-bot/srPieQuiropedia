<?php
require_once '../includes/db.php';

if (isset($_GET['cedula'])) {
    $cedula = $_GET['cedula'];
    $stmt = $conexion->prepare("SELECT * FROM pacientes WHERE cedula_id = ?");
    $stmt->execute([$cedula]);
    $p = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($p) {
        $diabetico_badge = ($p['diabetico'] == 'Si') ? '<span class="badge bg-danger"><i class="bi bi-exclamation-triangle-fill me-1"></i>Sí (Precaución)</span>' : '<span class="badge bg-success">No</span>';
        $instagram_val = !empty($p['instagram']) ? $p['instagram'] : 'N/A';

        echo '
        <div class="card border-0 bg-light shadow-sm">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-2 text-center">
                        <i class="bi bi-person-circle text-primary" style="font-size: 4rem;"></i>
                    </div>
                    <div class="col-md-10">
                        <h3 class="fw-bold mb-0">' . $p['primer_nombre'] . ' ' . $p['segundo_nombre'] . ' ' .$p['primer_apellido'] . ' ' .$p['segundo_apellido']. '</h3>
                        <p class="text-muted mb-1">C.I: ' . $p['tipo_doc'] . '-' . $p['cedula_id'] . ' | Teléfono: ' . $p['telefono'] . '</p>
                        <p class="mb-0"><i class="bi bi-instagram text-danger me-1"></i> ' . $instagram_val . ' &nbsp;&nbsp;|&nbsp;&nbsp; <strong>Diabético:</strong> ' . $diabetico_badge . '</p>
                    </div>
                </div>
                <hr>';

        // ======================================================
        // BLOQUE DINÁMICO DEL REPRESENTANTE
        // Si hay nombre o cédula de representante, mostramos el cuadro
        // ======================================================
        if (!empty($p['cedula_rep']) || !empty($p['nombre_rep'])) {
            echo '
            <div class="alert border-0 mb-4 shadow-sm" style="background-color: #ffffff; border-left: 5px solid #0d6efd !important;">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <i class="bi bi-person-vcard fs-3 text-primary"></i>
                    </div>
                    <div class="col">
                        <h6 class="fw-bold mb-1  text-darken">Información del Representante / Responsable</h6>
                        <div class="row small text-dark">
                            <div class="col-md-5">
                                <strong>Nombre:</strong> ' . (!empty($p['nombre_rep']) ? htmlspecialchars($p['nombre_rep']) : 'N/A') . '
                            </div>
                            <div class="col-md-3">
                                <strong>C.I:</strong> ' . (!empty($p['cedula_rep']) ? htmlspecialchars($p['cedula_rep']) : 'N/A') . '
                            </div>
                            <div class="col-md-4">
                                <strong>Parentesco:</strong> ' . (!empty($p['parentesco_rep']) ? htmlspecialchars($p['parentesco_rep']) : 'N/A') . '
                            </div>
                        </div>
                    </div>
                </div>
            </div>';
        }

        echo '
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="small text-muted d-block">Correo Electrónico</label>
                        <span class="fw-bold">' . (!empty($p['correo']) ? $p['correo'] : 'N/A') . '</span>
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





                 <div class="border-top pt-4 text-center">
                    
                    <div class="contenedor-acciones">
                        <button type="button" class="btn btn-warning btn-accion shadow-sm" onclick=\'abrirEditarPaciente(' . json_encode($p) . ')\'>
                            <i class="bi bi-pencil-square me-2"></i>Editar Datos
                        </button>   
                    
                        <a href="agregar_cita.php?id=' . $p['cedula_id'] . '" class="btn btn-primary btn-accion shadow-sm">
                            <i class="bi bi-calendar-plus me-2"></i>Agendar Cita
                        </a>

                        <a href="gestion_pacientes/historial.php?cedula=' . $p['cedula_id'] . '" class="btn btn-info btn-accion shadow-sm">
                            <i class="bi bi-file-medical me-2"></i>Historial
                        </a>

                        <a href="gestion_pacientes/historial_pago_paciente.php?cedula=' . $p['cedula_id'] . '" class="btn btn-success btn-accion shadow-sm">
                            <i class="bi bi-cash-coin me-2"></i> Pagos
                        </a>
                        
                        <a href="../controllers/descargar_justificativo.php?cedula=' . $p['cedula_id'] . '" target="_blank" class="btn btn-danger btn-accion shadow-sm">
                            <i class="bi bi-file-earmark-pdf me-2"></i>Justificativo
                        </a>
                    </div>
                </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalEditarPaciente" tabindex="-1" aria-hidden="true" style="z-index: 9999;">
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
                                <input type="text" name="primer_nombre" id="edit_p_nom1" class="form-control" required oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, \'\')">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Segundo Nombre</label>
                                <input type="text" name="segundo_nombre" id="edit_p_nom2" class="form-control" oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, \'\')">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Primer Apellido</label>
                                <input type="text" name="primer_apellido" id="edit_p_ape1" class="form-control" required oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, \'\')">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Segundo Apellido</label>
                                <input type="text" name="segundo_apellido" id="edit_p_ape2" class="form-control" oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, \'\')">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Fecha de Nacimiento</label>
                                <input type="date" name="fecha_nac" id="edit_p_fecha" class="form-control">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Instagram</label>
                                <input type="text" name="instagram" id="edit_p_inst" class="form-control" 
                                    pattern="^@[A-Za-z0-9._]+$" 
                                    title="Debes incluir el @ al inicio (ej: @quiropediasrpie)" 
                                    placeholder="@ejemplo.usuario">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Teléfono</label>
                                <input type="text" name="telefono" id="edit_p_tel" class="form-control" required oninput="this.value = this.value.replace(/[^0-9]/g, \'\')">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Correo</label>
                                <input type="email" name="correo" id="edit_p_correo" class="form-control" 
                                    
                                    title="El correo debe contener un @" 
                                    placeholder="ejemplo@correo.com">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold ">¿Es Diabético?</label>
                                <select name="diabetico" id="edit_p_dia" class="form-select ">
                                    <option value="No">No</option>
                                    <option value="Si">Sí</option>
                                </select>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">Dirección</label>
                                <textarea name="direccion" id="edit_p_dir" class="form-control" rows="2"></textarea>
                            </div>

                            <div class="col-12 mt-2 border-top pt-3">
                                <h6 class="fw-bold">Datos del Representante (Opcional)</h6>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold small">Cédula Rep.</label>
                                <input type="text" name="cedula_rep" id="edit_p_ced_rep" class="form-control" oninput="this.value = this.value.replace(/[^0-9]/g, \'\')">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold small">Nombre Rep.</label>
                                <input type="text" name="nombre_rep" id="edit_p_nom_rep" class="form-control" oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, \'\')">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold small">Parentesco</label>
                                <input type="text" name="parentesco_rep" id="edit_p_par_rep" class="form-control" oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, \'\')">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-2"></i>Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>';
    } else {
        echo "error";
    }
}


?>
