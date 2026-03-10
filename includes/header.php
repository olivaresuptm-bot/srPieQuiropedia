<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($nombre_completo)) {
    $nombre = $_SESSION['nombre'] ?? 'Usuario';
    $apellido = $_SESSION['apellido'] ?? '';
    $nombre_completo = trim($nombre . " " . $apellido);
}
if (!isset($rol_usuario)) {
    $rol_usuario = $_SESSION['rol'] ?? 'Personal';
}
?>
<header class="dash-header">
    <div class="welcome-text">
        <h1 class="mb-0">Bienvenido, <?php echo $nombre_completo; ?></h1>
        <span class="badge bg-primary rounded-pill px-3"><?php echo strtoupper($rol_usuario); ?></span>
    </div>
    
    <div class="d-flex align-items-center gap-4">
         <a href="../dashboard.php" >
                <img src="../assets/img/logo_sr_pie.png" width="80" class="mb-2">
        </a>
        <a href="../controllers/logout.php" class="btn-logout" title="Cerrar Sesión">
            <i class="bi bi-box-arrow-right"></i>
        </a>
    </div>
</header>