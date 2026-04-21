<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id'])) {
    header("Location: /../index.php");
    exit;
}

require_once '../includes/db.php'; 
require_once '../includes/tasa_BCV.php';

$tasa_actual = ($tasa_bcv) ? $tasa_bcv : 0;

// ESTADÍSTICAS GLOBALES
$sql_stats = "SELECT 
    -- HISTÓRICO TOTAL
    (SELECT COUNT(*) FROM citas WHERE estatus = 'atendida') as citas_hist,
    (SELECT COUNT(*) FROM pacientes) as pac_hist,
    (SELECT COALESCE(SUM(monto), 0) FROM pagos) as ing_hist,
    
    -- ESTE AÑO (ANUAL)
    (SELECT COUNT(*) FROM citas WHERE estatus = 'atendida' AND YEAR(fecha) = YEAR(CURDATE())) as citas_anual,
    (SELECT COUNT(*) FROM pacientes WHERE YEAR(fecha_registro) = YEAR(CURDATE())) as pac_anual,
    (SELECT COALESCE(SUM(monto), 0) FROM pagos WHERE YEAR(fecha_pago) = YEAR(CURDATE())) as ing_anual,
    
    -- ESTE MES (MENSUAL)
    (SELECT COUNT(*) FROM citas WHERE estatus = 'atendida' AND YEAR(fecha) = YEAR(CURDATE()) AND MONTH(fecha) = MONTH(CURDATE())) as citas_mensual,
    (SELECT COUNT(*) FROM pacientes WHERE YEAR(fecha_registro) = YEAR(CURDATE()) AND MONTH(fecha_registro) = MONTH(CURDATE())) as pac_mensual,
    (SELECT COALESCE(SUM(monto), 0) FROM pagos WHERE YEAR(fecha_pago) = YEAR(CURDATE()) AND MONTH(fecha_pago) = MONTH(CURDATE())) as ing_mensual,
    
    -- ESTA SEMANA (SEMANAL)
    (SELECT COUNT(*) FROM citas WHERE estatus = 'atendida' AND YEARWEEK(fecha, 1) = YEARWEEK(CURDATE(), 1)) as citas_semanal,
    (SELECT COUNT(*) FROM pacientes WHERE YEARWEEK(fecha_registro, 1) = YEARWEEK(CURDATE(), 1)) as pac_semanal,
    (SELECT COALESCE(SUM(monto), 0) FROM pagos WHERE YEARWEEK(fecha_pago, 1) = YEARWEEK(CURDATE(), 1)) as ing_semanal,

    -- ESTE DÍA (DIARIO)
    (SELECT COUNT(*) FROM citas WHERE estatus = 'atendida' AND DATE(fecha) = CURDATE()) as citas_diario,
    (SELECT COUNT(*) FROM pacientes WHERE DATE(fecha_registro) = CURDATE()) as pac_diario,
    (SELECT COALESCE(SUM(monto), 0) FROM pagos WHERE DATE(fecha_pago) = CURDATE()) as ing_diario
";
$stats = $conexion->query($sql_stats)->fetch(PDO::FETCH_ASSOC);

// SOLO SUMAMOS LOS PAGOS PENDIENTES (estado_comision = 0)

$sql_comisiones = "SELECT 
    u.cedula_id,
    u.correo,
    CONCAT(u.primer_nombre, ' ', u.primer_apellido) AS nombre_completo, 
    COALESCE(SUM(p.monto), 0) AS ventas_usd,
    COALESCE(SUM(p.monto * p.tasa_bcv), 0) AS ventas_bs,
    COALESCE(SUM(p.monto * (s.comision_porcentaje / 100)), 0) AS comision_usd,
    COALESCE(SUM(p.monto * p.tasa_bcv * (s.comision_porcentaje / 100)), 0) AS comision_bs
    FROM usuarios u
    INNER JOIN quiropedistas q ON u.cedula_id = q.usuario_cedula
    LEFT JOIN citas c ON u.cedula_id = c.quiropedista_cedula AND c.estado_comision = 0 AND c.estatus = 'atendida'
    LEFT JOIN pagos p ON c.cita_id = p.cita_id
    LEFT JOIN servicios s ON c.servicio_id = s.servicio_id
    WHERE u.estado = 1
    GROUP BY u.cedula_id";
$res_comisiones = $conexion->query($sql_comisiones);

// DESGLOSE DE SERVICIOS (Solo para las citas pendientes)
$sql_servicios = "SELECT 
    c.quiropedista_cedula,
    s.nombre AS servicio_nombre,
    COUNT(c.cita_id) AS cantidad
    FROM citas c
    INNER JOIN pagos p ON c.cita_id = p.cita_id
    INNER JOIN servicios s ON c.servicio_id = s.servicio_id
    WHERE c.estado_comision = 0 AND c.estatus = 'atendida'
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