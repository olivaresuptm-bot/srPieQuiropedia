<?php include 'includes/db.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva Clave - Sr. Pie</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100 bg-light">
    <div class="login-card p-4 shadow" style="max-width: 400px; width: 100%;">
        <h4 class="text-center mb-4">Nueva Contraseña</h4>
        <form action="controllers/actualizar_pass.php" method="POST">
            <input type="hidden" name="token" value="<?php echo $_GET['token'] ?? ''; ?>">
            <div class="mb-3">
                <input type="password" name="nueva_clave" class="form-control" placeholder="Nueva clave" required 
                       pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[\/\*\!\$\%\!]).{8,}" 
                       title="Mínimo 8 caracteres, 1 Mayúscula, 1 Número y 1 Símbolo">
            </div>
            <button type="submit" class="btn btn-primary w-100">Actualizar</button>
        </form>
    </div>
</body>
</html>