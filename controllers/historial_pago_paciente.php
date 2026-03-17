<?php
require_once '../../includes/db.php';

$cedula = $_GET['cedula'] ?? '';
$paciente = null;
$pagos = [];

if ($cedula) {
    try {
        // 1. Buscar datos básicos del paciente
        $stmt_pac = $conexion->prepare("SELECT primer_nombre, segundo_nombre, primer_apellido, segundo_apellido, cedula_id FROM pacientes WHERE cedula_id = ?");
        $stmt_pac->execute([$cedula]);
        $paciente = $stmt_pac->fetch(PDO::FETCH_ASSOC);

        // 2. Buscar el historial de pagos conectando 3 tablas (Pagos -> Citas -> Servicios)
        if ($paciente) {
            $sql_pagos = "SELECT pg.pago_id, pg.monto, pg.metodo_pago, pg.fecha_pago, pg.referencia,
                                 c.fecha as fecha_cita, s.nombre as servicio_nombre
                          FROM pagos pg
                          JOIN citas c ON pg.cita_id = c.cita_id
                          JOIN servicios s ON c.servicio_id = s.servicio_id
                          WHERE c.paciente_cedula = ?
                          ORDER BY pg.fecha_pago DESC";
            
            $stmt_pagos = $conexion->prepare($sql_pagos);
            $stmt_pagos->execute([$cedula]);
            $pagos = $stmt_pagos->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch(PDOException $e) {
        $error = "Error al cargar historial: " . $e->getMessage();
    }
}
?>