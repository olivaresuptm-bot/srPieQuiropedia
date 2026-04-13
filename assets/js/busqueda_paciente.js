
        function verificarEnter(e) {
            if (e.key === 'Enter') {
                realizarBusqueda();
            }
        }

        function realizarBusqueda() {
            let cedula = document.getElementById('inputBusqueda').value;
            let ficha = document.getElementById('resultadoFicha');
            let error = document.getElementById('errorBusqueda');

            if (cedula.trim() === "") return;

            fetch('../controllers/busqueda_paciente.php?cedula=' + cedula)
                .then(response => response.text())
                .then(data => {
                    if (data.trim() === "error") {
                        error.style.display = 'block';
                        ficha.style.display = 'none';
                    } else {
                        error.style.display = 'none';
                        ficha.innerHTML = data;
                        ficha.style.display = 'block';
                        // Efecto suave de scroll hacia el resultado
                        ficha.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                })
                .catch(err => console.error("Error en la búsqueda:", err));
        }

 
        document.addEventListener("DOMContentLoaded", function() {
            const inputVal = document.getElementById("inputBusqueda").value;
            
            // Si hay algo escrito en la barra...
            if (inputVal.trim() !== "") {
                setTimeout(() => {
                    // Buscamos el botón y simulamos un clic real
                    const botonBuscar = document.getElementById("btnBuscar");
                    if (botonBuscar) {
                        botonBuscar.click(); 
                    }
                }, 200);
            }
        });

