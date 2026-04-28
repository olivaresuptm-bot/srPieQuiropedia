<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit;
}
require_once '../controllers/reportes.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes y Analítica - Sr. Pie</title>
    <link rel="icon" type="image/png" href="../assets/img/logo_sr_pie.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/sidebar.css">
    <link rel="stylesheet" href="../assets/css/header.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-light">

<?php include '../includes/header.php'; ?>

<div class="d-flex" style="height: calc(100vh - 70px); overflow: hidden;">
    <?php include '../includes/sidebar.php'; 
    include '../includes/titulo_modulo.php';?>

    <div class="flex-grow-1 p-4" style="overflow-y: auto;">

        <div class="d-flex justify-content-end mb-3">
            <span class="badge bg-info text-dark fs-6 shadow-sm px-3 py-2">
                <i class="bi bi-currency-exchange me-1"></i> Tasa BCV: <?php echo number_format($tasa_actual, 2, ',', '.'); ?> Bs.
            </span>
        </div>

        <div class="row g-3 mb-4">
            <div class="row g-3 mb-4">
            
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-3 h-100">
                    <h6 class="text-primary fw-bold mb-3"><i class="bi bi-calendar2-check me-2"></i>CITAS ATENDIDAS</h6>
                    <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                        <span class="small text-secondary">Hoy:</span> <strong class="text-dark fs-5"><?php echo $stats['citas_diario']; ?></strong>
                    </div>
                    <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                        <span class="small text-secondary">Esta Semana:</span> <strong class="text-dark fs-5"><?php echo $stats['citas_semanal']; ?></strong>
                    </div>
                    <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                        <span class="small text-secondary">Este Mes:</span> <strong class="text-dark fs-5"><?php echo $stats['citas_mensual']; ?></strong>
                    </div>
                    <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                        <span class="small text-secondary">Este Año:</span> <strong class="text-dark fs-5"><?php echo $stats['citas_anual']; ?></strong>
                    </div>
                    <div class="d-flex justify-content-between pt-1">
                        <span class="small text-muted fw-bold">Histórico Total:</span> <strong class="text-muted"><?php echo $stats['citas_hist']; ?></strong>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-3 h-100">
                    <h6 class="text-info fw-bold mb-3"><i class="bi bi-people-fill me-2"></i>PACIENTES NUEVOS</h6>
                    <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                        <span class="small text-secondary">Hoy:</span> <strong class="text-dark fs-5"><?php echo $stats['pac_diario']; ?></strong>
                    </div>
                    <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                        <span class="small text-secondary">Esta Semana:</span> <strong class="text-dark fs-5"><?php echo $stats['pac_semanal']; ?></strong>
                    </div>
                    <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                        <span class="small text-secondary">Este Mes:</span> <strong class="text-dark fs-5"><?php echo $stats['pac_mensual']; ?></strong>
                    </div>
                    <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                        <span class="small text-secondary">Este Año:</span> <strong class="text-dark fs-5"><?php echo $stats['pac_anual']; ?></strong>
                    </div>
                    <div class="d-flex justify-content-between pt-1">
                        <span class="small text-muted fw-bold">Histórico Total:</span> <strong class="text-muted"><?php echo $stats['pac_hist']; ?></strong>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-3 h-100 border-start border-success border-4">
                    <h6 class="text-success fw-bold mb-3"><i class="bi bi-cash-coin me-2"></i>INGRESOS BRUTOS</h6>
                    
                     <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                        <span class="small text-secondary">Hoy:</span> 
                        <div class="text-end">
                            <strong class="text-success fs-5"><?php echo number_format($stats['ing_diario'], 2); ?> $</strong>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                        <span class="small text-secondary">Esta Semana:</span> 
                        <div class="text-end">
                            <strong class="text-success fs-5"><?php echo number_format($stats['ing_semanal'], 2); ?> $</strong>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                        <span class="small text-secondary">Este Mes:</span> 
                        <div class="text-end">
                            <strong class="text-success fs-5"><?php echo number_format($stats['ing_mensual'], 2); ?> $</strong>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                        <span class="small text-secondary">Este Año:</span> 
                        <div class="text-end">
                            <strong class="text-success fs-5"><?php echo number_format($stats['ing_anual'], 2); ?> $</strong>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between pt-1">
                        <span class="small text-muted fw-bold">Histórico Total:</span> <strong class="text-muted"><?php echo number_format($stats['ing_hist'], 2); ?> $</strong>
                    </div>
                </div>
            </div>
            
        </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 text-primary fw-bold"><i class="bi bi-cash-stack me-2"></i>Nómina Pendiente (Comisiones de la Semana)</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Quiropedista</th>
                                <th>Servicios de la Semana</th>
                                <th>Producido</th>
                                <th>Comisión a Pagar</th>
                                <?php if($rol_usuario == 'gerente' || $rol_usuario == 'recepcionista'): ?>
                                <th class="text-center">Acción</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($datos_tabla)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="bi bi-check2-all fs-2 d-block mb-2"></i>
                                        No hay comisiones pendientes por pagar esta semana.
                                    </td>
                                </tr>
                            <?php endif; ?>

                            <?php foreach($datos_tabla as $fila): ?>
                            <tr>
                                <td class="ps-4 fw-bold text-secondary"><?php echo $fila['nombre_completo']; ?></td>
                                <td>
                                    <?php 
                                    $cedula_quiro = $fila['cedula_id'];
                                    if(isset($desglose_servicios[$cedula_quiro])) {
                                        foreach($desglose_servicios[$cedula_quiro] as $detalle) {
                                            echo "<span class='badge bg-info text-dark me-1 mb-1 shadow-sm'>$detalle</span> ";
                                        }
                                    }
                                    ?>
                                </td>
                                <td>
                                    <span class="d-block"><?php echo number_format($fila['ventas_usd'], 2); ?> $</span>
                                </td>
                                <td class="fw-bold text-success fs-6">
                                    <span class="d-block"><?php echo number_format($fila['comision_usd'], 2); ?> $</span>
                                    <small class="text-muted fw-normal fs-6">A Pagar: <?php echo number_format($fila['comision_bs'], 2, ',', '.'); ?> Bs.</small>
                                </td>
                                 <?php if($rol_usuario == 'gerente' || $rol_usuario == 'recepcionista'): ?>
                                <td class="text-center">
                                    <button class="btn btn-success shadow-sm fw-bold px-3" 
                                            onclick="abrirModalPago('<?php echo $cedula_quiro; ?>', '<?php echo addslashes($fila['nombre_completo']); ?>', '<?php echo number_format($fila['comision_usd'], 2); ?>')">
                                        <i class="bi bi-wallet2 me-1"></i> Pagar Semana
                                    </button>
                                </td>
                                <?php endif; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 text-primary fw-bold"><i class="bi bi-bar-chart-line me-2"></i>Rendimiento Pendiente <i class="bi bi-currency-exchange me-1"></i></h6>
            </div>
            <div class="card-body">
                <canvas id="graficoIngresos" style="max-height: 350px;"></canvas>
            </div>
        </div>
        
    </div>
