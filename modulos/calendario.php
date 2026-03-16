<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit;
}
require_once '../controllers/calendario_citas.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Calendario de Citas - Sr. Pie</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/sidebar.css">
    <link rel="stylesheet" href="../assets/css/header.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
    <link rel="stylesheet" href="../assets/css/calendario.css">
</head>
<body class="bg-light">

    <?php include '../includes/header.php'; ?>
    
    <div class="d-flex">
        <?php include '../includes/sidebar.php'; 
        include '../includes/titulo_modulo.php'; ?>
        
        <div class="flex-grow-1 contenedor-principal">
            
            <div class="container-fluid p-4">
                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h5 class="mb-3"><i class="bi bi-info-circle text-primary me-2"></i>Leyenda de citas</h5>
                                <div class="d-flex flex-wrap gap-4">
                                    <div class="d-flex align-items-center"><span class="cita-indicador programada me-2"></span><span>Programada</span></div>
                                    <div class="d-flex align-items-center"><span class="cita-indicador atendida me-2"></span><span>Atendida</span></div>
                                    <div class="d-flex align-items-center"><span class="cita-indicador cancelada me-2"></span><span>Cancelada</span></div>
                                </div>
                            </div>
                            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                                <button class="btn btn-outline-primary" onclick="calendar.today()"><i class="bi bi-calendar-date me-2"></i>Hoy</button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card shadow border-0">
                    <div class="card-body">
                        <div id="calendar"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="modalCitasDia" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-calendar-day me-2"></i>Citas del <span id="fechaSeleccionada"></span></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body modal-citas bg-light" id="listaCitasDia"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    
    </div></div><?php include '../includes/footer.php'; ?>
    
    <script src="../assets/js/hamburguesa.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/es.js"></script>
    
    <script>
        const citasData = <?php echo json_encode($citas); ?>;
    </script>
    
    <script src="../assets/js/calendario.js"></script>
</body>
</html>