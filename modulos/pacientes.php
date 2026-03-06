<?php
session_start();

$dsn = "mysql:host=localhost;dbname=srpiequiropedia;charset=utf8mb4";
$usuario = "root";
$password = "";

try {
    $pdo = new PDO($dsn, $usuario, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Lógica de guardado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $cedula = $_POST['cedula_id'];

        // 1. Verificar si la cédula ya existe
        $verificarSql = "SELECT COUNT(*) FROM pacientes WHERE cedula_id = :cedula";
        $verificarSmt = $pdo->prepare($verificarSql);
        $verificarSmt->execute([':cedula' => $cedula]);
        
        if ($verificarSmt->fetchColumn() > 0) {
            // Si ya existe, lanzamos un error personalizado
            $_SESSION['mensaje'] = ["tipo" => "error", "texto" => "❌ Error: El número de cédula ya se encuentra registrado."];
        } else {
            // 2. Si no existe, procedemos con el registro
            $sql = "INSERT INTO pacientes (cedula_id, tipo_doc, primer_nombre, segundo_nombre, primer_apellido, segundo_apellido, fecha_nac, genero, telefono, correo, direccion, registrado_por, fecha_registro) 
                    VALUES (:cedula, :tipo, :nom1, :nom2, :ape1, :ape2, :f_nac, :gen, :tel, :cor, :dir, :reg_por, :f_reg)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':cedula'  => $cedula,
                ':tipo'    => $_POST['tipo_doc'],
                ':nom1'    => $_POST['primer_nombre'],
                ':nom2'    => $_POST['segundo_nombre'],
                ':ape1'    => $_POST['primer_apellido'],
                ':ape2'    => $_POST['segundo_apellido'],
                ':f_nac'   => $_POST['fecha_nac'],
                ':gen'     => $_POST['genero'],
                ':tel'     => $_POST['telefono'],
                ':cor'     => $_POST['correo'],
                ':dir'     => $_POST['direccion'],
                ':reg_por' => $_POST['registrado_por'],
                ':f_reg'   => date("Y-m-d H:i:s")
            ]);

            $_SESSION['mensaje'] = ["tipo" => "success", "texto" => "Paciente guardado con éxito."];
        }
    } catch (PDOException $e) {
        $_SESSION['mensaje'] = ["tipo" => "error", "texto" => "❌ Error: " . $e->getMessage()];
    }
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

$msj = $_SESSION['mensaje'] ?? null;
unset($_SESSION['mensaje']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Pacientes - Sr. Pie</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/pacientes.css">

</head>
<body>

<div class="main-card">
    <div class="row g-0">
        <div class="col-lg-5 panel-info">
            <div class="mb-4">
                <img src="LOGO_PAGINA" alt="Logo" style="width: 100px;">
            </div>
            <h1 class="fw-bold mb-3">Registro</h1>
            <p class="mb-5 opacity-75">Completa todos los campos para registrar al paciente en el sistema de gestión.</p>
            <a href="PAGINA_PRINCIPAL" class="btn-volver">Volver</a>
        </div>

        <div class="col-lg-7 form-panel">
            
            <?php if ($msj && $msj['tipo'] === "success"): ?>
                <div class="text-center">
                    <div class="check-icon">✓</div>
                    <h2 class="fw-bold mb-3" style="color: var(--azul-principal);">¡Registro Exitoso!</h2>
                    <p class="text-muted mb-4"><?php echo $msj['texto']; ?></p>
                    <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-crear">
                        Nuevo Paciente
                    </a>
                </div>

            <?php else: ?>
                <?php if($msj && $msj['tipo'] === "error"): ?>
                    <div class='alert alert-danger border-0 small'><?php echo $msj['texto']; ?></div>
                <?php endif; ?>

                <form method="POST" class="row g-2">
                    <div class="col-4">
                        <label class="form-label small text-muted">Tipo</label>
                        <select name="tipo_doc" class="form-select">
                            <option value="V">V</option>
                            <option value="E">E</option>
                        </select>
                    </div>
                    <div class="col-8">
                        <label class="form-label small text-muted">Cédula</label>
                        <input type="number" name="cedula_id" class="form-control" required>
                    </div>

                    <div class="col-6">
                        <label class="form-label small text-muted">Primer Nombre</label>
                        <input type="text" name="primer_nombre" class="form-control" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label small text-muted">Segundo Nombre</label>
                        <input type="text" name="segundo_nombre" class="form-control">
                    </div>

                    <div class="col-6">
                        <label class="form-label small text-muted">Primer Apellido</label>
                        <input type="text" name="primer_apellido" class="form-control" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label small text-muted">Segundo Apellido</label>
                        <input type="text" name="segundo_apellido" class="form-control">
                    </div>

                    <div class="col-6">
                        <label class="form-label small text-muted">Fecha de Nacimiento</label>
                        <input type="date" name="fecha_nac" class="form-control" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label small text-muted">Género</label>
                        <select name="genero" class="form-select">
                            <option value="M">Masculino</option>
                            <option value="F">Femenino</option>
                            <option value="O">Otro</option>
                        </select>
                    </div>

                    <div class="col-6">
                        <label class="form-label small text-muted">Teléfono</label>
                        <input type="text" name="telefono" class="form-control">
                    </div>
                    <div class="col-6">
                        <label class="form-label small text-muted">Correo Electrónico</label>
                        <input type="email" name="correo" class="form-control">
                    </div>

                    <div class="col-12">
                        <label class="form-label small text-muted">Dirección</label>
                        <textarea name="direccion" class="form-control" rows="1"></textarea>
                    </div>

                    <div class="col-12">
                        <label class="form-label small text-muted">Registrado Por</label>
                        <input type="text" name="registrado_por" class="form-control" placeholder="Nombre del operador" required>
                    </div>

                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-crear w-100">Crear Usuario</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>