</div> 

<div class="modal fade" id="modalPago" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-success text-white border-0">
                <h5 class="modal-title"><i class="bi bi-wallet2 me-2"></i>Procesar Pago de Comisión</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <i class="bi bi-exclamation-circle text-warning" style="font-size: 3rem;"></i>
                <h4 class="mt-3" id="nombre_pago_modal">Quiropedista</h4>
                <p class="text-muted">Monto a liquidar: <strong class="text-success fs-4" id="monto_pago_modal">0.00 $</strong></p>
                <p class="small text-secondary mb-4">Al confirmar, el contador de este quiropedista se reiniciará a cero para el proximo pago.</p>
                
                <div class="d-grid gap-3">
                    <a id="btn_enviar_pago" href="#" target="_blank" class="btn btn-outline-primary btn-lg" onclick="recargarPagina()">
                        <i class="bi bi-envelope-paper-fill me-2"></i> Registrar Pago y Enviar al Correo
                    </a>    
                    
                </div>
            </div>
        </div>
    </div>
</div>

</div></div></div><?php include '../includes/footer.php'; ?>

<script>
    // Gráfico
    const ctx = document.getElementById('graficoIngresos').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($nombres_grafico); ?>,
            datasets: [{
                label: 'Ingresos Generados ($)',
                data: <?php echo json_encode($ventas_grafico); ?>,
                backgroundColor: 'rgba(13, 110, 253, 0.6)',
                borderColor: 'rgb(13, 110, 253)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: { y: { beginAtZero: true } }
        }
    });

    // Función para abrir el modal y configurar los botones
    function abrirModalPago(cedula, nombre, monto) {
        document.getElementById('nombre_pago_modal').innerText = nombre;
        document.getElementById('monto_pago_modal').innerText = monto + " $";
        
        // Aquí le decimos a los botones qué archivo ejecutar y le mandamos el parámetro liquidar=1
        document.getElementById('btn_enviar_pago').href = "../controllers/enviar_recibo_quiro.php?cedula=" + cedula + "&liquidar=1";
        
        var myModal = new bootstrap.Modal(document.getElementById('modalPago'));
        myModal.show();
    }

   
    function recargarPagina() {
        setTimeout(function() {
            window.location.reload();
        }, 1500); 
    }
</script>

<script src="../assets/js/hamburguesa.js"></script>
</body>
</html>