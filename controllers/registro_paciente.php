<?php
$dsn = "mysql:host=localhost;dbname=srpiequiropedia;charset=utf8mb4";
$usuario = "root";
$password = "";

try {
    $pdo = new PDO($dsn, $usuario, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $cedula = $_POST['cedula_id'];

        // Verificar si la cédula ya existe
        $verificarSql = "SELECT COUNT(*) FROM pacientes WHERE cedula_id = :cedula";
        $verificarSmt = $pdo->prepare($verificarSql);
        $verificarSmt->execute([':cedula' => $cedula]);
        
        if ($verificarSmt->fetchColumn() > 0) {
        // Si existe lanza un error
            $_SESSION['mensaje'] = ["tipo" => "error", "texto" => "❌ Error: El número de cédula ya se encuentra registrado."];
        } else {
        // Si no existe se procesa el registro
            $sql = "INSERT INTO pacientes (cedula_id, tipo_doc, primer_nombre, segundo_nombre, primer_apellido, segundo_apellido, fecha_nac, genero, telefono, correo, direccion, registrado_por, fecha_registro) 
                    VALUES (:cedula, :tipo, :nom1, :nom2, :ape1, :ape2, :f_nac, :gen, :tel, :cor, :dir, :reg_por, :f_reg)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':cedula'  => $cedula,
                ':tipo'    => $_POST['tipo_doc'],
                ':nom1'    => $_POST['primer_nombre'],
                ':nom2'    => $_POST['segundo_nombre'],
                ':ape1'    => $_POST['primer_apellido'],
                ':ape2'    => $_POST['segundo_apellido'],
                ':f_nac'   => $_POST['fecha_nac'],
                ':gen'     => $_POST['genero'],
                ':tel'     => $_POST['telefono'],
                ':cor'     => $_POST['correo'],
                ':dir'     => $_POST['direccion'],
                ':reg_por' => $_POST['registrado_por'],
                ':f_reg'   => date("Y-m-d H:i:s")
            ]);

            $_SESSION['mensaje'] = ["tipo" => "success", "texto" => "Paciente guardado con éxito."];
        }
    } catch (PDOException $e) {
        $_SESSION['mensaje'] = ["tipo" => "error", "texto" => "❌ Error: " . $e->getMessage()];
    }
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

$msj = $_SESSION['mensaje'] ?? null;
unset($_SESSION['mensaje']);
?>