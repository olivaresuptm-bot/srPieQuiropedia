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
$stats = $conexion->query("SELECT 
    (SELECT COUNT(*) FROM citas WHERE estatus = 'atendida') as total_citas,
    (SELECT COUNT(*) FROM pacientes) as total_pacientes,
    (SELECT SUM(monto) FROM pagos) as total_ingresos")->fetch(PDO::FETCH_ASSOC);

// ========================================================
// NUEVO: SOLO SUMAMOS LOS PAGOS PENDIENTES (estado_comision = 0)
// ========================================================
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