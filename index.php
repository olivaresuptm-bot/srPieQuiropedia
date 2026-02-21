<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Sr. Pie</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/login.css">
    <link rel="stylesheet" href="assets/css/footer.css">
</head>
<body>

    <div class="main-wrapper">
        <div class="login-card">
            
            <div class="form-side">
                <div class="logo-container">
                    <img src="assets/img/logo_sr_pie.png" alt="Logo Sr. Pie">
                </div>
                <h1>Iniciar Sesión</h1>
                
                <form action="controllers/login.php" method="POST" class="w-100">
                    <div class="mb-3">
                        <input type="text" name="usuario" class="form-control custom-input" placeholder="Usuario/C.I." required>
                    </div>
                    <div class="mb-4 input-group">
                        <input type="password" name="password" id="password" class="form-control custom-input" placeholder="Contraseña" required>
                        <span class="input-group-text" style="background: #4a90e2; color: white; border: none; cursor: pointer;" id="togglePass">
                            <i class="bi bi-eye"></i>
                        </span>
                    </div>
                    <button type="submit" class="btn btn-primary btn-entrar">Entrar</button>
                </form>
            </div>

            <div class="blue-side">
                <h2>¿Aún no tienes una cuenta?</h2>
                <p>Regístrate para que inicies sesión en el sistema de gestión.</p>
                <a href="registro.php" class="btn btn-registrate">Regístrate</a>
            </div>

        </div>
    </div>

    <!-- Aqui inclui el footer -->
    <?php include 'includes/footer.php'; ?>
   
    <!-- Este script lo agregue para que funcione ojo de ocultar y mostra clave :) -->
    <script>
        const btn = document.querySelector('#togglePass');
        const input = document.querySelector('#password');
        btn.addEventListener('click', () => {
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
            btn.querySelector('i').classList.toggle('bi-eye');
            btn.querySelector('i').classList.toggle('bi-eye-slash');
        });
    </script>
</body>
</html>