<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../includes/db.php';

$mensaje = ''; 
$error = '';
$paciente_buscado = null;
$paciente_cedula = $_GET['id'] ?? ''; 
$servicios = [];
$quiropedistas = [];


try {
    $stmt_servicios = $conexion->query("SELECT * FROM servicios WHERE estatus = 1");
    if ($stmt_servicios) {
        $servicios = $stmt_servicios->fetchAll(PDO::FETCH_ASSOC);
    }

    $stmt_quiro = $conexion->query("SELECT u.cedula_id as usuario_cedula, u.primer_nombre, u.primer_apellido 
                                    FROM quiropedistas q 
                                    JOIN usuarios u ON q.usuario_cedula = u.cedula_id 
                                    WHERE u.estado = 1");
    if ($stmt_quiro) {
        $quiropedistas = $stmt_quiro->fetchAll(PDO::FETCH_ASSOC);
    }

} catch(PDOException $e) {
    $error = "Error al cargar listas: " . $e->getMessage();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Buscar Paciente
    if (isset($_POST['buscar_paciente'])) {
        $paciente_cedula = trim($_POST['cedula_buscar']);
        $stmt = $conexion->prepare("SELECT * FROM pacientes WHERE cedula_id = ?");
        $stmt->execute([$paciente_cedula]);
        $paciente_buscado = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$paciente_buscado) {
            $error = "Paciente no encontrado.";
        }
    } 
    
    // Guardar Cita
    elseif (isset($_POST['guardar_cita'])) {
        try {
            
            // PASO 1: VERIFICAR DISPONIBILIDAD (MARGEN DE 1 HORA)
     
            // Evaluamos: "Busca si existe alguna cita donde la diferencia de tiempo con la nueva hora sea menor a 1 hora"
            $check_sql = "SELECT hora FROM citas 
                          WHERE quiropedista_cedula = :quiro 
                          AND fecha = :fecha 
                          AND estatus = 'programada' 
                          AND ABS(TIME_TO_SEC(hora) - TIME_TO_SEC(:hora)) < 3600 
                          LIMIT 1";
            
            $check_stmt = $conexion->prepare($check_sql);
            $check_stmt->execute([
                ':quiro' => $_POST['quiropedista_cedula'],
                ':fecha' => $_POST['fecha'],
                ':hora'  => $_POST['hora']
            ]);

            // Obtenemos la hora de la cita que está chocando (si existe)
            $cita_conflictiva = $check_stmt->fetchColumn();

            if ($cita_conflictiva) {
                // Le damos formato a la hora 
                $hora_c = substr($cita_conflictiva, 0, 5);
                
                $error = "❌ Horario no disponible: El quiropedista ya tiene una cita activa a las <strong>{$hora_c}</strong>. Esta culminará 1 hora despues.";
                
                // Mantenemos al paciente buscado activo
                $stmt = $conexion->prepare("SELECT * FROM pacientes WHERE cedula_id = ?");
                $stmt->execute([$_POST['paciente_cedula']]);
                $paciente_buscado = $stmt->fetch(PDO::FETCH_ASSOC);
                
            } else {
                
                // PASO 2: SI PASÓ LA PRUEBA, GUARDAMOS LA CITA
                
                $sql = "INSERT INTO citas (paciente_cedula, quiropedista_cedula, servicio_id, fecha, hora, estatus, aviso) 
                        VALUES (:paciente, :quiro, :serv, :fecha, :hora, 'programada', 'N')";
                $stmt = $conexion->prepare($sql);
                $stmt->execute([
                    ':paciente' => $_POST['paciente_cedula'],
                    ':quiro' => $_POST['quiropedista_cedula'],
                    ':serv' => $_POST['servicio_id'],
                    ':fecha' => $_POST['fecha'],
                    ':hora' => $_POST['hora']
                ]);
                $mensaje = "Cita agendada correctamente.";
                $paciente_buscado = null; 
                $paciente_cedula = '';
            }
        } catch(PDOException $e) {
            $error = "Error al agendar: " . $e->getMessage();
        }
    }
} else if (!empty($paciente_cedula)) {
    // Buscar si viene de la URL
    $stmt = $conexion->prepare("SELECT * FROM pacientes WHERE cedula_id = ?");
    $stmt->execute([$paciente_cedula]);
    $paciente_buscado = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>