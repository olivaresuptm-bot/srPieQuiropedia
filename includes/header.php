<header class="dash-header">
        <div class="welcome-text">
            <h1 class="mb-0">Bienvenido, <?php echo $nombre_completo; ?></h1>
            <span class="badge bg-primary rounded-pill px-3"><?php echo strtoupper($rol_usuario); ?></span>
        </div>
        
        <div class="d-flex align-items-center gap-4">
            <img src="assets/img/logo_sr_pie.png" alt="Logo" style="width: 60px;">
            <a href="controllers/logout.php" class="btn-logout" title="Cerrar Sesión">
                <i class="bi bi-box-arrow-right"></i>
            </a>
        </div>
    </header>
