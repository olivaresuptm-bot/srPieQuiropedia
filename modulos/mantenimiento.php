<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit;
}

// Incluir conexión a BD
require_once __DIR__ . '/../includes/db.php';

// Verificar que la conexión existe
global $conexion, $conn;
if (isset($conexion)) {
    $conn = $conexion;
} elseif (!isset($conn)) {
    die("Error: No se pudo establecer la conexión a la base de datos");
}

$mensaje = '';
$error = '';
$rol_usuario = $_SESSION['rol'];

// ============================================
// 1. FUNCIONES DEL MÓDULO
// ============================================

// Crear respaldo
function crear_respaldo($conn) {
    $fecha = date('Y-m-d_H-i-s');
    $nombre = "respaldo_srpie_{$fecha}.sql";
    
    if (!file_exists('../backups')) {
        mkdir('../backups', 0777, true);
    }
    
    $ruta = "../backups/" . $nombre;
    
    // Obtener todas las tablas
    $tablas = [];
    $stmt = $conn->query("SHOW TABLES");
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        $tablas[] = $row[0];
    }
    
    $sql = "-- Respaldo Sr. Pie Quiropedia\n";
    $sql .= "-- Fecha: " . date('Y-m-d H:i:s') . "\n";
    $sql .= "-- Usuario: " . $_SESSION['usuario_id'] . "\n\n";
    $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";
    
    foreach ($tablas as $tabla) {
        // Estructura
        $stmt = $conn->query("SHOW CREATE TABLE $tabla");
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $sql .= "DROP TABLE IF EXISTS `$tabla`;\n";
        $sql .= $row[1] . ";\n\n";
        
        // Datos
        $stmt = $conn->query("SELECT * FROM $tabla");
        if ($stmt->rowCount() > 0) {
            // Obtener columnas
            $columnas = [];
            for ($i = 0; $i < $stmt->columnCount(); $i++) {
                $meta = $stmt->getColumnMeta($i);
                $columnas[] = "`{$meta['name']}`";
            }
            $columnas_str = implode(", ", $columnas);
            
            while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $valores = [];
                foreach ($fila as $valor) {
                    if ($valor === null) {
                        $valores[] = "NULL";
                    } else {
                        $valores[] = "'" . addslashes($valor) . "'";
                    }
                }
                $sql .= "INSERT INTO `$tabla` ($columnas_str) VALUES (" . implode(", ", $valores) . ");\n";
            }
            $sql .= "\n";
        }
    }
    
    $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
    file_put_contents($ruta, $sql);
    
    return ['exito' => true, 'archivo' => $nombre];
}

// Listar respaldos existentes
function listar_respaldos() {
    $respaldos = [];
    if (file_exists('../backups')) {
        $archivos = scandir('../backups');
        foreach ($archivos as $archivo) {
            if (preg_match('/^respaldo_srpie_.*\.sql$/', $archivo)) {
                $ruta = '../backups/' . $archivo;
                $respaldos[] = [
                    'nombre' => $archivo,
                    'fecha' => date("Y-m-d H:i:s", filemtime($ruta)),
                    'tamaño' => round(filesize($ruta) / 1024, 2) . ' KB'
                ];
            }
        }
        // Ordenar del más reciente al más antiguo
        usort($respaldos, function($a, $b) {
            return strtotime($b['fecha']) - strtotime($a['fecha']);
        });
    }
    return $respaldos;
}

