<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit;
}

// ✅ INCLUIR LA CONEXIÓN PDO DESDE INCLUDES
include '../includes/db.php';

// Variables para mensajes
$mensaje = "";
$error = "";

// Variables para el paciente buscado
$paciente_buscado = null;
$paciente_cedula = "";

// Procesar búsqueda de paciente
if (isset($_POST['buscar_paciente'])) {
    $cedula_buscar = $_POST['cedula_buscar'];
    
    try {
        $sql_buscar = "SELECT cedula_id, primer_nombre, segundo_nombre, primer_apellido, segundo_apellido 
                      FROM pacientes WHERE cedula_id = :cedula";
        $stmt_buscar = $conexion->prepare($sql_buscar);
        $stmt_buscar->execute([':cedula' => $cedula_buscar]);
        
        if ($stmt_buscar->rowCount() > 0) {
            $paciente_buscado = $stmt_buscar->fetch(PDO::FETCH_ASSOC);
            $paciente_cedula = $cedula_buscar;
        } else {
            $error = "❌ No se encontró ningún paciente con la cédula: " . $cedula_buscar;
        }
    } catch(PDOException $e) {
        $error = "Error al buscar: " . $e->getMessage();
    }
}

// Procesar el formulario cuando se envíe la cita
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['guardar_cita'])) {
    
    try {
        // Recibir datos
        $paciente_cedula = $_POST['paciente_cedula'];
        $quiropedista_cedula = $_POST['quiropedista_cedula'];
        $servicio_id = $_POST['servicio_id'];
        $fecha = $_POST['fecha'];
        $hora = $_POST['hora'];
        
        // Validar que el paciente exista
        $check_paciente = $conexion->prepare("SELECT cedula_id FROM pacientes WHERE cedula_id = :cedula");
        $check_paciente->execute([':cedula' => $paciente_cedula]);
        
        if ($check_paciente->rowCount() == 0) {
            $error = "❌ El paciente con cédula $paciente_cedula no existe en el sistema";
        } else {
            // INSERT con PDO (SEGURO)
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
            
            // Limpiar la búsqueda después de guardar
            $paciente_buscado = null;
            $paciente_cedula = "";
        }
        
    } catch(PDOException $e) {
        $error = "❌ Error: " . $e->getMessage();
    }
}

// Obtener datos para los otros selects
try {
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