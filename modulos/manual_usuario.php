<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manual - Sr. Pie</title>
    <link rel="icon" type="image/png" href="../assets/img/logo_sr_pie.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/sidebar.css">
    <link rel="stylesheet" href="../assets/css/header.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
</head>
<body class="bg-light">

    <?php include '../includes/header.php'; ?>

    <div class="d-flex" style="height: calc(100vh - 70px); overflow: hidden;">
        
        <?php include '../includes/sidebar.php'; ?>

        <?php include '../includes/titulo_modulo.php'; ?>

<div class="container ">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
                    <h2 class="mb-2">Manual de Usuario - Sr. Pie</h2>
                    <p class="text-muted mb-2">Haz clic en la imagen para descargar</p>
                    
            <a href="../assets/docs/Manual_Usuario_SrPie.pdf" download="Manual_Usuario_SrPie.pdf" 
                    class="manual-download-link">
                <img src="../assets/img/portada_manual.png" 
                     alt="Descargar Manual de Usuario" 
                     style="max-width: 300px; transition: transform 0.3s ease;">
             </a>
             <div>
                    <a href="../assets/docs/Manual_Usuario_SrPie.pdf" 
                        download="Manual_Usuario_SrPie.pdf" 
                        class="btn btn-primary ">
                        <i class="bi bi-file-earmark-pdf-fill me-2"></i>Descargar PDF
                    </a>
             </div>
        </div>
    </div>
</div>

            
        
        
    </div> </div> </div> <?php include '../includes/footer.php'; ?>
    <script src="../assets/js/hamburguesa.js"></script>
</body>
</html>