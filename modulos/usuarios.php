<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit;
}

include '../includes/db.php';
include '../controllers/usuario_edicion.php';

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - Sr. Pie</title>
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

        <div class="flex-grow-1 p-4 overflow-auto">

<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm p-1">
            <div class="card-body">
                <div class="row g-3 align-items-center">

                    <div class="col-md-6">
                        <div class="d-flex align-items-center gap-3 p-2">
                            <div class="icon-container flex-shrink-0" style="background: rgba(13,110,253,0.1); padding: 15px; border-radius: 50%;">
                                <i class="bi bi-person-plus-fill text-primary"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1">¿Usuario Nuevo?</h6>
                                <p class="text-muted">Registra un nuevo usuario. <a href="../registro.php" class="btn btn-primary  px-4 shadow-sm ms-4">Registrar</a></p>
                                
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 border-start">
                        <div class="d-flex align-items-center gap-3 p-2">
                            <div class="icon-container flex-shrink-0" style="background: rgba(25,135,84,0.1); padding: 15px; border-radius: 50%;">
                                <i class="bi bi-database text-success" ></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1">Mantenimiento</h6>
                                <p class="text-muted " >Respaldo y bitácora. <a href="mantenimiento.php" class="btn btn-success  px-4 shadow-sm ms-4">Ver</a></p>
                                
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
            <div class="card shadow-sm border-0 mt-3">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-primary">
                                <tr>
                                    <th class="ps-3">Cédula</th>
                                    <th>Nombre Completo</th>
                                    <th>Correo</th>
                                    <th>Rol</th>
                                    <th>Estado</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $stmt = $conexion->query("SELECT * FROM usuarios ORDER BY rol ASC");
                                while($row = $stmt->fetch()):
                                    $es_fila_gerente = ($row['rol'] === 'gerente');
                                ?>
                                <tr>
                                    <td class="ps-3"><?php echo $row['cedula_id']; ?></td>
                                    <td><?php echo $row['primer_nombre'] . " " . $row['segundo_nombre']." " . $row['primer_apellido']." " . $row['segundo_apellido']; ?></td>
                                    <td class="ps-3"><?php echo $row['correo']; ?></td>
                                    <td><span class="badge bg-secondary"><?php echo ucfirst($row['rol']); ?></span></td>
                                    <td>
                                        <?php if($row['estado'] == 1): ?>
                                            <span class="badge bg-success">Activo</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark">Pendiente</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-primary" 
                                            onclick='abrirEditar(<?php echo json_encode($row); ?>)'>
                                            <i class="bi bi-pencil-square"></i>
                                        </button>

                                        <?php if ($es_admin_actual && !$es_fila_gerente): ?>
                                            <?php if ($row['estado'] == 1): ?>
                                                <a href="usuarios.php?action=change_status&id=<?php echo $row['cedula_id']; ?>&nuevo_estado=0" 
                                                   class="btn btn-sm btn-danger" title="Desactivar">
                                                    <i class="bi bi-person-x-fill"></i>
                                                </a>
                                            <?php else: ?>
                                                <a href="usuarios.php?action=change_status&id=<?php echo $row['cedula_id']; ?>&nuevo_estado=1" 
                                                   class="btn btn-sm btn-success" title="Activar">
                                                    <i class="bi bi-person-check-fill"></i>
                                                </a>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
<br><br><br>
            
            </div>

        </div> 
    </div>

    <div class="modal fade" id="modalEditarUsuario" tabindex="-1" aria-hidden="true" style="z-index: 9999;">
        <div class="modal-dialog">
            <form action="usuarios.php" method="POST" class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-person-gear me-2"></i>Editar Datos de Usuario</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit_user"> 
                    <input type="hidden" name="cedula_id" id="edit_cedula">
                        
                    <div class="mb-3">
                        <label class="form-label">Primer Nombre</label>
                        <input type="text" name="primer_nombre" id="edit_primer_nombre" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Segundo Nombre</label>
                        <input type="text" name="segundo_nombre" id="edit_segundo_nombre" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Primer Apellido</label>
                        <input type="text" name="primer_apellido" id="edit_primer_apellido" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Segundo Apellido</label>
                        <input type="text" name="segundo_apellido" id="edit_segundo_apellido" class="form-control">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Correo Electrónico</label>
                        <input type="email" name="correo" id="edit_correo" class="form-control" required>
                    </div>
                        
                    <div class="mb-3">
                        <label class="form-label fw-bold">Rol</label>
                        <select name="rol" id="edit_rol" class="form-select" >
                            <option value="quiropedista">Quiropedista</option>
                            <option value="recepcionista">Recepcionista</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar Usuario</button>
                </div>
            </form>
        </div>
    </div>

    </div></div></div><?php include '../includes/footer.php'; ?>
    <script src="../assets/js/usuarios.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script src="../assets/js/hamburguesa.js"></script>
</body>
</html>