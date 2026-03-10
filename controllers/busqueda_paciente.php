<?php
require_once '../includes/db.php';

if (isset($_GET['cedula'])) {
    $cedula = $_GET['cedula'];
    $stmt = $conexion->prepare("SELECT * FROM pacientes WHERE cedula_id = ?");
    $stmt->execute([$cedula]);
    $p = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($p) {
        // Retornamos el diseño de la ficha
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
                <div class="mt-4 d-flex gap-2">
                                <a href="agregar_cita.php" class="btn btn-primary btn-lg  stretched-link">
                                    <i class="bi bi-calendar-check me-2"></i>Agendar Cita
                                </a>

                                <a href="agregar_cita.php" class="btn btn-dark btn-lg  stretched-link">
                                    <i class="bi bi-file-earmark-medical me-2"></i>Historial Clínico
                                </a>
                </div>
            </div>
        </div>';
    } else {
        echo "error";
    }
}