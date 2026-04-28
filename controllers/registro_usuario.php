<?php
session_start();

// 1. SEGURIDAD DEL BACKEND: Solo el gerente puede enviar datos a este controlador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'gerente') {
    die("Acceso denegado. Esta acción está restringida.");
}

include '../includes/db.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cedula_id = trim($_POST['cedula']);
    $rol       = trim($_POST['rol']);
    $correo    = trim($_POST['correo']);
    $nombre1   = trim($_POST['nombre1']);
    $nombre2   = trim($_POST['nombre2']);
    $apellido1 = trim($_POST['apellido1']);
    $apellido2 = trim($_POST['apellido2']);
    $pass_raw  = $_POST['clave'];

    // 2. Doble validación en el backend
    if (!preg_match('/^[0-9]+$/', $cedula_id)) {
        echo "<script>alert('Error crítico: La cédula solo debe contener números.'); window.history.back();</script>";
        exit;
    }

    if (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', $nombre1) || !preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', $apellido1)) {
        echo "<script>alert('Error crítico: Los nombres y apellidos solo deben contener letras.'); window.history.back();</script>";
        exit;
    }

    $patron_clave = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\/\*\$\%\!]).{8,}$/';
    if (!preg_match($patron_clave, $pass_raw)) {
        echo "<script>alert('La clave no cumple con los requisitos de seguridad.'); window.history.back();</script>";
        exit;
    }

    // 3. Como el gerente es quien los crea, TODOS los usuarios nacen activos
    $estado = 1; 

    // Verificación de Gerente Único
    if (strtolower($rol) === 'gerente') {
        $check = $conexion->prepare("SELECT COUNT(*) FROM usuarios WHERE LOWER(rol) = 'gerente'");
        $check->execute();
        if ($check->fetchColumn() > 0) {
            echo "<script>alert('Error: Ya existe un Gerente Administrador en el sistema.'); window.history.back();</script>";
            exit;
        }
    }
    
    $password_hash = password_hash($pass_raw, PASSWORD_BCRYPT);

    try {
        $conexion->beginTransaction();

        // AQUÍ LA CORRECCIÓN: Se eliminó la columna 'token' del INSERT
        $sql = "INSERT INTO usuarios (cedula_id, tipo_doc, primer_nombre, segundo_nombre, primer_apellido, segundo_apellido, correo, password, rol, estado) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([
            $cedula_id, $_POST['tipo_doc'], $nombre1, $nombre2, 
            $apellido1, $apellido2, $correo, $password_hash, $rol, $estado
        ]);

        // ==========================================
        // BITÁCORA 1: Registro del Usuario Base
        $det_usu = "Nuevo empleado registrado: $nombre1 $apellido1 ($cedula_id) - Rol: " . ucfirst($rol);
        registrar_bitacora($conexion, 'INSERTAR', 'usuarios', $cedula_id, $det_usu);
        // ==========================================

        // Si el rol es quiropedista, insertar en su tabla
        if (strtolower($rol) === 'quiropedista') {
            $sql_q = "INSERT INTO quiropedistas (usuario_cedula, especialidad, disponibilidad) VALUES (?, ?, ?)";
            $stmt_q = $conexion->prepare($sql_q);
            $stmt_q->execute([$cedula_id, 'General', 1]);
            
            // ==========================================
            // BITÁCORA 2: Registro del Perfil Quiropedista
            $det_quiro = "Perfil de quiropedista creado automáticamente para la cédula: $cedula_id";
            registrar_bitacora($conexion, 'INSERTAR', 'quiropedistas', $cedula_id, $det_quiro);
            // ==========================================
        }

        $conexion->commit();

        // 4. Redirección actualizada
        echo "<script>alert('✅ Empleado registrado y activado con éxito.'); window.location.href='../modulos/usuarios.php';</script>";

    } catch (Exception $e) {
        $conexion->rollBack();
        if (strpos($e->getMessage(), '1062') !== false || $e->getCode() == 23000) {
             echo "<script>alert('❌ Error: La cédula o el correo ya están registrados en el sistema.'); window.history.back();</script>";
        } else {
             echo "<script>alert('❌ Error en el registro: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
        }
    }
}
?>