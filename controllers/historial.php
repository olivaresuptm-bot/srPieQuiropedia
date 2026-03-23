<?php
$cedula = $_GET['cedula'] ?? '';
if (!$cedula) {
    die("<script>alert('Error: Cédula no proporcionada.'); window.location.href='pacientes.php';</script>");
}

$msj = null;

// --- 1. LÓGICA PARA CREAR UN HISTORIAL NUEVO (INSERT) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nuevo_historial'])) {
    try {
        $stmt_q = $conexion->prepare("SELECT quiropedista_cedula FROM citas WHERE cita_id = ?");
        $stmt_q->execute([$_POST['cita_id']]);
        $quiro_cedula = $stmt_q->fetchColumn();

        $sql = "INSERT INTO historial_clinico (paciente_cedula, cita_id, quiropedista_cedula, motivo_consulta, diagnostico, tratamiento, observaciones) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([
            $cedula, 
            $_POST['cita_id'], 
            $quiro_cedula,
            $_POST['motivo_consulta'], 
            $_POST['diagnostico'], 
            $_POST['tratamiento'], 
            $_POST['observaciones']
        ]);
        $msj = ["tipo" => "success", "texto" => "✅ Nuevo historial médico redactado correctamente."];
    } catch (PDOException $e) {
        $msj = ["tipo" => "error", "texto" => "❌ Error al guardar el historial: " . $e->getMessage()];
    }
}

// --- 2. LÓGICA PARA ACTUALIZAR UN REGISTRO EXISTENTE (UPDATE) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['editar_historial'])) {
    try {
        $sql = "UPDATE historial_clinico SET 
                motivo_consulta = ?, diagnostico = ?, tratamiento = ?, observaciones = ? 
                WHERE historial_id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([
            $_POST['motivo_consulta'], 
            $_POST['diagnostico'], 
            $_POST['tratamiento'], 
            $_POST['observaciones'], 
            $_POST['historial_id']
        ]);
        $msj = ["tipo" => "success", "texto" => "✅ Registro médico actualizado correctamente."];
    } catch (PDOException $e) {
        $msj = ["tipo" => "error", "texto" => "❌ Error al actualizar: " . $e->getMessage()];
    }
}

// --- 3. OBTENER DATOS DEL PACIENTE ---
$stmt_pac = $conexion->prepare("SELECT * FROM pacientes WHERE cedula_id = ?");
$stmt_pac->execute([$cedula]);
$paciente = $stmt_pac->fetch(PDO::FETCH_ASSOC);

if (!$paciente) {
    die("<script>alert('Paciente no encontrado.'); window.location.href='pacientes.php';</script>");
}

// --- 4. OBTENER TODAS LAS CITAS DEL PACIENTE ---
$stmt_todas_citas = $conexion->prepare("
    SELECT cita_id, fecha, estatus 
    FROM citas 
    WHERE paciente_cedula = ? 
    ORDER BY fecha DESC
");
$stmt_todas_citas->execute([$cedula]);
$todas_las_citas = $stmt_todas_citas->fetchAll(PDO::FETCH_ASSOC);

// --- 5. OBTENER TODOS LOS HISTORIALES VIEJOS DEL PACIENTE ---
$stmt_hist = $conexion->prepare("
    SELECT h.*, s.nombre AS servicio_nombre
    FROM historial_clinico h 
    LEFT JOIN citas c ON h.cita_id = c.cita_id
    LEFT JOIN servicios s ON c.servicio_id = s.servicio_id
    WHERE h.paciente_cedula = ? 
    ORDER BY h.fecha_registro DESC
");
$stmt_hist->execute([$cedula]);
$historiales = $stmt_hist->fetchAll(PDO::FETCH_ASSOC);
$fecha_nac = new DateTime($paciente['fecha_nac']);
$hoy = new DateTime();
$edad = $hoy->diff($fecha_nac)->y;
?>