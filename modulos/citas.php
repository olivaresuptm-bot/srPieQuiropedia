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
    <title>Gestión Citas - Sr. Pie</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/sidebar.css">
    <link rel="stylesheet" href="../assets/css/header.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
</head>
<body class="bg-light">

    <?php include '../includes/header.php'; ?>

    <div class="d-flex" style="height: calc(100vh - 70px); overflow: hidden;">
        
        <?php include '../includes/sidebar.php'; ?>

        <!-- Contenido principal -->
        <div class="flex-grow-1" style="overflow-y: auto;">
            
            <?php include '../includes/titulo_modulo.php'; ?>

            <!-- Contenedor del módulo de citas -->
            <div class="container-fluid p-4" style="overflow-y: auto; height: calc(100vh - 140px);">
                
                <!-- Tarjeta de bienvenida/módulo -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <h4 class="card-title text-primary">
                            <i class="bi bi-calendar-check me-2"></i>Módulo de Gestión de Citas
                        </h4>
                        <p class="card-text text-muted">Selecciona una de las siguientes opciones para administrar las citas:</p>
                    </div>
                </div>

                <!-- Grid de botones grandes -->
                <div class="row g-4">
                    
                    <!-- Botón AGREGAR CITA -->
                    <div class="col-md-4">
                        <div class="card h-100 shadow hover-card border-0">
                            <div class="card-body text-center p-4 d-flex flex-column justify-content-center align-items-center" style="min-height: 250px;">
                                <div class="bg-success bg-opacity-10 rounded-circle p-4 mb-3">
                                    <i class="bi bi-plus-circle-fill text-success" style="font-size: 3.5rem;"></i>
                                </div>
                                <h3 class="fw-bold mb-2">Agregar Cita</h3>
                                <p class="text-muted mb-3">Registrar una nueva cita en el sistema</p>
                                <a href="agregar_cita.php" class="btn btn-success btn-lg w-100 stretched-link">
                                    <i class="bi bi-plus-circle me-2"></i>Nueva Cita
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Botón ESTATUS DE CITAS -->
                    <div class="col-md-4">
                        <div class="card h-100 shadow hover-card border-0">
                            <div class="card-body text-center p-4 d-flex flex-column justify-content-center align-items-center" style="min-height: 250px;">
                                <div class="bg-warning bg-opacity-10 rounded-circle p-4 mb-3">
                                    <i class="bi bi-arrow-left-right text-warning" style="font-size: 3.5rem;"></i>
                                </div>
                                <h3 class="fw-bold mb-2">Estatus de Citas</h3>
                                <p class="text-muted mb-3">Actualizar estado de citas (programada, atendida, cancelada)</p>
                                <a href="actualizar_citas.php" class="btn btn-warning btn-lg w-100">
                                    <i class="bi bi-pencil-square me-2"></i>Gestionar Estados
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Botón CALENDARIO DE CITAS -->
                    <div class="col-md-4">
                        <div class="card h-100 shadow hover-card border-0">
                            <div class="card-body text-center p-4 d-flex flex-column justify-content-center align-items-center" style="min-height: 250px;">
                                <div class="bg-info bg-opacity-10 rounded-circle p-4 mb-3">
                                    <i class="bi bi-calendar-week-fill text-info" style="font-size: 3.5rem;"></i>
                                </div>
                                <h3 class="fw-bold mb-2">Calendario de Citas</h3>
                                <p class="text-muted mb-3">Visualizar todas las citas en formato calendario</p>
                                <a href="calendario.php" class="btn btn-info btn-lg w-100 text-white">
                                    <i class="bi bi-calendar3 me-2"></i>Ver Calendario
                                </a>
                            </div>
                        </div>
                    </div>
                    
                </div>

                <!-- Fila adicional con estadísticas rápidas (opcional) -->
                <div class="row mt-5">
                    <div class="col-12">
                        <div class="card shadow-sm border-0 bg-light">
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-md-4">
                                        <h5 class="text-success"><i class="bi bi-calendar-check me-2"></i>Hoy</h5>
                                        <h3 id="citas_hoy" class="fw-bold">-</h3>
                                    </div>
                                    <div class="col-md-4">
                                        <h5 class="text-warning"><i class="bi bi-hourglass-split me-2"></i>Próximas 24h</h5>
                                        <h3 id="citas_proximas" class="fw-bold">-</h3>
                                    </div>
                                    <div class="col-md-4">
                                        <h5 class="text-primary"><i class="bi bi-graph-up me-2"></i>Total mes</h5>
                                        <h3 id="citas_mes" class="fw-bold">-</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
    
    <script src="../assets/js/hamburguesa.js"></script>
    
    <!-- Script para cargar estadísticas -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Aquí puedes agregar una llamada AJAX para cargar estadísticas reales
        // Por ahora solo mostramos datos de ejemplo
        setTimeout(function() {
            document.getElementById('citas_hoy').innerText = '3';
            document.getElementById('citas_proximas').innerText = '7';
            document.getElementById('citas_mes').innerText = '24';
        }, 500);
    });
    </script>

    <!-- Estilos adicionales -->
    <style>
    .hover-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        cursor: pointer;
    }
    .hover-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.15) !important;
    }
    .hover-card .btn {
        transition: all 0.3s ease;
    }
    .hover-card:hover .btn {
        transform: scale(1.05);
    }
    </style>

</body>
</html>