// Respaldo de emergencia (se ejecuta automáticamente antes de restaurar)
function respaldo_emergencia($conn) {
    $fecha = date('Y-m-d_H-i-s');
    $nombre = "emergencia_antes_restaurar_{$fecha}.sql";
    
    if (!file_exists('../backups/emergencia')) {
        mkdir('../backups/emergencia', 0777, true);
    }
    
    $ruta = "../backups/emergencia/" . $nombre;
    
    $tablas = [];
    $stmt = $conn->query("SHOW TABLES");
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        $tablas[] = $row[0];
    }
    
    $sql = "-- RESPALDO DE EMERGENCIA (previo a restauración)\n";
    $sql .= "-- Fecha: " . date('Y-m-d H:i:s') . "\n\n";
    $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";
    
    foreach ($tablas as $tabla) {
        $stmt = $conn->query("SHOW CREATE TABLE $tabla");
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $sql .= "DROP TABLE IF EXISTS `$tabla`;\n";
        $sql .= $row[1] . ";\n\n";
        
        $stmt = $conn->query("SELECT * FROM $tabla");
        if ($stmt->rowCount() > 0) {
            $columnas = [];
            for ($i = 0; $i < $stmt->columnCount(); $i++) {
                $meta = $stmt->getColumnMeta($i);
                $columnas[] = "`{$meta['name']}`";
            }
            $columnas_str = implode(", ", $columnas);
            
            while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $valores = [];
                foreach ($fila as $valor) {
                    $valores[] = $valor === null ? "NULL" : "'" . addslashes($valor) . "'";
                }
                $sql .= "INSERT INTO `$tabla` ($columnas_str) VALUES (" . implode(", ", $valores) . ");\n";
            }
            $sql .= "\n";
        }
    }
    
    $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
    file_put_contents($ruta, $sql);
    
    return $nombre;
}

// Restaurar respaldo (SOLO GERENTE)
function restaurar_respaldo($conn, $archivo_ruta) {
    // 1. Crear respaldo de emergencia
    $emergencia = respaldo_emergencia($conn);
    
    // 2. Leer archivo SQL
    $sql = file_get_contents($archivo_ruta);
    if ($sql === false) {
        return ['exito' => false, 'error' => 'No se pudo leer el archivo'];
    }
    
    // 3. Ejecutar las consultas
    $conn->exec("SET FOREIGN_KEY_CHECKS=0");
    
    $consultas = explode(";\n", $sql);
    $error = null;
    
    foreach ($consultas as $consulta) {
        $consulta = trim($consulta);
        if (!empty($consulta) && substr($consulta, 0, 2) != '--') {
            try {
                $conn->exec($consulta);
            } catch (PDOException $e) {
                $error = "Error: " . $e->getMessage();
                break;
            }
        }
    }
    
    $conn->exec("SET FOREIGN_KEY_CHECKS=1");
    
    if ($error) {
        return ['exito' => false, 'error' => $error];
    }
    
    return ['exito' => true, 'emergencia' => $emergencia];
}

// Crear tabla bitácora si no existe
function crear_tabla_bitacora($conn) {
    $sql = "CREATE TABLE IF NOT EXISTS `bitacora` (
        `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
        `usuario` varchar(9) DEFAULT NULL,
        `accion` varchar(30) NOT NULL,
        `tabla` varchar(50) NOT NULL,
        `registro_id` varchar(50) DEFAULT NULL,
        `detalle` text DEFAULT NULL,
        `ip` varchar(45) DEFAULT NULL,
        `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
    
    $conn->exec($sql);
}

// Registrar en bitácora
function registrar_bitacora($conn, $accion, $tabla, $registro_id = null, $detalle = null) {
    $usuario = $_SESSION['usuario_id'] ?? 'sistema';
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    
    $sql = "INSERT INTO bitacora (usuario, accion, tabla, registro_id, detalle, ip) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$usuario, $accion, $tabla, $registro_id, $detalle, $ip]);
}

