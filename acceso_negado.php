
    <!DOCTYPE html>
    <html lang='es'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Error de Aprobación - Sr. Pie</title>
        <link rel="icon" type="image/png" href="assets/img/logo_sr_pie.png">
        <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
        <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css'>
        <link rel='stylesheet' href='../assets/css/login.css'>
        <style>
            .status-card-error {
                max-width: 500px; width: 100%;
                border-top: 6px solid #dc3545;
                padding: 40px !important;
                text-align: center;
                flex-direction: column !important;
            }
            .icon-circle-error {
                width: 80px; height: 80px;
                background-color: #f8d7da;
                color: #dc3545;
                border-radius: 50%;
                display: flex; align-items: center; justify-content: center;
                font-size: 40px; margin: 0 auto 20px;
            }
        </style>
    </head>
    <body>
        <div class='main-wrapper'>
            <div class='login-card status-card-error shadow'>
                <div class='icon-circle-error'>
                    <i class='bi bi-x-circle-fill'></i>
                </div>
                <h2 class='fw-bold mb-3' style='color: #dc3545;'>Acceso Denegado</h2>
                <p class='text-muted mb-4'>No puedes entrar al sistema.</p>
                <div class='w-100'>
                    <a href='../index.php' class='btn btn-entrar text-white text-decoration-none d-block'>
                        Volver al Inicio
                    </a>
                </div>
            </div>
        </div>
    </body>
    </html>