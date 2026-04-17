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

//RUTA BASE PARA EL HEADER
$ruta = isset($ruta_base) ? $ruta_base : '../'; 

// Detectar si estamos en el dashboard para ocultar la hamburguesa
$pagina_actual = basename($_SERVER['PHP_SELF']);
$es_dashboard = ($pagina_actual == 'dashboard.php');
?>
<header class="dash-header shadow-sm">
    <div class="d-flex align-items-center header-left-container">
        
        <?php if(!$es_dashboard): ?>
        <button id="btn-menu-movil" class="btn btn-outline-primary d-md-none me-3 border-0">
            <i class="bi bi-list fs-2"></i>
        </button>
        <?php endif; ?>
        
        <div class="welcome-text">
            <h1 class="mb-0 text-truncate">Bienvenida, <?php echo htmlspecialchars($nombre_completo); ?></h1>
            <span class="badge bg-primary rounded-pill px-3"><?php echo strtoupper($rol_usuario); ?></span>
        </div>
    </div>
    
    <div class="d-flex align-items-center gap-2 gap-md-4 header-right-container">
         <a href="<?php echo $ruta; ?>dashboard.php">
                <img src="<?php echo $ruta; ?>assets/img/logo_sr_pie.png" class="logo-header" alt="Logo">
        </a>
        <a href="<?php echo $ruta; ?>controllers/logout.php" class="btn-logout text-danger fs-4" title="Cerrar Sesión">
            <i class="bi bi-box-arrow-right"></i>
        </a>
    </div>
</header>