<?php
// Verificamos si el usuario actual es gerente
$es_admin_actual = (isset($_SESSION['rol']) && $_SESSION['rol'] === 'gerente');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit_user') {
    $cedula = $_POST['cedula_id'];
    
    $n1 = trim($_POST['primer_nombre']);
    $n2 = trim($_POST['segundo_nombre']);
    $a1 = trim($_POST['primer_apellido']);
    $a2 = trim($_POST['segundo_apellido']);
    $rol = $_POST['rol'];

    try {
        if (!$es_admin_actual) {
            header("Location: usuarios.php?res=error_permisos");
            exit;
        }

        $sql = "UPDATE usuarios SET 
                primer_nombre = :n1, 
                segundo_nombre = :n2, 
                primer_apellido = :a1, 
                segundo_apellido = :a2,
                rol = :rol 
                WHERE cedula_id = :id";
        
        $stmt = $conexion->prepare($sql);
        $stmt->execute([
            ':n1' => $n1,
            ':n2' => $n2,
            ':a1' => $a1,
            ':a2' => $a2,
            ':rol' => $rol,
            ':id'  => $cedula
        ]);

        header("Location: usuarios.php?res=user_updated");
        exit;
    } catch (PDOException $e) {
        die("Error al actualizar usuario: " . $e->getMessage());
    }
}


// --- 2. CAMBIO DE ESTADO (EXISTENTE) ---
if (isset($_GET['action']) && $_GET['action'] === 'change_status') {
    $id = $_GET['id'] ?? '';
    $nuevo_estado = $_GET['nuevo_estado'] ?? '';
    
    try {
        if (!$es_admin_actual) {
            header("Location: usuarios.php?res=error_permisos");
            exit;
        }

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