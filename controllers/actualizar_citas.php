<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../includes/db.php';

$mensaje = "";
$error = "";
$search = "";


// EDICIÓN DE CITA 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'editar_cita') {
    try {
        $sql_edit = "UPDATE citas SET 
                     quiropedista_cedula = :quiro, 
                     servicio_id = :serv, 
                     fecha = :fecha, 
                     hora = :hora 
                     WHERE cita_id = :id";
                     
        $stmt_edit = $conexion->prepare($sql_edit);
        $stmt_edit->execute([
            ':quiro' => $_POST['quiropedista_cedula'],
            ':serv'  => $_POST['servicio_id'],
            ':fecha' => $_POST['fecha'],
            ':hora'  => $_POST['hora'],
            ':id'    => $_POST['cita_id']
        ]);
        
        $mensaje = "✅ Cita actualizada correctamente.";
    } catch(PDOException $e) {
        $error = "❌ Error al editar la cita: " . $e->getMessage();
    }
}


// Cambio de estatus (Vía GET)

if (isset($_GET['accion']) && isset($_GET['id'])) {
    $cita_id = $_GET['id'];
    $nuevo_estatus = $_GET['accion'] == 'atendida' ? 'atendida' : 'cancelada';
    
    try {
        $sql_update = "UPDATE citas SET estatus = :estatus WHERE cita_id = :id";
        $stmt_update = $conexion->prepare($sql_update);
        $stmt_update->execute([
            ':estatus' => $nuevo_estatus,
            ':id' => $cita_id
        ]);
        
        $mensaje = "✅ Cita actualizada a: " . ucfirst($nuevo_estatus);
    } catch(PDOException $e) {
        $error = "❌ Error al actualizar: " . $e->getMessage();
    }
}


try {
    $stmt_quiro = $conexion->query("SELECT u.cedula_id as usuario_cedula, u.primer_nombre, u.primer_apellido FROM quiropedistas q JOIN usuarios u ON q.usuario_cedula = u.cedula_id WHERE u.estado = 1");
    $quiropedistas = $stmt_quiro->fetchAll(PDO::FETCH_ASSOC);

    $stmt_serv = $conexion->query("SELECT servicio_id, nombre FROM servicios WHERE estatus = 1");
    $servicios = $stmt_serv->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $quiropedistas = [];
    $servicios = [];
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

//
// Construir consulta con búsqueda del listado
// 
try {
  
    $sql = "SELECT 
                c.cita_id, c.fecha, c.hora, c.estatus,
                p.cedula_id as paciente_cedula, p.primer_nombre as paciente_nombre, p.primer_apellido as paciente_apellido, p.telefono as paciente_telefono,
                u.cedula_id as quiropedista_cedula, u.primer_nombre as quiropedista_nombre, u.primer_apellido as quiropedista_apellido,
                s.servicio_id, s.nombre as servicio_nombre, s.precio as servicio_precio,
                pg.pago_id 
            FROM citas c
            JOIN pacientes p ON c.paciente_cedula = p.cedula_id
            JOIN quiropedistas q ON c.quiropedista_cedula = q.usuario_cedula
            JOIN usuarios u ON q.usuario_cedula = u.cedula_id
            JOIN servicios s ON c.servicio_id = s.servicio_id
            LEFT JOIN pagos pg ON c.cita_id = pg.cita_id 
            WHERE 1=1";
    
    $params = [];
    
    if (!empty($search)) {
        $sql .= " AND (
                    p.primer_nombre LIKE :search 
                    OR p.primer_apellido LIKE :search
                    OR p.cedula_id LIKE :search
                    OR u.primer_nombre LIKE :search
                    OR u.primer_apellido LIKE :search
                    OR u.cedula_id LIKE :search
                    OR s.nombre LIKE :search
                    OR c.fecha LIKE :search
                    OR c.hora LIKE :search
                    OR c.estatus LIKE :search
                )";
        $params[':search'] = "%$search%";
    }
    
    $sql .= " ORDER BY c.fecha DESC, c.hora DESC";
    
    $stmt = $conexion->prepare($sql);
    $stmt->execute($params);
    $citas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    $error = "Error al cargar citas: " . $e->getMessage();
    $citas = [];
}
?>