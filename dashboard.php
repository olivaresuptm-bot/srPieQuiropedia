<?php require_once 'includes/dashboard_conexion.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control - Sr. Pie</title>
    <link rel="icon" type="image/png" href="assets/img/logo_sr_pie.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/login.css">
    <link rel="stylesheet" href="assets/css/footer.css">
    <link rel="stylesheet" href="assets/css/dashboard.css?v=1.1">
    <link rel="stylesheet" href="assets/css/header.css">
</head>
<body class="d-flex flex-column min-vh-100">

<?php include 'includes/header.php'; ?>


    <main class="mt-5 modules-container flex-grow-1">
        
        <a href="modulos/pacientes.php" class="module-card">
            <i class="bi bi-person-vcard"></i>
            <span>Pacientes e Historias</span>
            <small>Búsqueda de fichas y acceso a historiales clínicos.</small>
        </a>

<!-- Estos son los modulos a los que el Administrador y recepcionista solo tiene acceso ;) -->        
        <?php if($rol_usuario == 'gerente' || $rol_usuario == 'recepcionista'): ?> 
            <a href="modulos/citas.php" class="module-card">
                <i class="bi bi-calendar2-check"></i>
                <span>Gestión de Citas</span>
                <small>Agendar, reprogramar y controlar turnos diarios.</small>
            </a>
        <?php endif; ?>
  

<!-- Estos son los modulos a los que el Administrador solo tiene acceso ;) -->

        <?php if($rol_usuario == 'gerente'): ?> 
            <a href="modulos/servicios.php" class="module-card">
                <i class="bi bi-card-checklist"></i>
                <span>Servicios</span>
                <small>Acceso a servios ofrecidos.</small>
            </a>
        <?php endif; ?>

        <?php if($rol_usuario == 'gerente'): ?>
            <a href="modulos/usuarios.php" class="module-card">
                <i class="bi bi-shield-lock"></i>
                <span>Gestión Administrativa</span>
                <small>Control de personal, usuarios y permisos.</small>
            </a>
        <?php endif; ?>

<!-- ---------------------------------------------------------------------------------------- -->
        
        <a href="modulos/reportes.php" class="module-card">
            <i class="bi bi-bar-chart-line"></i>
            <span>Reportes y Analítica</span>
            <small>Estadísticas de atención y rendimiento del sistema.</small>
        </a>

        <a href="modulos/manual_usuario.php" class="module-card">
            <i class="bi bi-journal-bookmark"></i>
            <span>Manual de Sistema</span>
            <small>Instrucciones y guía de uso para el personal.</small>
        </a>

<!-- Este modulo el Administrador y Recepcionista solo tiene acceso ;) -->
        <?php if($rol_usuario == 'gerente' || $rol_usuario == 'recepcionista'): ?>
            <a href="modulos/mantenimiento.php" class="module-card">
                <i class="bi bi-database"></i>
                <span>Mantenimiento</span>
                <small>Mantenimiento del sistema, respaldo de la Base de datos</small>
            </a>
        <?php endif; ?>
    </main>

<?php include 'includes/footer.php'; ?>

</body>
</html>