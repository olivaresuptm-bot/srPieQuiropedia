<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit;
}

require_once '../includes/db.php'; 
require_once '../includes/tasa_BCV.php';

$tasa_actual = ($tasa_bcv) ? $tasa_bcv : 0;

// CONSULTA DE ESTADÍSTICAS: Ahora filtramos por el ENUM 'atendida' en la tabla citas
$stats = $conexion->query("SELECT 
    (SELECT COUNT(*) FROM citas WHERE estatus = 'atendida') as total_citas,
    (SELECT COUNT(*) FROM pacientes) as total_pacientes,
    (SELECT SUM(monto) FROM pagos) as total_ingresos")->fetch(PDO::FETCH_ASSOC);

// Consulta de Comisiones (Variable por Servicio)
$sql_comisiones = "SELECT 
    u.cedula_id,
    u.correo,
    CONCAT(u.primer_nombre, ' ', u.primer_apellido) AS nombre_completo, 
    SUM(p.monto) AS ventas_usd,
    SUM(p.monto * p.tasa_bcv) AS ventas_bs,
    SUM(p.monto * (s.comision_porcentaje / 100)) AS comision_usd,
    SUM(p.monto * p.tasa_bcv * (s.comision_porcentaje / 100)) AS comision_bs
    FROM pagos p
    INNER JOIN citas c ON p.cita_id = c.cita_id
    INNER JOIN usuarios u ON c.quiropedista_cedula = u.cedula_id
    INNER JOIN servicios s ON c.servicio_id = s.servicio_id
    GROUP BY u.cedula_id";
$res_comisiones = $conexion->query($sql_comisiones);

$sql_servicios = "SELECT 
    c.quiropedista_cedula,
    s.nombre AS servicio_nombre,
    COUNT(c.cita_id) AS cantidad
    FROM pagos p
    INNER JOIN citas c ON p.cita_id = c.cita_id
    INNER JOIN servicios s ON c.servicio_id = s.servicio_id
    GROUP BY c.quiropedista_cedula, s.servicio_id";
$res_servicios = $conexion->query($sql_servicios);

$desglose_servicios = [];
while ($s = $res_servicios->fetch(PDO::FETCH_ASSOC)) {
    $desglose_servicios[$s['quiropedista_cedula']][] = $s['servicio_nombre'] . " (x" . $s['cantidad'] . ")";
}

$nombres_grafico = [];
$ventas_grafico = [];
$datos_tabla = [];

while ($row = $res_comisiones->fetch(PDO::FETCH_ASSOC)) {
    $datos_tabla[] = $row; 
    $nombres_grafico[] = $row['nombre_completo']; 
    $ventas_grafico[] = $row['ventas_usd']; 
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
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
                <i class="bi bi-currency-exchange me-1"></i> Tasa BCV Actual: <?php echo number_format($tasa_actual, 2, ',', '.'); ?> Bs.
            </span>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-3">
                    <small class="text-muted fw-bold">CITAS ATENDIDAS</small>
                    <h3 class="mb-0"><?php echo $stats['total_citas']; ?></h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-3">
                    <small class="text-muted fw-bold">PACIENTES</small>
                    <h3 class="mb-0"><?php echo $stats['total_pacientes']; ?></h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-3 border-start border-success border-4">
                    <small class="text-muted fw-bold">INGRESOS BRUTOS</small>
                    <h3 class="mb-0 text-success"><?php echo number_format($stats['total_ingresos'] ?? 0, 2); ?> $</h3>
                    <span class="text-muted small fw-bold"> <?php echo number_format(($stats['total_ingresos'] ?? 0) * $tasa_actual, 2, ',', '.'); ?> Bs</span>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 text-primary fw-bold"><i class="bi bi-cash-stack me-2"></i>Pago de Comisiones (Variable por Servicio)</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Quiropedista</th>
                                <th>Servicios Realizados</th>
                                <th>Total Atendido</th>
                                <th>Comisión a Pagar</th>
                                <th class="text-center">Recibo</th>
                            </tr>
                        </thead>
                        <tbody>
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
                                    } else {
                                        echo "<span class='text-muted small'>Sin servicios pagos</span>";
                                    }
                                    ?>
                                </td>
                                <td>
                                    <span class="d-block"><?php echo number_format($fila['ventas_usd'], 2); ?> $</span>
                                    <small class="text-muted">Histórico: <?php echo number_format($fila['ventas_bs'], 2, ',', '.'); ?> Bs.</small>
                                </td>
                                <td class="fw-bold text-success fs-6">
                                    <span class="d-block"><?php echo number_format($fila['comision_usd'], 2); ?> $</span>
                                    <small class="text-muted fw-normal fs-6">A Pagar: <?php echo number_format($fila['comision_bs'], 2, ',', '.'); ?> Bs.</small>
                                </td>
                                <td class="text-center">
                                    <a href="../controllers/descargar_recibo_quiro.php?cedula=<?php echo $cedula_quiro; ?>" target="_blank" class="btn btn-sm btn-outline-danger shadow-sm me-1" title="Ver Recibo PDF">
                                        <i class="bi bi-file-pdf"></i>
                                    </a>
                                    <a href="../controllers/enviar_recibo_quiro.php?cedula=<?php echo $cedula_quiro; ?>" target="_blank" class="btn btn-sm btn-outline-primary shadow-sm" title="Enviar al Correo">
                                        <i class="bi bi-envelope-paper"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 text-primary fw-bold"><i class="bi bi-bar-chart-line me-2"></i>Rendimiento por Personal <i class="bi bi-currency-exchange me-1"></i></h6>
            </div>
            <div class="card-body">
                <canvas id="graficoIngresos" style="max-height: 350px;"></canvas>
            </div>
        </div>
        
    </div>
</div> 

<?php include '../includes/footer.php'; ?>

<script>
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
</script>

<script src="../assets/js/hamburguesa.js"></script>
</body>
</html>