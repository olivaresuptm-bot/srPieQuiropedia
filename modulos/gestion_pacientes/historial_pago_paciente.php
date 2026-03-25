<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../../index.php");
    exit;
}
include '../../controllers/historial_pago_paciente.php';

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial de Pagos - Sr. Pie</title>
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

                <?php if (!$paciente): ?>
                    <div class="alert alert-warning shadow-sm border-0">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> Paciente no encontrado o cédula no proporcionada.
                        <a href="../gestion_pacientes.php" class="alert-link ms-2">Volver al buscador</a>
                    </div>
                <?php else: ?>

                    <div class="card shadow-sm border-0 mb-4 border-start border-success border-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-1 text-secondary">Paciente:</h5>
                                    <h3 class="fw-bold text-dark mb-0">
                                       <?php 
                                               echo trim($paciente['primer_nombre'] . " " . 
                                                    ($paciente['segundo_nombre'] ?? '') . " " . 
                                                    $paciente['primer_apellido'] . " " . 
                                                    ($paciente['segundo_apellido'] ?? '')); 
                                        ?>
                                    </h3>
                                   <span class="text-muted me-3"><i class="bi bi-person-vcard me-1"></i> C.I: <?php echo $paciente['cedula_id']; ?></span>
                                        <span class="text-muted me-3"><i class="bi bi-instagram text-danger me-1"></i> <?php echo htmlspecialchars($paciente['instagram'] ?? 'N/A'); ?></span>
                                        
                                        <?php if(isset($paciente['diabetico']) && $paciente['diabetico'] == 'Si'): ?>
                                            <span class="badge bg-danger shadow-sm py-2 px-3"><i class="bi bi-exclamation-triangle-fill me-1"></i>Paciente Diabético</span>
                                        <?php endif; ?>
                                </div>
                                
                                <div class="text-end">
                                    <span class="badge bg-success bg-opacity-10 text-success p-3 fs-6 rounded-pill">
                                        Total Facturas: <?php echo count($pagos); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow border-0">
                        <div class="card-header bg-white py-3 border-0">
                            <h5 class="mb-0 text-primary"><i class="bi bi-card-list me-2"></i>Facturas Registradas</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-4">N° Recibo</th>
                                            <th>Fecha de Pago</th>
                                            <th>Servicio Cobrado</th>
                                            <th>Fecha Consulta</th>
                                            <th>Método</th>
                                            <th>Referencia</th>
                                            <th class="text-end pe-4">Monto ($)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($pagos)): ?>
                                            <tr>
                                                <td colspan="7" class="text-center py-5">
                                                    <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
                                                    <h5 class="text-muted">No hay pagos registrados para este paciente</h5>
                                                </td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($pagos as $p): ?>
                                                <tr>
                                                    <td class="ps-4 fw-bold text-secondary">#<?php echo str_pad($p['pago_id'], 5, '0', STR_PAD_LEFT); ?></td>
                                                    <td><?php echo date('d/m/Y - h:i A', strtotime($p['fecha_pago'])); ?></td>
                                                    <td><span class="badge bg-info text-dark"><?php echo $p['servicio_nombre']; ?></span></td>
                                                    <td><?php echo date('d/m/Y', strtotime($p['fecha_cita'])); ?></td>
                                                    <td>
                                                        <?php 
                                                            
                                                            $icono_metodo = 'bi-cash'; 
                                                            if($p['metodo_pago'] == 'efectivo_bs') $icono_metodo = 'bi-cash-stack'; 
                                                            if($p['metodo_pago'] == 'pago_movil') $icono_metodo = 'bi-phone-vibrate'; 
                                                            if($p['metodo_pago'] == 'transferencia') $icono_metodo = 'bi-bank'; 
                                                            if($p['metodo_pago'] == 'punto') $icono_metodo = 'bi-credit-card'; 

                                                            
                                                            $texto_mostrar = match($p['metodo_pago']) {
                                                                'efectivo'    => 'Efectivo $',
                                                                'efectivo_bs' => 'Efectivo Bs',
                                                                'pago_movil'  => 'Pago Móvil',
                                                                'punto'       => 'Punto de Venta',
                                                                'transferencia' => 'Transferencia',
                                                                default       => ucfirst(str_replace('_', ' ', $p['metodo_pago']))
                                                            };

                                                            echo "<i class='bi $icono_metodo me-1'></i>" . $texto_mostrar; 
                                                        ?>
                                                    </td>
                                                    <td class="text-muted"><?php echo $p['referencia'] ? '#' . $p['referencia'] : 'N/A'; ?></td>
                                                    <td class="text-end pe-4 fw-bold text-success fs-5">
                                                        $<?php echo number_format($p['monto'], 2); ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer bg-white border-0 py-3">
                            <a href="../gestion_pacientes.php?busqueda=<?php echo $paciente['cedula_id']; ?>" class="btn btn-outline-secondary shadow-sm">
                                <i class="bi bi-arrow-left me-2"></i>Volver al Perfil del Paciente
                            </a>
                           
                            <a href="../../controllers/factura_pagos_paciente_pdf.php?cedula=<?php echo $cedula; ?>" 
                                target="_blank" 
                                class="btn btn-warning shadow-sm ms-3">
                                    <i class="bi bi-file-earmark-pdf me-2"></i>Historial Pago Paciente PDF
                            </a>

                            <a href="../../controllers/factura_ultimo_pago_paciente_pdf.php?cedula=<?php echo $cedula; ?>" 
                                target="_blank" 
                                class="btn btn-primary shadow-sm">
                                    <i class="bi bi-file-earmark-pdf me-2"></i>Última Factura PDF
                            </a>

                            <a href="../../controllers/factura_pago_enviar_correo.php?cedula=<?php echo $cedula; ?>" 
                                target="_blank" 
                                class="btn btn-success shadow-sm">
                                    <i class="bi bi-envelope-paper me-2"></i>Enviar al Correo
                            </a>
                       
                        </div>
                        
                    </div>
                <?php endif; ?>

            </div> 
            
            

        </div>
    </div>
    </div></div></div><?php include '../../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/hamburguesa.js"></script>
</body>
</html>