<?php
// Verificamos si el usuario actual es gerente para permitir las acciones
$es_admin_actual = ($_SESSION['rol'] === 'gerente');

// --- CAMBIO DE ESTADO ---
if (isset($_GET['action']) && $_GET['action'] === 'change_status') {
    $id = $_GET['id'] ?? '';
    $nuevo_estado = $_GET['nuevo_estado'] ?? '';
    
    try {
        // Solo el gerente puede cambiar estados
        if (!$es_admin_actual) {
            header("Location: usuarios.php?res=error_permisos");
            exit;
        }

        // Actualizamos el estado (0 para Pendiente, 1 para Activo)
        // Protegemos al gerente para que no se dé de baja a sí mismo por accidente
        $sql = "UPDATE usuarios SET estado = :estado WHERE cedula_id = :id AND rol != 'gerente'";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([':estado' => $nuevo_estado, ':id' => $id]);
        
        header("Location: usuarios.php?res=status_changed");
        exit;
    } catch (PDOException $e) {
        die("Error crítico: " . $e->getMessage());
    }
}
?>