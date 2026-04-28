<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sr. Pie</title>
    <link rel="icon" type="image/png" href="assets/img/logo_sr_pie.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/login.css">
    <link rel="stylesheet" href="assets/css/footer.css">
    
       
      
    
</head>
<body>

<div class="main-wrapper">
    <div class="login-card shadow-lg">
        <div class="form-side p-5">
            <div class="logo-container text-center">
                <img src="assets/img/logo_sr_pie.png" alt="Logo Sr. Pie" style="max-width: 200px;">
            </div>
                
            <h2 class="text-center mb-4 text-primary fw-bold">Iniciar Sesión</h2>
                
            <form action="controllers/login.php" method="POST" class="w-100">
                <div class="mb-3 position-relative">
                    <label class="form-label text-muted small fw-bold">Cédula de Identidad</label>
                    <input type="text" name="usuario" id="cedula_login" class="form-control custom-input" placeholder="Ej: 26123456" required maxlength="9" title="Ingrese únicamente números">
                    <div id="cedula-warning" class="text-danger small mt-1" style="display: none;">
                        <i class="bi bi-exclamation-circle"></i> Solo se permiten números.
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label text-muted small fw-bold">Contraseña</label>
                    <div class="input-group">
                        <input type="password" name="password" id="password" class="form-control custom-input border-end-0" placeholder="********" required>
                        <span class="input-group-text bg-white border-start-0" style="color: #4a90e2; cursor: pointer;" id="togglePass">
                            <i class="bi bi-eye"></i>
                        </span>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-entrar w-100 py-2 fs-5 shadow-sm">Entrar</button>
                    
            </form>
            <div class="text-center mt-4">
                <a href="recuperar.php" class="text-decoration-none small text-muted">
                    ¿Olvidaste tu clave? <span class="text-primary fw-bold">Recupérala aquí</span>
                </a>
            </div>
        </div>
    </div>
</div>
    
<?php if (isset($_GET['error']) && $_GET['error'] == 'cuenta_desactivada'): ?>
    <div class="position-absolute top-0 end-0 p-3">
        <div class="alert alert-warning alert-dismissible fade show shadow" role="alert">
            <strong>Atención:</strong> Tu cuenta ha sido desactivada. Contacta al administrador.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
<script src="assets/js/login.js"></script>


</body>
</html>