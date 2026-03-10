<?php
include '../includes/db.php'; 
include '../includes/mailer.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cedula_id = $_POST['cedula'];
    $rol       = $_POST['rol'];
    $correo    = $_POST['correo'];
    $nombre    = $_POST['nombre1'];
    $pass_raw  = $_POST['clave'];

    // Validación de seguridad de la contraseña
    $patron = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\/\*\$\%\!]).{8,}$/';
    if (!preg_match($patron, $pass_raw)) {
        echo "<script>alert('La clave no cumple con los requisitos de seguridad.'); window.history.back();</script>";
        exit;
    }

    // ADMINISTRADOR ÚNICO
    if (strtolower($rol) === 'gerente') {
        $check = $conexion->prepare("SELECT COUNT(*) FROM usuarios WHERE LOWER(rol) = 'gerente'");
        $check->execute();
        if ($check->fetchColumn() > 0) {
            echo "<script>alert('Error: Ya existe un Gerente Administrador.'); window.history.back();</script>";
            exit;
        }
        $estado = 1; // El gerente se auto-activa
        $token  = null;
    } else {
        $estado = 0; // Otros roles quedan pendientes de aprobación
        $token  = bin2hex(random_bytes(16));
    }

    $password_hash = password_hash($pass_raw, PASSWORD_BCRYPT);

    try {
        $conexion->beginTransaction();

        // 1. Insertar en la tabla 'usuarios'
        $sql = "INSERT INTO usuarios (cedula_id, tipo_doc, primer_nombre, segundo_nombre, primer_apellido, segundo_apellido, correo, password, rol, estado, token) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([
            $cedula_id, $_POST['tipo_doc'], $nombre, $_POST['nombre2'], 
            $_POST['apellido1'], $_POST['apellido2'], $correo, $password_hash, $rol, $estado, $token
        ]);

        // 2. ACTUALIZACIÓN: Si el rol es quiropedista, insertar en la tabla 'quiropedistas'
        if (strtolower($rol) === 'quiropedista') {
        // Se inserta con especialidad 'General' y disponibilidad 1 por defecto
            $sql_q = "INSERT INTO quiropedistas (usuario_cedula, especialidad, disponibilidad) VALUES (?, ?, ?)";
            $stmt_q = $conexion->prepare($sql_q);
            $stmt_q->execute([$cedula_id, 'General', 1]);
        }

        $conexion->commit();

        // Lógica de notificaciones
        if ($estado === 0) {
            // Buscar correo del Gerente para avisarle de la nueva solicitud
            $admin_email = $conexion->query("SELECT correo FROM usuarios WHERE rol = 'gerente' LIMIT 1")->fetchColumn();
            
            if ($admin_email) {
                $link = "srpiequiropedia.com/controllers/aprobar_usuario.php?token=$token";
                $cuerpo = "
                    <div style='font-family: sans-serif;'>
                        <h3>Nueva Solicitud de Registro</h3>
                        <p>El usuario <b>$nombre</b> ha solicitado registrarse como <b>$rol</b>.</p>
                        <p>Para habilitar este acceso, haga clic en el siguiente enlace:</p>
                        <a href='$link' style='background: #0d6efd; color: white; padding: 10px; text-decoration: none; border-radius: 5px;'>Aprobar Usuario</a>
                    </div>";
                enviarEmail($admin_email, "Nueva solicitud de acceso - Sr. Pie", $cuerpo);
            }
            
            echo "<script>alert('Registro enviado con éxito. El administrador debe aprobar su cuenta para poder iniciar sesión.'); window.location.href='../index.php';</script>";
        } else {
            echo "<script>alert('Gerente registrado y activado con éxito.'); window.location.href='../index.php';</script>";
        }

    } catch (Exception $e) {
        // Si algo falla (ej: cédula duplicada), deshacemos todo
        $conexion->rollBack();
        echo "<script>alert('Error en el registro: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
    }
}