
        function toggleBuscador() {
            const panel = document.getElementById('panelBusqueda');
            if (panel.style.display === 'block') {
                panel.style.display = 'none';
            } else {
                panel.style.display = 'block';
                document.getElementById('inputBusqueda').focus();
            }
        }

        function verificarEnter(e) {
            if (e.key === 'Enter') {
                realizarBusqueda();
            }
        }

        function realizarBusqueda() {
            let cedula = document.getElementById('inputBusqueda').value;
            let ficha = document.getElementById('resultadoFicha');
            let error = document.getElementById('errorBusqueda');

            if (cedula === "") return;

            // Llamada al controlador que te proporcioné anteriormente
            fetch('../controllers/busqueda_paciente.php?cedula=' + cedula)
                .then(response => response.text())
                .then(data => {
                    if (data.includes("error") || data.trim() === "") {
                        error.style.display = 'block';
                        ficha.style.display = 'none';
                    } else {
                        error.style.display = 'none';
                        ficha.innerHTML = data;
                        ficha.style.display = 'block';
                    }
                });
        }
