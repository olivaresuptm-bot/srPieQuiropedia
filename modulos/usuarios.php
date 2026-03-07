<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit;
}

include '../includes/db.php';

// Verificamos si el usuario actual es gerente para permitir las acciones
$es_admin_actual = ($_SESSION['rol'] === 'gerente');

// --- LÓGICA DE PROCESAMIENTO: CAMBIO DE ESTADO ---
if (isset($_GET['action']) && $_GET['action'] === 'change_status') {
    $id = $_GET['id'] ?? '';
    $nuevo_estado = $_GET['nuevo_estado'] ?? '';
    
    try {
        // Solo el gerente puede cambiar estados
        if (!$es_admin_actual) {
            header("Location: usuarios.php?res=error_permisos");
            exit;
        }

        // Actualizamos el estado (0 para Pendiente, 1 para Activo)
        // Protegemos al gerente para que no se dé de baja a sí mismo por accidente
        $sql = "UPDATE usuarios SET estado = :estado WHERE cedula_id = :id AND rol != 'gerente'";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([':estado' => $nuevo_estado, ':id' => $id]);
        
        header("Location: usuarios.php?res=status_changed");
        exit;
    } catch (PDOException $e) {
        die("Error crítico: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
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
        <?php include '../includes/sidebar.php'; ?>

        <div class="flex-grow-1 p-4 overflow-auto">
            <?php include '../includes/titulo_modulo.php'; ?>

            <?php if(isset($_GET['res']) && $_GET['res'] === 'status_changed'): ?>
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    Estado del usuario actualizado correctamente.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card shadow-sm border-0 mt-3">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Identificación</th>
                                    <th>Nombre Completo</th>
                                    <th>Rol</th>
                                    <th>Estado</th>
                                    <th class="text-center">Acceso</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $stmt = $conexion->prepare("SELECT cedula_id, tipo_doc, primer_nombre, primer_apellido, rol, estado FROM usuarios");
                                $stmt->execute();
                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)):
                                    $es_fila_gerente = ($row['rol'] === 'gerente');
                                    $activo = ($row['estado'] == 1);
                                ?>
                                <tr>
                                    <td class="ps-4 fw-bold"><?php echo $row['tipo_doc'] . "-" . $row['cedula_id']; ?></td>
                                    <td><?php echo $row['primer_nombre'] . " " . $row['primer_apellido']; ?></td>
                                    <td><span class="badge bg-secondary"><?php echo ucfirst($row['rol']); ?></span></td>
                                    <td>
                                        <span class="badge <?php echo $activo ? 'bg-success' : 'bg-warning text-dark'; ?>">
                                            <?php echo $activo ? 'Activo' : 'Pendiente'; ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <?php if (!$es_fila_gerente && $es_admin_actual): ?>
                                            <?php if ($activo): ?>
                                                <a href="usuarios.php?action=change_status&id=<?php echo $row['cedula_id']; ?>&nuevo_estado=0" 
                                                   class="btn btn-sm btn-danger" title="Dar de baja">
                                                    <i class="bi bi-person-x-fill me-1"></i> Desactivar
                                                </a>
                                            <?php else: ?>
                                                <a href="usuarios.php?action=change_status&id=<?php echo $row['cedula_id']; ?>&nuevo_estado=1" 
                                                   class="btn btn-sm btn-success" title="Activar">
                                                    <i class="bi bi-person-check-fill me-1"></i> Activar
                                                </a>
                                            <?php endif; ?>
                                        <?php elseif ($es_fila_gerente): ?>
                                            <span class="text-muted small italic">Administrador</span>
                                        <?php else: ?>
                                            <i class="bi bi-lock text-muted"></i>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div> 
    </div>

    </div> </div> </div> <?php include '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/hamburguesa.js"></script>
</body>
</html>