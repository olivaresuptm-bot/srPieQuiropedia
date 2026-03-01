<?php
$pagina_actual = basename($_SERVER['PHP_SELF']);
?>

<nav id="sidebar">
    <div class="sidebar-header p-4">
        <img src="../assets/img/logo_sr_pie.png" width="80" class="mb-2">
        <h4>Sr. Pie</h4>
    </div>

    <ul class="list-unstyled components">
        <li class="<?= ($pagina_actual == 'dashboard.php') ? 'current-page' : '' ?>">
            <a href="../dashboard.php" class="nav-link"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
        </li>
        
        <li class="<?= ($pagina_actual == 'pacientes.php') ? 'current-page' : '' ?>">
            <a href="pacientes.php" class="nav-link"><i class="bi bi-people me-2"></i> Pacientes</a>
        </li>
        
        <li class="<?= ($pagina_actual == 'citas.php') ? 'current-page' : '' ?>">
            <a href="citas.php" class="nav-link"><i class="bi bi-calendar-event me-2"></i> Citas</a>
        </li>
        
        <li class="<?= ($pagina_actual == 'servicios.php') ? 'current-page' : '' ?>">
            <a href="servicios.php" class="nav-link"><i class="bi bi-clipboard-pulse me-2"></i> Servicios</a>
        </li>

        <li class="<?= ($pagina_actual == 'pagos.php') ? 'current-page' : '' ?>">
            <a href="pagos.php" class="nav-link"><i class="bi bi-cash-stack me-2"></i> Pagos</a>
        </li>

        <?php if($_SESSION['rol'] == 'gerente'): ?>
        <li class="<?= ($pagina_actual == 'configuracion.php') ? 'current-page' : '' ?>">
            <a href="configuracion.php" class="nav-link"><i class="bi bi-gear me-2"></i> Configuración</a>
        </li>
        <?php endif; ?>

        <li>
            <a href="../controllers/logout.php" class="nav-link"><i class="bi bi-box-arrow-right me-2"></i> Salir</a>
        </li>
    </ul>
</nav>