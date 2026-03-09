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
        <?php include '../includes/sidebar.php'; ?>
         
            <?php include '../includes/titulo_modulo.php'; ?>

        <div class="flex-grow-1 p-4" style="overflow-y: auto;">
            <div class="d-flex justify-content-between align-items-center mb-4">
               <div></div>
                <div class="text-end">
                    <span class="badge bg-info text-dark mb-2 fs-5 ">Tasa BCV:  <?php echo number_format($tasa_bcv, 2, ',', '.'); ?>  Bs.</span><br>
                    <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#modalServicio" onclick="prepararNuevo()">
                        <i class="bi bi-plus-circle me-2"></i>Nuevo Servicio
                    </button>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-primary">
                            <tr>
                                <th class="ps-4">Nombre</th>
                                <th>Descripción</th>
                                <th>Precio ($)</th>
                                <th>Precio (Bs.)</th>
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

    <div class="modal fade" id="modalServicio" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="../controllers/servicios_controller.php" method="POST" class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalTitulo">Nuevo Servicio</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="serv_id">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre</label>
                        <input type="text" name="nombre" id="serv_nombre" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Descripción</label>
                        <textarea name="descripcion" id="serv_desc" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Precio ($)</label>
                        <input type="number" step="0.01" name="precio" id="serv_precio" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" name="accion" id="btnAccion" value="crear" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
    </div>

   <script src="../assets/js/servicios.js"></script>
    </div> </div> </div> <?php include '../includes/footer.php'; ?>
     <script src="../assets/js/hamburguesa.js"></script>
</body>
</html>