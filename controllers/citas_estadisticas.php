<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../includes/db.php';

$citas_hoy = 0;
$citas_proximas_24h = 0;
$citas_proximos_7dias = 0;

try {
    // 1. Citas de HOY
    $sql_hoy = "SELECT COUNT(*) as total FROM citas 
                WHERE fecha = CURDATE() 
                AND estatus = 'programada'";
    $stmt_hoy = $conexion->query($sql_hoy);
    $citas_hoy = $stmt_hoy->fetch(PDO::FETCH_ASSOC)['total'];

    // 2. Citas Próximas 24h
    
    $sql_24h = "SELECT COUNT(*) as total FROM citas 
                WHERE CAST(CONCAT(fecha, ' ', hora) AS DATETIME) > NOW() 
                AND CAST(CONCAT(fecha, ' ', hora) AS DATETIME) <= DATE_ADD(NOW(), INTERVAL 24 HOUR) 
                AND estatus = 'programada'";
    $stmt_24h = $conexion->query($sql_24h);
    $citas_proximas_24h = $stmt_24h->fetch(PDO::FETCH_ASSOC)['total'];

    // 3. Citas próximos 7 días
    $sql_7d = "SELECT COUNT(*) as total FROM citas 
               WHERE fecha >= CURDATE() 
               AND fecha <= DATE_ADD(CURDATE(), INTERVAL 7 DAY) 
               AND estatus = 'programada'";
    $stmt_7d = $conexion->query($sql_7d);
    $citas_proximos_7dias = $stmt_7d->fetch(PDO::FETCH_ASSOC)['total'];

} catch(PDOException $e) {
    error_log("Error en estadísticas: " . $e->getMessage());
}
?>