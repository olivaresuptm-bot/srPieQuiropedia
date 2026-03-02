<?php
$pagina_actual = basename($_SERVER['PHP_SELF']);

// Diccionario de nombres para que se conecte con tus páginas
$nombres_modulos = [
    'citas.php'         => 'Gestión de Citas',    
    'pacientes.php'     => 'Gestión de Pacientes e historia',
    'servicios.php'     => 'Gestion de Servicios',
    'usuarios.php'      => 'Gestion administrativa',
    'reportes.php'      => 'Reportes y analítica',
    'manual_usuario.php'=> 'Manual de sistema',
    'mantenimiento.php' => 'Configuración y Sistema'
];

$titulo_modulo = $nombres_modulos[$pagina_actual] ?? 'Módulo';
$path = (file_exists('includes/sidebar.php')) ? '' : '../';
?>

<nav id="sidebar">
    <div id="header" class="sidebar-header p-4">
        <img src="../assets/img/logo_sr_pie.png" width="80" class="mb-2">
        <h4>Sr. Pie</h4>
    </div>

    <ul class="list-unstyled components">
        <li class="<?= ($pagina_actual == 'dashboard.php') ? 'current-page' : '' ?>">
            <a href="<?= $path ?>dashboard.php" class="nav-link <?= ($pagina_actual == 'dashboard.php') ? 'active-link' : 'text-dark' ?>">
                <i class="bi bi-speedometer2 me-2"></i> Pagina principal
            </a>
        </li>
        
        <li class="<?= ($pagina_actual == 'pacientes.php') ? 'current-page' : '' ?>">
            <a href="<?= $path ?>../modulos/pacientes.php" class="nav-link <?= ($pagina_actual == 'pacientes.php') ? 'active-link' : 'text-dark' ?>">
                <i class="bi bi-speedometer2 me-2"></i> Pacientes e historias
            </a>
        </li>
        
        <li class="<?= ($pagina_actual == 'citas.php') ? 'current-page' : '' ?>">
            <a href="<?= $path ?>../modulos/citas.php" class="nav-link <?= ($pagina_actual == 'citas.php') ? 'active-link' : 'text-dark' ?>">
                <i class="bi bi-speedometer2 me-2"></i> Citas
            </a>
        </li>

         <li class="<?= ($pagina_actual == 'servicios.php') ? 'current-page' : '' ?>">
            <a href="<?= $path ?>../modulos/servicios.php" class="nav-link <?= ($pagina_actual == 'servicios.php') ? 'active-link' : 'text-dark' ?>">
                <i class="bi bi-card-checklist me-2"></i>   Servicios
            </a>
        </li>
        
        <li class="<?= ($pagina_actual == 'usuarios.php') ? 'current-page' : '' ?>">
            <a href="<?= $path ?>../modulos/usuarios.php" class="nav-link <?= ($pagina_actual == 'usuarios.php') ? 'active-link' : 'text-dark' ?>">
                <i class="bi bi-speedometer2 me-2"></i> Gestion administrativa
            </a>
        </li>

        <li class="<?= ($pagina_actual == 'reportes.php') ? 'current-page' : '' ?>">
            <a href="<?= $path ?>../modulos/reportes.php" class="nav-link <?= ($pagina_actual == 'reportes.php') ? 'active-link' : 'text-dark' ?>">
                <i class="bi bi-speedometer2 me-2"></i> Reportes y analítica
            </a>
        </li>

        <?php if($_SESSION['rol'] == 'gerente'): ?>
        <li class="<?= ($pagina_actual == 'mantenimiento.php') ? 'current-page' : '' ?>">
            <a href="<?= $path ?>../modulos/mantenimiento.php" class="nav-link <?= ($pagina_actual == 'mantenimiento.php') ? 'active-link' : 'text-dark' ?>">
                <i class="bi bi-speedometer2 me-2"></i> Mantenimiento
            </a>
        </li>
        <?php endif; ?>

        <li>
            <a href="../controllers/logout.php" class="nav-link"><i class="bi bi-box-arrow-right me-2"></i> Salir</a>
        </li>
    </ul>
</nav>