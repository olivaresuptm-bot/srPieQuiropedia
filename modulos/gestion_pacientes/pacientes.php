<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../../index.php");
    exit;
}
include '../../controllers/registro_paciente.php'; 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Pacientes - Sr. Pie</title>
    <link rel="icon" type="image/png" href="../../assets/img/logo_sr_pie.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../assets/css/sidebar.css">
    <link rel="stylesheet" href="../../assets/css/header.css">
    <link rel="stylesheet" href="../../assets/css/footer.css">
</head>
<body class="bg-light">
    <?php $ruta_base = '../../'; 
    include '../../includes/header.php'; ?>

    <div class="d-flex" style="height: calc(100vh - 70px); overflow: hidden;">
        <?php $ruta_base = '../../'; 
        include '../../includes/sidebar.php'; 
        include '../../includes/titulo_modulo.php'; ?>
        
       <div class="container-fluid d-flex justify-content-center align-items-center bg-light">
        <div class="card border-0 shadow-lg p-4" style="max-width: 700px; border-radius: 15px;">
        
            <div class="row g-0">
                <div class="col-lg-12 panel-info text-center">
                <div class="d-flex align-items-center justify-content-center mb-2 text-primary">
                        <i class="bi bi-person-plus-fill me-2"></i>
                        <span class="small fw-bold text-uppercase" style="letter-spacing: 1px;">Gestión de Pacientes</span>
                </div>
                <h2 class="fw-bold mb-2" style="color: #333;">Registro</h2>
                <p class="mb-4 text-muted small">Completa todos los campos para registrar al paciente en el sistema.</p>
                <hr class="mb-4 opacity-25">
                </div>

            <div class="col-lg-12 form-panel">
                <?php if ($msj && $msj['tipo'] === "success"): ?>
                    <div class="text-center py-4">
                        <div class="check-icon mb-3" style="font-size: 3rem; color: #28a745;">✓</div>
                        <h2 class="fw-bold mb-3" style="color: #0d6efd;">¡Registro Exitoso!</h2>
                        <p class="text-muted mb-4"><?php echo $msj['texto']; ?></p>
                        <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-primary w-100 rounded-pill">
                            Nuevo Paciente
                        </a>
                    </div>

                <?php else: ?>
                    <?php if($msj && $msj['tipo'] === "error"): ?>
                        <div class='alert alert-danger border-0 small mb-3'><?php echo $msj['texto']; ?></div>
                    <?php endif; ?>

                    <form method="POST" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-secondary">Tipo</label>
                            <select name="tipo_doc" class="form-select border-light-subtle shadow-sm">
                                <option value="V">V</option>
                                <option value="E">E</option>
                            </select>
                        </div>
                        <div class="col-md-9">
                            <label class="form-label small fw-bold text-secondary">Cédula</label>
                            <input type="number" name="cedula_id" class="form-control border-light-subtle shadow-sm" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Primer Nombre</label>
                            <input type="text" name="primer_nombre" class="form-control border-light-subtle shadow-sm" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Segundo Nombre</label>
                            <input type="text" name="segundo_nombre" class="form-control border-light-subtle shadow-sm">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Primer Apellido</label>
                            <input type="text" name="primer_apellido" class="form-control border-light-subtle shadow-sm" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Segundo Apellido</label>
                            <input type="text" name="segundo_apellido" class="form-control border-light-subtle shadow-sm">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Nacimiento</label>
                            <input type="date" name="fecha_nac" class="form-control border-light-subtle shadow-sm" >
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Género</label>
                            <select name="genero" class="form-select border-light-subtle shadow-sm">
                                <option value="M">Masculino</option>
                                <option value="F">Femenino</option>
                                <option value="O">Otro</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-secondary">Teléfono</label>
                            <input type="text" name="telefono" class="form-control border-light-subtle shadow-sm" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-secondary">Correo</label>
                            <input type="email" name="correo" class="form-control border-light-subtle shadow-sm">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-secondary">Instagram (@)</label>
                            <input type="text" name="instagram" class="form-control border-light-subtle shadow-sm" placeholder="Ej: @usuario">
                        </div>

                        <div class="col-md-8">
                            <label class="form-label small fw-bold text-secondary">Dirección</label>
                            <textarea name="direccion" class="form-control border-light-subtle shadow-sm" rows="1" ></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-secondary">¿Es Diabético?</label>
                            <select name="diabetico" class="form-select shadow-sm">
                                <option value="No">No</option>
                                <option value="Si">Sí</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold text-secondary">Registrado Por</label>
                            <input type="text" name="registrado_por" class="form-control border-light-subtle shadow-sm" placeholder="Cédula del operador" required>
                        </div>

                        <div class="col-12 mt-4">
                            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm" style="border-radius: 8px; background-color: #0d6efd;">
                                <i class="bi bi-save me-2"></i>Registrar Paciente
                            </button>
                        </div>
                    </form>
                    <div class="text-center mt-3">
                        <a href="../gestion_pacientes.php" class="text-decoration-none small text-muted"> Volver a gestión de pacientes </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>     
    
    </div></div></div><?php include '../../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/hamburguesa.js"></script>
</body>
</html>