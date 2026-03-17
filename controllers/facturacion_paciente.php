<?php
require_once '../../includes/db.php';
include '../../includes/tasa_BCV.php'; 

$cita_id = $_GET['cita_id'] ?? null;
$cita_data = null;

if ($cita_id) {
    try {
        // Buscamos los datos exactos del paciente y el servicio
        $sql = "SELECT c.*, p.primer_nombre, p.primer_apellido, p.cedula_id as paciente_cedula, 
                       s.nombre as servicio, s.precio 
                FROM citas c 
                JOIN pacientes p ON c.paciente_cedula = p.cedula_id 
                JOIN servicios s ON c.servicio_id = s.servicio_id 
                WHERE c.cita_id = :cita_id AND c.estatus = 'atendida'";
        
        $stmt = $conexion->prepare($sql);
        $stmt->execute([':cita_id' => $cita_id]);
        $cita_data = $stmt->fetch(PDO::FETCH_ASSOC);
        
    } catch(PDOException $e) {
        $error = "Error al buscar datos: " . $e->getMessage();
    }
}
?>