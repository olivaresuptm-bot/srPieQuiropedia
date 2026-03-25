<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../../index.php");
    exit;
}

require_once '../../includes/db.php';
require_once '../../controllers/historial.php';


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial Clínico - Sr. Pie</title>
    <link rel="icon" type="image/png" href="../../assets/img/logo_sr_pie.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../assets/css/sidebar.css">
    <link rel="stylesheet" href="../../assets/css/header.css">
    <link rel="stylesheet" href="../../assets/css/historial.css">
    <link rel="stylesheet" href="../../assets/css/footer.css">
    
</head>
<body class="bg-light">
    <?php $ruta_base = '../../'; include '../../includes/header.php'; ?>

    <div class="d-flex" style="height: calc(100vh - 70px); overflow: hidden;">
        <?php $ruta_base = '../../'; include '../../includes/sidebar.php'; ?>
        
        <div class="flex-grow-1 p-4 overflow-auto">
            <div class="container-fluid">
                
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold m-0"><i class="bi bi-journal-medical text-primary me-2"></i>Historial Clínico</h2>
                    <a href="../gestion_pacientes.php?busqueda=<?php echo $paciente['cedula_id']; ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Volver</a>
                </div>

                <?php if($msj): ?>
                    <div class="alert alert-<?php echo $msj['tipo'] == 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show">
                        <?php echo $msj['texto']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="card shadow-sm border-0 card-paciente h-100">
                            <div class="card-body text-center pt-4">
                                <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px; font-size: 2rem;">
                                    <?php echo strtoupper(substr($paciente['primer_nombre'], 0, 1) . substr($paciente['primer_apellido'], 0, 1)); ?>
                                </div>
                                <h4 class="fw-bold"><?php echo $paciente['primer_nombre'] . ' ' . $paciente['segundo_nombre'] . ' ' . $paciente['primer_apellido'] . ' ' . $paciente['segundo_apellido']; ?></h4>
                                <p class="text-muted mb-1">C.I: <?php echo $paciente['tipo_doc'] . '-' . $paciente['cedula_id']; ?></p>
                                <hr>
                                <div class="text-start">
                                    <p class="mb-2"><i class="bi bi-calendar-event me-2 text-primary"></i> <strong>Edad:</strong> <?php echo $edad; ?> años</p>
                                    <p class="mb-2"><i class="bi bi-gender-ambiguous me-2 text-primary"></i> <strong>Género:</strong> <?php echo $paciente['genero'] == 'M' ? 'Masculino' : ($paciente['genero'] == 'F' ? 'Femenino' : 'Otro'); ?></p>
                                    <p class="mb-2"><i class="bi bi-telephone me-2 text-primary"></i> <strong>Tel:</strong> <?php echo $paciente['telefono']; ?></p>
                                    <p class="mb-0"><i class="bi bi-envelope me-2 text-primary"></i> <strong>Correo:</strong> <span class="small"><?php echo $paciente['correo']; ?></span></p>
                                    <p class="mb-2"><i class="bi bi-instagram me-2 text-primary"></i> <strong>Instagram:</strong> <span class="small"><?php echo htmlspecialchars($paciente['instagram'] ?? 'N/A'); ?></span></p>
                                    <p class="mb-0"><i class="bi bi-activity me-2 text-primary"></i> <strong>Diabético:</strong> <span class="small"><?php echo ($paciente['diabetico'] == 'Si') ? 'Sí' : 'No'; ?></span></p>
                                </div>
                                
                               

                            </div>
                        </div>
                    </div>

                    <div class="col-md-8 mb-4">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
                                    <h4 class="fw-bold m-0">Historial Clínico del paciente</h4>
                                    
                                    <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalNuevoHistorial">
                                        <i class="bi bi-plus-circle me-1"></i> Redactar Historial Nuevo
                                    </button>
                                </div>
                                
                                <?php if (count($historiales) > 0): ?>
                                    <?php foreach ($historiales as $registro): ?>
                                    
                                        <?php 
                                       
                                        $nombre_quiro = "C.I: " . $registro['quiropedista_cedula']; 
                                        try {
                                            $stmt_quiro = $conexion->prepare("SELECT primer_nombre, primer_apellido FROM usuarios WHERE cedula_id = ?");
                                            $stmt_quiro->execute([$registro['quiropedista_cedula']]);
                                            if ($quiro = $stmt_quiro->fetch(PDO::FETCH_ASSOC)) {
                                                $nombre_quiro = $quiro['primer_nombre'] . ' ' . $quiro['primer_apellido'];
                                            } else {
                                                $stmt_quiro2 = $conexion->prepare("SELECT nombre, apellido FROM quiropedistas WHERE usuario_cedula = ?");
                                                $stmt_quiro2->execute([$registro['quiropedista_cedula']]);
                                                if ($quiro2 = $stmt_quiro2->fetch(PDO::FETCH_ASSOC)) {
                                                    $nombre_quiro = $quiro2['nombre'] . ' ' . $quiro2['apellido'];
                                                }
                                            }
                                        } catch (Exception $e) {} 
                                        ?>
                                        
                                        <div class="timeline-item">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <div>
                                                    <h6 class="fw-bold text-primary m-0">Fecha de Consulta: <?php echo date('d/m/Y h:i A', strtotime($registro['fecha_registro'])); ?></h6>
                                                    
                                                    <span class="text-muted small">
                                                        <i class="bi bi-person-badge text-info me-1"></i>Atendido por: <strong><?php echo htmlspecialchars($nombre_quiro); ?></strong>
                                                    </span>
                                                </div>
                                                
                                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEditar<?php echo $registro['historial_id']; ?>">
                                                    <i class="bi bi-pencil-square"></i> Editar
                                                </button>
                                            </div>
                                            
                                            <div class="bg-light p-3 rounded mb-3 border">
                                                <p class="mb-2 text-primary">
                                                    <i class="bi bi-bandaid me-1"></i><strong>Servicio Aplicado:</strong> <?php echo htmlspecialchars($registro['servicio_nombre'] ?? 'No registrado'); ?>
                                                </p>
                                                
                                                <p class="mb-1"><strong>Causa de la Cita:</strong> <?php echo nl2br(htmlspecialchars($registro['motivo_consulta'] ?? 'No registrada')); ?></p>
                                                <p class="mb-1"><strong>Diagnóstico:</strong> <?php echo nl2br(htmlspecialchars($registro['diagnostico'] ?? 'No registrado')); ?></p>
                                                <p class="mb-1"><strong>Tratamiento:</strong> <?php echo nl2br(htmlspecialchars($registro['tratamiento'] ?? 'No registrado')); ?></p>
                                                <p class="mb-0"><strong>Observaciones:</strong> <?php echo nl2br(htmlspecialchars($registro['observaciones'] ?? 'Ninguna')); ?></p>
                                            </div>
                                        </div>

                                        <div class="modal fade" id="modalEditar<?php echo $registro['historial_id']; ?>" tabindex="-1">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-primary text-white">
                                                        <h5 class="modal-title">Editar Consulta del <?php echo date('d/m/Y', strtotime($registro['fecha_registro'])); ?></h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form method="POST">
                                                        <div class="modal-body">
                                                            <input type="hidden" name="editar_historial" value="1">
                                                            <input type="hidden" name="historial_id" value="<?php echo $registro['historial_id']; ?>">
                                                            
                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold">Motivo de Consulta (Causa de la cita)</label>
                                                                <textarea name="motivo_consulta" class="form-control" rows="2"><?php echo htmlspecialchars($registro['motivo_consulta'] ?? ''); ?></textarea>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold">Diagnóstico</label>
                                                                <textarea name="diagnostico" class="form-control" rows="2"><?php echo htmlspecialchars($registro['diagnostico'] ?? ''); ?></textarea>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold">Tratamiento Indicado</label>
                                                                <textarea name="tratamiento" class="form-control" rows="2"><?php echo htmlspecialchars($registro['tratamiento'] ?? ''); ?></textarea>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold">Observaciones Adicionales</label>
                                                                <textarea name="observaciones" class="form-control" rows="2"><?php echo htmlspecialchars($registro['observaciones'] ?? ''); ?></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                      
                                <div class="d-grid gap-2 mt-3">
                                    <a href="../../controllers/descargar_historial_pdf.php?cedula=<?php echo $cedula; ?>" target="_blank" class="btn btn-outline-danger shadow-sm">
                                        <i class="bi bi-file-pdf me-2"></i>Historial Clínico PDF
                                    </a>
                                    <a href="../../controllers/enviar_historial_correo.php?cedula=<?php echo $cedula; ?>" target="_blank" class="btn btn-outline-success shadow-sm">
                                        <i class="bi bi-envelope-paper me-2"></i>Enviar al Correo
                                    </a>
                                </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="text-center text-muted p-5">
                                        <i class="bi bi-file-earmark-medical" style="font-size: 3rem;"></i>
                                        <p class="mt-2">Este paciente aún no tiene consultas registradas.</p>
                                    </div>
                                <?php endif; ?>
                                
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalNuevoHistorial" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-journal-plus me-2"></i>Redactar Nuevo Historial</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="nuevo_historial" value="1">
                        
                        <?php if(count($todas_las_citas) > 0): ?>
                            <div class="mb-3 border-start border-4 border-info bg-light p-3">
                                <label class="form-label fw-bold text-info">Seleccione la cita que va a documentar:</label>
                                <select name="cita_id" class="form-select border-info" required>
                                    <option value="">-- Seleccione una cita --</option>
                                    <?php foreach($todas_las_citas as $cita_pac): ?>
                                        <option value="<?php echo $cita_pac['cita_id']; ?>">
                                            Cita del <?php echo date('d/m/Y', strtotime($cita_pac['fecha'])); ?> - Estatus: <?php echo ucfirst($cita_pac['estatus']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Motivo de Consulta</label>
                                <textarea name="motivo_consulta" class="form-control" rows="2" placeholder="Ej: Dolor en el pie derecho..." required></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Diagnóstico</label>
                                <textarea name="diagnostico" class="form-control" rows="2" placeholder="Describa el hallazgo médico..." required></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Tratamiento Indicado</label>
                                <textarea name="tratamiento" class="form-control" rows="2" placeholder="Procedimiento realizado o medicamentos indicados..." required></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Observaciones (Opcional)</label>
                                <textarea name="observaciones" class="form-control" rows="2"></textarea>
                            </div>
                            
                        <?php else: ?>
                            <div class="alert alert-warning mb-0">
                                <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                                <strong>¡Este paciente es completamente nuevo!</strong><br><br>
                                Tu base de datos requiere que todo historial médico esté vinculado a una cita. Por favor, cierra esta ventana y usa el botón de <strong>"Agendar Cita"</strong> en el perfil del paciente primero.
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <?php if(count($todas_las_citas) > 0): ?>
                            <button type="submit" class="btn btn-primary">Guardar Historial</button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include '../../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/hamburguesa.js"></script>
</body>
</html>