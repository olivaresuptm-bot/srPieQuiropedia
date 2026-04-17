<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../../index.php");
    exit;
}
include '../../controllers/facturacion_paciente.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facturación - Sr. Pie</title>
    <link rel="icon" type="image/png" href="../../assets/img/logo_sr_pie.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../assets/css/sidebar.css">
    <link rel="stylesheet" href="../../assets/css/header.css">
    <link rel="stylesheet" href="../../assets/css/footer.css">
</head>
<body class="bg-light">

   <?php $ruta_base = '../../';
        include '../../includes/header.php'; ?>

    <div class="d-flex" style="height: calc(100vh - 70px); overflow: hidden;">
        
        <?php $ruta_base = '../../';
            include '../../includes/sidebar.php'; 
        include '../../includes/titulo_modulo.php'; ?>

        <div class="flex-grow-1 d-flex flex-column contenedor-principal">
            
            <div class="p-4 flex-grow-1" style="overflow-y: auto;">
                
                <div class="row justify-content-center mt-4">
                    <div class="col-md-8 col-lg-6">
                        <div class="card shadow-sm border-0">
                            
                            <?php if ($cita_data): ?>
                                <div class="card-header bg-primary text-white p-3">
                                    <h5 class="mb-0"><i class="bi bi-file-earmark-medical me-2"></i>Detalle de Cobro</h5>
                                </div>
                                <div class="card-body p-4">
                                    
                                    <div class="bg-light p-3 rounded mb-4 border">
                                        <div class="row">
                                            <div class="col-sm-6 mb-2 mb-sm-0">
                                                <small class="text-muted d-block">Paciente</small>
                                                <strong><?php echo $cita_data['primer_nombre'] . " " . $cita_data['primer_apellido']; ?></strong><br>
                                                <small>C.I: <?php echo $cita_data['paciente_cedula']; ?></small>
                                            </div>
                                            <div class="col-sm-6 text-sm-end">
                                                <small class="text-muted d-block">Fecha de Cita</small>
                                                <strong><?php echo date('d/m/Y', strtotime($cita_data['fecha'])); ?></strong><br>
                                                <small>Tasa BCV: Bs. <?php echo number_format($tasa_bcv, 2, ',', '.'); ?></small>
                                            </div>
                                        </div>
                                    </div>

                                    <?php $monto_bs = $cita_data['precio'] * $tasa_bcv; ?>
                                    <div class="alert alert-info d-flex justify-content-between align-items-center mb-4 border-info">
                                        <div>
                                            <span class="d-block text-info-emphasis fw-bold mb-1">Servicio realizado:</span>
                                            <span class="fs-5"><?php echo $cita_data['servicio']; ?></span>
                                        </div>
                                        <div class="text-end">
                                            <span class="fs-3 fw-bold text-primary d-block">$<?php echo number_format($cita_data['precio'], 2); ?></span>
                                            <small class="text-muted fw-bold">Bs. <?php echo number_format($monto_bs, 2, ',', '.'); ?></small>
                                        </div>
                                    </div>
                                    
                                    <form action="../../controllers/procesar_pago_paciente.php" method="POST">
                                        <input type="hidden" name="cita_id" value="<?php echo $cita_id; ?>">
                                        <input type="hidden" name="monto" value="<?php echo $cita_data['precio']; ?>">
                                        
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Método de Pago</label>
                                            <select name="metodo_pago" id="metodo_pago" class="form-select form-select-lg" required onchange="verificarMetodo()">
                                                <option value="">-- Seleccione --</option>
                                                <option value="efectivo">Dólares (Efectivo)</option>
                                                <option value="efectivo_bs">Bolívares (Efectivo)</option>
                                                <option value="pago_movil">Pago Móvil (Bs)</option>
                                                <option value="transferencia">Transferencia</option>
                                                <option value="punto">Punto de Venta</option>
                                            </select>
                                        </div>

                                        <div class="mb-4" id="caja_referencia" style="display: none;">
                                            <label class="form-label fw-bold">Número de Referencia</label>
                                            <input type="text" name="referencia" id="referencia" class="form-control form-control-lg" placeholder="Últimos 4 o 6 dígitos">
                                        </div>

                                        <div class="d-grid gap-2">
                                            <button type="submit" class="btn btn-success btn-lg shadow-sm">
                                                <i class="bi bi-cash-stack me-2"></i>Registrar Pago
                                            </button>
                                            <a href="actualizar_citas.php" class="btn btn-outline-secondary">Cancelar</a>
                                        </div>
                                    </form>
                                </div>
                            
                            <?php else: ?>
                                <div class="card-body text-center py-5">
                                    <i class="bi bi-file-earmark-x fs-1 text-muted d-block mb-3"></i>
                                    <h5 class="text-muted">No hay una cita seleccionada</h5>
                                    <p class="text-muted mb-4">Debes seleccionar una cita desde el módulo de Estatus.</p>
                                    <a href="../actualizar_citas.php" class="btn btn-primary px-4">Ir a Estatus de Citas</a>
                                </div>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>

            </div> 

        </div>
    </div>
 </div></div></div><?php include '../../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/hamburguesa.js"></script>
    <script src="../../assets/js/facturacion_paciente.js"></script>
  
</body>
</html>