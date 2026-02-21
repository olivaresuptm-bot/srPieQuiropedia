<?php
include '../includes/db.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //Ya que esto es registro_usuario estos son los campos que deben coincidir con la bd tabla de usuario
    $cedula_id       = $_POST['cedula'];
    $tipo_doc        = $_POST['tipo_doc'];
    $primer_nombre   = $_POST['nombre1'];
    $segundo_nombre  = $_POST['nombre2'];
    $primer_apellido = $_POST['apellido1'];
    $segundo_apellido = $_POST['apellido2'];
    $correo          = $_POST['correo'];
    $password_raw    = $_POST['clave'];
    $rol             = $_POST['rol'];

    // Encriptación
    $password_hash = password_hash($password_raw, PASSWORD_BCRYPT);

    try {
        $sql = "INSERT INTO usuarios (cedula_id, tipo_doc, primer_nombre, segundo_nombre, primer_apellido, segundo_apellido, correo, password, rol) 
                VALUES (:cedula, :tipo, :nom1, :nom2, :ape1, :ape2, :correo, :pass, :rol)";
        
        $stmt = $conexion->prepare($sql);
        
        $stmt->execute([
            ':cedula' => $cedula_id,
            ':tipo'   => $tipo_doc,
            ':nom1'   => $primer_nombre,
            ':nom2'   => $segundo_nombre,
            ':ape1'   => $primer_apellido,
            ':ape2'   => $segundo_apellido,
            ':correo' => $correo,
            ':pass'   => $password_hash,
            ':rol'    => $rol
        ]);

        echo "<script>alert('¡Registro exitoso!'); window.location.href='../index.php';</script>";

    } catch (PDOException $e) {
        echo "Error en la base de datos: " . $e->getMessage();
    }
}
?>