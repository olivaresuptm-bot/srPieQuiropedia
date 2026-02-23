<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - Sr. Pie</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/login.css"> 
    <link rel="stylesheet" href="assets/css/registro.css">
    <link rel="stylesheet" href="assets/css/footer.css">
</head>
<body class="d-flex flex-column min-vh-100">

    <div class="main-wrapper flex-grow-1">
        <div class="login-card registro-card"> 
            <div class="blue-side d-none d-md-flex" style="flex: 0 0 30%;">
                <div class="logo-container mb-2">
                    <img src="assets/img/logo_sr_pie.png" alt="Logo">
                </div>
                <h2 class="fw-bold h4">Registro</h2>
                <p class="small text-center px-3">Completa todos los campos para crear la cuenta de acceso al sistema.</p>
                <a href="index.php" class="btn btn-registrate btn-sm py-1 px-4">Volver</a>
            </div>

            <div class="form-side p-4">
                <form action="controllers/registro_usuario.php" method="POST" class="w-100">
                    <div class="row g-2">
                        <div class="col-md-2">
                            <label class="form-label">Tipo</label>
                            <select name="tipo_doc" class="form-select custom-input" required>
                                <option value="V">V</option>
                                <option value="E">E</option>
                            </select>
                        </div>
                        <div class="col-md-10">
                            <label class="form-label">Cédula</label>
                            <input type="text" name="cedula" class="form-control custom-input" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Primer Nombre</label>
                            <input type="text" name="nombre1" class="form-control custom-input" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Segundo Nombre</label>
                            <input type="text" name="nombre2" class="form-control custom-input">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Primer Apellido</label>
                            <input type="text" name="apellido1" class="form-control custom-input" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Segundo Apellido</label>
                            <input type="text" name="apellido2" class="form-control custom-input">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Rol del Usuario</label>
                            <select name="rol" class="form-select custom-input" required>
                                <option value="gerente">Gerente</option>
                                <option value="recepcionista">Recepcionista</option>
                                <option value="quiropedista">Quiropedista</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Correo Electrónico</label>
                            <input type="email" name="correo" class="form-control custom-input" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Clave de Acceso</label>
                            <div class="input-group">
                                <input type="password" 
                                       name="clave" 
                                       id="password" 
                                       class="form-control custom-input" 
                                       required
                                       pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[\/\*\!\$\%]).{8,}"
                                       title="La clave debe tener al menos 8 caracteres, una mayúscula, una minúscula, un número y un símbolo (/*$%!)">
                                <span class="input-group-text bg-primary text-white border-0" id="togglePass" style="cursor: pointer;">
                                    <i class="bi bi-eye"></i>
                                </span>
                            </div>
                            <div class="form-text" style="font-size: 0.7rem;">
                                Mínimo 8 caracteres: Mayúscula, Minúscula, Número y Símbolo (/*$%)
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-3">
                        <button type="submit" class="btn btn-primary btn-entrar btn-sm py-2">Crear Usuario</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>

    <script src="assets/js/login.js"></script>
</body>
</html>