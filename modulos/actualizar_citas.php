<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit;
}

require_once '../controllers/actualizar_citas.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Estatus de Citas - Sr. Pie</title>
    <link rel="icon" type="image/png" href="../assets/img/logo_sr_pie.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/sidebar.css">
    <link rel="stylesheet" href="../assets/css/header.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
    <link rel="stylesheet" href="../assets/css/actualizar_citas.css">
</head>
<body class="bg-light">

    <?php include '../includes/header.php'; ?>
    
    <div class="d-flex" style="height: calc(100vh - 70px); overflow: hidden;">
        
        <?php include '../includes/sidebar.php'; 
        include '../includes/titulo_modulo.php';?>
        
        <div class="flex-grow-1 contenedor-principal d-flex flex-column">
            
            <div class="p-4 flex-grow-1">
                
                
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
                
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <form method="GET" action="" class="row g-3 align-items-end">
                            <div class="col-md-10">
                                <label class="form-label fw-bold">Buscar citas</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white">
                                        <i class="bi bi-search"></i>
                                    </span>
                                    <input type="text" class="form-control form-control-lg" 
                                           name="search" value="<?php echo htmlspecialchars($search); ?>" 
                                           placeholder="Buscar por cédula, nombre, servicio, fecha o estatus...">
                                </div>
                                <small class="text-muted mt-1">
                                    <i class="bi bi-info-circle"></i> Puedes buscar por cédula del paciente o quiropedista
                                </small>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary btn-lg w-100">
                                    <i class="bi bi-search me-2"></i>Buscar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="card shadow border-0">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-0">
                        <h5 class="mb-0">
                            <i class="bi bi-table me-2"></i>
                            Listado de Citas
                        </h5>
                        <span class="badge bg-primary">Total: <?php echo count($citas); ?> citas</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Hora</th>
                                        <th>Paciente</th>
                                        <th>Cédula Pac.</th>
                                        <th>Teléfono</th>
                                        <th>Quiropedista</th>
                                        <th>Cédula Quiro.</th>
                                        <th>Servicio</th>
                                        <th>Precio</th>
                                        <th>Estatus</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($citas)): ?>
                                        <tr>
                                            <td colspan="11" class="text-center py-5 border-bottom-0">
                                                <i class="bi bi-calendar-x fs-1 text-muted d-block mb-3"></i>
                                                <h5 class="text-muted">No hay citas para mostrar</h5>
                                                <?php if (!empty($search)): ?>
                                                    <p class="text-muted">Intenta con otros términos de búsqueda</p>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($citas as $cita): ?>
                                            <?php 
                                            $rowClass = '';
                                            if ($cita['estatus'] == 'cancelada') $rowClass = 'cita-cancelada';
                                            elseif ($cita['estatus'] == 'atendida') $rowClass = 'cita-atendida';
                                            
                                            $badgeClass = ''; $badgeIcon = '';
                                            if ($cita['estatus'] == 'programada') { $badgeClass = 'bg-warning text-dark'; $badgeIcon = 'bi-clock-history'; } 
                                            elseif ($cita['estatus'] == 'atendida') { $badgeClass = 'bg-success'; $badgeIcon = 'bi-check-circle'; } 
                                            elseif ($cita['estatus'] == 'cancelada') { $badgeClass = 'bg-danger'; $badgeIcon = 'bi-x-circle'; }
                                            ?>
                                            <tr class="<?php echo $rowClass; ?>">
                                                <td><?php echo date('d/m/Y', strtotime($cita['fecha'])); ?></td>
                                                <td><?php echo substr($cita['hora'], 0, 5); ?></td>
                                                <td><?php echo $cita['paciente_nombre'] . ' ' . $cita['paciente_apellido']; ?></td>
                                                <td><?php echo $cita['paciente_cedula']; ?></td>
                                                <td><?php echo $cita['paciente_telefono'] ?: 'N/A'; ?></td>
                                                <td><?php echo $cita['quiropedista_nombre'] . ' ' . $cita['quiropedista_apellido']; ?></td>
                                                <td><?php echo $cita['quiropedista_cedula']; ?></td>
                                                <td><?php echo $cita['servicio_nombre']; ?></td>
                                                <td class="text-end">$<?php echo number_format($cita['servicio_precio'], 2); ?></td>
                                                <td>
                                                    <span class="badge <?php echo $badgeClass; ?> badge-estatus">
                                                        <i class="bi <?php echo $badgeIcon; ?> me-1"></i>
                                                        <?php echo ucfirst($cita['estatus']); ?>
                                                    </span>
                                                </td>
                                                <td class="table-actions">
                                                    <?php if ($cita['estatus'] == 'programada'): ?>
                                                        <a href="?accion=atendida&id=<?php echo $cita['cita_id']; ?>&search=<?php echo urlencode($search); ?>" 
                                                           class="btn btn-success btn-sm shadow-sm" 
                                                           onclick="return confirm('¿Marcar esta cita como ATENDIDA?')" title="Marcar como atendida">
                                                            <i class="bi bi-check-lg"></i> Atendida
                                                        </a>
                                                        <a href="?accion=cancelada&id=<?php echo $cita['cita_id']; ?>&search=<?php echo urlencode($search); ?>" 
                                                           class="btn btn-danger btn-sm shadow-sm" 
                                                           onclick="return confirm('¿Estás seguro de CANCELAR esta cita?')" title="Cancelar cita">
                                                            <i class="bi bi-x-lg"></i> Cancelar
                                                        </a>
                                                    <?php elseif ($cita['estatus'] == 'atendida'): ?>
                                                        <span class="text-success fw-bold small"><i class="bi bi-check-circle-fill"></i> Completada</span>
                                                    <?php else: ?>
                                                        <span class="text-danger fw-bold small"><i class="bi bi-x-circle-fill"></i> Cancelada</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-0 py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="bi bi-info-circle text-muted me-2"></i>
                                <small class="text-muted">
                                    <span class="badge bg-warning text-dark me-2">Programada</span>
                                    <span class="badge bg-success me-2">Atendida</span>
                                    <span class="badge bg-danger">Cancelada</span>
                                </small>
                            </div>
                            <a href="citas.php" class="btn btn-outline-secondary btn-sm shadow-sm">
                                <i class="bi bi-arrow-left me-2"></i>Volver
                            </a>
                        </div>
                    </div>
                </div>
            </div> <div class="mt-auto">
                <?php include '../includes/footer.php'; ?>
            </div>

        </div>
    </div>
    
    <script src="../assets/js/hamburguesa.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/actualizar_citas.js"></script>
    
</body>
</html>