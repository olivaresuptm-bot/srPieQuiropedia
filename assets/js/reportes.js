
    // Gráfico
    const ctx = document.getElementById('graficoIngresos').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($nombres_grafico); ?>,
            datasets: [{
                label: 'Ingresos Generados ($)',
                data: <?php echo json_encode($ventas_grafico); ?>,
                backgroundColor: 'rgba(13, 110, 253, 0.6)',
                borderColor: 'rgb(13, 110, 253)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: { y: { beginAtZero: true } }
        }
    });

    // Función para abrir el modal y configurar los botones
    function abrirModalPago(cedula, nombre, monto) {
        document.getElementById('nombre_pago_modal').innerText = nombre;
        document.getElementById('monto_pago_modal').innerText = monto + " $";
        
        // Aquí le decimos a los botones qué archivo ejecutar y le mandamos el parámetro liquidar=1
        document.getElementById('btn_descargar_pago').href = "../controllers/descargar_recibo_quiro.php?cedula=" + cedula + "&liquidar=1";
        document.getElementById('btn_enviar_pago').href = "../controllers/enviar_recibo_quiro.php?cedula=" + cedula + "&liquidar=1";
        
        var myModal = new bootstrap.Modal(document.getElementById('modalPago'));
        myModal.show();
    }

    // Recargar la página después de generar el recibo para ver el contador en 0
    function recargarPagina() {
        setTimeout(function() {
            window.location.reload();
        }, 1500); // Espera 1.5 seg a que se descargue el PDF y luego refresca
    }
