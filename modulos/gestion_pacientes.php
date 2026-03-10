<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit;
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
        
        <?php include '../includes/sidebar.php'; ?>
        <?php include '../includes/titulo_modulo.php'; ?>

        <div class="flex-grow-1 p-4" style="overflow-y: auto;">
                   
           <div class="container mt-4">
                
                <div class="row justify-content-center">
                    <div class="col-md-10 col-lg-8">
                        <div class="card shadow-sm border-0 p-4 mb-4" style="border-radius: 15px;">
                            <h5 class="text-secondary fw-bold mb-3"><i class="bi bi-search me-2"></i>Buscar Paciente</h5>
                            <div class="input-group input-group-lg shadow-sm">
                                <span class="input-group-text bg-white border-end-0 text-primary"><i class="bi bi-person-vcard"></i></span>
                                <input type="text" id="inputBusqueda" class="form-control border-start-0" 
                                       placeholder="Ingrese número de cédula" onkeypress="verificarEnter(event)">
                            </div>
                            <div id="errorBusqueda" class="text-danger mt-2 small" style="display:none;">
                                <i class="bi bi-exclamation-circle"></i> No se encontró ningún paciente con esa cédula.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-md-10 col-lg-8">
                        <div id="resultadoFicha" class="animate__animated animate__fadeIn" style="display:none;">
                            </div>
                    </div>
                </div>

                <div class="row justify-content-center mt-4">
    
    
    <div class="col-md-9 col-lg-8">
        <div class=" shadow-sm card-registro-alto p-4">
            <div class="card-body d-flex align-items-center justify-content-between">
                
                <div class="d-flex align-items-center">
                    <div class="icon-container-registro">
                        <i class="bi bi-person-plus-fill text-primary" style="font-size: 2.5rem;"></i>
                    </div>
                    
                    <div>
                        <h4 class="fw-bold mb-1">¿Paciente nuevo?</h4>
                        <p class="mb-0" style="margin-right: 50px;">Ingresa los datos del paciente para registrar.</p>
                    </div>
                </div>

                <div>
                    <a href="pacientes.php" class="btn btn-primary btn-registro-alto">
                          Registrar paciente</i>
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>

            </div>
        </div>
    </div>

      </div> </div> </div> <?php include '../includes/footer.php'; ?>

    <script src="../assets/js/busqueda_paciente.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/hamburguesa.js"></script>
</body>
</html>