// Obtener registros de bitácora - CORREGIDA
function obtener_bitacora($conn, $limite = 100, $offset = 0) {
    $limite = (int)$limite;
    $offset = (int)$offset;
    $sql = "SELECT * FROM bitacora ORDER BY fecha DESC LIMIT $limite OFFSET $offset";
    $stmt = $conn->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function total_bitacora($conn) {
    $stmt = $conn->query("SELECT COUNT(*) as total FROM bitacora");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return (int)$row['total'];
}

// ============================================
// 2. PROCESAR ACCIONES DEL FORMULARIO (SOLO GERENTE)
// ============================================

// Crear tabla bitácora si no existe
crear_tabla_bitacora($conn);

$respaldos = listar_respaldos();

// Solo procesar acciones si es gerente
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $rol_usuario == 'gerente') {
    $accion = $_POST['accion'] ?? '';
    
    if ($accion == 'crear_respaldo') {
        $resultado = crear_respaldo($conn);
        if ($resultado['exito']) {
            $mensaje = "✅ Respaldo creado exitosamente: " . $resultado['archivo'];
            registrar_bitacora($conn, 'CREAR_RESPALDO', 'SISTEMA', null, $resultado['archivo']);
            $respaldos = listar_respaldos();
        } else {
            $error = "❌ Error al crear respaldo";
        }
    }
    
    elseif ($accion == 'restaurar') {
        $archivo_tmp = null;
        
        // Caso 1: archivo subido por drag & drop o input file
        if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] == 0) {
            $archivo_tmp = $_FILES['archivo']['tmp_name'];
            $extension = pathinfo($_FILES['archivo']['name'], PATHINFO_EXTENSION);
            if ($extension != 'sql') {
                $error = "❌ El archivo debe tener extensión .sql";
            }
        }
        // Caso 2: respaldo existente seleccionado
        elseif (!empty($_POST['respaldo_existente'])) {
            $ruta = "../backups/" . $_POST['respaldo_existente'];
            if (file_exists($ruta)) {
                $archivo_tmp = $ruta;
            } else {
                $error = "❌ El archivo de respaldo no existe";
            }
        } else {
            $error = "❌ Debes seleccionar un archivo .sql o un respaldo existente";
        }
        
        if ($archivo_tmp && !$error) {
            $resultado = restaurar_respaldo($conn, $archivo_tmp);
            if ($resultado['exito']) {
                $mensaje = "✅ Base de datos restaurada exitosamente. Respaldo de emergencia creado: " . $resultado['emergencia'];
                registrar_bitacora($conn, 'RESTAURAR', 'SISTEMA', null, "Restaurado desde archivo, respaldo previo: " . $resultado['emergencia']);
                $respaldos = listar_respaldos();
            } else {
                $error = "❌ Error al restaurar: " . $resultado['error'];
            }
        }
    }
    
    elseif ($accion == 'eliminar_respaldo' && !empty($_POST['archivo_eliminar'])) {
        $ruta = "../backups/" . $_POST['archivo_eliminar'];
        if (file_exists($ruta) && unlink($ruta)) {
            $mensaje = "✅ Respaldo eliminado: " . $_POST['archivo_eliminar'];
            registrar_bitacora($conn, 'ELIMINAR_RESPALDO', 'SISTEMA', null, $_POST['archivo_eliminar']);
            $respaldos = listar_respaldos();
        } else {
            $error = "❌ Error al eliminar el respaldo";
        }
    }
}

