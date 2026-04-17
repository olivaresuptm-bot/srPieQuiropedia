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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            
            <div class="p-4 flex-grow-1" style="overflow-y: auto;">
                
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
                     <div class="px-3 pb-2">
                                <i class="bi bi-info-circle text-muted me-2"></i>
                                <small class="text-muted">
                                    <span class="badge bg-warning text-dark me-2">Programada</span>
                                    <span class="badge bg-success me-2">Atendida</span>
                                    <span class="badge bg-danger">Cancelada</span>
                                </small>
                            </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Hora</th>
                                        <th>Cédula Pac.</th>
                                        <th>Paciente</th>
                                        <th>Teléfono</th>
                                        <th>Quiropedista</th>
                                        <th>Cédula Quiro.</th>
                                        <th>Servicio</th>
                                        <th>Precio</th>
                                        <th>Estatus</th>
                                        <th class="text-center">Acciones</th>
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
                                                <td class="fw-bold"><?php echo substr($cita['hora'], 0, 5); ?></td>
                                                
                                                <td>
                                                    <a href="gestion_pacientes.php?busqueda=<?php echo urlencode($cita['paciente_cedula']); ?>" 
                                                       class="text-primary fw-bold text-decoration-none" 
                                                       title="Ir a la cartilla del paciente">
                                                        <?php echo htmlspecialchars($cita['paciente_cedula']); ?>
                                                        
                                                    </a>
                                                </td>

                                                <td><?php echo htmlspecialchars($cita['paciente_nombre'] . ' ' . $cita['paciente_apellido']); ?></td>
                                                <td><?php echo htmlspecialchars($cita['paciente_telefono']) ?: 'N/A'; ?></td>
                                                <td class="text-secondary"><?php echo htmlspecialchars($cita['quiropedista_nombre'] . ' ' . $cita['quiropedista_apellido']); ?></td>
                                                <td class="small text-muted"><?php echo htmlspecialchars($cita['quiropedista_cedula']); ?></td>
                                                <td><?php echo htmlspecialchars($cita['servicio_nombre']); ?></td>
                                                <td class="text-end fw-bold text-success">$<?php echo number_format($cita['servicio_precio'], 2); ?></td>
                                                <td>
                                                    <span class="badge <?php echo $badgeClass; ?> badge-estatus px-2 py-1">
                                                        <i class="bi <?php echo $badgeIcon; ?> me-1"></i>
                                                        <?php echo ucfirst($cita['estatus']); ?>
                                                    </span>
                                                </td>
                                               <td class="table-actions text-center">
                                                    <?php if ($cita['estatus'] == 'programada'): ?>
                                                        <div class="btn-group shadow-sm">
                                                            <button type="button" class="btn btn-warning btn-sm text-dark" 
                                                                    onclick='abrirEditarCita(<?php echo json_encode($cita); ?>)' title="Editar fecha, hora o servicio">
                                                                <i class="bi bi-pencil-square"></i>
                                                            </button>
                                                            <a href="?accion=atendida&id=<?php echo $cita['cita_id']; ?>&search=<?php echo urlencode($search); ?>" 
                                                               class="btn btn-success btn-sm" 
                                                               onclick="return confirm('¿Marcar esta cita como ATENDIDA?')" title="Marcar como atendida">
                                                                <i class="bi bi-check-lg"></i>
                                                            </a>
                                                            <a href="?accion=cancelada&id=<?php echo $cita['cita_id']; ?>&search=<?php echo urlencode($search); ?>" 
                                                               class="btn btn-danger btn-sm" 
                                                               onclick="return confirm('¿Estás seguro de CANCELAR esta cita?')" title="Cancelar cita">
                                                                <i class="bi bi-x-lg"></i>
                                                            </a>
                                                        </div>

                                                    <?php elseif ($cita['estatus'] == 'atendida'): ?>
                                                        
                                                        <?php if (!empty($cita['pago_id'])): ?>
                                                            <span class="badge bg-success p-2 shadow-sm" style="font-size: 0.85rem;">
                                                                <i class="bi bi-check-circle-fill me-1"></i> Pagado
                                                            </span>
                                                        <?php else: ?>
                                                            <a href="gestion_pacientes/facturacion_paciente.php?cita_id=<?php echo $cita['cita_id']; ?>" 
                                                               class="btn btn-primary btn-sm shadow-sm">
                                                                <i class="bi bi-receipt-cutoff me-1"></i> Facturar
                                                            </a>
                                                        <?php endif; ?>

                                                    <?php else: ?>
                                                        <span class="text-danger fw-bold small"><i class="bi bi-dash-circle"></i></span>
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
                           
                            <a href="citas.php" class="btn btn-outline-secondary btn-sm shadow-sm">
                                <i class="bi bi-arrow-left me-2"></i>Volver
                            </a>
                        </div>
                    </div>
                </div>
            </div> 
            
            <div class="mt-auto">
                <?php include '../includes/footer.php'; ?>
            </div>

        </div>
    </div>

    <div class="modal fade" id="modalEditarCita" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="" method="POST" class="modal-content shadow border-0">
                <div class="modal-header bg-warning border-0">
                    <h5 class="modal-title fw-bold text-dark"><i class="bi bi-pencil-square me-2"></i>Reprogramar / Editar Cita</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" name="action" value="editar_cita">
                    <input type="hidden" name="cita_id" id="edit_cita_id">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">Paciente</label>
                        <input type="text" id="edit_paciente_nombre" class="form-control shadow-sm bg-light" readonly>
                        <small class="text-muted">El paciente no se puede modificar.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Quiropedista</label>
                        <select name="quiropedista_cedula" id="edit_quiropedista" class="form-select shadow-sm" required>
                            <option value="">-- Seleccione un quiropedista --</option>
                            <?php foreach ($quiropedistas as $q): ?>
                                <option value="<?php echo $q['usuario_cedula']; ?>">
                                    <?php echo htmlspecialchars($q['primer_nombre'] . ' ' . $q['primer_apellido']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Servicio</label>
                        <select name="servicio_id" id="edit_servicio" class="form-select shadow-sm" required>
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
                            <label class="form-label fw-bold">Fecha</label>
                            <input type="date" name="fecha" id="edit_fecha" class="form-control shadow-sm" min="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Hora</label>
                            <input type="time" name="hora" id="edit_hora" class="form-control shadow-sm" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning px-4 shadow-sm fw-bold">
                        <i class="bi bi-save me-2"></i>Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
    </div></div></div><?php include '../../includes/footer.php'; ?>
    <script src="../assets/js/hamburguesa.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/actualizar_citas.js"></script>
    
</body>
</html>