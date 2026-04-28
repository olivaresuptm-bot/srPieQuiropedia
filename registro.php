<?php
// 1. EL CANDADO DE SEGURIDAD
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'gerente') {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Sr. Pie</title>
    <link rel="icon" type="image/png" href="assets/img/logo_sr_pie.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/login.css"> 
    <link rel="stylesheet" href="assets/css/registro.css">
    <link rel="stylesheet" href="assets/css/footer.css">
</head>

<body class="d-flex flex-column min-vh-100 bg-light m-0 p-0">

    <div class="main-wrapper flex-grow-1">
        <div class="login-card registro-card shadow-lg"> 
            
            <div class="blue-side" style="flex: 0 0 30%;">
                <div class="logo-container mb-2">
                    <img src="assets/img/logo_sr_pie.png" alt="Logo">
                </div>
                
                <h2 class="fw-bold h4">Registro Interno</h2>
                <p class="small text-center px-3">Módulo exclusivo de administración para registrar nuevo personal.</p>
                <a href="modulos/usuarios.php" class="btn btn-registrate btn-sm py-1 px-4">Volver a Usuarios</a>
            </div>

            <div class="form-side p-4">
                <form action="controllers/registro_usuario.php" method="POST" class="w-100">
                    <div class="row g-2">
                        <div class="col-md-2 col-4 position-relative">
                            <label class="form-label fw-bold">Tipo</label>
                            <select name="tipo_doc" class="form-select custom-input" required>
                                <option value="V">V</option>
                                <option value="E">E</option>
                            </select>
                        </div>

                        <div class="col-md-10 col-8 position-relative">
                            <label class="form-label fw-bold">Cédula</label>
                            <input type="text" name="cedula" class="form-control custom-input" required maxlength="9" placeholder="Ej: 26123456" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        </div>

                        <div class="col-md-6 position-relative">
                            <label class="form-label fw-bold">Primer Nombre</label>
                            <input type="text" name="nombre1" class="form-control custom-input" required oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '')">
                        </div>

                        <div class="col-md-6 position-relative">
                            <label class="form-label fw-bold">Segundo Nombre</label>
                            <input type="text" name="nombre2" class="form-control custom-input" oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '')">
                        </div>

                        <div class="col-md-6 position-relative">
                            <label class="form-label fw-bold">Primer Apellido</label>
                            <input type="text" name="apellido1" class="form-control custom-input" required oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '')">
                        </div>

                        <div class="col-md-6 position-relative">
                            <label class="form-label fw-bold">Segundo Apellido</label>
                            <input type="text" name="apellido2" class="form-control custom-input" oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '')">
                        </div>

                        <div class="col-12 position-relative">
                            <label class="form-label fw-bold">Rol del Usuario</label>
                            <select name="rol" class="form-select custom-input" required>
                                <option value="" disabled selected>Seleccione un rol...</option>
                                <option value="gerente">Gerente</option>
                                <option value="recepcionista">Recepcionista</option>
                                <option value="quiropedista">Quiropedista</option>
                            </select>
                        </div>

                        <div class="col-12 position-relative">
                            <label class="form-label fw-bold">Correo Electrónico</label>
                            <input type="email" name="correo" class="form-control custom-input" required>
                        </div>

                        <div class="col-12 position-relative">
                            <label class="form-label fw-bold">Clave de Acceso</label>
                            <div class="input-group">
                                <input type="password" 
                                       name="clave" 
                                       id="password" 
                                       class="form-control custom-input border-end-0" 
                                       required
                                       pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[\/\*\!\$\%]).{8,}"
                                       title="Mínimo 8 caracteres, una mayúscula, minúscula, número y un símbolo (/*$%!)">
                                <span class="input-group-text bg-white border-start-0 text-primary" id="togglePass" style="cursor: pointer; border-color: #dee2e6;">
                                    <i class="bi bi-eye"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary btn-entrar btn-lg py-2 w-100 shadow-sm">Registrar usuario</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="w-100 p-0 m-0 border-top bg-dark" style="position: relative; z-index: 1200;">
        <?php include 'includes/footer.php'; ?>
    </div>
    
    <script src="assets/js/login.js"></script>
     <script src="assets/js/registro.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

   
</body>
</html>