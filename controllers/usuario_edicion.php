<?php
// Verificamos si el usuario actual es gerente para permitir las acciones
$es_admin_actual = (isset($_SESSION['rol']) && $_SESSION['rol'] === 'gerente');

// --- 1. CAMBIO DE ESTADO  ---
if (isset($_GET['action']) && $_GET['action'] === 'change_status') {
    $id = $_GET['id'] ?? '';
    $nuevo_estado = $_GET['nuevo_estado'] ?? '';
    
    try {
        if (!$es_admin_actual) {
            header("Location: usuarios.php?res=error_permisos");
            exit;
        }

        // Actualizamos el estado. Protegemos al gerente de desactivarse a sí mismo.
        $sql = "UPDATE usuarios SET estado = :estado WHERE cedula_id = :id AND rol != 'gerente'";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([':estado' => $nuevo_estado, ':id' => $id]);
        
        header("Location: usuarios.php?res=status_changed");
        exit;
    } catch (PDOException $e) {
        die("Error crítico: " . $e->getMessage());
    }
}

// --- 2. PROCESAR EDICIÓN (POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit_user') {
    $cedula = $_POST['cedula_id'];
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $rol = $_POST['rol'];

    try {
        if (!$es_admin_actual) {
            header("Location: usuarios.php?res=error_permisos");
            exit;
        }

        $sql = "UPDATE usuarios SET nombre = :nom, apellido = :ape, rol = :rol WHERE cedula_id = :id";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([
            ':nom' => $nombre,
            ':ape' => $apellido,
            ':rol' => $rol,
            ':id'  => $cedula
        ]);

        header("Location: usuarios.php?res=user_updated");
        exit;
    } catch (PDOException $e) {
        die("Error al actualizar usuario: " . $e->getMessage());
    }
}
?>