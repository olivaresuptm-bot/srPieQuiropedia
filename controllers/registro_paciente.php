<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$msj = null;


require_once __DIR__ . '/../includes/db.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // --- CASO 1: EDICIÓN ---
    if (isset($_POST['action']) && $_POST['action'] === 'edit_patient') {
        try {
           // Actualización inteligente: Si la fecha viene vacía, mantiene la que ya estaba
            $sql = "UPDATE pacientes SET 
                    primer_nombre = :n1, segundo_nombre = :n2, 
                    primer_apellido = :a1, segundo_apellido = :a2, 
                    fecha_nac = COALESCE(NULLIF(:fecha_nac, ''), fecha_nac),
                    telefono = :tel, correo = :correo, instagram = :instagram, 
                    direccion = :dir, diabetico = :diabetico
                    WHERE cedula_id = :id";
            
            $stmt = $conexion->prepare($sql);
            $stmt->execute([
                ':n1' => trim($_POST['primer_nombre']),
                ':n2' => trim($_POST['segundo_nombre']),
                ':a1' => trim($_POST['primer_apellido']),
                ':a2' => trim($_POST['segundo_apellido']),
                ':fecha_nac' => trim($_POST['fecha_nac'] ?? ''),
                ':tel' => trim($_POST['telefono']),
                ':correo' => trim($_POST['correo']),
                ':instagram' => trim($_POST['instagram'] ?? ''),
                ':dir' => trim($_POST['direccion']),
                ':diabetico' => trim($_POST['diabetico'] ?? 'No'),
                ':id' => trim($_POST['cedula_id'])
            ]);
            $_SESSION['mensaje'] = ["tipo" => "success", "texto" => "✅ Paciente actualizado con éxito."];
            
            // LA SOLUCIÓN: Forzamos el regreso exacto a la pantalla de búsqueda con la cartilla del paciente
            $cedula_busqueda = trim($_POST['cedula_id']);
            header("Location: ../modulos/gestion_pacientes.php?busqueda=" . urlencode($cedula_busqueda));
            exit();
            
        } catch (PDOException $e) {
            $_SESSION['mensaje'] = ["tipo" => "error", "texto" => "❌ Error al actualizar: " . $e->getMessage()];
            
           
            $cedula_busqueda = trim($_POST['cedula_id']);
            header("Location: ../modulos/gestion_pacientes.php?busqueda=" . urlencode($cedula_busqueda));
            exit();
        }
    } 

    // --- CASO 2: REGISTRO NUEVO ---
    else if (isset($_POST['cedula_id'])) {
        try {
            $check = $conexion->prepare("SELECT COUNT(*) FROM pacientes WHERE cedula_id = ?");
            $check->execute([$_POST['cedula_id']]);
            
            if ($check->fetchColumn() > 0) {
                $_SESSION['mensaje'] = ["tipo" => "error", "texto" => "❌ La cédula del paciente ya está registrada."];
            } else {
                
                $sql = "INSERT INTO pacientes (cedula_id, tipo_doc, primer_nombre, segundo_nombre, primer_apellido, segundo_apellido, fecha_nac, genero, telefono, correo, instagram, direccion, diabetico, registrado_por) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
                $stmt = $conexion->prepare($sql);
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
                    $_POST['instagram'] ?? '',
                    $_POST['direccion'],
                    $_POST['diabetico'] ?? 'No',
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
        
      
        header("Location: pacientes.php");
        exit();
    }
}

if (isset($_SESSION['mensaje'])) {
    $msj = $_SESSION['mensaje'];
    unset($_SESSION['mensaje']);
}
?>