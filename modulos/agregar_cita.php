<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit;
}
include '../controllers/registro_cita.php'; 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        <?php include '../includes/sidebar.php'; 
        include '../includes/titulo_modulo.php'; ?>
        
        <div class="flex-grow-1" style="overflow-y: auto;">
            
            <div class="container-fluid p-4">
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
                
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6">
                        <div class="card shadow border-0">
                            <div class="card-header bg-primary text-white border-0">
                                <h5 class="mb-0"><i class="bi bi-person-badge me-2"></i>Buscar Paciente por Cédula</h5>
                            </div>
                            <div class="card-body p-4">
                                
                                <form method="POST" action="" class="mb-4">
                                    <div class="input-group shadow-sm">
                                        <input type="text" name="cedula_buscar" class="form-control form-control-lg border-primary border-opacity-50" 
                                               placeholder="Ingrese cédula" value="<?php echo htmlspecialchars($paciente_cedula); ?>" required>
                                        <button type="submit" name="buscar_paciente" class="btn btn-primary btn-lg">
                                            <i class="bi bi-search"></i> Buscar
                                        </button>
                                    </div>
                                </form>
                                
                                <?php if ($paciente_buscado): ?>
                                    <div class="alert alert-success mb-4 shadow-sm border-0">
                                        <i class="bi bi-person-check-fill me-2"></i>
                                        <strong>Paciente encontrado:</strong><br>
                                        <?php 
                                        $nombre_completo = $paciente_buscado['primer_nombre'] . ' ' . 
                                                          ($paciente_buscado['segundo_nombre'] ? $paciente_buscado['segundo_nombre'] . ' ' : '') . 
                                                          $paciente_buscado['primer_apellido'] . ' ' . 
                                                          ($paciente_buscado['segundo_apellido'] ? $paciente_buscado['segundo_apellido'] : '');
                                        ?>
                                        <span class="h5"><?php echo $nombre_completo; ?></span>
                                        <br><small>Cédula: <?php echo $paciente_buscado['cedula_id']; ?></small>
                                    </div>
                                <?php endif; ?>
                                
                                <hr class="opacity-25 my-4">
                                
                                <form method="POST" action="">
                                    <input type="hidden" name="paciente_cedula" value="<?php echo htmlspecialchars($paciente_cedula); ?>">
                                    
                                <div class="mb-3">
                                    <label class="form-label fw-bold ">Quiropedista</label>
                                    <select name="quiropedista_cedula" class="form-select shadow-sm" required>
                                        <option value="">-- Seleccione un quiropedista --</option>
                                        <?php foreach ($quiropedistas as $q): ?>
                                            <option value="<?php echo $q['usuario_cedula']; ?>">
                                                <?php echo htmlspecialchars($q['primer_nombre'] . ' ' . $q['primer_apellido']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold ">Servicio</label>
                                    <select name="servicio_id" class="form-select shadow-sm" required>
                                        <option value="">-- Seleccione un servicio --</option>
                                        <?php foreach ($servicios as $s): ?>
                                            <option value="<?php echo $s['servicio_id']; ?>">
                                                <?php echo htmlspecialchars($s['nombre']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold ">Fecha</label>
                                            <input type="date" name="fecha" class="form-control shadow-sm" min="<?php echo date('Y-m-d'); ?>" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold ">Hora</label>
                                            <input type="time" name="hora" class="form-control shadow-sm" required>
                                        </div>
                                    </div>
                                    
                                    <div class="d-grid gap-2 mt-4">
                                        <button type="submit" name="guardar_cita" class="btn btn-success btn-lg shadow-sm" <?php echo !$paciente_buscado ? 'disabled' : ''; ?>>
                                            <i class="bi bi-save me-2"></i>Guardar Cita
                                        </button>
                                        <a href="citas.php" class="btn btn-outline-secondary">
                                            <i class="bi bi-arrow-left me-2"></i>Volver a gestión de citas
                                        </a>
                                    </div>
                                    
                                    <?php if (!$paciente_buscado): ?>
                                        <div class="alert alert-warning mt-3 mb-0 border-0 shadow-sm">
                                            <i class="bi bi-info-circle-fill me-2"></i> Debe buscar y seleccionar un paciente antes de agendar.
                                        </div>
                                    <?php endif; ?>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/hamburguesa.js"></script>
    <script src="../assets/js/agregar_cita.js"></script>
</body>
</html>