// Paginación para bitácora
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$por_pagina = 50;
$offset = ($pagina - 1) * $por_pagina;
$total_bitacora = total_bitacora($conn);
$total_paginas = ceil($total_bitacora / $por_pagina);
$registros_bitacora = obtener_bitacora($conn, $por_pagina, $offset);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mantenimiento - Sr. Pie</title>
    <link rel="icon" type="image/png" href="../assets/img/logo_sr_pie.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/sidebar.css">
    <link rel="stylesheet" href="../assets/css/header.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
    <style>
        .drag-area {
            border: 2px dashed #6c757d;
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            background: #f8f9fa;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .drag-area.drag-over {
            border-color: #0d6efd;
            background: #e7f1ff;
        }
        .drag-area i {
            font-size: 48px;
            color: #6c757d;
        }
        .drag-area.drag-over i {
            color: #0d6efd;
        }
        .badge-backup { background-color: #0dcaf0; }
        .badge-restore { background-color: #6f42c1; }
        .table-bitacora {
            font-size: 0.85rem;
        }
        .table-bitacora td, .table-bitacora th {
            vertical-align: middle;
        }
        .btn-disabled {
            opacity: 0.6;
            pointer-events: none;
        }
    </style>
</head>
<body class="bg-light">

    <?php include '../includes/header.php'; ?>

    <div class="d-flex" style="min-height: calc(100vh - 70px);">
        
        <?php include '../includes/sidebar.php'; ?>

        <div class="flex-grow-1" style="overflow-x: auto;">
            
            <div class="container-fluid p-4">
                
                <!-- Título -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="text-primary">
                        <i class="bi bi-database-gear me-2"></i>Mantenimiento del Sistema
                    </h2>
                    <small class="text-muted">Usuario: <?php echo $_SESSION['usuario_id']; ?> (<?php echo $rol_usuario; ?>)</small>
                </div>
                
                <!-- Alertas -->
                <?php if ($mensaje): ?>
                    <div class="alert alert-success alert-dismissible fade show shadow-sm">
                        <i class="bi bi-check-circle-fill me-2"></i> <?php echo $mensaje; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php if ($rol_usuario != 'gerente'): ?>
                    <div class="alert alert-warning shadow-sm">
                        <i class="bi bi-shield-exclamation me-2"></i>
                        <strong>Modo solo lectura:</strong> Tu rol es <strong><?php echo $rol_usuario; ?></strong>. Solo puedes ver los respaldos y la bitácora. Las acciones de respaldo y restauración son exclusivas para gerentes.
                    </div>
                <?php endif; ?>
                
                <div class="row">
                    
                    <!-- ==================== COLUMNA 1: RESPALDOS ==================== -->
                    <div class="col-lg-6 mb-4">
                        <div class="card shadow">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="bi bi-database-check me-2"></i>Gestión de Respaldos</h5>
                            </div>
                            <div class="card-body">
                                
                                <!-- Botón crear respaldo -->
                                <form method="POST" action="" class="mb-4">
                                    <button type="submit" name="accion" value="crear_respaldo" class="btn btn-success w-100 py-2" <?php echo $rol_usuario != 'gerente' ? 'disabled' : ''; ?>>
                                        <i class="bi bi-cloud-arrow-up me-2"></i>Crear Nuevo Respaldo
                                    </button>
                                </form>
                                
                                <hr>
                                
                                <!-- Restaurar -->
                                <h6 class="fw-bold mb-3"><i class="bi bi-arrow-repeat me-2"></i>Restaurar Base de Datos</h6>
                                
                                <form method="POST" action="" enctype="multipart/form-data" id="formRestaurar">
                                    <input type="hidden" name="accion" value="restaurar">
                                    
                                    <!-- Drag & Drop -->
                                    <div id="dragArea" class="drag-area mb-3 <?php echo $rol_usuario != 'gerente' ? 'btn-disabled' : ''; ?>">
                                        <i class="bi bi-cloud-upload"></i>
                                        <p class="mt-2 mb-0">Arrastra y suelta tu archivo .sql aquí</p>
                                        <small class="text-muted">o haz clic para seleccionar</small>
                                        <input type="file" name="archivo" id="archivoInput" accept=".sql" style="display: none;" <?php echo $rol_usuario != 'gerente' ? 'disabled' : ''; ?>>
                                    </div>
                                    
                                    <!-- O seleccionar respaldo existente -->
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">O selecciona un respaldo existente:</label>
                                        <select name="respaldo_existente" class="form-select" <?php echo $rol_usuario != 'gerente' ? 'disabled' : ''; ?>>
                                            <option value="">-- Seleccionar respaldo --</option>
                                            <?php foreach ($respaldos as $r): ?>
                                                <option value="<?php echo htmlspecialchars($r['nombre']); ?>">
                                                    <?php echo $r['nombre']; ?> (<?php echo $r['fecha']; ?> - <?php echo $r['tamaño']; ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-warning w-100 py-2" <?php echo $rol_usuario != 'gerente' ? 'disabled' : ''; ?>>
                                        <i class="bi bi-arrow-repeat me-2"></i>Restaurar Base de Datos
                                    </button>
                                    
                                    <div class="alert alert-info mt-3 mb-0 small">
                                        <i class="bi bi-info-circle-fill me-2"></i>
                                        <strong>Seguridad:</strong> Antes de cada restauración se crea automáticamente un respaldo de emergencia en <code>backups/emergencia/</code>
                                    </div>
                                </form>
                                
                                <hr>
                                
                                <!-- Lista de respaldos -->
                                <h6 class="fw-bold mb-3"><i class="bi bi-archive me-2"></i>Respaldos Guardados</h6>
                                <div class="table-responsive" style="max-height: 300px;">
                                    <table class="table table-sm table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Archivo</th>
                                                <th>Fecha</th>
                                                <th>Tamaño</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($respaldos)): ?>
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted py-3">
                                                        <i class="bi bi-inbox"></i> No hay respaldos disponibles
                                                    </td>
                                                </tr>
                                            <?php else: ?>
                                                <?php foreach ($respaldos as $r): ?>
                                                    <tr>
                                                        <td class="small"><?php echo htmlspecialchars($r['nombre']); ?></td>
                                                        <td class="small"><?php echo $r['fecha']; ?></td>
                                                        <td class="small"><?php echo $r['tamaño']; ?></td>
                                                        <td>
                                                            <form method="POST" action="" style="display: inline;" 
                                                                  onsubmit="return confirm('¿Eliminar este respaldo?');">
                                                                <input type="hidden" name="accion" value="eliminar_respaldo">
                                                                <input type="hidden" name="archivo_eliminar" value="<?php echo htmlspecialchars($r['nombre']); ?>">
                                                                <button type="submit" class="btn btn-sm btn-outline-danger" <?php echo $rol_usuario != 'gerente' ? 'disabled' : ''; ?>>
                                                                    <i class="bi bi-trash"></i>
                                                                </button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- ==================== COLUMNA 2: BITÁCORA ==================== -->
                    <div class="col-lg-6 mb-4">
                        <div class="card shadow">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0"><i class="bi bi-journal-bookmark-fill me-2"></i>Bitácora del Sistema</h5>
                            </div>
                            <div class="card-body">
                                
                                <div class="table-responsive" style="max-height: 500px;">
                                    <table class="table table-sm table-hover table-bitacora">
                                        <thead class="table-light sticky-top">
                                            <tr>
                                                <th>Fecha/Hora</th>
                                                <th>Usuario</th>
                                                <th>Acción</th>
                                                <th>Detalle</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($registros_bitacora)): ?>
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted py-3">
                                                        <i class="bi bi-inbox"></i> No hay registros en la bitácora
                                                    </td>
                                                </tr>
                                            <?php else: ?>
                                                <?php foreach ($registros_bitacora as $reg): ?>
                                                    <tr>
                                                        <td class="small"><?php echo date('d/m/Y H:i:s', strtotime($reg['fecha'])); ?></td>
                                                        <td class="small"><?php echo htmlspecialchars($reg['usuario']); ?></td>
                                                        <td>
                                                            <?php
                                                            $badge = '';
                                                            if (strpos($reg['accion'], 'RESPALDO') !== false) $badge = 'badge-backup';
                                                            elseif ($reg['accion'] == 'RESTAURAR') $badge = 'badge-restore';
                                                            else $badge = 'bg-secondary';
                                                            ?>
                                                            <span class="badge <?php echo $badge; ?>"><?php echo $reg['accion']; ?></span>
                                                        </td>
                                                        <td class="small"><?php echo htmlspecialchars(substr($reg['detalle'] ?? '', 0, 100)); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                                
                                <!-- Paginación -->
                                <?php if ($total_paginas > 1): ?>
                                <nav class="mt-3">
                                    <ul class="pagination pagination-sm justify-content-center">
                                        <li class="page-item <?php echo $pagina == 1 ? 'disabled' : ''; ?>">
                                            <a class="page-link" href="?pagina=1"><<</a>
                                        </li>
                                        <li class="page-item <?php echo $pagina == 1 ? 'disabled' : ''; ?>">
                                            <a class="page-link" href="?pagina=<?php echo $pagina - 1; ?>"><</a>
                                        </li>
                                        <li class="page-item active">
                                            <span class="page-link"><?php echo $pagina; ?> / <?php echo $total_paginas; ?></span>
                                        </li>
                                        <li class="page-item <?php echo $pagina == $total_paginas ? 'disabled' : ''; ?>">
                                            <a class="page-link" href="?pagina=<?php echo $pagina + 1; ?>">></a>
                                        </li>
                                        <li class="page-item <?php echo $pagina == $total_paginas ? 'disabled' : ''; ?>">
                                            <a class="page-link" href="?pagina=<?php echo $total_paginas; ?>">>></a>
                                        </li>
                                    </ul>
                                </nav>
                                <?php endif; ?>
                                
                                <div class="alert alert-secondary mt-3 small mb-0">
                                    <i class="bi bi-info-circle me-2"></i>
                                    Total de eventos registrados: <strong><?php echo number_format($total_bitacora); ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recomendaciones -->
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-light border shadow-sm">
                            <h6 class="mb-2"><i class="bi bi-lightbulb-fill text-warning me-2"></i>Recomendaciones</h6>
                            <ul class="mb-0 small text-muted">
                                <li>Realiza respaldos periódicos, especialmente antes de hacer cambios importantes</li>
                                <li>Los respaldos de emergencia se guardan en <code>backups/emergencia/</code></li>
                                <li>Solo el rol <strong>gerente</strong> puede crear, restaurar y eliminar respaldos</li>
                                <li>La bitácora registra todas las acciones de respaldo y restauración</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
    
    <script src="../assets/js/hamburguesa.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Drag & Drop (solo si es gerente)
        <?php if ($rol_usuario == 'gerente'): ?>
        const dragArea = document.getElementById('dragArea');
        const fileInput = document.getElementById('archivoInput');
        
        if (dragArea && fileInput) {
            dragArea.addEventListener('click', () => fileInput.click());
            
            dragArea.addEventListener('dragover', (e) => {
                e.preventDefault();
                dragArea.classList.add('drag-over');
            });
            
            dragArea.addEventListener('dragleave', () => {
                dragArea.classList.remove('drag-over');
            });
            
            dragArea.addEventListener('drop', (e) => {
                e.preventDefault();
                dragArea.classList.remove('drag-over');
                
                const files = e.dataTransfer.files;
                if (files.length > 0 && files[0].name.endsWith('.sql')) {
                    fileInput.files = files;
                    const span = document.createElement('div');
                    span.className = 'alert alert-success mt-2 mb-0 small';
                    span.innerHTML = `<i class="bi bi-filetype-sql"></i> Archivo: ${files[0].name}`;
                    const old = dragArea.parentElement.querySelector('.alert-success');
                    if (old) old.remove();
                    dragArea.parentElement.appendChild(span);
                    setTimeout(() => span.remove(), 3000);
                } else {
                    alert('Solo archivos .sql');
                }
            });
        }
        
        // Confirmar restauración
        document.getElementById('formRestaurar')?.addEventListener('submit', function(e) {
            const file = document.getElementById('archivoInput');
            const select = document.querySelector('select[name="respaldo_existente"]');
            if ((!file.files.length) && (!select.value)) {
                e.preventDefault();
                alert('Selecciona un archivo .sql o un respaldo existente');
            } else {
                return confirm('⚠️ ADVERTENCIA: Restaurar reemplazará TODA la base de datos actual.\n\nSe creará un respaldo de emergencia automáticamente.\n\n¿Estás seguro?');
            }
        });
        <?php endif; ?>
    </script>
</body>
</html>