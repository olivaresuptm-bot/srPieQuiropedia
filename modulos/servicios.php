<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit;
}
require_once '../includes/db.php';
include '../includes/tasa_BCV.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Servicios - Sr. Pie</title>
    <link rel="icon" type="image/png" href="../assets/img/logo_sr_pie.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/sidebar.css">
    <link rel="stylesheet" href="../assets/css/header.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
</head>
<body class="bg-light">

    <?php include '../includes/header.php'; ?>

    <div class="d-flex" style="height: calc(100vh - 70px); overflow: hidden;">
        <?php include '../includes/sidebar.php'; 
        include '../includes/titulo_modulo.php';?>

        <div class="flex-grow-1 d-flex flex-column contenedor-principal">
            <div class="p-4 flex-grow-1" style="overflow-y: auto;">
                <div class="d-flex justify-content-between align-items-center mb-4 mt-3">
                   <div></div>
                    <div class="text-end">
                        <span class="badge bg-info text-dark mb-2 fs-5 shadow-sm">
                            <i class="bi bi-currency-exchange me-1"></i> Tasa BCV: <?php echo number_format($tasa_bcv, 2, ',', '.'); ?> Bs.
                        </span><br>
                        <button class="btn btn-primary mt-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalServicio" onclick="prepararNuevo()">
                            <i class="bi bi-plus-circle me-2"></i>Nuevo Servicio
                        </button>
                    </div>
                </div>

                <div class="card shadow border-0">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-primary">
                                    <tr>
                                        <th class="ps-4">Nombre</th>
                                        <th>Descripción</th>
                                        <th>Precio ($)</th>
                                        <th>Precio (Bs.)</th>
                                        <th>Comisión Quiropedista</th>
                                        <th>Estado</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                   <?php include '../controllers/subservicios.php'; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div> 
            
                
           
        </div> 
    </div> 
    </div></div></div><?php include '../includes/footer.php'; ?>
    <div class="modal fade" id="modalServicio" tabindex="-1" aria-hidden="true" style="z-index: 9999;">
        <div class="modal-dialog">
            <form action="../controllers/servicios_controller.php" method="POST" class="modal-content shadow border-0">
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title" id="modalTitulo"><i class="bi bi-tag me-2"></i>Nuevo Servicio</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" name="accion" id="inputAccion" value="crear">
                    <input type="hidden" name="id" id="serv_id">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">Nombre</label>
                        <input type="text" name="nombre" id="serv_nombre" class="form-control shadow-sm" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">Descripción</label>
                        <textarea name="descripcion" id="serv_desc" class="form-control shadow-sm" rows="2"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold text-secondary">Precio ($)</label>
                            <input type="number" step="0.01" name="precio" id="serv_precio" class="form-control shadow-sm" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold text-primary">Comisión (%)</label>
                            <input type="number" step="0.01" name="comision_porcentaje" id="serv_comision" class="form-control shadow-sm border-primary" value="40" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4 shadow-sm"><i class="bi bi-save me-2"></i>Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/hamburguesa.js"></script>
    <script src="../assets/js/servicios.js"></script>
</body>
</html>