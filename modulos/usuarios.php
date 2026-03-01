<?php
session_start();
if (!isset($_SESSION['usuario_id']) || strtolower($_SESSION['rol']) !== 'gerente') {
    header("Location: ../dashboard.php");
    exit;
}

$pagina_actual = basename(__FILE__);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios - Sr. Pie</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  
    <link rel="stylesheet" href="../assets/css/sidebar.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
    <link rel="stylesheet" href="../assets/css/header.css">
</head>
<body>

   <body class="bg-light">
    <?php include '../includes/header.php'; ?>

    <div class="d-flex" style="height: calc(100vh - 70px); overflow: hidden;">
        
        <?php include '../includes/sidebar.php'; ?>

        <div id="content" class="w-100" style="overflow-y: auto;">
            <nav class="navbar navbar-light bg-white py-1 px-3 border-bottom shadow-sm">
                <button type="button" id="sidebarCollapse" class="btn btn-sm btn-primary">
                    <i class="bi bi-list"></i>
                </button>
                <span class="ms-2 small fw-bold text-secondary">Gestión administrativa</span>
            </nav>

            <div class="container-fluid p-4">
                </div>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const btn = document.getElementById('sidebarCollapse');
        const sidebar = document.getElementById('sidebar');
        if(btn && sidebar) {
            btn.addEventListener('click', function() {
                sidebar.classList.toggle('active');
            });
        }
    });
    </script>
</body>
</html>