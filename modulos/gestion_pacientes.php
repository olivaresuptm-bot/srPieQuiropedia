<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit;
}

// 1. Incluimos la base de datos para poder consultar
require_once '../includes/db.php';

// 2. Hacemos el conteo total de pacientes registrados
try {
    $stmt_total = $conexion->query("SELECT COUNT(*) FROM pacientes");
    $total_pacientes = $stmt_total->fetchColumn();
} catch(PDOException $e) {
    $total_pacientes = 0;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pacientes - Sr. Pie</title>
    <link rel="icon" type="image/png" href="../assets/img/logo_sr_pie.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/gestion_pacientes.css">
    <link rel="stylesheet" href="../assets/css/sidebar.css">
    <link rel="stylesheet" href="../assets/css/header.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
</head>
<body class="bg-light">
   
    <?php include '../includes/header.php'; ?>

    <div class="d-flex" style="height: calc(100vh - 70px); overflow: hidden;">
        <?php include '../includes/sidebar.php'; 
        include '../includes/titulo_modulo.php';?>
        
        <div class="flex-grow-1 p-4 overflow-auto">
        
            <div class="card shadow-sm border-0 mb-5">
                <div class="card-body p-4">
                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <?php 
                                $cedula_busqueda = $_GET['busqueda'] ?? ''; 
                                ?>
                                <div class="input-group input-group-lg shadow-sm">
                                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-person-vcard text-primary"></i></span>
                                    
                                    <input type="text" id="inputBusqueda" class="form-control border-start-0" 
                                        placeholder="Ingrese cédula del paciente..." 
                                        onkeypress="verificarEnter(event)" 
                                        value="<?php echo htmlspecialchars($cedula_busqueda); ?>">
                                        
                                    <button class="btn btn-primary" id="btnBuscar" onclick="realizarBusqueda()">Buscar</button>
                                </div>
                            <div id="errorBusqueda" class="text-danger mt-2" style="display:none;">
                                <i class="bi bi-exclamation-circle"></i> No se encontró el paciente.
                            </div>
                        </div>
                    </div>
                    <div id="resultadoFicha" class="mt-4 pt-4 border-top" style="display:none;"></div>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-md-9 col-lg-8">
                    <div class="card border-0 shadow-sm card-registro-alto p-4" style="border-left: 10px solid #0d6efd !important;">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="icon-container-registro" style="background: rgba(13,110,253,0.1); padding:20px; border-radius:50%; margin-right:20px;">
                                    <i class="bi bi-person-plus-fill text-primary" style="font-size: 2.5rem;"></i>
                                </div>
                                <div>
                                    <h4 class="fw-bold mb-1 ">¿Paciente nuevo?</h4>
                                    <p class="mb-0" style="margin-right: 20px;">Registra un nuevo paciente en el sistema.</p>
                                </div>
                            </div>
                            <a href="gestion_pacientes/pacientes.php" class="btn btn-primary btn-lg px-4">Registrar paciente</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center mt-4">
                <div class="col-md-9 col-lg-8 text-center">
                    <span class="badge bg-white text-secondary border px-4 py-2 fs-6 shadow-sm" style="border-radius: 20px;">
                        <i class="bi bi-people-fill text-primary me-2"></i>
                        Total de pacientes en el sistema: <strong class="text-dark fs-5"><?php echo number_format($total_pacientes); ?></strong>
                    </span>
                </div>
            </div>

        </div>
    </div>

    </div></div></div><?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/busqueda_paciente.js"></script>
    <script src="../assets/js/editar_paciente.js"></script>
    <script src="../assets/js/hamburguesa.js"></script>
    
</body>
</html>