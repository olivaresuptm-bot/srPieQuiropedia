<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$msj = null;
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

    // --- CASO 1: EDICIÓN---
    if (isset($_POST['action']) && $_POST['action'] === 'edit_patient') {
        try {
            $sql = "UPDATE pacientes SET 
                    primer_nombre = :n1, segundo_nombre = :n2, 
                    primer_apellido = :a1, segundo_apellido = :a2, 
                    telefono = :tel, correo = :correo, direccion = :dir 
                    WHERE cedula_id = :id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':n1' => trim($_POST['primer_nombre']),
                ':n2' => trim($_POST['segundo_nombre']),
                ':a1' => trim($_POST['primer_apellido']),
                ':a2' => trim($_POST['segundo_apellido']),
                ':tel' => trim($_POST['telefono']),
                ':correo' => trim($_POST['correo']),
                ':dir' => trim($_POST['direccion']),
                ':id' => trim($_POST['cedula_id'])
            ]);

            $_SESSION['mensaje'] = ["tipo" => "success", "texto" => "✅ Paciente actualizado con éxito."];
            header("Location: ../modulos/gestion_pacientes.php");
            exit();
        } catch (PDOException $e) {
            $_SESSION['mensaje'] = ["tipo" => "error", "texto" => "❌ Error al actualizar: " . $e->getMessage()];
            header("Location: ../modulos/gestion_pacientes.php");
            exit();
        }
    } 

    // --- CASO 2: REGISTRO NUEVO ---
    else if (isset($_POST['cedula_id'])) {
        try {
            // Verificar si la cédula del paciente ya existe
            $check = $pdo->prepare("SELECT COUNT(*) FROM pacientes WHERE cedula_id = ?");
            $check->execute([$_POST['cedula_id']]);
            
            if ($check->fetchColumn() > 0) {
                $_SESSION['mensaje'] = ["tipo" => "error", "texto" => "❌ La cédula del paciente ya está registrada."];
            } else {
                
                $sql = "INSERT INTO pacientes (cedula_id, tipo_doc, primer_nombre, segundo_nombre, primer_apellido, segundo_apellido, fecha_nac, genero, telefono, correo, direccion, registrado_por) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    $_POST['cedula_id'], 
                    $_POST['tipo_doc'], 
                    $_POST['primer_nombre'], 
                    $_POST['segundo_nombre'], 
                    $_POST['primer_apellido'], 
                    $_POST['segundo_apellido'], 
                    $_POST['fecha_nac'], 
                    $_POST['genero'], 
                    $_POST['telefono'], 
                    $_POST['correo'], 
                    $_POST['direccion'],
                    $_POST['registrado_por'] 
                ]);

                $_SESSION['mensaje'] = ["tipo" => "success", "texto" => "✅ Paciente registrado con éxito."];
            }
        } catch (PDOException $e) {
            
            if (strpos($e->getMessage(), '1452') !== false || $e->getCode() == 23000) {
                $_SESSION['mensaje'] = ["tipo" => "error", "texto" => "❌ Error: La cédula ingresada en 'Registrado Por' no corresponde a ningún usuario del sistema. Verifique la cédula del operador."];
            } else {
                $_SESSION['mensaje'] = ["tipo" => "error", "texto" => "❌ Error: " . $e->getMessage()];
            }
        }
        header("Location: ../modulos/pacientes.php");
        exit();
    }
}


if (isset($_SESSION['mensaje'])) {
    $msj = $_SESSION['mensaje'];
    unset($_SESSION['mensaje']);
}
?>