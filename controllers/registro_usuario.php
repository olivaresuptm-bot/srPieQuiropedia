<?php
include '../includes/db.php'; 
include '../includes/mailer.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cedula_id = $_POST['cedula'];
    $rol       = $_POST['rol'];
    $correo    = $_POST['correo'];
    $nombre    = $_POST['nombre1'];
    $pass_raw  = $_POST['clave'];

    $patron = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\/\*\$\%\!]).{8,}$/';
    if (!preg_match($patron, $pass_raw)) {
        echo "<script>alert('La clave no cumple con los requisitos de seguridad.'); window.history.back();</script>";
        exit;
    }

    // REGLA: ADMINISTRADOR ÚNICO
    if (strtolower($rol) === 'gerente') {
        $check = $conexion->prepare("SELECT COUNT(*) FROM usuarios WHERE LOWER(rol) = 'gerente'");
        $check->execute();
        if ($check->fetchColumn() > 0) {
            echo "<script>alert('Error: Ya existe un Gerente Administrador.'); window.history.back();</script>";
            exit;
        }
        $estado = 1; // Auto-activo
        $token  = null;
    } else {
        $estado = 0; // Pendiente de aprobación
        $token  = bin2hex(random_bytes(16));
    }

    $password_hash = password_hash($pass_raw, PASSWORD_BCRYPT);

    try {
        $sql = "INSERT INTO usuarios (cedula_id, tipo_doc, primer_nombre, segundo_nombre, primer_apellido, segundo_apellido, correo, password, rol, estado, token) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([
            $cedula_id, $_POST['tipo_doc'], $nombre, $_POST['nombre2'], 
            $_POST['apellido1'], $_POST['apellido2'], $correo, $password_hash, $rol, $estado, $token
        ]);

        if ($estado === 0) {
            // Buscar correo del Gerente para avisarle
            $admin = $conexion->query("SELECT correo FROM usuarios WHERE rol = 'gerente' LIMIT 1")->fetchColumn();
            $link = "srpiequiropedia.com/controllers/aprobar_usuario.php?token=$token";
            $cuerpo = "<h3>Solicitud de Registro</h3><p>El usuario $nombre ($rol) espera aprobación.</p><a href='$link'>Aprobar</a>";
            enviarEmail($admin, "Nueva solicitud", $cuerpo);
            
            echo "<script>alert('Registro exitoso. Espere aprobación.'); window.location.href='../index.php';</script>";
        } else {
            echo "<script>alert('Gerente creado correctamente.'); window.location.href='../index.php';</script>";
        }
    } catch (Exception $e) { echo "Error: " . $e->getMessage(); }
}