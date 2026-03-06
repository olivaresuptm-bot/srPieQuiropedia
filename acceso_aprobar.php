<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuario Aprobado - Sr. Pie</title>
    <link rel="icon" type="image/png" href="assets/img/logo_sr_pie.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/login.css">
    <style>
        .status-card-success {
            max-width: 500px; 
            width: 100%;
            border-top: 6px solid #28a745; /* Verde de éxito */
            padding: 40px !important;
            text-align: center;
            flex-direction: column !important; /* Mantiene el flujo vertical */
        }
        .icon-circle-success {
            width: 80px; 
            height: 80px;
            background-color: #d4edda; /* Fondo verde suave */
            color: #28a745; /* Icono verde fuerte */
            border-radius: 50%;
            display: flex; 
            align-items: center; 
            justify-content: center;
            font-size: 40px; 
            margin: 0 auto 20px;
        }
        .logo-small {
            width: 70px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="main-wrapper">
        <div class="login-card status-card-success shadow">
            
            <img src="../assets/img/logo_sr_pie.png" alt="Logo Sr. Pie" class="logo-small">

            <div class="icon-circle-success">
                <i class="bi bi-person-check-fill"></i>
            </div>

            <h2 class="fw-bold mb-3" style="color: #28a745;">¡Registro Aprobado!</h2>
            
            <p class="text-muted mb-1">El usuario <strong><?php echo $nombre_completo; ?></strong></p>
            <p class="text-muted mb-4">ha sido activado exitosamente como <strong><?php echo $rol_usuario; ?></strong>.</p>
            
            <div class="w-100">
                <a href="../index.php" class="btn btn-entrar text-white text-decoration-none d-block">
                    Ir al Inicio de Sesión
                </a>
            </div>
        </div>
    </div>
</body>
</html>