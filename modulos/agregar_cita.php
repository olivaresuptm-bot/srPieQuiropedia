<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit;
}

include '../includes/db.php';

// Variables para mensajes
$mensaje = "";
$error = "";

// Duración de la cita en minutos (para evitar citas cercanas)
$duracion_cita = 50; // 60 minutos = 1 hora

// Procesar el formulario cuando se envíe
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    try {
        // Recibir datos
        $paciente_cedula = $_POST['paciente_cedula'];
        $quiropedista_cedula = $_POST['quiropedista_cedula'];
        $servicio_id = $_POST['servicio_id'];
        $fecha = $_POST['fecha'];
        $hora = $_POST['hora'];
        
        // Validar que la hora esté dentro del horario laboral (8am - 6pm)
        if ($hora < '08:00' || $hora > '18:00') {
            $error = "❌ La hora debe estar entre 8:00 AM y 6:00 PM";
        }
        
        // ============================================
        // VALIDACIÓN 1: Verificar si ya existe una cita EXACTAMENTE a la misma hora
        // ============================================
        if (empty($error)) {
            $sql_misma_hora = "SELECT cita_id FROM citas 
                              WHERE quiropedista_cedula = :quiropedista 
                              AND fecha = :fecha 
                              AND hora = :hora 
                              AND estatus != 'cancelada'";
            
            $stmt_misma_hora = $conexion->prepare($sql_misma_hora);
            $stmt_misma_hora->execute([
                ':quiropedista' => $quiropedista_cedula,
                ':fecha' => $fecha,
                ':hora' => $hora
            ]);
            
            if ($stmt_misma_hora->rowCount() > 0) {
                $error = "❌ El quiropedista ya tiene una cita programada exactamente a las $hora en esa fecha.";
            }
        }
        
        // ============================================
        // VALIDACIÓN 2: Verificar si hay citas en un rango de +/- duración_cita minutos
        // ============================================
        if (empty($error)) {
            // Convertir hora a timestamp para calcular rangos
            $hora_cita = strtotime($hora);
            $hora_inicio = date('H:i:s', strtotime("-$duracion_cita minutes", $hora_cita));
            $hora_fin = date('H:i:s', strtotime("+$duracion_cita minutes", $hora_cita));
            
            $sql_rango = "SELECT cita_id, hora FROM citas 
                         WHERE quiropedista_cedula = :quiropedista 
                         AND fecha = :fecha 
                         AND hora BETWEEN :hora_inicio AND :hora_fin
                         AND estatus != 'cancelada'";
            
            $stmt_rango = $conexion->prepare($sql_rango);
            $stmt_rango->execute([
                ':quiropedista' => $quiropedista_cedula,
                ':fecha' => $fecha,
                ':hora_inicio' => $hora_inicio,
                ':hora_fin' => $hora_fin
            ]);
            
            if ($stmt_rango->rowCount() > 0) {
                $cita_conflicto = $stmt_rango->fetch(PDO::FETCH_ASSOC);
                $error = "❌ El quiropedista ya tiene una cita cercana a las " . $cita_conflicto['hora'] . 
                         ". Debe haber al menos $duracion_cita minutos de diferencia entre citas.";
            }
        }
        
        // ============================================
        // Si no hay errores, proceder a guardar
        // ============================================
        if (empty($error)) {
            $sql = "INSERT INTO citas (paciente_cedula, quiropedista_cedula, servicio_id, fecha, hora, estatus, aviso) 
                    VALUES (:paciente, :quiropedista, :servicio, :fecha, :hora, 'programada', 'N')";
            
            $stmt = $conexion->prepare($sql);
            $stmt->execute([
                ':paciente' => $paciente_cedula,
                ':quiropedista' => $quiropedista_cedula,
                ':servicio' => $servicio_id,
                ':fecha' => $fecha,
                ':hora' => $hora
            ]);
            
            $mensaje = "✅ Cita guardada correctamente";
        }
        
    } catch(PDOException $e) {
        $error = "❌ Error: " . $e->getMessage();
    }
}

