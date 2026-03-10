<?php include '../controllers/registro_cita.php'; ?>
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
            
            <?php  include '../includes/titulo_modulo.php'; ?>
            
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
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="bi bi-person-badge me-2"></i>Buscar Paciente por Cédula</h5>
                            </div>
                            <div class="card-body p-4">
                                
                                <!-- BUSCADOR DE PACIENTE -->
                                <form method="POST" action="" class="mb-4">
                                    <div class="input-group">
                                        <input type="text" name="cedula_buscar" class="form-control form-control-lg" 
                                               placeholder="Ingrese cédula del paciente" value="<?php echo htmlspecialchars($paciente_cedula); ?>" required>
                                        <button type="submit" name="buscar_paciente" class="btn btn-primary btn-lg">
                                            <i class="bi bi-search"></i> Buscar
                                        </button>
                                    </div>
                                </form>
                                
                                <!-- RESULTADO DE LA BÚSQUEDA -->
                                <?php if ($paciente_buscado): ?>
                                    <div class="alert alert-success mb-4">
                                        <i class="bi bi-person-check-fill me-2"></i>
                                        <strong>Paciente encontrado:</strong><br>
                                        <?php 
                                        $nombre_completo = $paciente_buscado['primer_nombre'] . ' ' . 
                                                          ($paciente_buscado['segundo_nombre'] ? $paciente_buscado['segundo_nombre'] . ' ' : '') . 
                                                          $paciente_buscado['primer_apellido'] . ' ' . 
                                                          ($paciente_buscado['segundo_apellido'] ? $paciente_buscado['segundo_apellido'] : '');
                                        ?>
                                        <span class="h5"><?php echo $nombre_completo; ?></span>
                                        <br>
                                        <small>Cédula: <?php echo $paciente_buscado['cedula_id']; ?></small>
                                    </div>
                                <?php endif; ?>
                                
                                <hr>
                                
                                <!-- FORMULARIO PARA AGENDAR CITA -->
                                <form method="POST" action="">
                                    
                                    <!-- Campo oculto con la cédula del paciente -->
                                    <input type="hidden" name="paciente_cedula" value="<?php echo htmlspecialchars($paciente_cedula); ?>">
                                    
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
                                            <input type="date" name="fecha" class="form-control form-control-lg" 
                                                   min="<?php echo date('Y-m-d'); ?>" required>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Hora</label>
                                            <input type="time" name="hora" class="form-control form-control-lg" required>
                                        </div>
                                    </div>
                                    
                                    <!-- Aviso siempre N (oculto) -->
                                    <input type="hidden" name="aviso" value="N">
                                    
                                    <div class="d-grid gap-2 mt-4">
                                        <button type="submit" name="guardar_cita" class="btn btn-success btn-lg" <?php echo !$paciente_buscado ? 'disabled' : ''; ?>>
                                            <i class="bi bi-save me-2"></i>Guardar Cita
                                        </button>
                                        <a href="citas.php" class="btn btn-outline-secondary">
                                            <i class="bi bi-arrow-left me-2"></i>Volver al menú
                                        </a>
                                    </div>
                                    
                                    <?php if (!$paciente_buscado): ?>
                                        <div class="alert alert-warning mt-3 mb-0">
                                            <i class="bi bi-info-circle-fill me-2"></i>
                                            Debe buscar y seleccionar un paciente antes de guardar la cita.
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
    
</div> </div> </div> <?php include '../includes/footer.php'; ?>

<script src="../assets/js/hamburguesa.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/agregar_cita.js"></script>

</body>
</html>