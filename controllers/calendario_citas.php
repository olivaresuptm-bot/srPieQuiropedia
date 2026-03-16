<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../includes/db.php';

$citas = [];
$error = null;

try {
    $sql = "SELECT c.cita_id, c.fecha, c.hora, c.estatus, 
            p.cedula_id as paciente_cedula, p.primer_nombre as paciente_nombre, p.primer_apellido as paciente_apellido, 
            u.primer_nombre as quiropedista_nombre, u.primer_apellido as quiropedista_apellido, s.nombre as servicio_nombre
            FROM citas c
            JOIN pacientes p ON c.paciente_cedula = p.cedula_id
            JOIN quiropedistas q ON c.quiropedista_cedula = q.usuario_cedula
            JOIN usuarios u ON q.usuario_cedula = u.cedula_id
            JOIN servicios s ON c.servicio_id = s.servicio_id
            ORDER BY c.fecha DESC, c.hora DESC";
    
    $stmt = $conexion->prepare($sql);
    $stmt->execute();
    $citas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "Error al cargar citas: " . $e->getMessage();
}
?>