// Obtener datos para los selects (con PDO)
try {
    $pacientes = $conexion->query("SELECT cedula_id, primer_nombre, primer_apellido FROM pacientes ORDER BY primer_nombre");
    $quiropedistas = $conexion->query("SELECT q.usuario_cedula, u.primer_nombre, u.primer_apellido 
                                       FROM quiropedistas q 
                                       JOIN usuarios u ON q.usuario_cedula = u.cedula_id 
                                       WHERE q.disponibilidad = 1
                                       ORDER BY u.primer_nombre");
    $servicios = $conexion->query("SELECT servicio_id, nombre FROM servicios WHERE estatus = 1 ORDER BY nombre");
} catch(PDOException $e) {
    $error = "Error al cargar datos: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agendar Cita - Sr. Pie</title>
    <link rel="icon" type="image/png" href="../assets/img/logo_sr_pie.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/sidebar.css">
    <link rel="stylesheet" href="../assets/css/header.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
</head>
<body class="bg-light">

    <?php include '../includes/header.php'; ?>
    
    <div class="d-flex" style="height: calc(100vh - 70px); overflow: hidden;">
        
        <?php include '../includes/sidebar.php'; ?>
        
        <div class="flex-grow-1" style="overflow-y: auto;">
            
            <?php 
            $titulo_modulo = "Agendar Cita";
            $icono_modulo = "bi-calendar-plus";
            include '../includes/titulo_modulo.php'; 
            ?>
            
            <div class="container-fluid p-4">
                
                <!-- Mensajes -->
                <?php if ($mensaje): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="bi bi-check-circle-fill me-2"></i> <?php echo $mensaje; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <!-- FORMULARIO -->
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6">
                        <div class="card shadow">
                            <div class="card-header bg-white py-3">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-clock-history text-primary me-2"></i>
                                    Duración estimada: 50 minutos por cita
                                </h5>
                            </div>
                            <div class="card-body p-4">
                                
                                <form method="POST" action="" id="formCita">
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Paciente</label>
                                        <select name="paciente_cedula" class="form-select form-select-lg" required>
                                            <option value="">-- Seleccione un paciente --</option>
                                            <?php if ($pacientes && $pacientes->rowCount() > 0): ?>
                                                <?php foreach ($pacientes as $p): ?>
                                                    <option value="<?php echo $p['cedula_id']; ?>">
                                                        <?php echo $p['cedula_id'] . ' - ' . $p['primer_nombre'] . ' ' . $p['primer_apellido']; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <option value="" disabled>No hay pacientes registrados</option>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Quiropedista</label>
                                        <select name="quiropedista_cedula" class="form-select form-select-lg" required>
                                            <option value="">-- Seleccione un quiropedista --</option>
                                            <?php if ($quiropedistas && $quiropedistas->rowCount() > 0): ?>
                                                <?php foreach ($quiropedistas as $q): ?>
                                                    <option value="<?php echo $q['usuario_cedula']; ?>">
                                                        <?php echo $q['primer_nombre'] . ' ' . $q['primer_apellido']; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <option value="" disabled>No hay quiropedistas disponibles</option>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Servicio</label>
                                        <select name="servicio_id" class="form-select form-select-lg" required>
                                            <option value="">-- Seleccione un servicio --</option>
                                            <?php if ($servicios && $servicios->rowCount() > 0): ?>
                                                <?php foreach ($servicios as $s): ?>
                                                    <option value="<?php echo $s['servicio_id']; ?>">
                                                        <?php echo $s['nombre']; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <option value="" disabled>No hay servicios activos</option>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Fecha</label>
                                            <input type="date" name="fecha" id="fecha" class="form-control form-control-lg" 
                                                   min="<?php echo date('Y-m-d'); ?>" required>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Hora</label>
                                            <input type="time" name="hora" id="hora" class="form-control form-control-lg" 
                                                   min="08:00" max="18:00" required>
                                            <small class="text-muted">Horario: 8:00 AM - 6:00 PM (puedes poner cualquier minuto, ej: 8:23, 9:47)</small>
                                        </div>
                                    </div>
                                    
                                    <!-- Aviso siempre N (oculto) -->
                                    <input type="hidden" name="aviso" value="N">
                                    
                                    <div class="d-grid gap-2 mt-4">
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="bi bi-save me-2"></i>Guardar Cita
                                        </button>
                                        <a href="citas.php" class="btn btn-outline-secondary">
                                            <i class="bi bi-arrow-left me-2"></i>Volver al menú
                                        </a>
                                    </div>
                                </form>
                                
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
    
    <script src="../assets/js/hamburguesa.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/agregar_cita.js"></script>
    
</body>
</html>
