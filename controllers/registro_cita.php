<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit;
}

include '../includes/db.php';

// Variables para mensajes
$mensaje = "";
$error = "";

// Duración de la cita en minutos (para evitar citas cercanas)
$duracion_cita = 50; // 60 minutos = 1 hora

// Procesar el formulario cuando se envíe
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    try {
        // Recibir datos
        $paciente_cedula = $_POST['paciente_cedula'];
        $quiropedista_cedula = $_POST['quiropedista_cedula'];
        $servicio_id = $_POST['servicio_id'];
        $fecha = $_POST['fecha'];
        $hora = $_POST['hora'];
        
        // Validar que la hora esté dentro del horario laboral (8am - 6pm)
        if ($hora < '08:00' || $hora > '18:00') {
            $error = "❌ La hora debe estar entre 8:00 AM y 6:00 PM";
        }
        
        // ============================================
        // VALIDACIÓN 1: Verificar si ya existe una cita EXACTAMENTE a la misma hora
        // ============================================
        if (empty($error)) {
            $sql_misma_hora = "SELECT cita_id FROM citas 
                              WHERE quiropedista_cedula = :quiropedista 
                              AND fecha = :fecha 
                              AND hora = :hora 
                              AND estatus != 'cancelada'";
            
            $stmt_misma_hora = $conexion->prepare($sql_misma_hora);
            $stmt_misma_hora->execute([
                ':quiropedista' => $quiropedista_cedula,
                ':fecha' => $fecha,
                ':hora' => $hora
            ]);
            
            if ($stmt_misma_hora->rowCount() > 0) {
                $error = "❌ El quiropedista ya tiene una cita programada exactamente a las $hora en esa fecha.";
            }
        }
        
        // ============================================
        // VALIDACIÓN 2: Verificar si hay citas en un rango de +/- duración_cita minutos
        // ============================================
        if (empty($error)) {
            // Convertir hora a timestamp para calcular rangos
            $hora_cita = strtotime($hora);
            $hora_inicio = date('H:i:s', strtotime("-$duracion_cita minutes", $hora_cita));
            $hora_fin = date('H:i:s', strtotime("+$duracion_cita minutes", $hora_cita));
            
            $sql_rango = "SELECT cita_id, hora FROM citas 
                         WHERE quiropedista_cedula = :quiropedista 
                         AND fecha = :fecha 
                         AND hora BETWEEN :hora_inicio AND :hora_fin
                         AND estatus != 'cancelada'";
            
            $stmt_rango = $conexion->prepare($sql_rango);
            $stmt_rango->execute([
                ':quiropedista' => $quiropedista_cedula,
                ':fecha' => $fecha,
                ':hora_inicio' => $hora_inicio,
                ':hora_fin' => $hora_fin
            ]);
            
            if ($stmt_rango->rowCount() > 0) {
                $cita_conflicto = $stmt_rango->fetch(PDO::FETCH_ASSOC);
                $error = "❌ El quiropedista ya tiene una cita cercana a las " . $cita_conflicto['hora'] . 
                         ". Debe haber al menos $duracion_cita minutos de diferencia entre citas.";
            }
        }
        
        // ============================================
        // Si no hay errores, proceder a guardar
        // ============================================
        if (empty($error)) {
            $sql = "INSERT INTO citas (paciente_cedula, quiropedista_cedula, servicio_id, fecha, hora, estatus, aviso) 
                    VALUES (:paciente, :quiropedista, :servicio, :fecha, :hora, 'programada', 'N')";
            
            $stmt = $conexion->prepare($sql);
            $stmt->execute([
                ':paciente' => $paciente_cedula,
                ':quiropedista' => $quiropedista_cedula,
                ':servicio' => $servicio_id,
                ':fecha' => $fecha,
                ':hora' => $hora
            ]);
            
            $mensaje = "✅ Cita guardada correctamente";
        }
        
    } catch(PDOException $e) {
        $error = "❌ Error: " . $e->getMessage();
    }
}

// Obtener datos para los selects (con PDO)
try {
    $pacientes = $conexion->query("SELECT cedula_id, primer_nombre, primer_apellido FROM pacientes ORDER BY primer_nombre");
    $quiropedistas = $conexion->query("SELECT q.usuario_cedula, u.primer_nombre, u.primer_apellido 
                                       FROM quiropedistas q 
                                       JOIN usuarios u ON q.usuario_cedula = u.cedula_id 
                                       WHERE q.disponibilidad = 1
                                       ORDER BY u.primer_nombre");
    $servicios = $conexion->query("SELECT servicio_id, nombre FROM servicios WHERE estatus = 1 ORDER BY nombre");
} catch(PDOException $e) {
    $error = "Error al cargar datos: " . $e->getMessage();
}
?>