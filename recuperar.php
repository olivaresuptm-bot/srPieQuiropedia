<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - Sr. Pie</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/login.css">
    <link rel="stylesheet" href="assets/css/footer.css">
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100">

    <div class="main-wrapper">
        <div class="login-card p-4 p-md-5">
            <div class="text-center mb-4">
                <img src="assets/img/logo_sr_pie.png" alt="Logo Sr. Pie" class="mb-3" style="width: 80px;">
                <h1>¿Olvidaste tu clave?</h1>
                <p class="text-muted small">Valida tus datos para restablecer el acceso</p>
            </div>

            <form action="controllers/recuperar_clave.php" method="POST">
                <div class="mb-3">
                    <label class="form-label small">Cédula de Identidad</label>
                    <input type="text" name="cedula" class="form-control" placeholder="Ej: 26123456" required>
                </div>

                <div class="mb-3">
                    <label class="form-label small">Correo Electrónico</label>
                    <input type="email" name="correo" class="form-control" placeholder="correo@ejemplo.com" required>
                </div>

                <div class="mb-4">
                    <label class="form-label small">Nueva Contraseña</label>
                    <div class="input-group">
                        <input type="password" 
                               id="nueva_clave" 
                               name="nueva_clave" 
                               class="form-control" 
                               placeholder="Mínimo 8 caracteres" 
                               required
                               pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[\/\*\$\%]).{8,}"
                               title="Debe tener 8 caracteres, incluir Mayúscula, Minúscula, Número y Símbolo (/*$%!)">
                        <span class="input-group-text" style="background: #4a90e2; color: white; border: none; cursor: pointer;" id="togglePass">                    
                            <i class="bi bi-eye"></i>
                        </span>
                    </div>
                    <div class="form-text" style="font-size: 0.7rem;">
                        Mínimo 8 caracteres: Mayúscula, Minúscula, Número y Símbolo (/*$%)
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
                    Actualizar Contraseña
                </button>

                <div class="text-center mt-4">
                    <a href="index.php" class="text-decoration-none small text-primary fw-bold">
                        <i class="bi bi-arrow-left"></i> Volver al inicio de sesión
                    </a>
                </div>
            </form>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    
    <script src="assets/js/recuperar_clave.js"></script>
</body>
</html>
