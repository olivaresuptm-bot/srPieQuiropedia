<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit;
}
$titulo_modulo = "Gestión de Pacientes";
$icono_modulo = "bi-people-fill";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pacientes - Sr. Pie</title>
    <link rel="icon" type="image/png" href="../assets/img/logo_sr_pie.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/sidebar.css">
    <link rel="stylesheet" href="../assets/css/header.css">
    <link rel="stylesheet" href="../assets/css/gestion_pacientes.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
    
</head>
<body class="bg-light">

     <?php include '../includes/header.php'; ?>

    <div class="d-flex" style="height: calc(100vh - 70px); overflow: hidden;">
        
        <?php include '../includes/sidebar.php'; ?>

        <?php include '../includes/titulo_modulo.php'; ?>

            <div class="container mt-4">
                <div class="row justify-content-center g-4">
                    <div class="col-md-5">
                        <div class="card h-100 shadow-sm text-center p-4 opcion-card" onclick="toggleBuscador()">
                            <div class="card-body">
                                <div class="icon-circle bg-primary text-white">
                                    <i class="bi bi-search"></i>
                                </div>
                                <h4 class="fw-bold">Buscar Paciente</h4>
                                <p class="text-muted small">Haz clic para desplegar el panel de búsqueda rápida.</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-5">
                        <div class="card h-100 shadow-sm text-center p-4 opcion-card" onclick="location.href='pacientes.php'">
                            <div class="card-body">
                                <div class="icon-circle bg-success text-white">
                                    <i class="bi bi-person-plus-fill"></i>
                                </div>
                                <h4 class="fw-bold">Nuevo Registro</h4>
                                <p class="text-muted small">Ingresar un nuevo paciente al sistema.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="panelBusqueda" class="panel-desplegable animate__animated animate__fadeIn">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="text-primary mb-0 fw-bold"><i class="bi bi-filter-left me-2"></i>Busqueda paciente</h5>
                        <button type="button" class="btn-close" onclick="toggleBuscador()"></button>
                    </div>

                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <div class="input-group input-group-lg shadow-sm">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-person-vcard text-primary"></i></span>
                                <input type="text" id="inputBusqueda" class="form-control border-start-0" 
                                       placeholder="Cédula del paciente" onkeypress="verificarEnter(event)">
                            </div>
                            <div id="errorBusqueda" class="text-danger mt-2" style="display:none;">
                                <i class="bi bi-exclamation-circle"></i> No se encontró ningún paciente con esa cédula.
                            </div>
                        </div>
                    </div>

                    <div id="resultadoFicha" class="mt-4 pt-4 border-top" style="display:none;">
                        </div>
                </div>

            </div>
        </div>
    </div>

    </div></div></div><?php include '../includes/footer.php'; ?>

    <script src="../assets/js/busqueda_paciente.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/hamburguesa.js"></script>
</body>
</html>