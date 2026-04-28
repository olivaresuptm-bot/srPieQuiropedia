<?php 
session_start();
// CANDADO DE SEGURIDAD: Si alguien intenta entrar aquí directo por la URL sin validar sus datos, lo expulsamos.
if (!isset($_SESSION['reset_autorizado']) || $_SESSION['reset_autorizado'] !== true) {
    header("Location: index.php");
    exit();
}
include 'includes/db.php'; 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Clave - Sr. Pie</title>
    <link rel="icon" type="image/png" href="assets/img/logo_sr_pie.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/login.css"> 
    <link rel="stylesheet" href="assets/css/registro.css">
    <link rel="stylesheet" href="assets/css/footer.css">
</head>
<body class="d-flex flex-column min-vh-100 bg-light"> 
    <div class="main-wrapper flex-grow-1 d-flex align-items-center justify-content-center">
        <div class="login-card p-4 shadow" style="max-width: 450px; width: 100%; flex-direction: column;">
        <div class="text-center mb-4">
            <img src="assets/img/logo_sr_pie.png" alt="Logo" style="width: 80px;">
            <h3 class="mt-3 fw-bold" style="color: #4a90e2;">Nueva Contraseña</h3>
            <p class="text-muted small">Ingresa tu nueva clave de acceso</p>
        </div>

        <form action="controllers/actualizar_pass.php" method="POST">
            
            <div class="mb-3">
                 <div class="input-group">
                <input type="password" name="nueva_clave" id="password" class="form-control custom-input" 
                        placeholder="Mínimo 8 caracteres" required 
                        pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[\/\*\!\$\%\!]).{8,}" 
                        title="Debe contener: 8+ caracteres, 1 Mayúscula, 1 Número y 1 Símbolo">
                       <span class="input-group-text bg-primary text-white border-0" id="togglePass" style="cursor: pointer;">
                                    <i class="bi bi-eye"></i>
                        </span>
                </div>
            </div>

            <div class="form-text mb-4" style="font-size: 0.75rem;">
                Mínimo 8 caracteres: Mayúscula, Minúscula, Número y Símbolo (/*$%)
            </div>

            <button type="submit" class="btn btn-entrar text-white w-100 py-2 fs-5">
                Guardar Contraseña
            </button>

        </form>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/login.js"></script>
   
</body>
</html>