<?php
$pagina_actual = basename($_SERVER['PHP_SELF']);

$nombres_modulos = [
    'citas.php'                     => 'Gestión de Citas',
    'agregar_cita.php'              => 'Gestión de Citas', 
    'actualizar_citas.php'          => 'Gestión de Citas',  
    'calendario.php'                => 'Gestión de Citas',   
    'gestion_pacientes.php'         => 'Gestión de Pacientes e historia',
    'historial_pago_paciente.php'   => 'Gestión de Pacientes e historia',
    'facturacion_paciente.php'      => 'Gestión de Pacientes e historia',
    'pacientes.php'                 => 'Gestión de Pacientes e historia',
    'servicios.php'                 => 'Gestion de Servicios',
    'usuarios.php'                  => 'Gestion administrativa',
    'reportes.php'                  => 'Reportes y analítica',
    'manual_usuario.php'            => 'Manual de sistema',
    'mantenimiento.php'             => 'Configuración y Sistema'
];

$titulo_modulo = $nombres_modulos[$pagina_actual] ?? 'Módulo';

// DEFINIR RUTA BASE PARA EL SIDEBAR (Reemplaza al $path anterior)
$ruta = isset($ruta_base) ? $ruta_base : '../';
?>

<nav id="sidebar">
    <div id="header" class="sidebar-header p-4">
        <a href="<?php echo $ruta; ?>dashboard.php" class="nav-link" style="display: inline-block;">
    <img src="<?php echo $ruta; ?>assets/img/logo_sr_pie.png" alt="Logo Sr. Pie" width="50">
</a>
        <h4>Sr. Pie</h4>
    </div>

    <ul class="list-unstyled components">
        <li class="<?= ($pagina_actual == 'dashboard.php') ? 'current-page' : '' ?>">
            <a href="<?= $ruta ?>dashboard.php" class="nav-link <?= ($pagina_actual == 'dashboard.php') ? 'active-link' : 'text-dark' ?>">
                <i class="bi bi-house-door me-2"></i> Pagina principal
            </a>
        </li>
        
        <li class="<?= ($pagina_actual == 'gestion_pacientes.php') ? 'current-page' : '' ?>">
            <a href="<?= $ruta ?>modulos/gestion_pacientes.php" class="nav-link <?= ($pagina_actual == 'gestion_pacientes.php') ? 'active-link' : 'text-dark' ?>">
                <i class="bi bi-person-vcard me-2"></i> Pacientes e historias
            </a>
        </li>
        
        <li class="<?= ($pagina_actual == 'citas.php') ? 'current-page' : '' ?>">
            <a href="<?= $ruta ?>modulos/citas.php" class="nav-link <?= ($pagina_actual == 'citas.php') ? 'active-link' : 'text-dark' ?>">
                <i class="bi bi-calendar2-check me-2"></i> Citas
            </a>
        </li>

         <li class="<?= ($pagina_actual == 'servicios.php') ? 'current-page' : '' ?>">
            <a href="<?= $ruta ?>modulos/servicios.php" class="nav-link <?= ($pagina_actual == 'servicios.php') ? 'active-link' : 'text-dark' ?>">
                <i class="bi bi-card-checklist me-2"></i> Servicios
            </a>
        </li>
        
        <li class="<?= ($pagina_actual == 'usuarios.php') ? 'current-page' : '' ?>">
            <a href="<?= $ruta ?>modulos/usuarios.php" class="nav-link <?= ($pagina_actual == 'usuarios.php') ? 'active-link' : 'text-dark' ?>">
                <i class="bi bi-shield-lock me-2"></i> Gestion administrativa
            </a>
        </li>

        <li class="<?= ($pagina_actual == 'reportes.php') ? 'current-page' : '' ?>">
            <a href="<?= $ruta ?>modulos/reportes.php" class="nav-link <?= ($pagina_actual == 'reportes.php') ? 'active-link' : 'text-dark' ?>">
                <i class="bi bi-bar-chart-line me-2"></i> Reportes y analítica
            </a>
        </li>

        <li class="<?= ($pagina_actual == 'manual_usuario.php') ? 'current-page' : '' ?>">
            <a href="<?= $ruta ?>modulos/manual_usuario.php" class="nav-link <?= ($pagina_actual == 'manual_usuario.php') ? 'active-link' : 'text-dark' ?>">
                <i class="bi bi-journal-bookmark me-2"></i> Manual de Usuario
            </a>
        </li>

        <?php if(isset($_SESSION['rol']) && $_SESSION['rol'] == 'gerente'): ?>
        <li class="<?= ($pagina_actual == 'mantenimiento.php') ? 'current-page' : '' ?>">
            <a href="<?= $ruta ?>modulos/mantenimiento.php" class="nav-link <?= ($pagina_actual == 'mantenimiento.php') ? 'active-link' : 'text-dark' ?>">
                <i class="bi bi-database me-2"></i> Mantenimiento
            </a>
        </li>
        <?php endif; ?>

        <li>
            <a href="<?= $ruta ?>controllers/logout.php" class="nav-link"><i class="bi bi-box-arrow-right me-2"></i> Salir</a>
        </li>
    </ul>
</nav>